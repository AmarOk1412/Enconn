---
title: Chronique du libre n°1
subtitle:  Gaël Langevin et son robot Inmoov
date: 2016-02-17
tags: ["blog", "chronique-du-libre", "robot"]
bigimg: [{src: "/img/dev/inmoov/_min.jpg", desc: "Inmoov"}]
---

Le monde du libre est composé de bien de projets dans de nombreux domaines. De nombreuses communautés se sont formées autour de ces projets. Cette nouvelle série d'articles a pour but de présenter des projets communautaires dans différents domaines. Aujourd'hui, nous allons débuter cette série avec un projet de robot humanoïde open-source : InMoov.

![](/img/dev/inmoov/inmoov_ban.png)

<!--more-->

Bonjour Gaël et merci de bien vouloir répondre à ces quelques questions !  

__Pour commencer, parle-nous un peu de toi. Qui es-tu, que fais-tu ?__

Bonjour, je m'appelle Gaël Langevin, je suis un sculpteur et designer français. Mon travail consiste à créer des objets pour les plus grandes marques avec toutes sortes de matières, le but étant de faire rêver et de faire appel à l’imaginaire. Je suis donc en permanence en quête d’idées et de solutions techniques et artistiques.

__Aujourd'hui, nous allons parler de ton projet de robot humanoïde InMoov. Réalises-tu des robots depuis longtemps ? Quels sont les robots que tu as réalisés avant InMoov ?__

Non, pas vraiment. J'avais déjà réalisé des objets qui ressemblaient à des pièces de robots, mais elles ne pouvaient ni bouger, ni être programmées.  
J'ai vraiment commencé la robotique il y a 3 ans, en janvier 2012. J'avais alors acheté une imprimante 3D pour mon travail, mais je souhaitais réaliser un grand projet avec cet outil. J'ai alors réalisé une prothèse de main 3D (ce qui fut le début du projet InMoov).

__Combien de temps par semaine consacres-tu au projet InMoov ?__

Il ne s'agit pas d'un travail à plein temps. Je travaille sur le projet seulement pendant mon temps libre. Le temps consacré au projet est donc très variable chaque semaine.

__Qu'est-ce que le projet InMoov concrètement ? Pourquoi ce projet ?__

InMoov est un projet de robot humanoïde open-source.  

Pour la petite histoire, en 2012, j’ai fait l’acquisition d’une petite imprimante 3D afin d’enrichir mon travail. L'imprimante devait me servir pour la création d’une prothèse futuriste, mais celle-ci n’a pas été concluante. Par contre, ce projet m’a donné envie d’utiliser mon imprimante 3D pour créer cette prothèse moi-même. J’ai donc modélisé et imprimé une main et un avant-bras pendant mon temps libre. Ayant profité depuis plusieurs années d'outils comme Linux et [Blender](http://www.blender.org/), j’ai décidé de contribuer à ma manière à cette notion de partage disponible sur Internet, en mettant à disposition les fichiers de cette main sur [Thingiverse](https://www.thingiverse.com/).  

L’enthousiasme de la communauté a été fulgurant, cela m’a donné envie de trouver des solutions pour motoriser et piloter cette main via mon ordinateur. C’est en cherchant un moyen open-source que j’ai découvert [Arduino](http://www.arduino.cc/), une plateforme électronique supportée par une énorme communauté de Makers qui est toujours en expansion.    
Une fois les solutions trouvées, j’ai de nouveau partagé toutes mes informations sur le [site](http://inmoov.fr) du projet.  

À partir de ce moment, j’ai décidé d’avancer et de créer un robot humanoïde complet, disponible et reproductible par tous, et d’en faire une plateforme de développement pour les Makers, les écoles et les universités. Je conçois donc toutes les pièces dans un format de 12 cm^3^ afin que n’importe quelle petite imprimante puisse les reproduire.

Après la main, j’ai créé un biceps puis une épaule. Cela a été relativement facile, une fois le bras droit créé sous Blender, j’ai fait une mirrorisation (symétrie axiale) de ce bras afin d’obtenir et d’imprimer le bras gauche. J’avais ainsi deux bras fonctionnels que j’ai aussitôt partagé sur Internet.  
Il me fallait trouver une solution pour synchroniser ces deux bras afin de poursuivre la création de mon robot. En cherchant pendant de longues heures sur Internet, majoritairement la nuit, je suis tombé sur [MyRobotLab](https://github.com/MyRobotLab/), un service Java open-source, grâce auquel j’ai rencontré Greg Perry, un personnage incroyable avec qui j’ai tout de suite accroché.

__Quelles sont aujourd'hui les possibilités du projet InMoov ? De quoi le robot est-il capable ?__

Aujourd'hui, InMoov est capable d'être commandé par la voix, de parler et de [bouger](https://www.youtube.com/watch?v=q2zsUVFKZwY). Les mouvements du robot sont vraiment anthropomorphes. Il peut voir, rechercher des personnes, des objets, les suivre dans plusieurs environnements grâce à des caméras. Il peut répondre à des questions et agir en fonction de vos questions. Une kinect est présente pour gérer la reconnaissance de gestes. De plus, InMoov partage ses scripts avec tous les autres InMoov. Ce qui veut dire qu'ils apprennent grâce aux autres (pour le moment, l'apprentissage est très faible). Tout ce travail est rendu possible grâce à la communauté qui entoure le projet.

![Gaël Langevin et InMoov](/img/dev/inmoov/inmoov_gael.jpg)

__Comment se passe les relations avec ton robot ? Les discussions sont-elles intéressantes ?__

On ne peut dire que les conversations soient très intéressantes pour l'instant, je m'efforce surtout de lui rentrer des phrases et des mots qui permettent de le commander vocalement. Toutefois, avec MyRobotLab, nous cherchons à développer une sorte d'intelligence primaire, un peu comme "Siri" qui permettrait de converser et non pas seulement de le commander.

__Qu'est-ce que InMoov ne peut pas faire aujourd'hui ? Qu'est-ce qui est en projet ?__

Marcher !  En effet, le défi de ce moment est de créer les jambes du robot.
Autre défi : avec Greg Perry on envisage des applications et des améliorations intéressantes pour InMoov et la centaine de répliques qui existent déjà dans le monde. Étant tous branchés à la même base des données, la mise à jour des informations et des capacités du robot a la possibilité d'être directe et automatique pour tous les InMoov. On peut par exemple leur apprendre à reconnaître de nouveaux objets et les nommer et en faire profiter la communauté.

__Pourquoi avoir choisi de rendre le projet open-source ?__

L’entraide peut nous faire aller plus loin. Je crois que nous pouvons changer notre vision de la société. Je crois sincèrement en ce monde utopique où le partage permet de vivre sans vendre. Partager les informations et les connaissances est la clé d'une compréhension multi-culturelle. Alors, je donne tout mon temps libre à ce robot qui apprend à faire des robots et qui, en plus, peut réparer les hommes !

__Combien de robots InMoov sont en ce moment autour du monde ?__

Je ne peux vraiment pas répondre à cette question. Je peux seulement faire une estimation basée sur le nombre de téléchargements et le nombre de vidéos/photos sur Internet. Je pense qu'il y a environ 150 InMoov construits, mais à différents niveaux. Pour vous donner une idée, la main a été téléchargée 50000 fois, mais je ne connais seulement que quelques robots complets, parce qu'ils sont proches de la communauté et partagent leurs idées.  
Pour répertorier les robots InMoov, une [carte](https://mapsengine.google.com/map/u/0/embed?mid=z97J-N7M1tkw.kszNjXUbWLl4) a été créée. Et le nombre de robots InMoov grandit rapidement. J'ai personnellement imprimé 3 robots sans considérer toutes les améliorations que j'ai apportées. Mais un seul est imprimé avec toutes les pièces. C'est celui que je présente lors d'évènements. Depuis le début du projet, j'ai imprimé 6 mains complètes.

__La communauté formée autour du projet est juste énorme ! D'après toi, pourquoi autant de robots InMoov sont réalisés ?__

L'open-source est un terrain de jeu fabuleux et il n'existait aucun autre projet de robot imprimé en 3D permettant à quiconque de se construire son propre robot. InMoov étant pionnier dans ce domaine, c'est la raison de cet engouement. D'autre part, beaucoup de gens rêvent d'une imprimante 3D mais sans savoir quoi imprimer mis à part des petits lapins et des figurines de Yoda, InMoov donne à ces personnes une vraie raison de plonger dans l'impression 3D.

__D'ailleurs, cette communauté, comment peut-on la rejoindre ?__

Vous pouvez la rejoindre en vous rendant sur le site du projet. Sur ce site, vous pouvez retrouver toute la documentation du projet, ainsi que tous les [tutoriels](http://www.inmoov.fr/assembly-help/) pour pouvoir commencer à monter votre propre InMoov. Vous pouvez aussi rejoindre un des projets se trouvant à proximité de chez vous à l'aide de la [carte](http://www.inmoov.fr/builders-near-you/) et trouver de l'aide de la communauté avec les [forums](https://groups.google.com/forum/#!forum/inmoov).

__Ton projet a été beaucoup détourné. Notamment avec le projet [Bionico](http://bionico.org/), la mission [PiBot](http://www.generation-nt.com/pibot-robot-pilote-avion-actualite-1906685.html), des projets [universitaires](https://www.youtube.com/watch?v=hhXKoOboHIQ), etc. Quel est ton détournement préféré ? Qu'est-ce que cette multitude de projets a apporté à InMoov ?__

Bionico est un des projets que j'affectionne particulièrement, car il me touche sincèrement, c'est une des raisons pour laquelle j'ai créé la main InMoov2. Il y a InMoov Explorer que je développe avec [WeVolver](https://www.wevolver.com/). Une plateforme automotrice, permettant à des enfants hospitalisés de voyager grâce à la télé-présence du robot Inmoov Explorer. Mais aussi tant d'autres liés à MyRobotLab qui sont autant de maillons nécessaires à la chaine de la communauté.

__Quelles ont été les difficultés rencontrées par cette communauté ?__

La configuration de la Kinect liée au squelette et le concept des articulations du robot reste encore à résoudre. Certaines positions humaines restent irréalisables pour l'instant.

__Tu as beaucoup voyagé autour du monde pour présenter ce projet. Y a-t-il un endroit qui te reste particulièrement gravé en mémoire ?__

Bonne question... Tous ces endroits sont intéressants pour moi, parce que je vis une palanquée de nouvelles expériences et je découvre de nouvelles choses à chaque voyage. J'ai vraiment été ravi d'aller à Rome ainsi qu'à St. Petersburg pour un grand évènement (le [Geek Picnic](http://geek-picnic.ru/)). Je me suis aussi rendu aux [Maker Faire](http://makerfaire.com/) de Paris et de New York.

![InMoov à la MakerFaire de New York](/img/dev/inmoov/makerfaire.jpg)

__Si nous souhaitons réaliser notre propre InMoov, de quoi avons-nous besoin ? Combien coûte-t-il ?__  

Le robot complet coûte moins de 1 500 €, ce qui est relativement peu cher comparé aux robots du même type. Il ne s'agit pas d'un projet facile, mais il est conçu pour que vous puissiez apprendre et découvrir comment on construit le robot. J'ai conçu ce que j'appelle le "[Finger Starter Kit](http://www.inmoov.fr/product/finger-starter-kit/)". Il s'agit d'un kit d'un doigt imprimé en 3D que nous pouvons relier à un Arduino Uno et un servomoteur. Une fois que vous savez comment ce kit fonctionne, vous pouvez passer à l'étape suivante : La main. Et continuer, étape par étape.  
Le matériel demandé pour réaliser ce projet est plutôt classique.

Vous avez bien entendu besoin d'une imprimante 3D bien configurée. Autrement, vous allez vous retrouver avec des pièces inutilisables. Le temps d'impression du robot complet prend énormément de temps, il y a environ 200 pièces à imprimer (réalisé avec Blender, qui n'est pas forcément le plus facile outil pour débuter dans la modélisation 3D), mais toutes les pièces font moins de 12cm^3^ et sont donc imprimables avec n'importe quelle imprimante 3D. Le projet ressemble à un gros jeu Lego ou à un gros puzzle.  
Pour faire fonctionner le robot, il faut pour le moment 32 servomoteurs et 2 arduinos.  
La construction du robot nécessite aussi un ordinateur, une drill ou dremel, des tournevis, un cutter, et d'autres outils se trouvant dans n'importe quelle boite à outils.  
Le robot InMoov est porté par la communauté MyRobotLab. Pour rendre InMoov autonome, nous cherchons à rendre le logiciel compatible avec une carte Odroid U3 compatible avec Linux.

__Quel outil est utilisé pour le système de synthèse vocale ?__

Pour gérer toute la partie synthèse vocale, nous utilisons [Sphinx](http://cmusphinx.sourceforge.net/) ainsi que récemment [ProgramAB](https://github.com/jonbaer/program-ab).

__Des conseils à donner à quelqu'un qui voudrait se lancer dans une aventure similaire, ou qui voudrait contribuer au projet ?__

Je pense qu'un débutant devrait démarrer avec le Finger Starter Kit, le tutoriel est disponible sur le site et si vous ne possédez pas d'imprimante 3D, je vends un petit kit de démarrage.

__Et dernière question : Si toute la communauté présente autour d'InMoov pouvait t'entendre, que leur dirais-tu ?__

Une seule chose: _InMoov, by sharing knowledge, we build a positive future_.

![ ](/img/dev/inmoov/inmoov.jpg)
