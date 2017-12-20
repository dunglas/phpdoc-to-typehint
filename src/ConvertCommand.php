<?php

/*
 * This file is part of the PHPDoc to Type Hint package.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare (strict_types = 1);

namespace Dunglas\PhpDocToTypeHint;

use phpDocumentor\Reflection\Php\ProjectFactory;
use SebastianBergmann\Diff\Differ;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
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
     * @var Differ
     */
    private $differ;

    /**
     * {@inheritdoc}
     */
    public function __construct($name = null)
    {
        $this->differ = new Differ();

        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('convert')
            ->setDescription('Convert files')
            ->addOption('exclude', 'e', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Directories to exclude', ['vendor'])
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Displays diff instead of modifying files')
            ->addOption('no-nullable-types', null, InputOption::VALUE_NONE, 'Uses a default `null` value instead of PHP 7.1 nullable types')
            ->addArgument('input', InputArgument::OPTIONAL | InputArgument::IS_ARRAY, 'Input directories', ['.'])
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

        $output->writeln('<comment>Running the PHPDoc to Type Hint converter. Brought to you by Kévin Dunglas and Les-Tilleuls.coop.</comment>');
        $output->writeln('');

        $progress = new ProgressBar($output, count($files));

        $changed = [];
        foreach ($project->getFiles() as $file) {
            $old = $file->getSource();
            $new = $converter->convert($project, $file, !$input->getOption('no-nullable-types'));

            if ($new !== $old) {
                if ($input->getOption('dry-run')) {
                    $changed[] = ['path' => $file->getPath(), 'diff' => $this->differ->diff($old, $new)];
                } else {
                    file_put_contents($file->getPath(), $new);
                }
            }

            $progress->advance();
        }

        $progress->finish();

        $output->writeln('');
        $output->writeln('');

        foreach ($changed as $i => $file) {
            $output->writeln(sprintf('<fg=blue>%d) %s</>', $i + 1, $file['path']));
            $output->writeln('');
            $output->writeln($file['diff']);
            $output->writeln('');
        }

        $output->writeln('<info>Conversion done.</info>');
    }
}
