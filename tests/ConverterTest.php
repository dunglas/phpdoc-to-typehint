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

namespace Dunglas\PhpDocToTypeHint\Tests;

use phpDocumentor\Reflection\Php\ProjectFactory;
use Dunglas\PhpDocToTypeHint\Converter;
use PHPUnit\Framework\TestCase;

/**
 * @covers Converter
 */
final class ConverterTest extends TestCase
{
    /**
     * @var Converter
     */
    private static $converter;

    public static function setUpBeforeClass()
    {
        self::$converter = new Converter();
    }

    /**
     * @dataProvider filesProvider
     */
    public function testSingleFiles(string $projectName, string $fileName)
    {
        $projectFactory = ProjectFactory::createInstance();
        $project = $projectFactory->create(
            $projectName,
            [__DIR__.'/Fixtures/'.$fileName]
        );

        foreach ($project->getFiles() as $file) {
            $this->assertStringEqualsFile(
                __DIR__.'/Results/'.$fileName,
                self::$converter->convert($project, $file)
            );
        }
    }

    public function testInheritance()
    {
        $projectFactory = ProjectFactory::createInstance();
        $childPath = __DIR__.'/Fixtures/Child.php';
        $project = $projectFactory->create(
            'inheritance',
            [
                $childPath,
                __DIR__.'/Fixtures/Foo.php',
                __DIR__.'/Fixtures/BarInterface.php'
            ]
        );

        foreach ($project->getFiles() as $path => $file) {
            if ($childPath === $path) {
                $this->assertStringEqualsFile(
                    __DIR__.'/Results/Child.php',
                    self::$converter->convert($project, $file)
                );
            }
        }
    }

    /**
     * @return string[][]
     */
    public function filesProvider(): array
    {
        return [
            ['functions1', 'functions1.php'],
            ['functions2', 'functions2.php'],
            ['classFoo', 'Foo.php'],
            ['interfaceBar', 'BarInterface.php'],
            ['traitBaz', 'BazTrait.php'],
            ['paramNoType', 'param_no_type.php'],
            ['arrayNoTypes', 'array_no_types.php'],
            ['typeAliasesWhitelisting', 'type_aliases_and_whitelisting.php'],
        ];
    }
}
