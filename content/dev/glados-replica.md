---
title: GLaDOs Replica
subtitle: Un prototype de robot bras
date: 2016-02-20
tags: ["project", "robot"]
bigimg: [{src: "/img/dev/glados/_min.jpg", desc: "GLaDOs"}]
---

**LE PROTOTYPE EST FINI, UNE VERSION PLUS GROSSE, PLUS PROPRE, PLUS FINIE VERRA LE JOUR (un jour, pas de suite).**

## Idée

3 types de robots m'intéressaient pour cette année 2013 (pour donner une interprétation physique de mon projet RORI). Soit un drône (mais un master a voulu en réaliser un en projet et j'en ai vu assez à l'évènement "faîtes du numérique" de Rennes). Soit une araignée (mais il y en avait 2 déjà à Rennes (dont Spooky que j'avais déjà utilisé)). Ou un bras mécanique style robot Kuka/GLaDOs de Portal. Mon choix s'est porté sur la réalisation d'une réplique de GLaDOs (qui me permettrait de jouer avec la caméra) et qui est pour moi un robot bien plus expressif qu'un drône ou une araignée.

## Plans

Première étape. Trouver de nombreuses ressources pour réaliser un design de la réplique. De nombreux croquis furent nécessaires, réalisés grâce à de nombreuses ressources. Il a fallu aussi réfléchir à comment assembler les pièces ainsi qu'aux déplacements du robot.  
Voici l'aboutissement de cette étape de recherche :  

Croquis vue de face  
![face](https://github.com/AmarOk1412/GLaDOsReplica/blob/master/Plans%202D/gladosface.png?raw=true)
Croquis vue de côté  
![côté](https://github.com/AmarOk1412/GLaDOsReplica/blob/master/Plans%202D/glados.png?raw=true)
Croquis type d'une pièce  
![piece](https://github.com/AmarOk1412/GLaDOsReplica/blob/master/Plans%202D/finalBase.png?raw=true)
[More](https://github.com/AmarOk1412/GLaDOsReplica/blob/master/Plans%202D/)

L'ensemble des croquis a été réalisé avec The Gimp 2.8

## Modélisation 3D

Voici la partie la plus problématique du projet. Je ne connaissais absolument pas la modélisation 3D et j'ai eu énormément de problèmes à imprimer les pièces. Je n'ai donc pas réussi à avoir l'homogénéité voulue. Cependant, l'ensemble des pièces (sauf la tête) fut réalisé avec Blender et FreeCAD. La génération du GCode pour l'impression a été réalisé avec Replicator. L'ensemble des fichiers est disponible sur le [Github](https://github.com/AmarOk1412/GLaDOsReplica/) du projet.

## Réalisation

La partie électronique du robot est assez simple. Voici le matériel dont j'ai eu besoin :
- Une [beaglebone white](https://github.com/AmarOk1412/GLaDOsReplica/blob/master/Photos/beaglebone.jpg?raw=true) pour embarquer [RORI](/dev/rori/).
- Une Arduino Uno & une breadboard pour le prototypage.
- Une [caméra](https://github.com/AmarOk1412/GLaDOsReplica/blob/master/Photos/IMG_20131107_144010_629.jpg?raw=true) récupérée d'un robot et son support pan & tilt (composé de 2 servomoteurs TowerPro SG90)
- Un servomoteur HS-65HB
- 1 moteur de lecteur CD d'un vieil ordi ou un servomoteur HS-422 Hitec pour la base
- 2 leds RGB et leurs résistances
- Un tube flexible
- Beaucoup de câbles pour la décoration
- 2 stickers imprimés pour la décoration
- De la peinture en bombe (sous-couche, gris clair, gris 80/90%).

Voici donc la construction étape par étape :

1. Imprimer les pièces et les poncer :
Toutes les pièces peuvent être trouvées [ici](https://github.com/AmarOk1412/GLaDOsReplica/tree/master/Maquette%203D)

![](https://github.com/AmarOk1412/GLaDOsReplica/blob/master/Photos/all.jpg?raw=true)
![](https://github.com/AmarOk1412/GLaDOsReplica/blob/master/Photos/total.jpg?raw=true)

2. Les peindre

![ ](https://github.com/AmarOk1412/GLaDOsReplica/blob/master/Photos/basepeinte.jpg?raw=true)
![ ](https://github.com/AmarOk1412/GLaDOsReplica/blob/master/Photos/highbasepeinte.jpg?raw=true)
![ ](https://github.com/AmarOk1412/GLaDOsReplica/blob/master/Photos/coquehautpeinte.jpg?raw=true)
![ ](https://github.com/AmarOk1412/GLaDOsReplica/blob/master/Photos/centreledpeint.jpg?raw=true)
![ ](https://github.com/AmarOk1412/GLaDOsReplica/blob/master/Photos/coquebaspeinte.jpg?raw=true)
![ ](https://github.com/AmarOk1412/GLaDOsReplica/blob/master/Photos/tetepeinte.jpg?raw=true)

3. Assemblage :
Pour la tête, il faut commencer par fixer la LED RGB.

![](http://static.projects.hackaday.com/images/7130201398523692645.png)
![](http://static.projects.hackaday.com/images/9908431398523748415.png)

Puis la caméra :

![](http://static.projects.hackaday.com/images/1583181398523780106.png)
![](http://static.projects.hackaday.com/images/917611398523801517.png)

Les moteurs

![](http://static.projects.hackaday.com/images/7375941398523839287.png)

Puis vient la partie haute.

![](http://static.projects.hackaday.com/images/9269991398523919016.png)
![](http://static.projects.hackaday.com/images/4733171398523944502.png)

Puis la partie basse.

![](http://static.projects.hackaday.com/images/5487211398524415579.png)

La base.

![](http://static.projects.hackaday.com/images/3676151398524121692.jpg)

Vient la décoration

![](http://static.projects.hackaday.com/images/4511271398524169982.png)
![](http://static.projects.hackaday.com/images/3644691398524211557.png)
![](http://static.projects.hackaday.com/images/3390531398524247006.png)
![](http://static.projects.hackaday.com/images/8524651398524318584.png)
![](http://static.projects.hackaday.com/images/9969751398524368685.jpg)
![](http://static.projects.hackaday.com/images/6492661398524386696.jpg)

## Code

[RORI](/dev/rori) est un chatterbot. Je vous invite à visiter la page du projet. Il a suffit de le compiler pour [ARM](https://github.com/AmarOk1412/GLaDOsReplica/blob/master/Photos/compilation.png?raw=true) afin de le mettre sur [BeagleBone][https://github.com/AmarOk1412/GLaDOsReplica/blob/master/Photos/RORI_Beagle.png?raw=true]. Ce site peut servir de référence : http://www.cloud-rocket.com/2013/07/building-qt-for-beaglebone/
RORI peut très bien contrôler GLaDOs. On peut le faire via l'interface Linux ou Android :

![](https://github.com/AmarOk1412/GLaDOsReplica/blob/master/Photos/RORILinuxClient.png?raw=true)
![](https://github.com/AmarOk1412/GLaDOsReplica/blob/master/Photos/androidClientRORI.png?raw=true)

## Futur

Il me reste quelques points à réaliser pour finir GLaDOs :
- Fixer le servomoteur de la base.
- Porter le tout sur Beagleboard.
- Réaliser une boite de transport.
- Enlever les choses non libres comme tout ce qui utilise Google par exemple.

## Vidéos

Voici la première vidéo de RORI. Il n'y avait pas encore d'interface finie. Cependant, on pouvait déjà contrôler RORI avec la voix (Google), répondre à certaines questions et réaliser des tâches, comme naviguer sur Internet, rendre l'ordinateur muer, le bloquer, ...
{{< youtube jlHHbtdXrBU >}}
Puis la démonstration de l'interface graphique et de deux/trois modules :
{{< youtube 0QCpE57EK2g >}}
Vient ensuite la démonstration de l'application Android ainsi que de la synthèse vocale
{{< youtube k5rDRLOymHI >}}
Enfin, la démonstration de RORI avec GLaDOs
{{< youtube tcwZJqQ56SE >}}

## Démonstrations

J'ai présenté GLaDOs à quelques évènements comme :
L'Arduino Day en Avril 2014 (juste la tête à ce moment) :

![](http://static.projects.hackaday.com/images/7324371397764876071.png)
![](http://static.projects.hackaday.com/images/1285501397764895076.png)
![](http://static.projects.hackaday.com/images/6778641397764909120.png)

D'autres photos [ici](http://universite-foraine.fr/index.php?/occupations/labfab/).

Au Jardin Numérique 3:

![](http://jardinnumerique.org/wp-content/uploads/2014/04/ap%C3%A9ro-codelab-7.jpg)

D'autres photos [ici](http://jardinnumerique.org/2014/04/galerie-photos-jn3//)
Au concours de réalisations en rapport avec la science fiction sur [hackaday](https://hackaday.io/project/509-GLaDOs-Replica-for-RORI)

## Remerciements

Je remercie le pôle mécatronique de l'université de Rennes 1, l'INRIA de Rennes ainsi que le [labfab](http://www.labfab.fr/) de Rennes pour m'avoir laissé la possibilité d'imprimer mes pièces. Je remercie également le Club de Robotique de l'université de Rennes 1 pour avoir financé le matériel manquant. Voici un autre [projet](https://www.youtube.com/watch?v=f8j4JvpSNMA) de ce type réalisé par un russe. Cependant, ce n'était pas exactement ce que je voulais (je souhaitais le faire par moi-même et en profiter pour mettre une caméra et une beaglebone). Enfin, je remercie Mickael Mackrez pour m'avoir modélisé bénévolement la tête de GLaDOs.
