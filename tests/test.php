#!/usr/bin/env php
<?php

/*
 * This file is part of the PHPDoc to Type Hint package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

// Cannot use PHPUnit right now because of this bug: https://github.com/phpDocumentor/Reflection/issues/85

require __DIR__.'/../vendor/autoload.php';

use phpDocumentor\Reflection\Php\ProjectFactory;
use Dunglas\PhpDocToTypeHint\Converter;

function same($value1, $value2)
{
    if ($value1 !== $value2) {
        throw new \RuntimeException('Values are not the same.');
    }
}

$converter = new Converter();

$projectFactory = ProjectFactory::createInstance();
$project = $projectFactory->create('functions1', [__DIR__.'/Fixtures/functions1.php']);

foreach ($project->getFiles() as $file) {
    same(<<<'PHP'
<?php

/**
 * @var string
 */
$foo = 'bar';

/**
 * Must not be modified.
 *
 * @return string|null
 */
function foo()
{
}

/**
 * Must be converted.
 *
 * @param int|null $c
 * @param string   $d
 *
 * @return float
 */
function bar(\DateTime $a = null, array $b, int $c = null, string $d, bool $e, callable $f = null): float
{
    return 0.0;
}

/**
 * Must not be modified (no params defined).
 */
function baz($a)
{
}

/**
 * Must not be converted (already using type hints).
 *
 * @param int $a
 *
 * @return string
 */
function bat(int $a): string
{
}

PHP
    , $converter->convert($project, $file));
}

$projectFactory = ProjectFactory::createInstance();
$project = $projectFactory->create('functions2', [__DIR__.'/Fixtures/functions2.php']);

foreach ($project->getFiles() as $file) {
    same(<<<'PHP'
<?php

namespace foo;

/**
 * @param string|null $a
 *
 * @return int
 */
function test(string $a = null): int
{
}

PHP
    , $converter->convert($project, $file));
}

$projectFactory = ProjectFactory::createInstance();
$project = $projectFactory->create('classFoo', [__DIR__.'/Fixtures/Foo.php']);

foreach ($project->getFiles() as $file) {
    same(<<<'PHP'
<?php

namespace bar;

/**
 * Foo.
 */
class Foo
{
    public function bar($foo)
    {
    }

    /**
     * @param float $a
     */
    public function test(float $a)
    {
        $closure = function ($a, $c) {
        };
    }
}

PHP
    , $converter->convert($project, $file));
}

$projectFactory = ProjectFactory::createInstance();
$project = $projectFactory->create('interfaceBar', [__DIR__.'/Fixtures/BarInterface.php']);

foreach ($project->getFiles() as $file) {
    same(<<<'PHP'
<?php

namespace bar;

interface BarInterface
{
    /**
     * @param array $a
     * @param int   $b
     *
     * @return float
     */
    public function baz(array $a, int $b): float;
}

PHP
    , $converter->convert($project, $file));
}

$projectFactory = ProjectFactory::createInstance();
$project = $projectFactory->create('traitBaz', [__DIR__.'/Fixtures/BazTrait.php']);

foreach ($project->getFiles() as $file) {
    same(<<<'PHP'
<?php

namespace bar;

trait BazTrait
{
    /**
     * @param int $a
     *
     * @return \DateTime
     */
    protected function inTrait(int $a): \DateTime
    {
    }
}

PHP
    , $converter->convert($project, $file));
}

$projectFactory = ProjectFactory::createInstance();
$childPath = __DIR__.'/Fixtures/Child.php';
$project = $projectFactory->create('inheritance', [$childPath, __DIR__.'/Fixtures/Foo.php', __DIR__.'/Fixtures/BarInterface.php']);

foreach ($project->getFiles() as $path => $file) {
    if ($childPath === $path) {
        same(<<<'PHP'
<?php

namespace bar;

class Child extends Foo implements BarInterface
{
    use BazTrait;

    public function test(float $a)
    {
        parent::test($a);
    }

    /**
     * {@inheritdoc}
     */
    public function baz(array $a, int $b): float
    {
    }
}

PHP
        , $converter->convert($project, $file));
    }
}

$projectFactory = ProjectFactory::createInstance();
$project = $projectFactory->create('paramNoType', [__DIR__.'/Fixtures/param_no_type.php']);

foreach ($project->getFiles() as $path => $file) {
    same(<<<'PHP'
<?php
/**
 * @param $noType
 */
function param_no_type($noType)
{
}

PHP
        , $converter->convert($project, $file));
}

echo 'Good job! Everything is fine.'.PHP_EOL;
