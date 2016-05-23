# PHPDoc to Type Hint

`phpdoc-to-typehint` adds automatically scalar type hints and return types to all functions and methods of a PHP project
using existing PHPDoc annotations.

[![Build Status](https://travis-ci.org/dunglas/phpdoc-to-typehint.svg?branch=master)](https://travis-ci.org/dunglas/phpdoc-to-typehint)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dunglas/phpdoc-to-typehint/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/dunglas/phpdoc-to-typehint/?branch=master)

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
 * @param bool     $c
 * @param callable $d
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
 * @param bool     $c
 * @param callable $d
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

## Credits

Created by [Kévin Dunglas](https://dunglas.fr). Sponsored by [Les-Tilleuls.coop](https://les-tilleuls.coop).
