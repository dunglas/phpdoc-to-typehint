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

namespace Dunglas\PhpDocToTypeHint;

use phpDocumentor\Reflection\Php\ProjectFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * The convert command.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class ConvertCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('convert')
            ->setDescription('Convert files')
            ->addOption('exclude', 'e', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Directories to exclude.', ['vendor'])
            ->addArgument('input', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Input directories.', ['.'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = (new Finder())
            ->in($input->getArgument('input'))
            ->exclude($input->getOption('exclude'))
            ->name('*.php')
            ->files()
        ;

        $files = [];
        foreach ($finder as $file) {
            $files[] = $file->getRealpath();
        }

        $project = ProjectFactory::createInstance()->create('current', $files);
        $converter = new Converter();

        foreach ($project->getFiles() as $file) {
            file_put_contents($file->getPath(), $converter->convert($project, $file));
        }
    }
}
