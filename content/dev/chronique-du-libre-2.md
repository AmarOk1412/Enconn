---
title: Chronique du libre n°2
subtitle:  Le Mozilla Science Lab
date: 2016-05-28
tags: ["blog", "chronique-du-libre", "mozilla", "science"]
---

Tout le monde connait Mozilla grâce à Firefox mais les projets de l'organisation ne se limitent pas à un navigateur. La [mission](https://www.mozilla.org/fr/mission/) de Mozilla est de promouvoir l’ouverture, l’innovation et la bonne santé du Web. La Fondation Mozilla propose des programmes pour mapper ces valeurs à la science, les médias, à la politique ou l'éducation afin de les rendre ouverts, collaboratifs et partageables.

Dans ce deuxième article des Chroniques du Libre, nous allons donc parler d'open science et plus particulièrement d'un projet de la [Fondation Mozilla](https://www.mozilla.org/en-US/foundation/) relativement peu connu : Le [Mozilla Science Lab](http://mozillascience.org/).

![MSL](/img/dev/mozilla-science-lab/_min.jpg)

# Le Mozilla Science Lab

Comme vous le savez sans doute, [Internet](https://en.wikipedia.org/wiki/History_of_the_Internet) s'est beaucoup développé grâce aux chercheurs et aux travaux de recherche. Les scientifiques ont créé cet outil qui a considérablement changé nos habitudes. Mais les habitudes des scientifiques n'ont que très peu changé. Les travaux scientifiques sont encore basés sur des papiers dont le partage est un [sujet souvent évoqué](http://blogs.mediapart.fr/blog/hervelecrosnier/150109/sd-5-libre-acces-aux-publications-scientifiques). C'est pour cette raison que le Mozilla Science Lab a vu le jour en 2013.

Ce projet est mené par [Kaitlin Thaney](http://kaythaney.com/) qui défend depuis des années l'idée d'open science (qui a notamment eu un rôle dans le développement des [sciences commons](https://creativecommons.org/science/), travaillé au [MIT](http://icampus.mit.edu/), puis aidé à lancer le groupe [Digital Science](http://www.digital-science.com/) à Londres). Elle a été rejoint par Greg Wilson, fondateur du programme [Software Carpentry](http://software-carpentry.org), qui aide les chercheurs à développer leurs compétences en Informatique depuis 1998.

Le Mozilla Science Lab permet de mettre en relation les étudiants, les programmeurs, les graphistes et les chercheurs (5000 aujourd'hui en comptant ceux de Software Carpentry) autour du développement de projets scientifique open source. D'ailleurs, ils recherchent constamment des personnes pour enseigner ou mentorer des projets afin d'aider le Software Carpentry à organiser de petits groupes de travail dans plusieurs universités. De plus, la communauté organise aussi des [Code Sprint](http://www.mozillascience.org/new-collaborators-at-the-toronto-open-science-code-sprint), participe à des festivals et organise chaque mois des appels de la communauté.

En 2015, le MSL cherche à travailler autour de l'apprentissage et de l'engagement et lancera un programme de bourses afin d'aider les chercheurs en début de carrière. Il organisera aussi des évènements (workshop, code sprint), développera ses ressources et formera des partenariats avec d'autres groupes pour soutenir les régions telles que l'Australasie. Le Mozilla Science Lab cherche aujourd'hui à implanter cette façon de travailler et de la faire reconnaitre afin de pouvoir inciter plus de personnes à contribuer.

# Projets
Dans cette seconde partie, nous allons parler de 3 [projets](http://www.mozillascience.org/collaborate) du Mozilla Science Lab.

# Pathogens


![Pathogens. Image de Stefan Walkowski](/img/dev/mozilla-science-lab/pathogens.png)

L'objectif du projet est de rassembler et organiser les données génétiques accessibles au public afin de mieux comprendre les interactions entre les organismes dans des microbiomes. Récemment, des chercheurs ont découvert que certaines bactéries pouvaient tenir un journal de leurs précédentes infections en mémorisant certaines parties d'ADN des phages les attaquant dans leur propre génome (plus précisément dans le [CRISPR](http://fr.wikipedia.org/wiki/Clustered_Regularly_Interspaced_Short_Palindromic_Repeats?oldformat=true)). Ce "log d'attaque" permet à la bactérie d'identifier les phages qui les attaquent et fournit une voie pour l'évolution des interactions de ces organismes.

Ce projet vise à fournir des outils pour assembler et filtrer efficacement de grandes quantités de données. Le résultat idéal étant de réaliser un réseau d'interactions hôte-pathogène pouvant être facilement interrogé, permettant ainsi aux chercheurs de répondre à des questions sur l'évolution d'un microbiome.

Le projet contient environ 5 membres en ce moment. Le plus facile pour commencer à contribuer est soit de visiter la [page](http://www.mozillascience.org/projects/pathogens) du site ou le [github](https://github.com/goyalsid/phageParser). Le projet en cours est de produire une carte interactive pour montrer le lien entre les phages et les bactéries pour permettre aux chercheurs de regarder les connexions entre les organismes dans un environnement spécifique.

# Diversibee


![Diversibee](/img/dev/mozilla-science-lab/diversibee.jpg)

[Jana Vamosi](http://homepages.ucalgary.ca/~jvamosi/) est professeur agrégé à l'Université de Calgary au Canada. Elle étudie comment la biodiversité fonctionne et le rôle que jouent certaines espèces dans le fonctionnement des écosystèmes.

La perte de la biodiversité est un problème depuis de nombreuses années. Malheureusement, beaucoup de personnes ignorent le rôle de ces espèces dans l'environnement (outre le fait qu'une espèce soit jolie, charismatique voir fascinante). Ce programme vise à résoudre ce problème et d'illustrer le concept de la diversité fonctionnelle.

Se basant sur des [travaux précédents](http://www.sciencedaily.com/releases/2011/09/110918144955.htm), nous savons aujourd'hui que les joueurs sont capables de trouver rapidement la solution optimale à un problème. Diversibee est un jeu basé sur une [récente étude](https://news.ncsu.edu/2014/05/burrack-bee-diversity-2014/) où les scientifiques ont montré que les myrtilles étaient plus grosses lorsqu'elles étaient pollinisées par plusieurs groupes d'abeilles. Chaque groupe ajoutant aux myrtilles une valeur de 311 \$ par acre (par exemple, si 2 groupes d'abeilles étaient recensés, les myrtilles avaient une valeur ajoutée de 311 \$ et de 622 \$ pour 3 groupes). Le problème étant que ces abeilles habitent dans des forêts. L'agriculteur possédant donc quelques hectares de forêt peut donc augmenter la valeur de ses myrtilles. Par contre, s'il déboise ses terres, il peut alors mettre plus de myrtilles pour être une entreprise lucrative. Le but du jeu est alors de trouver la configuration maximale, celle où la configuration de la forêt maximise le rendement des myrtilles ainsi que le maintien des populations d'abeilles.

Ce projet est actuellement développé par 3 personnes du Mozilla Science Lab, ainsi que 3 autres personnes via github. Si vous souhaitez contribuer, vous pouvez vous rendre sur le [repository git](https://github.com/jvamosi/Diversibee) ou la [page](http://www.mozillascience.org/projects/diversibee) du projet et de contacter [Jana Vamosi](http://homepages.ucalgary.ca/~jvamosi/) si vous avez la moindre question. Toute idée pour développer le projet est le bienvenue. En ce moment, des développeurs JavaScript et des designers sont recherchés.

# Trillian


![Trillian](/img/dev/mozilla-science-lab/trillian.png)

Pour continuer dans la lignée du premier [CdS](https://zestedesavoir.com/articles/?tag=CdS), parlons un peu d'espace. L'astronomie génère un nombre astronomique (sans mauvais jeu de mots) de données. Ces données peuvent être issues de télescopes, de missions spatiales ou de simulations (par exemple les données de [WISE](http://wise.ssl.berkeley.edu/astronomers.html), [SDSS](http://www.sdss.org/), [2MASS](http://www.ipac.caltech.edu/2mass/), [Spitzer](http://www.spitzer.caltech.edu/), [GALEX](http://www.astronomy.com/tags/galex), etc.). Autant de sources d'informations qui peuvent servir pour de multiples projets. Malheureusement, aujourd'hui aucune institution ne peut héberger autant de données. Rechercher quelque chose dans le ciel entier est donc quasiment impossible. En effet, un chercheur est obligé de télécharger une petite partie de ces données, d'écrire un code pour en extraire la partie intéressante avant de pouvoir analyser les données. [Trillian](http://trillianverse.org/) cherche à inverser cette méthode. C'est-à-dire que l'utilisateur n'aura qu'à fournir un modèle en entrée (c'est-à-dire décrire ce qu'il recherche. Par exemple se baser sur l'âge, la masse, la galaxie hôte, les coordonnées, etc.) de Trillian et le [moteur de calcul](http://trillianverse.org/astronomers.html) appliquera le modèle à toutes les données possibles. Pour travailler sur autant de données, la base de données de Trillian est distribuée sur tous les espaces de stockage mis à la disposition par la communauté.

Pour le moment, 5 personnes travaillent activement sur le projet. Il existe de nombreuses manières de les rejoindre :

- Aider dans le design de l'[infrastructure](http://trillianverse.org/developers.html) de l'application ainsi que sa sécurité.
- Héberger des données sur un espace de stockage libre.
- [Coder](http://github.com/trillian/trillian) si vous maîtrisez Python, PostgreSQL ou Docker
- [etc](http://www.mozillascience.org/projects/trillian)

# Les avantages de l'open sciencedaily
On peut tout naturellement se demander pourquoi ces projets sont open source. Faire de l'open science demande d'avoir une certaine confiance dans les données fournies ainsi que dans les personnes contribuant au projet. Si le projet nécessite plusieurs groupes de travail, il y aura sans aucun doute un manque de normalisation des compétences. Mais une fois ces désavantages montrés, on peut enfin voir les bénéfices qu'apportent cette manière de penser. On peut aujourd'hui collecter, utiliser, analyser des données assez facilement. Si ces données n'avaient pas été accessibles, ces projets n'auraient pas vu le jour.

De plus, l'open science permet à des personnes de différents environnements de discuter et de rendre des projets possibles. Il s'agit d'une bonne occasion de rendre possible un projet porté par une personne n'ayant aucune connaissance en développement informatique. Et vice versa, une personne possédant des connaissances en développement informatique peut se servir de ses connaissances pour résoudre de véritables problèmes. Ce système peut vous proposer diverses compétences (ainsi que du matériel) que vous n'auriez pas pu avoir à votre disposition. S'appuyer sur les compétences de la communauté permet d'ouvrir le champ des possibles et de favoriser le dialogue dans différentes disciplines. Le Mozilla Science Lab est une bonne plateforme pour pouvoir lancer ce dialogue et permet de trouver des liens entre des domaines apparemment sans lien. L'open science accélère la communication et la collaboration, qui sont les clés de la recherche.

Enfin, le web peut permettre d'améliorer la façon dont la recherche se fait aujourd'hui (par exemple pour éliminer les goulots d'étranglement que représente la perte de temps apportée par la rétroingénierie d'une expérience, la perte financière causée par un manque de données, une mauvaise gestion et par les projets qui ont besoin de repartir de zéro).

Pour conclure cet article, je vous propose une citation d'Isaac Newton que m'a cité [Madeleine Bonsma](http://madeleinebonsma.com/) pour définir son point de vue de l'open science :  
> Si j'ai vu plus loin, c'est en montant sur les épaules de géants.

# Pour aller plus loin

- Les autres projets du Mozilla Science Lab : http://www.mozillascience.org/collaborate
- Un petit lien sur la diffusion des papiers scientifiques : http://www.acrimed.org/article3646.html
- Le blog de Software Carpentry : http://software-carpentry.org/blog/index.html
- Le blog de Mozilla Science Lab : http://www.mozillascience.org/blog
- Le twitter : https://twitter.com/MozillaScience
