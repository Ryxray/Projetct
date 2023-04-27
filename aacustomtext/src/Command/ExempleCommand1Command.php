<?php

declare(strict_types=1);

namespace Aality\CustomText\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


class ExempleCommand1Command extends Command
{
    /** @var $io SymfonyStyle */
    private $io = null;
    private $verbose = false;
    private $doSomething = false;
    private $somethingElse = false;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('aality:exemple-command-1')
            ->setDescription('Exemple command with arguments')
            ->addOption('do-something', false, InputOption::VALUE_NONE, 'If you add --do-something to the command, then this will have true value')
            ->addOption('something-else', false, InputOption::VALUE_NONE, 'If you don\'t add --something-else, then this is false')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            # Generic option
            $this->verbose = $input->getOption('verbose');
            $this->doSomething = $input->getOption('do-something');
            $this->somethingElse = $input->getOption('something-else');
            # Handle style
            $this->io = new SymfonyStyle($input, $output);
            $this->io->title('Aality : Exemple command');

            $this->io->text('Display some text');

            $count = 10;
            # Set a progress bar
            $this->io->progressStart($count);
            for ($i = 0; $i < $count; $i++) {
                $this->io->progressAdvance();

                if ($this->verbose) {
                    $this->io->newLine();
                }
            }
        } catch (\Exception $e) {
            $this->io->error('Exception : ' . $e->getMessage());
        }

        # The end
        $this->io->success('Finished.');

        return 0;
    }

}
