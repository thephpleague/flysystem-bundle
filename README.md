# flysystem-bundle

[![Build Status](https://travis-ci.org/tgalopin/flysystem-bundle.svg?branch=master)](https://travis-ci.org/tgalopin/flysystem-bundle)

[![SymfonyInsight](https://insight.symfony.com/projects/525fdfa3-d482-4218-b4b9-3c2efc305fac/big.svg)](https://insight.symfony.com/projects/525fdfa3-d482-4218-b4b9-3c2efc305fac)

This repository is a light Symfony bundle integrating the [Flysystem](https://flysystem.thephpleague.com)
library into Symfony applications. It mainly aims at injecting Flysystem instance(s) efficiently into your 
Dependency Injection container. 

## Installation

flysystem-bundle requires PHP 7.1+ and Symfony 4.2+.

You can install the bundle using Symfony Flex:

```
composer require tgalopin/flysystem-bundle
```

## Documentation

1. [Getting started](https://github.com/tgalopin/flysystem-bundle/blob/master/docs/1-getting-started.md)
2. [Caching](https://github.com/tgalopin/flysystem-bundle/blob/master/docs/2-caching.md)
3. [Using the mount manager](https://github.com/tgalopin/flysystem-bundle/blob/master/docs/3-using-mount-manager.md)
4. [Configuration reference](https://github.com/tgalopin/flysystem-bundle/blob/master/docs/4-configuration-reference.md)

## Security Issues

If you discover a security vulnerability within the bundle, please follow
[our disclosure procedure](https://github.com/tgalopin/flysystem-bundle/blob/master/docs/A-security-disclosure-procedure.md).

## Backward Compatibility promise

This library follows the same Backward Compatibility promise as the Symfony framework:
[https://symfony.com/doc/current/contributing/code/bc.html](https://symfony.com/doc/current/contributing/code/bc.html)

> *Note*: many classes in this library are either marked `@final` or `@internal`.
> `@internal` classes are excluded from any Backward Compatiblity promise (you should not use them in your code)
> whereas `@final` classes can be used but should not be extended (use composition instead).
