<?php

namespace App\Command;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-oauth-client',
    description: 'Creates a new OAuth2 client',
)]
class CreateOAuthClientCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('clientId', InputArgument::REQUIRED, 'Client ID')
            ->addArgument('redirectUri', InputArgument::REQUIRED, 'Redirect URI')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $client = new Client();
        $client->setClientId($input->getArgument('clientId'));
        $client->setRedirectUris([$input->getArgument('redirectUri')]);
        $client->setScopes(['email', 'profile']);

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $io->success(sprintf('OAuth2 client "%s" created successfully.', $client->getClientId()));

        return Command::SUCCESS;
    }
}
