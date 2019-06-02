---
title: P2P internals - episode 1
subtitle: TURN over TCP (RFC 6062)
date: 2019-06-01
tags: ["p2p", "dev"]
bigimg: [{src: "/img/dev/p2p_rfc6062/banner.jpg"}]
---

I love distributed systems for a various of reasons. But these systems are complex and require interactions with many layers. Even if a lot of distributed systems and software exists, such systems can be difficult to use for several reasons. Today, our operating systems tend to avoid doing as many calculations as possible (i.e. mobile devices need to save battery and avoid data consumption). Also, IPv4 is still massively used so NATs are still needed, interfaces are mainly created to interact with servers, etc. This is why I want to explain some common problems we can encounter when creating a peer to peer system and to describe existing solutions.

So, **p2p internals** is a serie of articles made to explain how distributed systems work. In this first article, let's imagine Alice wants to create a peer to peer software that transmit files directly from one device to one another as we have today in our browsers (ShareDrop for example, thanks to webrtc), p2p chat applications (I will take a lot of examples from [Jami](https://jami.net)), file transfer applications, etc. Let's call the application *DEL* (*del* means *share* in Norwegian and looks like *delete*).

# First transfers

So, *DEL* is really simple to understand. For a transfer, two clients are necessary, one on each devices. Each clients will get several addresses from different networks (LAN/WAN). An address is in fact composed by an IP and a port where the client is listening. Then the user provides a file to share and the address of the peer. Finally, a TCP link will be created and the file will be transmitted via this link. Alice starts to send files between her devices. She is happy because she did her first p2p application. She needs to show *DEL* to Bob.

Note that the link MUST be a TCP link, because UDP is not reliable and you don't want to miss packets.

As soon as Bob receives Alice's message, he starts *DEL* to share some pictures with Alice… but nothing is transmitted. The transfer is failing and he cannot get a direct link with Alice…

In fact, the creation of the link can fail due to a lot of reasons. The most common reason is the **NAT**. I think I will have the time to describe what is this thing in the next episodes, but basically the **NAT** is something created to have more addresses available on the internet but it also makes your local address unavailable from other networks, unless if you ask your router to forward the incoming traffic or if you use a NAT traversal techniques such as *UPnP, STUN, TURN*, etc.


# Let's avoid the NAT!

Don't be scare of the *NAT*! Many applications can pass through this problem, and *DEL* too! In fact, a lot of literature exists on the subject. Today, I will just explain how a *TURN* (*Traversal Using Relays around NAT*) server works (and only for TCP). Other methods exist, but these techniques only need a *TURN* server available. The process is fully described in [RFC 6062](https://tools.ietf.org/html/rfc6062). To summarize, a *TURN* server will be used as a relay for the connection with Alice. Instead trying to open a connection directly with Bob, the client opens a connection with a *TURN* server. This *TURN* will open a port and Alice will be able to connect to this port (if the TURN server accepts connections) or the *TURN* server will try to connect to Alice (if Bob send data). So, the NAT will not block the connection, because Alice will never contact Bob, but will connect to a point where Bob is listening.

This will looks like that:

![TURN - RFC 6062](/img/dev/p2p_rfc6062/turn.jpg)


# Technical overview

So, now that *DEL* needs to be compatible with a *TURN* server, this is how the application will work.

First, *DEL* has to open a connection with a TURN server and send a request asking for a TCP transport (the `REQUESTED-TRANSPORT` attribute). The TURN server opens a new port and listen for incoming connections. The tuple (ip, port) of the TURN is called a relayed address. So, *DEL* will have 2 addresses, the one from the local network and the relayed one. This first connection (Bob->TURN) is called the control connection. Now, there is two possibilities:

+ the *TURN* server can be used to send data. But, it's not the case here.
+ the *TURN* server can be used to receive data.

For the second case, Bob's client will send to the *TURN* a `CreatePermission` request to allow Alice to connect to the relayed address. Then Alice is able to connect to the relayed address. When she does, Bob's client will receive a `ConnectionAttempt` request with an id.

If Bob's client accepts the connection, it will initiate a new connection to the *TURN* server. This new connection is the data connection. And then, the client will send a `ConnectionBind` request with the previously received id. Finally, the TCP link is here! Bob can share files with Alice without any NAT issues!

# More details

If you want to dig the subject, here is some links:

+ I wrote how the file sharing feature works in [Jami](https://jami.net) and how to setup a *TURN* server here: https://git.jami.net/savoirfairelinux/ring-project/wikis/tutorials/file-transfer
+ Coturn is a easy to setup *TURN* server: https://github.com/coturn/coturn 
+ RFC 6062 will be supported in PJProject 2.9 (https://trac.pjsip.org/repos/ticket/2197, https://github.com/pjsip/pjproject/commit/fa6616c43c7e19797084f4e02a67d1fb6fd99473)