---
title: P2P internals - episode 2
subtitle: ICE (RFC 6544, RFC 8445)
date: 2019-09-03
tags: ["p2p", "dev"]
bigimg: [{src: "/img/dev/p2p_ice/banner.jpg"}]
---

In the [first article](/post/p2p-internals-rfc6062/) of this series, we saw how to make an application (*DEL*) which is able to transfer a file from one peer to one another by successfully bypassing the NAT thanks to a TURN server. However, this technique is not really effective, because, in a lot of cases, a TURN server is completely useless. Indeed, if both peers are on the same network or both peers has an IPv6 address, the TURN server is not mandatory. Now, *Alice* wants to improve *DEL* to only use the TURN server as a fallback.

# ICE or how to choose the best path

There is **A LOT** of paths possible between two peers on the Internet. In fact, a peer can have several IP addresses. For example, a peer can have 2 local addresses (IPv4, IPv6), two public ips (IPv4, IPv6) and in our example, one relayed address (from the TURN in IPv6). So, our peer has 5 addresses. Now, let's imagine that the other peer got 5 addresses too, we already have 25 paths! But a lot of paths will not work (IPv4->IPv6, NATed ones, etc) and several protocols are used (TURN, STUN, etc). Choosing the best path is quickly a difficult task. ICE is here to manage this complexity.

The protocol is actually described in 3 RFCs (but ICE also depends on a lot of other protocols detailled in RFCs we will not describe here).

+ [RFC 5245](https://tools.ietf.org/html/rfc5245) which is now deprecated but contains an interesting part I will detail later.
+ [RFC 6544](https://tools.ietf.org/html/rfc6544) about ICE over TCP, where 5245 only describes the UDP part.
+ [RFC 8445](https://tools.ietf.org/html/rfc8445) the new one.

# Get around the TURN (whenever possible)

So that *DEL* can use the TURN only as a fallback and to support ICE, *Alice* has to implement the 3 steps needed by ICE.

## Build the list of candidates

The first step of ICE is to build a list of candidates and to prioritize them. To be done, *DEL* needs to find all tuples (ip, port) that the app is currently using. So, let's imagine that the application is listening on port 1412 on the local address, on port 1214 on the router (UPnP will come in another article) and port 4211 on the TURN server. We will have the following:

```
(local_v4, 1412) host
(local_v6, 1412) host
(public_v4, 1214) srflx
(public_v6, 1214) srflx
(turn_v6, 4211) srflx
```

Note: `host` and `srflx` (Server Reflexive) are the type of the candidate.

Each candidate is then prioritized via the formula described [here](https://tools.ietf.org/html/rfc8445#section-5.1.2.1).

Finally, the type of the transport is added (UDP, passive TCP, active TCP, simultaneous open TCP).

## Test the combinations

After that each peers know the list of candidates of the other side, ICE will start to negotiate the best path. First of all, by building a check list to process (described [here](https://tools.ietf.org/html/rfc8445#section-6.1.2)). This step is necessary to avoid to check the 25 links mentionned in the previous example.

Then, ICE process the check list in order. So the connectivity for each pair is tested. A big difference between *RFC 5245* and *8445* is that the first RFC details a process called *aggressive negotiation*. This kind of negotiation considerates every successful path as a possible nominated peer (a peer which will be used as the link). The major advantage of this negotiation is that latency is highly reduced. But its drawback is that the nominated peer is able to change during the process.

## Choose the best peer

Now that we know all the paths available. ICE can choose the best candidate (by priority). So, if Bob and Alice want to send files now, *DEL* will only use a TURN server if all previous links failed.

# Conclusion

ICE is a heavy and complex protocol which interacts with a lot of other protocols. Even if the creation of this protocol is due to the fact that NAT exists and that IPv6 is STILL not democratized, you certainly already used an application which uses ICE. In fact, WebRTC based applications use ICE a lot.

Finally, even if the protocol is hard to understand, there is some [lite implementations](https://tools.ietf.org/html/rfc8445#section-2.5).