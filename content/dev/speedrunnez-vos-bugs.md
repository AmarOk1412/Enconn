---
title: Speedrunnez vos bugs
subtitle: (glitch autorisés)
date: 2017-09-03
tags: ["software"]
---

Imaginons nous un jeu. Comme pour un speedrun, le but ici est de le finir le plus rapidement possible, mais de manière reproductible (100% de réussite). Par contre, il ne s’agit pas d’un Zelda, Mirror’s edge, Mega man, mais ici, il s’agit de trouver la raison de pourquoi votre logiciel de messagerie reçoit bien les notifications de messages entrant, mais n’affichent pas les messages au client (du beau nom de : bug 1564).

En voici un court synopsis:

Eli, une personne utilisant le logiciel pour communiquer avec Clem, voit des notifications de messages entrant ou d’appels de Clem, mais la fenêtre où s’affiche la conversation avec Clem reste vide. L’énigme ici est de trouver pourquoi l’affichage ne se fait pas à ce moment, même si ce comportement est résolu si Eli supprime et remet Clem dans ses contacts.

#  Finir le jeu une fois

Vous connaissez maintenant la zone qui contient le glitch. Mais vous comprenez pas encore trop comment réussir à aller dedans à 100%.

Le but, maintenant est d’essayer de trouver comment reproduire ce glitch pour aller directement à l’endroit voulu. Et pour ceci, il y a la super arme du développeur : le papier/crayon + trace. Il suffit ensuite de reproduire plusieurs séries où vous tentez de glitcher. Vous allez alors arriver à glitcher de plus en plus souvent et reproduire le comportement voulu de plus en plus vite.

Ainsi, d’un bug un peu mystérieux, vous allez pouvoir obtenir un bug reproductible et donc plus facilement travailler dessus.

Par exemple, pour ce jeu, obtenir la série suivante pour glitcher et passer à la fin :

+ Clem appelle Eli.
+ Eli accepte l’appelle, raccroche.
+ Eli stop/relance l’application.
+ Eli supprime Clem des contacts.
+ Eli stop/relance l’application.
+ Eli ajoute Clem des contacts.
+ Clem appelle Eli.
+ Eli accepte l’appel, raccroche, resupprime Clem.
+ Eli stop/relance l’application.
+ Clem appelle Eli, Eli glitch et ne voit plus les messages de Clem.

Bien sûr, le speedrun n’est pas forcément parfait, et sera peut-être amélioré par un autre joueur, mais vous êtes passé d’un jeu quasi impossible, à un jeu que vous terminez à chaque fois.

Au final, ce billet n’a pas vraiment de but précis, il s’agissait juste d’une réflexion que j’ai eu un soir en rentrant sur le fait de tester un programme avait des points communs avec le fait de speedrunner un jeu.
