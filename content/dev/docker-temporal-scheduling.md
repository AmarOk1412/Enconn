---
title: Docker temporal scheduling
subtitle: How to?
date: 2016-05-05
tags: ["student-project", "project", "docker", "software"]
---

## Description

The goal of this project was to encode some videos on a cluster of Raspberry py. The encoding is divide in some task made by containers. The first step is to get the video to encode via a web interface (cf. the following image). Then, to split the video in several fragments (`N`). Then, `N` containers encode the fragments and finally, a last container re-assemble fragments.

<!--more-->

![The web interface](/img/dev/docker-temporal-scheduling/fileuploader.png)

The file system is shared between containers with **GlusterFS** and containers are launched through **Docker Swarm**.

## Docker Swarm

[Docker Swarm](https://docs.docker.com/swarm/) is a tool used to clusterize Docker containers. With this tool you can spatially schedule containers, for example, you can have a Docker on node 1 and explicitly ask (with the affinity system) to launch another container on the same node.

## Cluster redefinition

For this project, we need to wait for previous step containers to finish. For now, it's not possible to do that with Swarm. So we need to create a system to temporally schedule dockers. We can write our own version of Swarm and recreate a scheduler in `/swarm/cluster/state`. We just need to create the interface, but we need to answer to some questions first. In our use case, we need to launch containers in the good order (ensure that the container we wait exists to avoid inter-dependency problems an the ability to easily make dependencies with regex (the *merge* container wait for all *encode\**)).

Finally, to use our system, you just have to launch *swarm-manager* with the `-c state` option (in *cli/manage.go*)

To speak about the implementation, we add to the cluster structure an attribute `blockedContainers` (*map[string]\*cluster.Container*). When we launch a container, we check dependencies (given by `-e waitfor:container==foo`). If all dependencies are ok, we launch the container, else we add this container in the previous attribute. When a container finish, it sends a signal to Swarm we retrieve and try to launch previously blocked containers.

## Links

The code can be found [here](https://github.com/AmarOk1412/swarm/). And the documentation for Swarm is [here](https://docs.docker.com/swarm/).
