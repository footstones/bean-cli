<?php
namespace Footstones\BeanCLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

use Beanstalk\Client;

class TubeEmptyCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('tube:empty')
            ->setDescription('Tube Stats.')
            ->addArgument(
                'tube',
                InputArgument::REQUIRED,
                'Tube name.'
            );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tube = $input->getArgument('tube');

        $client = new Client();
        $client->connect();

        $stats = $client->statsTube($tube);

        $stats = array(
            'current-jobs-ready' => $stats['current-jobs-ready'],
            'current-jobs-reserved' => $stats['current-jobs-reserved'],
            'current-jobs-delayed' => $stats['current-jobs-delayed'],
            'current-jobs-buried' => $stats['current-jobs-buried'],
        );

        echo json_encode($stats, JSON_PRETTY_PRINT) . "\n";

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion("<question>Are you real empty tube `{$tube}` jobs?<question> ", false);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $client->useTube($tube);

        while ($job = $client->peekReady()) {
            $client->delete($job['id']);
        }

        while ($job = $client->peekDelayed()) {
            $client->delete($job['id']);
        }

        while ($job = $client->peekBuried()) {
            $client->delete($job['id']);
        }

    }

}