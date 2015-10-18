<?php
namespace Footstones\BeanCLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Beanstalk\Client;

class JobPutCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('job:put')
            ->setDescription('Put a job to tube.')
            ->addArgument(
                'tube',
                InputArgument::REQUIRED,
                'Tube name.'
            )->addArgument(
                'job',
                InputArgument::REQUIRED,
                'Job content.'
            );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tube = $input->getArgument('tube');
        $job = $input->getArgument('job');

        $client = new Client();
        $client->connect();
        $client->useTube($tube);

        $id = $client->put(1000, 0, 60, $job);

        $result = array('id' => $id);

        $result = json_encode($result, JSON_PRETTY_PRINT);

        echo $result;
    }

}