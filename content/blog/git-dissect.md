---
title: Git dissect
subtitle: P2P internals - episode 4
date: 2021-03-25
tags: ["p2p", "dev", "git"]
---

It's been a long time since my previous post, but let's continue this serie about distributed systems with a tool that a lot of people use every day: *git*.

*Git* is a versioning tool. Unlike some systems like *subversion*, you don't need to have a server to use *git*. Every member of the project own a (partial or not) copy of the project and can directly send data to another member. So, even if today a lot of people use platforms like [Gitea](https://gitea.com/), [GitLab](https://gitlab.com/), [GitHub](https://github.com/), etc. it's possible to work without any of them (can be useful when the platform is down).

In this article I will **not describe how to use *git*** because there is already plenty of articles about this  (e.g. https://try.github.io/). If you want to learn how to use *git*, this article is not for you. In this article, I will describe what is going on when you do `git clone https://github.com/zestedesavoir/zmarkdown/` from the network perspective and will describe how to implement a minimalist *git* server working on a custom protocol.

# What is a git transport?

## The different layers

To exchange information between a server and a client, *git* uses *packfiles* transmitted via the `pack-protocol` or the `http-protocol`. Both protocols have a similar logic, but in this article we will just check the `pack-protocol`. For the curious, here is the [definition of the `http-protocol`](https://github.com/git/git/blob/master/Documentation/technical/http-protocol.txt) (note that the documentation is still incomplete).

The protocols are offering two services:

1. `upload-pack` (server side, `fetch-pack` client side) to transmit data from server to client. We will check this part in detail.
2. `received-pack` (server side, `send-pack` client side) to send data from client to server.

Finally, protocols are used over various transports. [Four kinds of transports](https://git-scm.com/book/en/v2/Git-on-the-Server-The-Protocols) are supported by default: `ssh://` (SSH), `file://` (a classic pipe), `git://` ([a TCP server](https://git-scm.com/book/en/v2/Git-on-the-Server-Git-Daemon) generally combined with SSH for authentication) for the `pack-protocol` and `http://` for the `http-protocol`.

Note: the HTTP protocol is divided in two implementations. `Dumb` which are classic web servers providing files and `smart` where servers answer to requests.

So, this is what a *git* transport looks like:

| `upload-pack` OR `receive-pack` | `upload-pack` OR `receive-pack` |
|---------------------------------|---------------------------------|
| `pack-protocol` + `pack-format` | `http-protocol` + `pack-format` |
| `ssh://`, `file://`, `git://`   | `http://` ou `https://`.        |

And this is what I will describe:

| `upload-pack`                   |
|---------------------------------|
| `pack-protocol` + `pack-format` |
| `custom://`                     |

Finally, each protocols define two big notions, the *pkt-line format* and the *pack format*.


### The pkt-line format

A *pkt-line* is a binary string. The first four bytes of this string are used to store the total length of the line via a hexadecimal string. So, a *pkt-line* starting with `0023` (`0x30,0x30,0x32,0x33`) is a *pkt-line* of 35 bytes.

There is some other things to know like:
+ If the string is not a binary string (e.g. without any `\0`) it should finish with `LF` (`\n`) which is counted in the size.
+ It's forbidden to send the empty line: `0004`. The empty line (called *flush packet*) is `0000`.
+ The maximum size of a *pkt-line* is 65520 bytes, so 65516 bytes of data.

E.g. if you want to send "foobar" the *pkt-line* will be `000bfoobar\n`.

### The pack format

Once the negotiation part is done, it's time to transmit objects! Those objects are transmitted via the pack format. A lot of libraries (like *libgit2* that we will use later) already have the abstraction to generate objects in this format, so, I won't dwell on it here.

However, if you want to fully understand how to create a *packfile*, you can read this [page](https://git-scm.com/docs/pack-format).

To summarize, this is generally what you will see:

| Header | Data | Checksum |
|--------|------|----------|
| signature (PACK), version (2), number of objects | type, content | trailer |

Where objects are transferred in four types (commit, tree, tag, blob) and can be compressed in something called a *deltified representation*.

## git upload-pack: utterly useless, therefore essential

*git* is a command with a loooooong list of subprograms. A lot are (hopefully) not necessary, but they are still interesting; like `git http-backend` (to play with the `http-protocol` mentioned before).  Yet, some of these commands only exist to be invoked by others (but **never** by the end-user). It's the case for `git upload-pack` that we will use to start to play with the `pack-protocol`. This program is used by `git fetch-pack` which is used by `git fetch`. It allows us to do a `git clone/fetch` by communicating with a server with *pkt-lines*.

This is an example of `git upload-pack` used on [ZMarkdown's repository](https://github.com/zestedesavoir/zmarkdown):

```bash
# https://github.com/zestedesavoir/zmarkdown
# `git clone git@github.com:zestedesavoir/zmarkdown.git`
# 7e31d5dcc19fa412df5674676f5fb7092d035275 is the current master

$ echo "0032want 7e31d5dcc19fa412df5674676f5fb7092d035275\n00000009done\n" | git upload-pack zmarkdown > dump_git_upload_pack
```

To summarize, this command retrieves the tree of commits behind (and including) the commit `7e31d5dcc19fa412df5674676f5fb7092d035275` and write the whole content into *dump_git_upload_file*. You can check the content of this file if you want, but it's not important. However, if one day you need to read what a *git* server sends, you can check the file via `xxd dump_git_upload_pack | less` if you need to dissect a *packfile* or just `less dump_git_upload_pack` if you only care about the beginning of the transmission.

# How a git server works?

Let's get down to the nitty gritty of how a git server works by exploring how to implement a minimal server answering to a `git clone` or `git fetch`.

## References discovery

First, the client announces to the server what it wants by sending the `git upload-pack` command following this format:

| pkt_len (4 bytes)  | git-upload-pack | space | pathname | \0 | host-parameter | \0 | \0 | extra-parameters | \0 |
---------------------|-----------------|-------|----------|----|----------------|----|----|------------------|----|

For example:

```
003dgit-upload-pack zmarkdown.git\0host=localhost\0\0version=1\0
```

Note that `version=1` is the only extra parameter accepted for now, and is totally optional. This will be valid too:

```
0032git-upload-pack zmarkdown.git\0host=localhost\0
```

Then, for our server, it's time to send its references so that the client will know about what they can download. This is what the documentation says:

+ If `version` is present in the extra parameters, the server starts by announcing its version `000eversion 1\n`.
+ Then it announces all known references and pointed hashes.
+ The list of references is sorted by name.
+ If HEAD is present, it must be the first announced reference.
+ The first reference is followed by `\0` followed by the supported features.
+ And it's finished by a `0000` (*flush-pkt*).

Thus, the beginning of our negotiation looks like:

```
C: 0028git-upload-pack /zdshost=localhost\0host=localhost\0
S: 006ac29eb686c3638633bff5b852ee1ed76211882ba1 HEAD\0side-band side-band-64k shallow no-progress include-tag
S: 003f7300c6f39366e2c0bcaf99efc05cc45e8511a384 refs/heads/master
S: 0046c29eb686c3638633bff5b852ee1ed76211882ba1 refs/remotes/origin/HEAD
S: 0048c29eb686c3638633bff5b852ee1ed76211882ba1 refs/remotes/origin/master
S: 0000
```

Our server can support a lot of features I will not describe in this article, but if both sides support `side-band` or `side-band-64k` the server sends data in a multiplexed format by chunks of 999 bytes for `side-band` or 65519 bytes for `side-band-64k` and one byte to specify the channel (described later). `no-progress` means that the server doesn't send the progress. Other properties are described [here](https://git-scm.com/docs/protocol-capabilities).

Now that our client knows what the server has, it's time to negotiate the packfile! 

## Packfile negotiation

There's basically two possibilities for the client during this step. Either the client doesn't need any other data (for example after a `ls-remote`). In this case, it just sends a `0000` to close the connection. Or it enters in the negotiation phase. In this case, the client announces what commits are necessary, what commit it has and the supported features.

Then we can announce the wanted objects via some `want` lines (at least one, maximum 32). Wanted hashes must be in the list announced by the server. If the client has *shallowed copies* of some objects, it must be announced. Also, the maximum wanted depth is transmitted. Then, to obtain the minimal *packfile* a list of already downloaded commits must be sent.

Both the want-list and the have-list are followed by a `0000` packet.

After each have-list sent by the client, the server will ack common objects. Three different methods are supported:

+ `multi-ack` where:
  + `ACK obj-id continue` is sent for the common commit,
  + `ACK` for every commit when a common commit is found,
  + `NAK` to wait a new response by the client
+ `multi-ack-detailed`:
  + `ACK obj-id ready` and `ACK obj-id common` can be sent
+ else:
  + `ACK obj-id` on the first common commit then nothing until client sends `done`.
  + `NAK` if it receives a flush packet and no common commit is found.

Then, the client decides if enough information is received. In that case, it sends a `0009done\n` packet. Else, a new have-list is sent until maximum 256 `have` (else all objects can be downloaded).

So, a clone looks like:

```
C: 004dwant c42412dfee248e8acbd21df6038991dff6fd1b2a side-band-64k include-tag\n
C: 0000
C: 0009done\n
S: 0008NAK\n
```

and a fetch:

```
C: 0057want 04930e2b1a6a4f92cb6bbc4fac9237383f052b81 multi_ack side-band-64k include-tag\n
C: 0000
C: 0032have 3648793596d9a971632b86805949d036ab5918a0\n
C: 0000
S: 003aACK 3648793596d9a971632b86805949d036ab5918a0 continue\n
S: 0008NAK\n
C: 0009done\n
S: 0031ACK 3648793596d9a971632b86805949d036ab5918a0\n
```

## Sending data

Finally, it's time to send the objects into a *packfile*. `libgit2`, the library I will use in the last part has the `PackBuilder` object, which is useful to prepare packfiles. On our side, it's just a matter of adding wanted commits. So, we add every commit between the wanted one and the commit with only one parent below the common commit. For example, if we add commits from two branches (and the common is only located in one branch) we add everything until the common commit of these two branches.

I also talked previously about `side-band-64k`. It means that the packfile will be sent by chunks of 64k via the pkt-line format. After the 4 bytes for the size, the following byte is the channel (`0x1` for the data channel, `0x2` for progress, `0x3` for errors) and finally `65515` bytes for the data.

To terminate the transfer, a flush packet is sent.

And that's it! Our `clone` is finished! Now let's implement a minimal git server supporting fetch and clone on a custom transport:

# A minimal git server in Rust

Note: you can get the code [here](https://github.com/AmarOk1412/p2p-internals-git/).

I'll use [libgit2](https://libgit2.org/) because it provides methods to create the packfiles and interact with *git* repositories. It also handles custom transports. This lib has many wrappers, and I'll use the Rust one because I think it's the most complete (outside the C one): [git2-rs](https://github.com/rust-lang/git2-rs/). The main problem is that the [documentation](https://libgit2.org/libgit2/#HEAD) is pretty incomplete and the whole part on *transport* is missing.
Let's use a completely fictional protocol, called WOLF (because *git* transmits *packfiles* we can talk about [wolf-pack](https://www.youtube.com/watch?v=u4_SbUcv7OY)). This protocol is useless and only adds four bytes ("WOLF") before each packet.

Our `src/wolftransport.rs` will be:

```rust
// https://docs.rs/git2/0.13.17/git2/transport/fn.register.html

use bichannel::{ SendError, RecvError };
use git2::Error;
use git2::transport::SmartSubtransportStream;
use git2::transport::{Service, SmartSubtransport, Transport};
use std::io;
use std::io::prelude::*;
use std::sync::{Arc, Mutex};

static HOST_TAG: &str = "host=";

// Note: this transport is useless, but is only here for an example.
// Every packet is predeceased by a header of 4 bytes: "WOLF".
pub struct WolfChannel
{
    pub channel: bichannel::Channel<Vec<u8>, Vec<u8>>,
}

impl WolfChannel
{
    pub fn recv(&self) -> Result<Vec<u8>, RecvError> {
        let res = self.channel.recv();
        if !res.is_ok() {
            return res;
        }
        let mut res = res.unwrap();
        res.drain(0..4);
        Ok(res)
    }

    pub fn send(&self, data: Vec<u8>) -> Result<(), SendError<Vec<u8>>> {
        let mut to_send = "WOLF".as_bytes().to_vec();
        to_send.extend(data);
        self.channel.send(to_send)
    }
}

pub type Channel = Arc<Mutex<WolfChannel>>;

/**
 * Now, let's write a smart transport for git2-rs to answer to the scheme wolf://
 */
struct WolfTransport {
    channel: Channel,
}

struct WolfSubTransport {
    action: Service,
    channel: Channel,
    url: String,
    sent_request: bool
}

// git2 will use our smart transport for wolf://
pub unsafe fn register(channel: Channel) {
    git2::transport::register("wolf", move |remote| factory(remote, channel.clone())).unwrap();
}

fn factory(remote: &git2::Remote<'_>, channel: Channel) -> Result<Transport, Error> {
    Transport::smart(
        remote,
        false, // rpc = false, this means that our channel is connected during all the transaction.
        WolfTransport {
            channel
        },
    )
}

impl SmartSubtransport for WolfTransport {
    /**
     * Generate a new transport to use (because rpc = false), we will only answer to upload-pack-ls & receive-pack-ls
     */
    fn action(
        &self,
        url: &str,
        action: Service,
    ) -> Result<Box<dyn SmartSubtransportStream>, Error> {
        Ok(Box::new(WolfSubTransport {
            action,
            channel: self.channel.clone(),
            url: String::from(url),
            sent_request: false,
        }))
    }

    fn close(&self) -> Result<(), Error> {
        Ok(())
    }
}

impl WolfTransport {
    fn generate_request(cmd: &str, url: &str) -> Vec<u8> {
        // url format = wolf://host/repo
        // Note: This request is sent when the client's part is starting, to notify the server about what we want to do.
        let sep = url.rfind('/').unwrap();
        let host = url.get(7..sep).unwrap();
        let repo = url.get(sep..).unwrap();

        let null_char = '\0';
        let total = 4                                   /* 4 bytes for the len len */
                    + cmd.len()                         /* followed by the command */
                    + 1                                 /* space */
                    + repo.len()                        /* repo to clone */
                    + 1                                 /* \0 */
                    + HOST_TAG.len() + host.len()       /* host=server */
                    + 1                                 /* \0 */;
        let request = format!("{:04x}{} {}{}{}{}{}", total, cmd, repo, null_char, HOST_TAG, host, null_char);
        request.as_bytes().to_vec()
    }
}

impl Read for WolfSubTransport {
    fn read(&mut self, buf: &mut [u8]) -> io::Result<usize> {
        // send a request when  starting
        if !self.sent_request {
            let cmd = match self.action {
                Service::UploadPackLs => "git-upload-pack",
                Service::UploadPack => "git-upload-pack",
                Service::ReceivePackLs => "git-receive-pack",
                Service::ReceivePack => "git-receive-pack",
            };
            let cmd = WolfTransport::generate_request(cmd, &*self.url);
            let _ = self.channel.lock().unwrap().send(cmd);
            self.sent_request = true;
        }
        // Write what the server sends into buf.
        let mut recv = self.channel.lock().unwrap().recv().unwrap_or(Vec::new());
        let mut iter = recv.drain(..);
        let mut idx = 0;
        while let Some(v) = iter.next() {
            buf[idx] = v;
            idx += 1;
        }
        Ok(idx)
    }
}

impl Write for WolfSubTransport {
    fn write(&mut self, data: &[u8]) -> io::Result<usize> {
        let _ = self.channel.lock().unwrap().send(data.to_vec());
        Ok(data.len())
    }
    fn flush(&mut self) -> io::Result<()> {
        // Unused in our case
        Ok(())
    }
}
```

Then, we can do the server - `src/server.rs`:

```rust
use crate::wolftransport::Channel;
use git2::{ Buf, Oid, Repository, Sort };
use std::cmp::{ max, min };
use std::i64;
use std::str;

static FLUSH_PKT: &str = "0000";
static NAK_PKT: &str = "0008NAK\n";
static DONE_PKT: &str = "0009done\n";
static WANT_CMD: &str = "want";
static HAVE_CMD: &str = "have";
static UPLOAD_PACK_CMD: &str = "git-upload-pack";

/**
 * Represents a git server working a our custom transport, serving a given repository
 */
pub struct Server {
    repository: Repository,
    channel: Channel,
    wanted: String,
    common: String,
    have: Vec<String>,
    buf: Vec<u8>,
    stop: bool,
}

impl Server {
    /**
     * Creates a new Server for a given path and transport
     * @param channel       Our custom transport
     * @param path          Repository
     * @return the server
     */
    pub fn new(channel: Channel, path: &str) -> Self {
        let repository = Repository::open(path).unwrap();
        Self {
            repository,
            channel,
            wanted: String::new(),
            common: String::new(),
            have: Vec::new(),
            buf: Vec::new(),
            stop: false,
        }
    }

    pub fn run(&mut self) {
        // stop is set to true when the clone finished.
        // until then, answer to the client.
        while !self.stop {
            let buf = self.channel.lock().unwrap().recv().unwrap();
            self.recv(buf);
        }
    }

    fn recv(&mut self, buf: Vec<u8>) {
        let mut buf = Some(buf);
        let mut need_more_parsing = true;
        // Then client can send multiple pkt-lines in one buffer,
        // so, parse until ready to receive new orders.
        while need_more_parsing {
            need_more_parsing = self.parse(buf.take());
        }
    }

    fn parse(&mut self, buf: Option<Vec<u8>>) -> bool {
        // Parse pkt len
        // Reference: https://github.com/git/git/blob/master/Documentation/technical/protocol-common.txt#L51
        // The first four bytes define the length of the packet and 0000 is a FLUSH pkt
        if buf.is_some() {
            self.buf.append(&mut buf.unwrap());
        }
        let pkt_len = str::from_utf8(&self.buf[0..4]).unwrap();
        let pkt_len = max(4 as usize, i64::from_str_radix(pkt_len, 16).unwrap() as usize);
        let pkt : Vec<u8> = self.buf.drain(0..pkt_len).collect();
        let pkt = str::from_utf8(&pkt[0..pkt_len]).unwrap();
        println!("RECV: {}", pkt);

        if pkt.find(UPLOAD_PACK_CMD) == Some(4) {
            // Cf: https://github.com/git/git/blob/master/Documentation/technical/pack-protocol.txt#L166
            // References discovery
            println!("Upload pack command detected");
            // NOTE: the upload-pack command can contains some parameters like version=1
            // For now git supports only version=1 so we can ignore this part for this article.
            self.send_references_capabilities();
        } else if pkt.find(WANT_CMD) == Some(4) {
            // Reference:
            // https://github.com/git/git/blob/master/Documentation/technical/pack-protocol.txt#L229
            // NOTE: a client may sends more than one want. Moreover, the first want line will sends
            // wanted capabilities such as `side-band-64, multi-ack, etc`. To simplify the code, we
            // just ignore capabilities & mutli-lines
            self.wanted = String::from(pkt.get(9..49).unwrap()); // take just the commit id
            println!("Detected wanted commit: {}", self.wanted);
        } else if pkt.find(HAVE_CMD) == Some(4) {
            // Reference:
            // https://github.com/git/git/blob/master/Documentation/technical/pack-protocol.txt#L390
            // NOTE: improve this part for multi-ack
            let have_commit = String::from(pkt.get(9..49).unwrap()); // take just the commit id
            if self.common.is_empty() {
                if self.repository.find_commit(Oid::from_str(&*have_commit).unwrap()).is_ok() {
                    self.common = have_commit.clone();
                    println!("Set common commit to: {}", self.common);
                }
            }
            self.have.push(have_commit);
        } else if pkt == DONE_PKT {
            // Reference:
            // https://github.com/git/git/blob/master/Documentation/technical/pack-protocol.txt#L390
            // NOTE: Do not do multi-ack, just send ACK + pack file
            // In case of no common base, send NAK
            println!("Peer negotiation is done. Answering to want order");
            let send_data = match self.common.is_empty() {
                true => self.nak(),
                false => self.ack_first(),
            };
            if send_data {
                self.send_pack_data();
            }
            self.stop = true;
        } else if pkt == FLUSH_PKT {
            if !self.have.is_empty() {
                // Reference:
                // https://github.com/git/git/blob/master/Documentation/technical/pack-protocol.txt#L390
                // NOTE: Do not do multi-ack, just send ACK + pack file In case of no common base ACK
                self.ack_common();
                self.nak();
            } else if self.wanted.is_empty() {
                self.stop = true;
            }
        } else {
            println!("Unwanted packet received: {}", pkt);
        }
        self.buf.len() != 0
    }

    fn send_references_capabilities(&self) {
        let current_head = self.repository.refname_to_id("HEAD").unwrap();
        let mut capabilities = format!("{} HEAD\0side-band side-band-64k shallow no-progress include-tag multi_ack", current_head);
        capabilities = format!("{:04x}{}\n", capabilities.len() + 5 /* size + \n */, capabilities);

        for name in self.repository.references().unwrap().names() {
            let reference: &str = name.unwrap();
            let oid = self.repository.refname_to_id(reference).unwrap();
            capabilities += &*format!("{:04x}{} {}\n", 6 /* size + space + \n */ + 40 /* oid */ + reference.len(), oid, reference);
        }

        print!("Send: {}", capabilities);
        self.channel.lock().unwrap().send(capabilities.as_bytes().to_vec()).unwrap();
        println!("Send: {}", FLUSH_PKT);
        self.channel.lock().unwrap().send(FLUSH_PKT.as_bytes().to_vec()).unwrap();
    }

    fn nak(&self) -> bool {
        print!("Send: {}", NAK_PKT);
        self.channel.lock().unwrap().send(NAK_PKT.as_bytes().to_vec()).is_ok()
    }

    fn ack_common(&self) -> bool {
        let length = 18 /* size + ACK + space * 2 + continue + \n */ + self.common.len();
        let msg = format!("{:04x}ACK {} continue\n", length, self.common);
        print!("Send: {}", msg);
        self.channel.lock().unwrap().send(msg.as_bytes().to_vec()).is_ok()
    }

    fn ack_first(&self) -> bool {
        let length = 9 /* size + ACK + space + \n */ + self.common.len();
        let msg = format!("{:04x}ACK {}\n", length, self.common);
        print!("Send: {}", msg);
        self.channel.lock().unwrap().send(msg.as_bytes().to_vec()).is_ok()
    }

    fn send_pack_data(&self) {
        println!("Send: [PACKFILE]");
        // Note: this part of the code adds every commits into
        // the packfile until a commit announced by the client
        // is found AND a common parent is found (or until the
        // initial commit).
        let mut pb = self.repository.packbuilder().unwrap();
        let fetched = Oid::from_str(&*self.wanted).unwrap();
        let mut revwalk = self.repository.revwalk().unwrap();
        let _ = revwalk.push(fetched);
        let _ = revwalk.set_sorting(Sort::TOPOLOGICAL);

        let mut parents : Vec<String> = Vec::new();
        let mut have = false;

        while let Some(oid) = revwalk.next() {
            let oid = oid.unwrap();
            let oid_str = oid.to_string();
            have |= self.have.iter().find(|&o| *o == oid_str).is_some();
            if let Some(pos) = parents.iter().position(|p| *p == oid_str) {
                parents.remove(pos);
            }
            if have && parents.is_empty() {
                // All commits are fetched
                break;
            }
            let _ = pb.insert_commit(oid);
            let commit = self.repository.find_commit(oid).unwrap();
            let mut commit_parents = commit.parents();
            // Make sure to explore every branches.
            while let Some(p) = commit_parents.next() {
                parents.push(p.id().to_string());
            }
        }

        // Note: the buf can be huge. Packbuilder has some methods to get chunks.
        let mut data = Buf::new();
        let _ = pb.write_buf(&mut data);

        let len = data.len();
        let data : Vec<u8> = data.to_vec();
        let mut sent = 0;
        while sent < len {
            // cf https://github.com/git/git/blob/master/Documentation/technical/pack-protocol.txt#L166
            // In 'side-band-64k' mode it will send up to 65519 data bytes plus 1 control code, for a
            // total of up to 65520 bytes in a pkt-line.
            let pkt_size = min(65515, len - sent);
            // The packet is Size (4 bytes), Control byte (0x01 for data), pack data.
            let pkt = format!("{:04x}", pkt_size + 5 /* size + control */);
            self.channel.lock().unwrap().send(pkt.as_bytes().to_vec()).unwrap();
            self.channel.lock().unwrap().send(b"\x01".to_vec()).unwrap();
            self.channel.lock().unwrap().send(data[sent..(sent+pkt_size)].to_vec()).unwrap();
            sent += pkt_size;
        }

        println!("Send: {}", FLUSH_PKT);
        // And finish by a little FLUSH
        self.channel.lock().unwrap().send(FLUSH_PKT.as_bytes().to_vec()).unwrap();
    }
}
```

Finally `src/main.rs`:

```rust
mod wolftransport;
mod server;

use bichannel::channel;
use wolftransport::WolfChannel;
use git2::build::RepoBuilder;
use server::Server;
use std::env;
use std::sync::{Arc, Mutex};
use std::thread;
use std::path::Path;

fn main() {
    let args: Vec<String> = env::args().collect();
    if args.len() != 3 {
        println!("Usage: ./p2p-internal-git <src_dir> <dest_dir>");
        return;
    }
    
    let src_dir = args[1].clone();
    let dest_dir = Path::new(&args[2]);

    // For fetch, comment the 4 following lines
    if dest_dir.is_dir() {
        println!("Can't clone into an existing directory");
        return;
    }

    // Note: here we use a mpsc::channel for demo purposes, but
    // the transport can be on top of anything you want/need.
    // It can be replaced by a real server with TLS support for
    // example.
    let (server_channel, transport_channel) = channel();
    let transport_channel = Arc::new(Mutex::new(WolfChannel {
        channel: transport_channel
    }));
    let server_channel = Arc::new(Mutex::new(WolfChannel {
        channel: server_channel
    }));
    unsafe {
        wolftransport::register(transport_channel);
    }

    let server = thread::spawn(move || {
        println!("Starting server for {}", src_dir);
        let mut server = Server::new(server_channel, &*src_dir);
        server.run();
    });

    // For fetch
    // let repository = git2::Repository::open(dest_dir).unwrap();
    // let mut remote = repository.remote_anonymous("wolf://localhost/zds").unwrap();
    // let mut fo = git2::FetchOptions::new();
    // remote.fetch(&[] as &[&str], Some(&mut fo), None).unwrap();

    // For clone
    RepoBuilder::new().clone("wolf://localhost/zds", dest_dir).unwrap();
    // Note: "wolf://" triggers our registered transport. localhost/zds is unused
    // as our server only serves one repository and the address is not resolved.
    println!("Cloned into {:?}!", dest_dir);

    server.join().expect("The server panicked");
}
```

And `Cargo.toml`:

```
[package]
name = "p2p-internals-git"
version = "0.1.0"
authors = ["AmarOk"]
edition = "2018"

[dependencies]
bichannel = "0.0.4"
git2 = { git = "https://github.com/AmarOk1412/git2-rs" }
```

Now, we can use the program to copy a *git* repository from one directory to another:


```bash
# Note, multi-ack is not supported, so just clone the master branch without the tags
 amarok@tars3  ~/Projects/p2p-internals-git   main  git clone -b master --single-branch --no-tags https://github.com/zestedesavoir/zmarkdown.git
Cloning into 'zmarkdown'...
remote: Enumerating objects: 1207, done.
remote: Counting objects: 100% (1207/1207), done.
remote: Compressing objects: 100% (206/206), done.
remote: Total 16609 (delta 1006), reused 1192 (delta 1001), pack-reused 15402
Receiving objects: 100% (16609/16609), 10.85 MiB | 2.25 MiB/s, done.
Resolving deltas: 100% (11966/11966), done.
# Then, the magic:
 amarok@tars3  ~/Projects/p2p-internals-git   main  ./target/debug/p2p-internals-git zmarkdown zmarkdown-copy                                   
Starting server for zmarkdown
RECV: 0028git-upload-pack /zdshost=localhost
Upload pack command detected
Send: 0074c42412dfee248e8acbd21df6038991dff6fd1b2a HEADside-band side-band-64k shallow no-progress include-tag multi_ack
Send: 003fc42412dfee248e8acbd21df6038991dff6fd1b2a refs/heads/master
Send: 0046c42412dfee248e8acbd21df6038991dff6fd1b2a refs/remotes/origin/HEAD
Send: 0048c42412dfee248e8acbd21df6038991dff6fd1b2a refs/remotes/origin/master
Send: 0000
RECV: 0057want c42412dfee248e8acbd21df6038991dff6fd1b2a multi_ack side-band-64k include-tag 

Detected wanted commit: c42412dfee248e8acbd21df6038991dff6fd1b2a
RECV: 0000
RECV: 0009done

Peer negotiation is done. Answering to want order
Send: 0008NAK
Send: [PACKFILE]
Send: 0000
Cloned into "zmarkdown-copy"!
# And we can validate
 amarok@tars3  ~/Projects/p2p-internals-git   main  git -C zmarkdown rev-parse HEAD
c42412dfee248e8acbd21df6038991dff6fd1b2a
 amarok@tars3  ~/Projects/p2p-internals-git   main  git -C zmarkdown-copy rev-parse HEAD 
c42412dfee248e8acbd21df6038991dff6fd1b2a
```

And that's all! We did a minimal *git* server able to clone a repository. Sure, it's far from complete and there is a lot of missing features, but I hope I have enlightened you about how *git* exchanges data between a client and a server.

## References

Thanks for reading! If you want to dig the subject, this is a list of references I used for this article or for related projects:

+ https://libgit2.org/libgit2/#HEAD (**!!Warning, the doc is pretty incomplete, the transport part is missing!!**)
+ https://docs.rs/git2/0.13.17/git2/ the Rust wrapper, which is pretty good
+ https://git-scm.com/docs/ & https://github.com/git/git/blob/master/Documentation/technical/ describing how protocols used by *git* works.
+ https://github.com/rust-lang/git2-rs/tree/master/git2-curl et https://github.com/libgit2/libgit2/blob/main/src/transports/git.c for custom transport with *libgit2*
+ https://github.com/AmarOk1412/p2p-internals-git/
