<?php

namespace Tg\Bundle\FixtureBundle\Command;

use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Tg\Bundle\FixtureBundle\Event\LoadFixtureFileEvent;
use Tg\Bundle\FixtureBundle\Event\LoadFixturesEvent;
use Tg\Bundle\FixtureBundle\Event\PostLoadFixturesEvent;
use Tg\Bundle\FixtureBundle\Event\PrepareLoadFixturesEvent;
use Tg\Bundle\FixtureBundle\FixtureContext;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Escaper;
use Symfony\Component\Yaml\Parser;

class LoadDataFixturesCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('fixtures:load')
            ->setDescription('Load data fixtures to your database.')
            ->addOption(
                'environment',
                null,
                InputOption::VALUE_REQUIRED,
                'Please Enter for which Environment Fixtures should be loaded ("dev", "test" or "prod")',
                'dev'
            )->addOption(
                'f',
                null,
                InputOption::VALUE_NONE,
                'If set no Warning will be displayed before purging Database'
            );
    }

    private function getFixtureFiles()
    {
        $paths = array();
        foreach ($this->getContainer()->get('kernel')->getBundles() as $bundle) {
            if (!is_dir($bundle->getPath() . '/Fixture')) {
                continue;
            }

            $paths[] = $bundle->getPath() . '/Fixture';
        }


        if (empty($paths)) {
            return [];
        }

        return iterator_to_array(Finder::create()->name('*.yml.twig')->in($paths));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $environment = $input->getOption('environment');

        if ($input->getOption('f')) {
            $input->setInteractive(false);
        }

        if ($input->isInteractive()) {
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion('<question>Careful, everything will be purged. Do you want to continue Y/N ?</question>', false);

            if (!$helper->ask($input, $output, $question)) {
                return;
            }
        }

        $context = new FixtureContext($output, $environment);

        //$em->getConnection()->exec('SET foreign_key_checks=1;');

        $dispatcher = $this->getContainer()->get('event_dispatcher');


        $dispatcher->dispatch(PrepareLoadFixturesEvent::class, new PrepareLoadFixturesEvent($context));
        $dispatcher->dispatch(LoadFixturesEvent::class, new LoadFixturesEvent($context));

        foreach ($this->getFixtureFiles() as $file) {
            try {
                try {
                    $parsedFile = $this->getParsedYml($file);
                } catch (\Twig_Error_Syntax $e) {
                    $output->writeln("<error>Twig Syntax error on </error>$file\n{$e->getMessage()}");
                    throw new \LogicException();
                }

                $dispatcher->dispatch(LoadFixtureFileEvent::class, new LoadFixtureFileEvent($context, $file, $parsedFile));
            } catch (\Twig_Error_Syntax $e) {
                $output->writeln("<error>Twig Syntax error on </error>$file\n{$e->getMessage()}");
                throw new \LogicException();
            }
        }

        $dispatcher->dispatch(PostLoadFixturesEvent::class, new PostLoadFixturesEvent($context));

        //$em->getConnection()->exec('FLUSH TABLES;');
    }

    private function getParsedYml($file)
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Array(['base' => file_get_contents($file)]), [
            'autoescape' => 'yaml'
        ]);

        /** @var $twigCoreExtension \Twig_Extension_Core */
        $twigCoreExtension = $twig->getExtension('Twig_Extension_Core');
        $twigCoreExtension->setEscaper('yaml', function($e, $v) {
           return Escaper::escapeWithDoubleQuotes($v);
        });


        $renderedYml = $twig->render('base', [
            '__FILE__' => $file,
            '__DIR__' => dirname($file),
            'faker' => Factory::create()
        ]);

        return (new Parser())->parse($renderedYml);
    }

}