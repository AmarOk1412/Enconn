---
title: How to host your own Jitsi instance
subtitle: A short feedback from experience
date: 2019-05-12
tags: ["free software", "devops"]
---

For the local future [CHATONS](https://chatons.org/) (*Chatons* is a french word which means *kitten*), I am currently helping to host a new [Jitsi-meet](https://jitsi.org/jitsi-meet/) instance. This instance will be available at this address: https://conference.facil.services/ and this is a little post about how we did it and what problems we encountered.

# First step - The manual installation

For the story, the first attempt for hosting the instance worked during several weeks. But, after a migration to our final server, we started to see a lot of problems with that instance (due to the migration or the installation of the back-up system or the monitoring tools). Anyway, even the logs were odds.

I arrived in this project at this precise moment. So, I decided to start to do my own virtual machine, in my local network (bridge mode) to replicate the final server config (a Debian Stretch, 1 Go RAM, 2 CPUs) and I followed the instructions from this page: https://github.com/jitsi/jitsi-meet/blob/master/doc/quick-install.md

The only big difference between my setup and the final server is that the VM is only available in my local network, so I used an auto-signed certificate instead of our Let's Encrypt certificate. But we saw that the instance was functionnal and we did some conferences with multiple participants.

Because everything seemed to work, I did the same steps (with the auto-signed certificate) but on the final production server (in a clean state). Again, we did some tests and the instance was working great. So the next step is to setup the back-up system, monitoring tools and automate the installation of *Jitsi* and the certificate.

# Second step - Automate all the things

*Ansible* is used ot automate the installation of our servers. And, to avoid to re-invent the wheel, we use existing ansible roles made by others. For *Jitsi*, I found this project: https://github.com/freedomofpress/ansible-role-jitsi-meet

Even if the role is not up-to-date (last commit is from 2017), a [pull request](https://github.com/freedomofpress/ansible-role-jitsi-meet/pull/43) exists to update the role and is only 2 months old! Let's try with that patched role. So, I did this little playbook (`jitsi.yml`)

```yml
- name: Configure jitsi-meet server.
  hosts: [server]
  user: amarok
  vars:
    jitsi_meet_server_name: conference.facil.services
    jitsi_meet_lang: fr
  roles:
    - role: thefinn93.letsencrypt
      become: yes
      letsencrypt_email: "webmaster@{{ jitsi_meet_server_name }}"
      ssl_certificate: /etc/ssl/{{ jitsi_meet_server_name }}/server.cert
      ssl_certificate_key: /etc/ssl/{{ jitsi_meet_server_name }}/server.key
      letsencrypt_cert_domains:
        - "{{ jitsi_meet_server_name }}"
      tags: letsencrypt

    - role: freedomofpress.jitsi-meet
      jitsi_meet_ssl_cert_path: "/etc/ssl/{{ jitsi_meet_server_name }}/server.cert"
      jitsi_meet_ssl_key_path: "/etc/ssl/{{ jitsi_meet_server_name }}/server.key"
      become: yes
      tags: jitsi

```

I press the BIG launch button, *Ansible* is starting and... **FAIL**. The certificate can't be created.  :sad:

# Third step - Debugging the role

First problem, the challenge for the certificate is failing. It was a problem with the `nginx` server which were listening only on IPv4, so it was failing for the IPv6 challenge.

Let's retry. Re-click on the BIG launch button... certificate: **OK** ; installing *Jitsi*: **OK** ; reloading services: **NOK**. Shit, the website is not responding. Let's see on the server. I don't really understand why `nginx` is not started, but let's start it and see if it works. `service nginx restart`, I can see that *Jitsi* is running. I create a new room, nice, the camera is working. Let's try with somebody else... BOTH SIDES ARE CRASHING :sad:.

Maybe the problem is from the role. I purge all the server, and re-launch the role. Quick note, `jitsi-meet-web-config` can't be removed directly if `nginx` is removed before *Jitsi*. To fix it, you have to remove the following lines from `/var/lib/dpkg/info/jitsi-meet-web-config.postrm`

```bash
if [ -x "/etc/init.d/nginx" ]; then
    invoke-rc.d nginx reload
fi
```

So, the whole role is ok this time. I try to connect two computers and again - crash!

Let's check the logs. Basically for this kind of issue, *Jitsi* have at least two interesting log files:

+ `/var/log/jitsi/jicofo.log` which help me to find a little `ssl` mis-configuration while I used the auto-signed certificate. This first file is telling me that it can't find a bridge and indeed, `service status jitsi-videobridge` is showing that the bridge is down, killed after a few seconds of life.
+ `/var/log/jitsi/jvb.log` which are the video bridge's logs. I can see the following line: `Error: Could not find or load main class $JVB_EXTRA_JVM_PARAMS` which is actually a variable in ` /etc/jitsi/videobridge/config`. So, I remove the line from this file, restart the bridge, start a first device, now the second one. Nice, I can see both cameras! The instance is working now!

Let's finish the work by installing the monitoring tools, the back-up system, and now, the instance is complete and production ready!

# Conclusion


To conclude, if you want to host your own *Jitsi* instance:

+ You can generate your own *Let's Encrypt* certificate and follow the quick-install method from Jitsi. It will work great and you will be able to update your instance from time to time.
+ I didn't try in a dockerized environment, but i think this will also work. Some problems may exists, but [Santiago](https://github.com/santiagomr) is answering fast and already fixed his role! 
+ If you want to automate the whole process with *Ansible*, actually you can. The *Freedom of Press* role is working if you take the patched role. 

Finally, if you see some problems with your instances, actually, the logs are complete and useful.