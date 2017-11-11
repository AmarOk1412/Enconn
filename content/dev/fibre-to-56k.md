---
title: Passer de la fibre au 56k
subtitle: Ou comment baisser la qualité de son réseau
date: 2017-07-07
tags: ["software", "network", "linux"]
---

Contenu posté sur [Zeste de Savoir](https://zestedesavoir.com/contenus/2012/passer-de-la-fibre-au-56k/)

Vous avez peut-être chez vous un réseau avec un débit trop élevé ou qui marche (trop) bien. Dans ce billet, je ne vais pas parler du côté matériel d'un réseau, ni de brancher un switch sans STP en boucle pour le mettre down, mais de quelques commandes linux que j'ai eu l'occasion d'utiliser ces derniers jours pour simuler un mauvais réseau.

Note : Il ne s'agit pas d'un tutoriel, ce billet cherche seulement à donner un aperçu des possibilités de quelques commandes.

# Pourquoi vouloir faire ça ?

Bon, je me doute que vous vous demandez pourquoi vouloir avoir un réseau médiocre ? À vrai dire, j'avoue que dans ma vie, j'essaye généralement d'avoir un réseau correct, mais pour mon travail je développe un logiciel de visio-conférence. Le problème, c'est que tout le monde ne peut pas faire des conférences à plusieurs avec des résolutions HD avec un encodage de très bonne qualité.

Je travaille donc sur l'amélioration de la qualité de la vidéo lorsque le logiciel détecte des pertes de paquets quand la bandwith n'est pas suffisante ou que le réseau a trop de délai ou bien d'autres cas. Du coup, pour debugger ou simuler ce type de conférence, il faut donc que je puisse simuler ce type de réseau, afin de récupérer les logs, ce qui se passe au niveau des différents composants et tester différentes approches.

Il existe bien sûr d'autres cas où ça peut-être utile. Par exemple :
> Un cas d’usage que tu peux rajouter dans ton article, c’est la limitation de l’upload sur une connexion câble NC.
Dès que tu dépasses les 4M d’up, ton ping explose (passant à environ 500 ms).
> J’avais donc limité mon upload à 3M sur cette connexion pour garder un ping inférieur à 100 ms tout en ayant un serveur chez moi.  
> Source : https://mammout.bzh/users/alarig/updates/673

# Simuler un cas précis avec tc-netem

Dans un premier temps, il était intéressant de voir comment réagissait le stream de vidéo dans plusieurs cas. Par exemple, quel est le débit nécessaire pour faire passer une vidéo avec un flux en 1280x720 (30fps) avec tel ou tel qualité (pour le h.264, on parle de CRF).

Je vous épargne cette partie, il s'agit de graphes et de commandes de monitoring (`iptraf`, `ifstat` qui donne la consommation pour chaque interface en continu, `iperf`, etc).

Puis, de voir comment se comporte le stream lorsque le réseau se met à avoir du délai ou des pertes de paquets. Une des solutions était de faire ça au niveau du daemon du programme qu'on développe, mais ça demande de relancer le programme trop souvent. C'est à ce moment que sert une commande super utile `tc` (pour traffic control) et `netem`. Je n'ai pas pour but de faire un tutorial complet pour ces commandes (j'avoue que je ne maîtrise pas bien ces commandes), mais voilà ce qu'on peut trouver dans le `man tc-netem` :

```
SYNOPSIS
       tc qdisc ... dev DEVICE ] add netem OPTIONS

       OPTIONS := [ LIMIT ] [ DELAY ] [ LOSS ] [ CORRUPT ] [ DUPLICATION ] [ REORDERING ][ RATE ]

       LIMIT := limit packets

       DELAY := delay TIME [ JITTER [ CORRELATION ]]]
              [ distribution { uniform | normal | pareto |  paretonormal } ]

       LOSS := loss { random PERCENT [ CORRELATION ]  |
                      state p13 [ p31 [ p32 [ p23 [ p14]]]] |
                      gemodel p [ r [ 1-h [ 1-k ]]] }  [ ecn ]

       CORRUPT := corrupt PERCENT [ CORRELATION ]]

       DUPLICATION := duplicate PERCENT [ CORRELATION ]]

       REORDERING := reorder PERCENT [ CORRELATION ] [ gap DISTANCE ]

       RATE := rate RATE [ PACKETOVERHEAD [ CELLSIZE [ CELLOVERHEAD ]]]]

DESCRIPTION
       NetEm  is  an enhancement of the Linux traffic control facilities that allow to add delay, packet loss, duplication and more other characteristics to packets outgoing from a selected
       network interface. NetEm is built using the existing Quality Of Service (QOS) and Differentiated Services (diffserv) facilities in the Linux kernel.
```

Ainsi, cette commande va permettre de simuler un réseau avec des paquets corrompus, droppés, etc. Ainsi, si nous souhaitons simuler un réseau qui perd 30% des paquets, il suffit d'éxécuter :
```bash
tc qdisc add dev wlp9s0 root netem loss 30%
```

`wlp9s0` étant mon interface. On peut aussi affiner la simulation en ajoutant une probabilité supplémentaire pour simuler le burst. (`tc qdisc change dev wlp9s0 root netem loss 0.5% 25%` par exemple).
Notons que le mot clé `add` a été modifié par `change` quand on souhaite modifier la règle. Il est aussi possible d'ajouter du délai (pour simuler un serveur plus loin) avec `netem delay xxxms` ou corrompre les paquets.

Pour plus d'informations, je vous invite à lire ce post [SO](https://stackoverflow.com/questions/614795/simulate-delayed-and-dropped-packets-on-linux)

On a maintenant un réseau avec un bon débit, mais qui peut ajouter du délai ou dropper des paquets. Cependant, le résultat n'est pas suffisant et faussé. On a encore que des cas particuliers. Par exemple, dans mon cas, je peux simuler un stream avec un encodage particulier avec un taux de perte simulé. Ainsi, si l'application détecte des pertes de paquets, la qualité de la vidéo va baisser, mais le taux de perte ne changera pas. Du coup, la qualité de la vidéo va continuer à baisser et ce n'est pas ce qui m'intéresse. Ce que je souhaite c'est de fixer la bande passante à un seuil afin que la perte de paquets ne soit pas fixe, mais dépend de la qualité du flux.

# Limiter la bandwith en continu

Pour ce faire, je souhaite trouver une commande qui limite la bande passante de mon interface. Par exemple, limiter mon ethernet à 128kbps.

Pour ce faire, j'ai trouvé plusieurs options.

## wondershaper

La commande la plus simple : [`wondershaper`](https://github.com/magnific0/wondershaper/) qui sert à limiter la bande passante en download et en upload.

Elle s'utilise en deux étapes :
```
sudo wondershaper wlp9s0 -d 1024 -u 1024 # limit wlp9s0 à 1Mbps en up et download
sudo wondershaper clear wlp9s0 # efface la limite
```

Cependant, le problème c'est que pour travailler à côté, c'est chiant de limiter toute l'interface. Le plus intéressant serait de gérer ça par process ou par port.

## trickle

`trickle` est une commande très simple comme `wondershaper`, mais qui prend en argument la commande à exécuter. Par exemple : `trickle -u 10 -d 20 wget https://zestedesavoir.com`. Cependant, ça n'avait pas l'air de fonctionner chez moi et je n'ai pas trop cherché, vu que j'ai utilisé la troisième solution.

## tc HTB

Un autre module de `tc` est `htb`. Il permet, en combinaison de iptables, de créer des limites de débits par port. Ce qui justement m'intéressait.

La première étape est de se créer une règle pour limiter le débit.
```bash
tc qdisc add dev wlp9s0 root handle 1:0 htb default 10
tc class add dev wlp9s0 parent 1:0 classid 1:10 htb rate 128kbps ceil 128kbps prio 0
```

Puis d'ajouter la règle sur le port que l'on souhaite.
```bash
iptables -A OUTPUT -t mangle -p tcp --sport 49152 -j MARK --set-mark 10
tc filter add dev wlp9s0 parent 1:0 prio 0 protocol ip handle 10 fw flowid 1:10
```

Pour comprendre l'ensemble des arguments je vous invite à lire `man tc-htb iptables`.

Puis de faire passer le traffic que l'on souhaite. On peut le monitorer avec les commandes que j'ai cité plus haut ou encore avec `tc -s -d class show dev wlp9s0`

Enfin il fallait effacer la limite :
```bash
tc qdisc del dev wlp9s0 root
```

Voici donc quelques pistes pour simuler des réseaux différents depuis son propre réseau. Je vous invite à jouer un peu avec `tc`, même si la commande est loin d'être simple à comprendre/maîtriser. Si quelqu'un maîtrise le fonctionnement de cette commande, je l'invite à en faire un tutoriel :)

À noter que la simulation ne sera toujours pas parfaite. Par exemple, la perte de paquets que l'on obtient en limitant la bande-passante sur un port avec `tc-htb` ne sera pas forcément identique à celle que l'on peut observer sur des réseaux avec cette capacité. Par exemple, les paquets ne vont pas être droppés par un réseau, mais seulement ralenti car des buffers gardent ces paquets. Pour simuler certains cas, il faudra opter pour des outils plus adaptés (à priori des routeurs sous FreeBSD font la job encore mieux).
