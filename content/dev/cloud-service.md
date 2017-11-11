---
title: Cloud Service
subtitle: Synchronization back-end between Google Drive + Dropbox.
date: 2016-05-20
tags: ["student-project", "project", "software"]
bigimg: [{src: "/img/dev/cloud-service/_min.jpg", desc: "Cloud Service"}]
---


## Description

*Cloud Service* is a student project created to synchronize files between multiple Cloud services like *Google Drive* or *Dropbox* during the course *JXS*. I made the back-end part.
<!--more-->

This is a list of some features:

+ Get the auth address
+ Auth via a callback
+ Compatibility with *Google Drive* and *Dropbox*
+ Retrieve file informations
+ Push files
+ Move files
+ Delete files
+ Get service infos
+ Synchronize the file tree between multiple services
+ Create directories
+ Share file
+ Encrypt/Decrypt a file via PGP
+ Build with TravisCI
+ MVN Support
+ Begin the support of webhooks from Dropbox
+ Begin the configuration from a file.

For this project I use tools like *Jersey*, *Apache commonsIO*, *Apache httpcomponents*, *Maven* or *GnuPG*. Moreover, plugins like *maven-checkstyle-plugin* and *maven-javadoc-plugin* was added to generate reports for *mvn site*. Finally, I use *TravisCI* to build on each commits.

## Free

Code is under **WTFPL** and can be found on [Github](https://github.com/MerwanK/projetSI/)
