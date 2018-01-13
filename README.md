# Conduit - EVE Online ESI Client

## Requirements
PHP 7.1 or later

## Installation

Use composer to install:

```
composer require nullx27/conduit
```

## Getting started

Conduit is a easy to use minimalistic ESI APi client.

```php
<?php
   
require_once(__DIR__ . '/vendor/autoload.php');

$api = new \Conduit\Conduit();

$api->alliances(434243723)->get();

```

Authenticated ESI calls

```php
<?php
   
require_once(__DIR__ . '/vendor/autoload.php');

$clientId = 'your-client-id';
$clientSecret = 'your-client-secrete';
$refreshToken = 'user-refresh-token';

$auth = new \Conduit\Authentication($clientId, $clientSecret, $refreshToken);
$api = new \Conduit\Conduit($auth);

```

Conduit requests its own access tokens and renews them when they expire.

## Configuration

Conduit can use any PSR-6 compatible caching library to stay in line with CCP request guidelines

```php
<?php

$api = new \Conduit\Conduit();
$api->getConfiguration()->setCache($yourCacheInstance);
```

You can set a different datasource for your esi requests. The default is 'tranquility'.

```php
<?php

$api = new \Conduit\Conduit();
$api->getConfiguration()->setDatasource('singularity');
```

## Bug repots

Please use [Github Issues](https://github.com/nullx27/conduit/issues) for bug reports.
