---
title: Tarpaulin ou kcov ?
subtitle: Outils de couverture de code pour les projets Rust
date: 2018-04-23
tags: ["programmation", "tests", "rust"]
---

Pour un projet (que je présenterais plus tard sur le forum), j'ai eu besoin d'avoir du retour sur mes tests. Une des métriques m'intéresse est le pourcentage de couverture, car il me permet de connaître les branches que je n'ai pas encore testé et d'ajouter des scénarios.

En cherchant un peu sur Internet, je suis tombé sur 2 outils :

1. [kcov](https://github.com/SimonKagstrom/kcov/)
2. [tarpaulin](https://github.com/xd009642/tarpaulin)

En voici un petit comparatif.

# Comparatif

## Facilité d'installation

### kcov

```bash
wget https://github.com/SimonKagstrom/kcov/archive/master.tar.gz &&
  tar xzf master.tar.gz &&
  cd kcov-master &&
  mkdir build &&
  cd build &&
  cmake .. &&
  make &&
  make install DESTDIR=../../kcov-build &&
  cd ../.. &&
  rm -rf kcov-master
```

### Tarpaulin

```bash
# install libssl-dev pkg-config cmake zlib1g-dev
cargo install cargo-tarpaulin
```

**Tarpaulin 1 ; kcov 0**

## Utilisation simple et explicite

### kcov

```bash
kcov --exclude-pattern=/.cargo,/usr/lib --verify target/cov target/debug/<PROJECT-NAME>-<hash>
```

### Tarpaulin

```bash
cargo tarpaulin
```

**Tarpaulin 2 ; kcov 1**

Les 2 restent simples d'utilisation. Tarpaulin ayant moins d'options pour le moment.

## Fonctionne

### kcov

Pour mon projet... les rapports donnaient des %NAN...

### Tarpaulin

Fonctionne directement, les rapports sont corrects.

**Tarpaulin 3 ; kcov 1**

## Support rapide

### kcov

Une issue posté, réponse rapide, mais pas de solution trouvée

### Tarpaulin

Une issue posté, la personne est rapide a répondre et a cherché en profondeur (l'issue venait de `rustc` et est fixée en nightly)

**Tarpaulin 4 ; kcov 1**

## Compatibilité avec coveralls.io

Les deux sont compatibles et peuvent envoyer des rapports facilement à coveralls.io

**Tarpaulin 5 ; kcov 2**

## Produit des rapports HTML (pour avant de soumettre mon code)

kcov le permet, tarpaulin dans une version future

**Tarpaulin 5 ; kcov 3**

## Permet d'exclure des fichiers du rapport

**Tarpaulin 5 ; kcov 4**

Au final même si `kcov` possède plus de fonctionnalités, `tarpaulin` l'emporte (et le fait que seul lui fonctionne dans mon cas est ce qui m'intéresse le plus je l'avoue).


# Conclusion

Au final, si `tarpaulin` est intéressant pour votre projet, voici le `.travis.yml` du projet :

```yml
language: rust
rust:
  - stable
  - nightly
sudo: true
git:
    submodules: false
before_install:
  - sed -i 's/git@github.com:/https:\/\/github.com\//' .gitmodules
  - git submodule update --init --recursive
install:
  - sudo apt-get install -y libdbus-1-dev openssl
  - |
    # tarpaulin deps
    if [[ "$TRAVIS_RUST_VERSION" == nightly ]]; then
      sudo apt-get update -y && sudo apt-get -y install libssl-dev pkg-config cmake zlib1g-dev;
      cargo install cargo-tarpaulin;
    fi
  - rustc -V
before_script:
  - "export DISPLAY=:99.0"
  - "sh -e /etc/init.d/xvfb start"
  - sleep 3 # give xvfb some time to start
script:
  - cargo build
  - RUST_TEST_THREADS=1 cargo test -- --nocapture
  - |
    # tarpaulin deps
    if [[ "$TRAVIS_RUST_VERSION" == nightly ]]; then
      cargo tarpaulin --ciserver travis-ci --coveralls $TRAVIS_JOB_ID;
    fi
```
