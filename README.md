# PHPDoc to Type Hint

**Archived!** This repository is now archived. Consider using [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) (and espacially the 
`phpdoc_to_param_type` and `phpdoc_to_return_type` rules) or [Rector](https://tomasvotruba.com/blog/2018/11/15/how-to-get-php-74-typed-properties-to-your-code-in-few-seconds/) instead.

`phpdoc-to-typehint` adds automatically scalar type hints and return types to all functions and methods of a PHP project
using existing PHPDoc annotations.

[![Build Status](https://travis-ci.org/dunglas/phpdoc-to-typehint.svg?branch=master)](https://travis-ci.org/dunglas/phpdoc-to-typehint)

**Warning**: this project is an early stage of development. It **can** damage your code.
Be sure to make a backup before running this command and to run your test suite after.

Please [report](https://github.com/dunglas/phpdoc-to-typehint/issues) any bug you find using this tool.

## Install and usage

1. [Download the latest PHAR file](https://github.com/dunglas/phpdoc-to-typehint/releases)
2. Run `php phpdoc-to-typehint.phar <your-project-directory>`

Your project should have scalar type hints and return type declarations.

Before:

```php
<?php

/**
 * @param int|null $a
 * @param string   $b
 *
 * @return float
 */
function bar($a, $b, bool $c, callable $d = null)
{
    return 0.0;
}
```

After:

```php
<?php

/**
 * @param int|null $a
 * @param string   $b
 *
 * @return float
 */
function bar(int $a = null, string $b, bool $c, callable $d = null) : float
{
    return 0.0;
}
```

## Features

Supports:

* functions
* methods of classes and traits
* method definitions in interfaces
* PHPDoc inheritance
* PHP 7.1 nullable types (can be disabled with `--no-nullable-types` option)

## Credits

Created by [Kévin Dunglas](https://dunglas.fr). Sponsored by [Les-Tilleuls.coop](https://les-tilleuls.coop).
