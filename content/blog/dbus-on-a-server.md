---
title:  Dbus on a server
subtitle:  and without any graphical session
date: 2019-08-20
tags: ["software", "dbus", "quickie"]
---

Some time ago, I had to run a script to test an application by sending dbus calls. The script was working great, until I moved it on a server with no graphical session. I ran the script as a System V init script, but you can encounter the same problem in a terminal or in a cron. The thing is that a dbus session is generally managed by KDE, Gnome or your graphical session.

# How to solve it

The important thing to remember with dbus is you can share a dbus session via `DBUS_SESSION_BUS_ADDRESS` (an environment variable). So, the key is to save `DBUS_SESSION_BUS_ADDRESS` in a place you can read from all the scripts you want a dbus session.

# Example

```sh
sudo dnf install dbus-x11 # or apt-get install dbus-x11
dbus-launch --auto-syntax
env | grep DBUS_SESSION_BUS_ADDRESS > /tmp/env
# On another terminal
export `cat /tmp/env`
dbus-monitor
```

And that's it!