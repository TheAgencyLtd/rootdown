---
title: "Rootdown"
description: "Simple, lightweight content management for developers"
template: "full.php"
---

# Simple, lightweight content management for developers

Rootdown is built to augment the [Slim](http://www.slimframework.com/) Micro framework with content taxonomies making it suitable for prototyping, [rapid application development](http://en.wikipedia.org/wiki/Rapid_application_development) and web sites that require only simple content editing.

**Features**

- Simple to use.
- Easy to integrate into Slim applications or any other framework.
- Capable of deep and complex taxonomies.
- Flexible [YAML](http://yaml.org/) / [Markdown](http://en.wikipedia.org/wiki/Markdown_Extra) approach.
- Fast file systems storage, no database required.

## Installation

We recommend you install Rootdown with the [Composer](http://composer.org) dependency manager. Run this bash command in your project's root directory to download and install the latest stable version into your project's vendor/ directory.

~~~
composer require rootdown/rootdown
~~~

[Read more about installation](/docs/install)

## Usage

Rootdown is simple to use.

<script src="https://gist.github.com/netotaku/ad6cad05bcce069ece95.js"></script>

### Pages & Taxonomy

The CMS page content and taxonomy is stored in the file system as [YAML](http://yaml.org/) /
[Markdown](http://en.wikipedia.org/wiki/Markdown_Extra) files. This not only makes it fast, but easier to version than a database driven site. The site Taxonomy is mirrored by the file's own hierarchy.

[Read more about Pages & Taxonomy](/docs/pages-taxonomy)
