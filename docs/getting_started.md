# Getting started

## Installation (new project)

```
composer init --require="johnwilson/jsondb"
```

**Don't forget to update configuration file to suit your environment** `vendor/johnwilson/jsondb/config/config.php`

## Call autoloader

```PHP
require_once __DIR__ . '/vendor/autoload.php';
```

## Require the bootstrap file

```PHP
require_once __DIR__ . '/vendor/johnwilson/jsondb/jsondb.php';
```

## Import the relevant namespaces as required:

```PHP
use IBT\JsonDB\Client;
use IBT\JsonDB\Collection;
```

[Continue to Tutorial](https://github.com/johnwilson/jsondb/blob/master/docs/tutorial.md) or [Back to Table of Contents](https://github.com/johnwilson/jsondb/blob/master/docs/index.md)