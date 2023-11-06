# phpdns: PHP DNS Client Library

## Overview

The PHP DNS client is a GPL set of PHP classes originally developed by [David Cutting](https://davecutting.uk) providing a direct socket-level domain name service client API. Originally developed to be a testing module for the [FreeNATS network monitor](https://www.purplepixie.org/freenats/) it was decided to package it up as a standalone library as well.

> Although there are plenty of other DNS classes/clients out there I found them to either be too overblown or actually non-functional. This API is intended to be a half-way house offering direct-to-server queries, the ability to process the response in detail but still with a simple interface for the programmer. -- *David Cutting*

The library is now held at [github.com/purplepixie/phpdns](https://github.com/purplepixie/phpdns/) and contains contributions from a number of people both credited in the commits and prior to moving to Github.

## Licence and Copyright

Unless otherwise stated in a specific file the PHP DNS Query Library is (C) Copyright 2008-2023 [PurplePixie Systems / David Cutting](https://purplepixie.org) and all rights are reserved.

The software is provided on an "as-is" basis without warranty or liability of any kind under the [GNU General Public Licence (GPL)](http://www.gnu.org/licences/gpl.html) (version 3 or later at your discretion).

## Errors and Bugs

Should be reported through raising an [issue on Github](https://github.com/purplepixie/phpdns/issues). You are also very welcome to contribute fixes to the code (or documentation) via a PR.

# Installing / Obtaining phpdns

There are two main ways to get phpdns: via composer or using the source code directly.

## Composer

By far the easiest way to use phpdns is through the use of [composer](https://getcomposer.org/) which allows you to use the latest release via [Packagist](https://packagist.org/packages/purplepixie/phpdns). With composer installed:

```bash
composer require purplepixie/phpdns
``````

Will install the latest release of phpdns which can then be used with autoload.

You can now use the classes in the ```PurplePixie\PhpDns``` namespace through autoload, for example:

```php
// Autoload
require("vendor/autoload.php");
// Namespace for DNSQuery
use PurplePixie\PhpDns\DNSQuery;
// Do a query
$query = new DNSQuery("8.8.8.8");
$result = $query->query('purplepixie.org', \PurplePixie\PhpDns\DNSTypes::NAME_A);
print_r($result);
```

## Using Source Directly

You can just download and use the PHP source files directly, either by downloading an archive (this could be the [current codebase](https://github.com/purplepixie/phpdns/archive/refs/heads/master.zip) or a [specific release tag](https://github.com/purplepixie/phpdns/tags)) or by cloning [the repository](https://github.com/purplepixie/phpdns).

To then use the library you must include the relevant files. You can do this manually or just include the ```dns.inc.php``` file (all this assumes you are above the ```src``` folder, otherwise you will need to adjust as required).

Example of using source code manually:

```php
// Include the files - note: require_once 'dns.inc.php' will do this for you
require_once __DIR__ . '/src/PurplePixie/PhpDns/DNSAnswer.php';
require_once __DIR__ . '/src/PurplePixie/PhpDns/DNSQuery.php';
require_once __DIR__ . '/src/PurplePixie/PhpDns/DNSResult.php';
require_once __DIR__ . '/src/PurplePixie/PhpDns/DNSTypes.php';
// Namespace for DNSQuery for ease
use PurplePixie\PhpDns\DNSQuery;
// Do a query
$query = new DNSQuery("8.8.8.8");
$result = $query->query('purplepixie.org', \PurplePixie\PhpDns\DNSTypes::NAME_A);
print_r($result);
```

# Using phpdns

For usage examples and instructions please see the [user documentation](./usage.md). There is also a [technical reference document](./technical.md).

# Contributing to phpdns

Contributions to phpdns are very welcome and the codebase is made stronger by the many contributions we have already seen. Feel free to contribute via Pull Requests in github for the main codebase or the documentation (main codebase is in the ```master``` branch and other dev branches, the docs are in the ```website``` branch).

If you have found phpdns particularly useful then please feel free to give us a star on Github and if you're feeling financially generous make a donation to a charity of your choice (phpdns is *free* software in every sense).