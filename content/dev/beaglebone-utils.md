---
title: Beaglebone Utils
subtitle: Création d'une bibliothèque destinée à la Beaglebone White.
date: 2015-06-05
tags: ["hardware", "project", "software", "beaglebone"]
---

# Qu'est-ce-que la beaglebone ?

Il s'agit d'une carte électronique open-hardware type mini-PC. Elle possède un système d'exploitation type linux (Angström de base). Vous pouvez retrouver la description technique [ici](http://beagleboard.org/Products/BeagleBone).

# Motivation

Depuis le début de mon projet [GLaDOs Replica](/dev/glados-replica/), je voulais porter le robot sur Beaglebone (et utiliser l'Arduino seulement pour le prototypage). Or la beaglebone blanche possède une communauté plus faible que la beaglebone noire. Les bibliothèques sont souvent incompatibles ou inexistantes (exemple avec la bibliothèque d'[Adafruit](https://learn.adafruit.com/controlling-a-servo-with-a-beaglebone-black/servo-motors)). J'ai donc décidé de me créer ma propre bibliothèque pour combler ce manque et obtenir ce que je souhaitais, c'est-à-dire un contrôle simplifié de mes DEL RVB et de mes servomoteurs.

# Contrôle des DELs

Comme dit plus haut, cette carte possède son propre système d'exploitation (un linux embarqué tel que Angström/Debian/Ubuntu/Fedora...). Sur linux, tout est considéré comme un fichier. C'est le cas des pins. En effet, on peut accéder aux GPIO depuis le dossier /sys/class/gpio.

{{< highlight bash >}}
root@beaglebone:/sys/class/gpio# cd /sys/class/gpio/
root@beaglebone:/sys/class/gpio# ls -F
export   gpiochip0@   gpiochip64@ gpiochip32@  gpiochip96@ unexport
root@beaglebone:/sys/class/gpio#
{{< /highlight >}}

On remarque ici deux fichiers (export et unexport). Le premier sert à "ouvrir" un pin, l'autre à le "fermer". En fait, "ouvrir" un pin nous permet d'y accéder comme un dossier classique avec des paramètres représentés par des fichiers. Le "fermer" revient à rendre le dossier inaccessible.
Pour allumer une DEL, il suffit donc d'ouvrir le pin voulu, de le mettre en mode sortie, de lui donner l'ordre d'envoyer 5V, puis de le fermer. La correspondance des pins/dossier sur beaglebone peut être difficile à gérer au début. Voici donc une table de correspondance :

![Table de correspondance](/img/dev/beagle-utils/bb_pin.png)

Imaginons donc le montage suivant avec une DEL RVB à cathode commune :
![Del](/img/dev/beagle-utils/RGBLED_digital_bb.png)

Ici, le gpio60 correspond au rouge, le 50 au bleu et le 51 au vert. On peut donc exécuter la suite de commandes suivante :
{{< highlight bash >}}
root@beaglebone:/sys/class/gpio/gpio51# #On ouvre les pins
root@beaglebone:/sys/class/gpio# echo 60 > export
root@beaglebone:/sys/class/gpio# echo 50 > export
root@beaglebone:/sys/class/gpio# echo 51 > export
root@beaglebone:/sys/class/gpio/gpio51# #On allume le rouge
root@beaglebone:/sys/class/gpio# cd gpio60
root@beaglebone:/sys/class/gpio/gpio60# echo out > direction
root@beaglebone:/sys/class/gpio/gpio60# echo 1 > value
root@beaglebone:/sys/class/gpio/gpio51# #On allume le vert
root@beaglebone:/sys/class/gpio/gpio60# cd ../gpio50
root@beaglebone:/sys/class/gpio/gpio50# echo out > direction
root@beaglebone:/sys/class/gpio/gpio50# echo 1 > value
root@beaglebone:/sys/class/gpio/gpio51# #On allume le bleu
root@beaglebone:/sys/class/gpio/gpio50# cd ../gpio51
root@beaglebone:/sys/class/gpio/gpio51# echo out > direction
root@beaglebone:/sys/class/gpio/gpio51# echo 1 > value
root@beaglebone:/sys/class/gpio/gpio51# #On ferme les pins
root@beaglebone:/sys/class/gpio/gpio50# cd ..
root@beaglebone:/sys/class/gpio# echo 60 > unexport
root@beaglebone:/sys/class/gpio# echo 50 > unexport
root@beaglebone:/sys/class/gpio# echo 51 > unexport
{{< /highlight >}}

Une partie de ma bibliothèque a donc été créée dans le but de faciliter l'accès à ces entrées/sorties. En effet, la bibliothèque possède un objet GPIO donnant accès aux fonctions :

{{< highlight cpp "linenos=table">}}
bool pinMode(int pin, std::string mode);
//Pour ouvrir un pin dans le mode "in" ou "out" (macros INPUT/OUTPUT)
bool freePin(int pin);
//Pour libérer le pin
bool digitalWrite(int pin, int value);
//Pour écrire une valeur pour le pin donné (macros HIGH/LOW)
int digitalRead(int pin);
//Pour lire la valeur du pin
{{< /highlight >}}

## Contrôle de la luminosité via PWM

Avec la méthode vue précédemment, la valeur du pin est soit égale à 0 soit à 1. Cette méthode n'est donc pas suffisante pour contrôler la luminosité d'une DEL. On va donc utiliser une sortie PWM. Il y en a 8 contrôlables depuis /sys/class/pwm/.
Tout d'abord câblez le montage suivant :

![bb PWN LED](/img/dev/beagle-utils/bbPWMLED.png)

Et rendez-vous dans le dossier `/sys/class/pwm//ehrpwm.1:0`. Vous pouvez y trouver quelques fichiers intéressants :
- request : pour rendre le pin occupé
- polarity
- run : pour lancer l'exécution
- period_ns et period_freq : pour donner la période en NS ou en fréquence
- duty_percent : la largeur de la bande en pourcent.

Les autres fichiers nous serviront plus tard.
Voici donc comment allumer une DEL à 10% (en se basant sur le montage précédent) :

{{< highlight bash >}}
#EHRPWM0A=P9.29
echo 1 > /sys/kernel/debug/omap_mux/mcasp0_fsx
#EHRPWM0B=P9.31
echo 1 > /sys/kernel/debug/omap_mux/mcasp0_aclkx
#EHRPWM1A=P9.14 <- Ici seule la ligne suivant est utile pour activer le pin voulu.
echo 6 > /sys/kernel/debug/omap_mux/gpmc_a2
#EHRPWM1B=P9.16
echo 6 > /sys/kernel/debug/omap_mux/gpmc_a3
#EHRPWM2A=P8.13
echo 4 > /sys/kernel/debug/omap_mux/gpmc_ad8
#EHRPWM2A=P8.19
echo 4 > /sys/kernel/debug/omap_mux/gpmc_ad9

#On informe que le pin P9.14 est occupé
echo 1 > request
echo 0 > run
#On donne une fréquence (ici 50 Hz)
echo 50 > period_freq
#On définit notre largeur de bande (ici 10%)
echo 10 > duty_percent
#On exécute le tout
echo 1 > run
{{< /highlight >}}


## Plus de lisibilité
Vous vous rappelez la table de correspondance pour les pins ?
![bb_pin](/img/dev/beagle-utils/bb_pin.png)

Une partie de la bibliothèque créée sert de simplification d'accès à ces pins. Exemple : la macro "P9_12" (12ème pin de l'extension P9) fait référence au GPIO60. On peut donc écrire le code suivant pour obtenir 5V au pin P9_12 :

{{< highlight cpp "linenos=table">}}
gpio.pinMode(P9_12, OUTPUT);
gpio.digitalWrite(P9_12, HIGH);
{{< /highlight >}}

Mais nous pouvons aussi imaginer un fading de DEL. Il faut donc utiliser un pin compatible PWM. Voici le code d'exemple pour :
{{< highlight cpp "linenos=table">}}
#include "lib/Pin.h"
#include "lib/PWM.h"
#include <iostream>
#include <fstream>
#include <unistd.h>

int main(int argc, char *argv[])
{
  PWM ledRouge;
  ledRouge.enablePin(PWMP9_14);
  ledRouge.attach(PWMP9_14);
  ledRouge.writeDutyPercent(PWMP9_14,100);
  PWM ledVerte;
  ledVerte.enablePin(PWMP9_29);
  ledVerte.attach(PWMP9_29);
  ledVerte.writeDutyPercent(PWMP9_29,10);
  PWM ledBleue;
  ledBleue.enablePin(PWMP9_16);
  ledBleue.attach(PWMP9_16);
  ledBleue.writeDutyPercent(PWMP9_16,10);
  sleep(5);
  ledRouge.detach(PWMP9_14);
  ledVerte.detach(PWMP9_29);
  ledBleue.detach(PWMP9_16);
  return 0;
}
{{< /highlight >}}

On obtient une DEL Orange avec le rouge connecté sur le pin P9_14, le vert sur le P9_29 et le bleu sur le P9_16.

## Contrôle des servomoteurs

Voici donc une suite d'instructions pour contrôler un servomoteur branché de cette manière :
![bb servo](/img/dev/beagle-utils/bbservo.png)

La suite d'instructions :
{{< highlight bash >}}
#On se rend dans le dossier
cd /sys/class/pwm/ehrpwm.1\:0
#On informe que le pin est occupé
echo 1 > request
echo 0 > run
#On donne une fréquence de 50Hz
echo 50 > period_freq
#On l'initialise à 0 degrés
echo 500000 > duty_ns
#On exécute le tout
echo 1 > run
#On le fait tourner à 180°
echo 2000000 > duty_ns
{{< /highlight >}}

La bibliothèque doit donc s'occuper de rendre le pin choisi disponible ou indisponible, d'écrire la bonne fréquence et de convertir les degrés NS en degrés de mesure (0-180° plus lisible que 500 000 - 2 000 000).

Voici donc un code d'exemple pour passer plusieurs fois un servomoteur de 0 à 180° (branché sur le pn P9_14) :

{{< highlight cpp "linenos=table">}}
#include "lib/Pin.h"
#include "lib/PWM.h"
#include <iostream>
#include <fstream>
#include <unistd.h>

int main(int argc, char *argv[])
{
  PWM servo;
  servo.enablePin(PWMP9_14);
  servo.attach(PWMP9_14);
  servo.writePos(PWMP9_14, 0);
  sleep(1);
  servo.writePos(PWMP9_14, 180);
  sleep(1);
  servo.writePos(PWMP9_14, 0);
  sleep(1);
  servo.writePos(PWMP9_14, 180);
  sleep(1);
  servo.writePos(PWMP9_14, 0);
  sleep(1);
  servo.writePos(PWMP9_14, 180);
  sleep(5);
  servo.detach(PWMP9_14);
  return 0;
}
{{< /highlight >}}

## Conclusion

Voilà, j'espère que la bibliothèque pourra vous être utile. Vous pouvez l'obtenir [ici](https://github.com/AmarOk1412/BeagleBone-Utils). N'hésitez pas à remonter d'éventuels bugs.
