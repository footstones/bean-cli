<?php
namespace Footstones\BeanCLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Beanstalk\Client;

class TubeStatsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('tube:stats')
            ->setDescription('Tube Stats.')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Tube name.'
            );
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        $client = new Client();
        $client->connect();

        $result = $client->statsTube($name);

        $result = json_encode($result, JSON_PRETTY_PRINT);

        echo $result;
    }

}