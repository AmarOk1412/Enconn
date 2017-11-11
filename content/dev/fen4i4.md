---
title: Fen4i4
subtitle: A simple twitter bot
date: 2017-03-15
tags: ["bot", "project"]
---

# Goal

The main purpose of this bot is to tweet and retweet "interesting" things. Also, the bot must follow interesting accounts. Moreover, anyone can make a version on this bot, so, the bot must be trainable on a classical computer in one night: for example, my computer with a second generation's i5 and 4Gb of RAM.

![First tweet](/img/dev/fenrir/home.jpg)

# How it works?

Because anyone must be capable of create this twitter bot, I can't use neural nets (anyone can't get enough power to run a good neural net to train a bot to tweet or retweet things). Also, I don't use Markov Chains to generate texts because it will create a lot of false texts. I decided to use a simple Naïve Bayes Classifier based on words, because I think it's easy to train and it can be done in one night on a lot of computers. I think it can give a sufficient result on positive tweets, but the classifier will drop a lot of negatives tweets. In fact, the bot can miss positives, but I don't want to tweet negatives tweets.

# Create the classifier

This is the *very* boring part of this project. I must train my own classifier to make the difference between interesting and non interesting tweets. And... I need A LOOOOOOT of tweets. Negatives tweets are easy to find, we can get tweets from a lot of accounts. But I must manually classify interesting tweets. And I need to do it alone, to make a logical direction of my bot. So, I created a little script to archive tweets from some accounts, clean the tweet (change é,è,ë to 'e', remove some characters, get title from links, etc) and write this tweet in a file (positive or negative). We can imagine a variant to have (to_retweet, to_fav, to_drop). Then, I've got a little script to train the classifier using from textblob.classifiers import NaiveBayesClassifier in Python and pickle the classifier in a file.

# Run the bot!

After a night of computation (and a lot of days of manual classification), I finally got my classifier. I can create the script to run my bot. I use Python3 and [Tweepy](http://docs.tweepy.org/en/v3.5.0/api.html). The API is well documented, but with Tweepy, you can do all the things you want. To initialize my bot, I just classify 5/6 accounts, and after that it's all automated. The bot will classify tweets in the timeline. If the bot detect an interesting tweet, it will classify the user who tweet this and follow this user if the bot find 3 or more interesting tweet in the last 100 tweets of this user. If the bot detect a new follower, it will classify this user. Every interesting tweet in the timeline is added to a list and the bot pick between 1 to 12 tweet in this list and then, sleep during 1 to 12 hours. Because it's just a classifier and not a text generator, I can't create tweets. Text generation is hard and it's a more way harder without any power to compute things. Mechanical reactions are easy to simulate. So, the bot can try to create reactions with the classifier. In fact, when fen4i4 meet a tweet with a link, it can try to parse the page, get sentence with less than 140 characters and classify this sentence with our classifier. We can imagine other simple way to generate pertinent tweets, but it's the only thing implemented yet.

![First tweet](/img/dev/fenrir/tweet.jpg)


# Code

You can find my bot on twitter with [@fen4i4](https://twitter.com/fen4i4) and the code on [Github](http://github.com/AmarOk1412/fen4i4).
