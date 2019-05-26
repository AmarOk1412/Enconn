---
title:  Créer un bridge IRC - Ring
subtitle:  Réalisation d'un bridge IRC/Ring en python
date: 2017-05-06
tags: ["software", "ring", "python"]
---

Contenu posté sur [Zeste de Savoir](https://zestedesavoir.com/contenus/1829/creer-un-bridge-irc-ring/)

*J'aime faire des bots*. J'ai des bots pour tout et n'importe quoi. J'ai des bots sur twitter, sur des serveurs [IRC](https://github.com/zestedesavoir/clem-irc-bot), des bots plus ou moins utile, des bots qui contrôlent [des trucs](/dev/glados-replica) et d'autres qui bougent.  
Ce matin, j'ai décidé de réaliser un petit bot qui sert de bridge entre un channel IRC et [ring.cx](https://ring.cx). Ce bot peut servir à plusieurs choses :

1. Pouvoir pinguer une personne par Ring depuis le channel IRC.
2. Pouvoir choisir de récupérer tout le contenu d'un channel.
3. Pouvoir arrêter de recevoir les informations par Ring.

Dans ce billet je vais expliquer comment je l'ai réalisé.

# Ring

[Ring.cx](https://ring.cx/) est un logiciel de conférence vidéo et de communication instantanée open-source et compatible SIP sous licence GPL 3.

Le truc intéressant et qu'il permet de manière simple d'avoir une base pour contrôler ce qu'on veut via un chat et même jouer avec la vidéo de manière assez simple. En soit, il est possible d'imaginer un script qui contrôle ce qu'on souhaite comme une [lampe](https://blog.savoirfairelinux.com/fr-ca/2015/internet-des-objets-ring-appareils-connectes-iot/), des capteurs, des caméras ou encore un bot IRC tout en déléguant la partie authentification, communication et sécurité en grande partie à GNU Ring. Il est ensuite possible de communiquer avec ce bot via n'importe quel client (il en existe sur Linux, Windows, Android, etc). Le seul truc à faire est de réaliser un script qui se connectera au daemon et va réagir aux nombreux signaux.

Il est aussi possible de l'installer depuis les sources depuis le [git](https://gerrit-ring.savoirfairelinux.com/ring-project) du projet.

# L'interface du daemon

Le daemon possède de nombreux signaux et méthodes. Une fois installé, on peut trouver le dossier contenant les binaires dans `daemon/bin/`. On peut ainsi le lancer avec la commande `./dring`. Dans ce même dossier, on peut trouver les fichiers `bin/dbus/cx.ring.Ring.*.xml` qui vont contenir la liste des signaux et des méthodes ainsi que comment les utiliser.

Par exemple, dans `bin/dbus/cx.ring.Ring.ConfigurationManager.xml` nous pouvons trouver le signal que nous utiliserons plus loin :
{{< highlight xml >}}
<signal name="incomingAccountMessage" tp:name-for-bindings="incomingAccountMessage">
   <tp:added version="2.2.0"/>
   <tp:docstring>
       Notify clients that a new text message has been received at the account level.
   </tp:docstring>
   <arg type="s" name="accountID"/>
   <arg type="s" name="from"/>
   <annotation name="org.qtproject.QtDBus.QtTypeName.In2" value="MapStringString"/>
  <arg type="a{ss}" name="payloads"/>
</signal>
{{< /highlight >}}

Ce signal va permettre d'agir à la réception d'un message.

Il y a moyen de gérer énormément de choses comme la vidéo, appeler quelqu'un, modifier le niveau du son, envoyer un message, etc. Bref, on connaît l'interface pour communiquer avec le daemon, il nous reste plus qu'à l'utiliser afin de faire ce que l'on souhaite.

# Contrôlons Ring

Un autre dossier s'avère intéressant afin de parvenir à faire ce que l'on souhaite. Jetons un oeil du côté des outils fournis avec le daemon, c'est-à-dire dans le dossier `/tools/` où nous pouvons trouver un projet nommé `dringctrl` qui contient un fichier nommé `controler.py`, avec comme description **DRing controling class through DBUS**, ce qui est exactement ce que l'on cherche.

Dans le fichier, on trouve une classe nommée `DRingCtrl` qui permet de réagir aux évènements du *VideoManager*, *CallManager*, *ConfigurationManager* et autres parties en connectant les signaux à des callbacks. Ainsi, pour obtenir une application réagissant aux évènements envoyés par le daemon de connecter le signal à une callback ; puis de baser notre application sur `DRingCtrl`. Par exemple pour le signal donné plus haut, il nous suffit d'ajouter dans la méthode `register()`:

{{< highlight python "linenos=table">}}
proxy_confmgr.connect_to_signal('incomingAccountMessage', self.onIncomingAccountMessage)
{{< /highlight >}}

et lorsqu'on recevra un message, on pourra le traiter avec uns méthode nommée *onIncomingAccountMessage*.

Par exemple, on peut imaginer la base de notre contrôleur comme ceci :

{{< highlight python "linenos=table">}}
class IRCRingController(DRingCtrl):
    def __init__(self, config_bridge):
        super().__init__('ircbridge', True)
        self.accountId = self.configurationmanager.getAccountList()[0]

    def onIncomingAccountMessage(self, accountId, fromAccount, payloads):
        message = '%s: %s' % (fromAccount, payloads['text/plain'])
        print('Receive new message from %s' % message)
{{< /highlight >}}

# Un bot IRC

Il existe de nombreuses bibliothèques pour réaliser des bots IRC. On peut en trouver dans pas mal de langages existants. Cette fois, j'ai choisi d'utiliser [irc3](https://github.com/gawel/irc3/) de *gawel*. Elle consiste à développer un plugin pour l'ajouter à un bot. Voici, les scénarios que j'imaginais pour mon bot :

+ Le contrôle de la configuration se fait seulement via Ring, car généralement sur IRC, les personnes ne pensent pas forcément à protéger leur pseudo. Ici l'authentification est gérée via Ring.
+ Une personne peut choisir de pouvoir recevoir des informations du bot. Il suffit d'utiliser la commande `!add nick` au bot via ring. Depuis IRC on pourra communiquer avec cette personne avec `!tell nick message` et le bot se chargera d'envoyer à la bonne personne le message.
+ Une personne peut vouloir ne plus recevoir de message, il suffit d'envoyer `!rm`
+ Une personne peut vouloir recevoir l'intégralité du contenu du chan, il suffit d'envoyer `!follow` et `!unfollow` pour arrêter de recevoir les informations.


Le code est disponible ici : https://gist.github.com/AmarOk1412/df88402e9a1e5710898cd2df2be117d8

![Le programme en action](/img/dev/bridge-irc-ring/capture.png)

Au final, on peut voir que Ring peut s'avérer être une bonne base pour tout type de bots se basant sur la communication en profitant de toute l'abstraction qu'il peut fournir au niveau de l'authentification, de la communication, de la décentralisation.
