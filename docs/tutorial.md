# Quick tutorial

This tutorial will get you up and running with JsonDB by performing basic CRUD (Create, Read, Update and Delete) operations. For further usage guidelines you can have a look at the code in the `tests` directory.

## Preparation

If you've setup your project according to the guidelines in the [getting started](https://github.com/johnwilson/jsondb/blob/master/docs/getting_started.md) section, you should be ready to follow this tutorial.

The following code will get you ready to run your CRUD operations

```PHP
<?php

// Autoload
require_once __DIR__ . '/vendor/autoload.php';

// import namespace
use IBT\JsonDB\Client;
use IBT\JsonDB\Collection;

// create a new db client
$c = new Client([
    'database' => 'database',
    'username' => 'username',
    'password' => 'password'
]);

// setup
$c->setup();
```

## Creating a document

JSON documents reside in **Collections** which can be thought of as tables. Their main purpose is to keep your documents grouped in any logical manner you see fit.

```PHP
// create collection
$col = $c->newCollection("users");
```

Each JSON document has a special `_id` field which you can add yourself or let the library create it for you. the `_id` is an alpha-numeric only string.

```PHP
// insert json
$j = '{"name":"jason bourne", "category":"agent", "_id": "1"}';
$oid = $col->insert($j);
```

The value returned on insert will be either the library generated `_id` which will be similar to `oid57b57b407fe93` or the value you supplied in the `_id` field of your JSON document.

## Reading a document

Retrieving a document for which you have the `_id` is as simple as running the following which will return your document as a PHP `object`:

```PHP
$obj = $col->get($oid);
```

## Updating a document

You can modify your JSON document and save it to the database just as simply as doing the following:

```PHP
$obj->category = "ghost";
$col->update(json_encode($obj));
```

A boolean is returned to indicate whether the operation was successful.

Care should be taken not to change or remove the `_id` field or the library will assume the document is new and create a new entry.

## Deleting a document

Finally to delete the document you'd do the following:

```PHP
$col->delete($oid);
```

Again a boolean value will indicate the success or failure of the operation.

[Back to Table of Contents](https://github.com/johnwilson/jsondb/blob/master/docs/index.md)

