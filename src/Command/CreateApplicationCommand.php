<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Application;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Uid\Uuid;

class CreateApplicationCommand extends Command
{
    protected static $defaultName = 'app:create-app';

    /**
     * @var ManagerRegistry
     */
    private $managerRepository;

    public function __construct(ManagerRegistry $managerRegistry) {
        $this->managerRepository = $managerRegistry;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Application name');
        $this->addArgument('url', InputArgument::REQUIRED, 'Application URL');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $url = $input->getArgument('url');
        $token = Uuid::v4();

        $output->writeln([
            'Application Creator',
            '============',
            '',
        ]);

        $entityManager = $this->managerRepository->getManager();

        $application = new Application();
        $application->setName($name);
        $application->setUrl($url);
        $application->setToken((string) $token);

        $entityManager->persist($application);
        $entityManager->flush();

        $output->writeln([
            'Your API token is: ' . $token,
        ]);

        return Command::SUCCESS;
    }
}
