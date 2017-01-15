# JsonDB Library

[![JsonDB](https://github.com/johnwilson/jsondb/raw/master/jsondb.png)](#JsonDB)

## About

JSONDb is a PHP database abstraction library for MySQL which allows you to easily store and query JSON data. Because it doesn't require the Json Data type available in Mysql version 5.7, this means JSONDb can be used in hosted environments that offer older versions of Mysql for example.

**JSONDb is primarily designed to be used for rapid application prototyping and is not advisable for use in production.**

## Quick Start

```PHP

// Autoload
require_once __DIR__ . '/vendor/autoload.php';

// import namespace
use IBT\JsonDB\Client;
use IBT\JsonDB\Collection;

// create client and initialize database
$c = new Client([
    'database' => 'database',
    'username' => 'username',
    'password' => 'password'
]);
$c->setup();

// create collection
$col = $c->newCollection("users");

// insert json
$col->insert('{"name":"jason bourne", "category":"agent"}');
$col->insert('{"name":"james bond", "category":"agent"}');
$col->insert('{"name":"mathew murdock", "category":"superhero"}');

// search data
$f = $col->newFilter();
$r = $f->whereIn('category', ['agent'])->run();
var_dump($r)
```

## Learn More

* View [documentation](https://github.com/johnwilson/jsondb/blob/master/docs/index.md) for additional information and tips.