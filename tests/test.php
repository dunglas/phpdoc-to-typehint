<?php

#!/usr/bin/env php

/*
 * This file is part of the PHPDoc to Type Hint package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

class BazTrait
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

echo 'Good job! Everything is fine.'.PHP_EOL;
