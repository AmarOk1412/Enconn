---
title: ZMarkdown
subtitle: The Markdown engine powering Zeste de Savoir.
date: 2017-11-11
tags: ["zds", "project", "web", "js"]
---

# Description

ZMarkdown is the markdown engine used by [Zeste de Savoir](https://zestedesavoir.com). It translates Markdown input to an [AST](https://en.wikipedia.org/wiki/Abstract_syntax_tree), then the *AST* to *LaTeX* or *HTML*. So, we can render markdown in a web page or generate PDF from our tutorials. This project is based on [Remark](http://remark.js.org/) and created in NodeJs.

# Why?

Similar projects like Pandoc don't use AST to translate Markdown and they are difficult to improve with modules. Because *Remark* is extremely modulable and uses an AST, it's the best solution to craft a markdown engine to translate a custom markdown into any format (HTML, slides, PDF, generate Table Of Contents, generate man pages, etc).

# How it works?

Remark is based on [unified](https://github.com/unifiedjs/unified) which process text with AST. Syntax trees are a representation of an entity (a text) in a understandable structure for programs.

For example:

```
text
|->Chapter
   |-> paragraph
       |-> text
       |-> link
       |-> text
   |-> paragraph
```
Will represent a text which contains a Chapter with 2 paragraph. The first paragraph got a link surrounded by two simple texts.

Then you can use this tree to generate a text, for example the link will be transformed by `<a href="link">text</a>`.

The code eating text to produce an AST or which eats the AST to produce a text are separated into several plugins. And so, you can build your own markdown in taking the module you want.

So you have something like
```
| ....................... process() ......................... |
| ......... parse() ..... | run() | ..... stringify() ....... |

          +--------+                     +----------+
Input ->- | Parser | ->- Syntax Tree ->- | Compiler | ->- Output
          +--------+          |          +----------+
                              X
                              |
                       +--------------+
                       | Transformers |
                       +--------------+
```
Source: https://github.com/unifiedjs/unified

# Demo

ZMarkdown can be tested here: https://zestedesavoir.github.io/zmarkdown/public/

[This is an example](https://github.com/zestedesavoir/latex-template/files/1431863/test.pdf) for a tutorial from https://zestedesavoir.com.


# Contribute

This project is under the MIT License and you can find the code on [Github](https://github.com/zestedesavoir/zmarkdown)
