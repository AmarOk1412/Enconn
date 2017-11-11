---
title: RORI
subtitle: a modulable chatterbot
date: 2017-04-04
tags: ["software", "project", "rori"]
bigimg: [{src: "/img/dev/rori/_min.jpg", desc: "RORI"}]
---
Just a modulable chatterbot created and released for free under the WTFPL on [Github](https://github.com/AmarOk1412/rori).

<video controls="" width="70%">
  <source src="/videos/rori.webm" type="video/webm">
  Your browser does not support the video tag.
</video>

# How it works?

See [wiki](https://github.com/AmarOk1412/rori/wiki)

# Downloads

## Core server

This is the central point of RORI. This application get data from entry points, call modules to process this data and send data to endpoints to execute commands. [Download](https://github.com/AmarOk1412/rori_server)

## Irc bot

IRC Entry Module is an entry and an endpoint for RORI. This application run an IRC bot based on this library to interact with RORI. [Download](https://github.com/AmarOk1412/irc_entry_module)

## Discord bot

RORI Discord Bot is an entry and an endpoint for RORI. This application is designed for a bot user on Discord based on this library to interact with RORI. [Download](https://github.com/AmarOk1412/rori_discord_bot)

## Desktop Endpoint

This is a simple endpoint for RORI. Just a simple application to execute shell and music commands from rori_server on linux. [Download](https://github.com/AmarOk1412/rori_desktop_endpoint)
