---
title: r2
subtitle: pour changer de gdb
date: 2018-03-25
tags: ["security", "tools"]
---

*Article original [ici](https://zestedesavoir.com/billets/2479/r2-pour-changer-de-gdb/)*

Hei, je n'avais pas n'avais pas écrit de billet ici alors que les sujets ne manquent pas...

Aujourd'hui avec des personnes de ma boîte, nous allons peut-être monter une équipe pour le [NorthSec 2018](https://nsec.io/) à Montréal. Il m'arrive très rarement de faire des CTF et je n'en ai pas fait depuis un moment.

Pour se préparer, on va se faire une séance de quelques heures par semaine pour faire quelques challenges. Et j'ai décidé d'apprendre à utiliser `radare2` (http://radare.org/). Dans ce billet, je vais donc partager mon expérimentation du jour, ou des suivantes si j'en fais une série.

Pour symboliser cette décision, une des personnes a proposé un petit challenge, où, comme tous les CTF, il suffit de récupérer un flag. Voici comment j'ai utilisé `r2`:

# Première résolution, simple analyse

Le challenge n'avait rien de compliqué et prenait quelques secondes avec `gdb` à résoudre, je ne vais donc pas en faire un write-up, juste me concentrer sur `r2`.

J'ai tout d'abord tenté une approche sans exécuter le programme, mais en regardant les fonctionnalités de reverse basiques.

La première étape est de lancer `r2` en lui passant le programme

```bash
 AmarOk@tars3  ~/Downloads  r2 -d ./easy_reverse
```

(le `-d` servira pour la seconde approche, par le debug)


Puis avec des tutoriels en ligne, je trouve les commandes `aa` pour lancer une analyse du programme (pour *anyalyze all*) puis (`pdf @main` pour obtenir le code qui m'intéresse). Ce qui donne :

![pdfmain](/img/dev/radare/pdfmain.png)

Chose cool, il est possible de visualiser son code par blocs via `VV @main`.

![VV](/img/dev/radare/VV.jpg)

En regardant vite fait, on voit bien le processus du programme :
1. On demande a l'utilisateur un password
```
 mov edi, str.Password_:     ; 0x4007c4 ; "Password : "
 mov eax, 0
 call sym.imp.printf         ; int printf(const char *format)
 lea rax, [local_70h]
 mov rsi, rax
 mov edi, 0x4007d0
 mov eax, 0
 call sym.imp.__isoc99_scanf
```
via `printf` et `scanf`.

Puis on compare cette valeur avec une autre (` call sym.imp.strcmp         ; int strcmp(const char *s1, const char *s2)`)

Et on félicite ou non la personne.

```
   ,=< 0x00400704      7511           jne 0x400717
   |   0x00400706      bfd3074000     mov edi, str.You_re_a_Wizard ; 0x4007d3 ; "You're a Wizard"
   |   0x0040070b      e810feffff     call sym.imp.puts           ; int puts(const char *s)
   |   0x00400710      b800000000     mov eax, 0
  ,==< 0x00400715      eb0f           jmp 0x400726
  |`-> 0x00400717      bfe3074000     mov edi, str.Try_next_year_... ; 0x4007e3 ; "Try next year ..."
  |    0x0040071c      e8fffdffff     call sym.imp.puts           ; int puts(const char *s)
  |    0x00400721      b801000000     mov eax, 1
```

On suppose simplement que la valeur qu'on cherche c'est le bon mot de passe et je m'intéresse donc à `strcmp`. En suivant les `mov` de dessus, on voit que `strcmp` compare le password qu'on à entré et une valeur que nous voyons en haut :

```
movabs rax, 0x3857397b47414c46
mov qword [local_80h], rax
mov dword [local_78h], 0x7a6e6273
mov word [local_74h], 0x7d38
mov byte [local_72h], 0
```

qui correspond a `FLAG{9W8sbnz8}` à l'envers, notre flag. Pour savoir pourquoi c'est à l'envers, je vous recommande les articles que [Ge0](https://zestedesavoir.com/membres/voir/Ge0/) a pu faire sur ce site.

# Seconde méthode, à l'exécution

Pour le coup, sans radare, j'aurais utilisé `gdb` en mettant un breakpoint et regardant le contenu de ce qui m'intéressait. Faisons donc ceci avec `r2`

Pour ceci, il faut lancer *radare* en mode debug (`-d` de tout à l'heure). Toutes les commandes de `r2` pour le debug commencent par `d`, voir ici : https://radare.gitbooks.io/radare2book/content/introduction/basic_debugger_session.html. Ainsi prenons la ligne qui nous intéresse :

`0x004006f2      e869feffff     call sym.imp.strcmp         ; int strcmp(const char *s1, const char *s2)`

Puis, on peut lancer le programme avec `dc`. Une fois le breakpoint atteint, on peut alors récupérer le contenu de `rdx` contenant notre flag avec `px @ rdx`:

![VV](/img/dev/radare/px.jpg)


Voilà pour ma petite découverte de radare, peut-etre la suite au prochain episode.
