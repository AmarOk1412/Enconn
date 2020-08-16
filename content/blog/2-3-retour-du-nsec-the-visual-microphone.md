---
title: (2/3)Retour du nsec - The visual microphone
date: 2018-05-31
tags: ["ctf", "sécurité"]
---

Suite de la petite série sur le retour du nsec. Dans ce second article, je vais m'intéresser la dernière partie d'une track en 3 points réalisée par un autre membre de mon équipe.

La première partie consistait à trouver la fréquence du mouvement du diaphragme d'un haut parleur. Puis de trouver la musique qui était jouée par cette enceinte (bien entendu, le son n'était pas présent). La dernière partie consistait à retrouver une conversation réalisée dans une pièce où un paquet de chips était filmé à très haute fréquence.

Note, je ne vais pas m'attarder sur le côté mathématiques de l'exercice, mais le code étant utilisable et le papier publié, toute personne souhaitant creuser le pourra. Dans notre cas, nous devions trouver le maximum de  flags en un temps court, l'idée est donc d'aller au plus rapide/efficace.

# Le MIT à la rescousse

Alors certes, obtenir une conversation secrète à l'aide d'une vidéo de paquet de chips peut sembler un peu bizarre voir directement sortie d'une scène des experts où des gens zooment dans tous les sens sur une vidéo et reconstruisent magiquement le son, mais c'est possible et des personnes ont déjà travaillés dessus.

Il y a quelques années (2014), j'étais tombé sur un travail du MIT filmant les vibrations d'une plante afin de retrouver le son présent dans une pièce. Cet article venait de hackaday : https://hackaday.com/2014/08/06/focus-your-ears-with-the-visual-microphone/

Ce flag est donc possible à obtenir. Le problème étant de comment le faire rapidement ?

*The Visual microphone* possède une [page](https://people.csail.mit.edu/mrub/VisualMic/) bien documentée avec le papier, des exemples (dont un paquet de chips) et... du code matlab, prenant une vidéo en entrée et fournit un fichier son en sortie. Super !

{{< youtube FKXOucXB4a8 >}}

# Parle moi petit paquet de chips

![chips](/media/galleries/5133/abf4142c-afbc-4ab4-8b31-f0f1ee41a196.jpg)

Le script étant gentillement donné directement dans la page du papier, il suffit de l'exploiter. Le script ne marche malheureusement pas sous `Octave` et l'installateur de Matlab ne supportant que l'IPv4 (non disponible au nsec), l'installation à pris un peu de temps. Cependant, on se rend alors compte qu'il suffit de changer le nom de la vidéo, le format et le sampling rate pour faire fonctionner le script (et modifier la fonction d'écriture par `audiowrite` pour ne pas faire planter le script à la toute fin).

Le point d'entrée du script ressemble au final à ceci :

```
%%
%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
%
%By Abe Davis:
%This code runs a very basic version of the Visual Microphone on high speed
%video of a bag of chips being hit by a linear ramp of frequencies.
%
%The provided default video (crabchipsRamp.avi) has a very strong signal,
%so you can still get results if you downsample by a significant factor.
%The default below is to downsample to 0.1 times the original size, which
%is enough to make things runable on my laptop in a reasonable amount of
%time.

%This code does not leverage rolling shutter.
%note that the recovered sound is sampled at 2200Hz, which may not play by
%default in some media players. It will play in MATLAB though.

%this code also uses matlabPyrTools by Eero P. Simoncelli
%parts of it also use the signal processing toolbox

%A lot of the functions pass around objects that represent sounds. They
%have fields:
%'x' - the time signal
%'samplingRate' - the sampling rate.

%This work is based on:
%"The Visual Microphone: Passive Recovery of Sound from Video"
%by Abe Davis, Michael Rubinstein, Neal Wadhwa, Gautham J. Mysore,
%Fredo Durand, and William T. Freeman
%from SIGGRAPH 2014
%
%MIT has a patent pending on this work.
%
%(c) Myers Abraham Davis (Abe Davis), MIT
%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
%%
%

%change the below path to wherever you put the VisualMicCode Directory
%cd H:\VisualMicCode

clear
setPath
currentDirectory = pwd;
%dataDir = fullfile('', 'data');%dataDir should be directory to data
dataDir = uigetdir();
vidName = 'q4';
vidExtension = '.mp4';
testcasename = vidName;
nscales = 1;
norientations = 2;
dsamplefactor = 0.1; %downsample to 0.1 full size

filename = [vidName vidExtension];

vr = VideoReader(fullfile(dataDir, filename));

%if the video is saved with the actual framerate, you can set as follows.
%samplingrate = vr.FrameRate;

%otherwise, specify the framerate manually
samplingrate = 2400;

wndw = 80;
olap = 40;


%%
S = vmSoundFromVideo(vr, nscales, norientations, 'SamplingRate', samplingrate,'DownsampleFactor', dsamplefactor);
S.fileName = vidName;

%For very clean videos (loud sound, good magnification) S will already be
%enough. Videos with more noise may not be clean enough to hear the result
%right away, in which case you should try spectral subtraction. The best
%way to do this is manually in software like Adobe Audition, but if that is
%not available you can try our MATLAB implementation.

%%
%show spectrogram and play sound
close all;
spectrogram(S.x, 100, 50)
vmPlaySound(S)

%%
%compute spectral subtraction (often helps with noisier signals)
Sspecsub = vmGetSoundSpecSub(S);

%%
close all;
spectrogram(Sspecsub.x, wndw, olap)
vmPlaySound(Sspecsub)

%%
%in this case you will see that highpassing and spectral subtraction
%weren't completely necessary, but each helps a little bit.
%The signal is already pretty clear in this video.

%Spectral subtraction seems to take away a bit of signal once the ramp
%starts, but it helps remove noise in the silent portion.

wndw = 80;
olap = 40;

S_unfiltered = S;
S_unfiltered.x = S.aligned;

nc = 3;nr = 2;pn=1;
close all;

subplot(nc,nr,pn);pn=pn+1;
plot(S_unfiltered.x);
title('recovered time signal')
subplot(nc,nr,pn);pn=pn+1;
spectrogram(S_unfiltered.x, wndw, olap)
title('recovered spectrogram')

subplot(nc,nr,pn);pn=pn+1;
plot(S.x);
title('highpass time signal')
subplot(nc,nr,pn);pn=pn+1;
spectrogram(S.x, wndw, olap)
title('highpass spectrogram')

subplot(nc,nr,pn);pn=pn+1;
plot(Sspecsub.x)
title('spec sub time signal')
subplot(nc,nr,pn);pn=pn+1;
spectrogram(Sspecsub.x, wndw, olap)
title('spec sub spectrogram')


%%
%vmWriteWAV(S, 'RecoveredSound.wav');
audiowrite('chips.wav',S.x,samplingrate);
```

Après quelques minutes de processing (voir le papier pour plus de détails du pourquoi ça marche), on obtient un [fichier audio](http://enconn.fr/chips.ogg) peu compréhensible. Après plusieurs heures d'écoute en boucle, la phrase finale devient plus claire : "we will deport the ones with less than two merit points". Le flag est trouvé !

Merci pour les orgas du nsec pour l'épreuve originale et le temps passé à tester les paquets de chips. Le sujet est clairement à creuser et il y a moyen de faire une bonne bibliothèque Rust pour extraire du son d'une vidéo de chips.

Même si le résultat n'est pas excellent, même avec une vidéo tournée à 2400 images/secondes, il est intéressant de voir ce qu'il est possible de faire avec un petit script et des mois de recherche pour une équipe. À noter que dans le papier du MIT, il à l'air possible d'utiliser des caméras à plus basse fréquence pour faire le même travail (avec ce qu'ils nomment le Rolling Shutter). Avis aux personnes ont eu le temps de creuser plus le sujet et qu'ils souhaitent en faire un article plus détaillé que mon petit billet.
