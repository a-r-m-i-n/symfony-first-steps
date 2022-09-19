<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:make-user',
    description: 'Creates a new user',
)]
class MakeUserCommand extends Command
{
    private UserPasswordHasherInterface $passwordHasher;
    private UserRepository $userRepository;

    public function __construct(UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository, string $name = null)
    {
        parent::__construct($name);
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email address and username for new user')
            ->addOption('roles', null, InputOption::VALUE_OPTIONAL, 'Roles to get assigned to new user', ['ROLE_USER'])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Check for existing user with given email (which is not allowed)
        if ($this->userRepository->findOneBy(['email' => $email = $input->getArgument('email')])) {
            throw new \RuntimeException('User with email "' . $email  . '" already exists. Aborting.');
        }

        // Ask for new password
        $plainPassword = $io->askHidden('Please enter new password', function(string $value): string {
            if (empty($value) || strlen($value) < 4) {
                throw new \InvalidArgumentException('Please enter a password with at least 4 chars length.');
            }
            return $value;
        });

        // Create the new user instance
        $newUser = new User();
        $newUser->setEmail($email);
        $newUser->setRoles($input->getOption('roles'));
        $newUser->setPassword($this->passwordHasher->hashPassword($newUser, $plainPassword));

        // Save new user in DB
        $this->userRepository->add($newUser, true);

        $io->success(sprintf('New user "%s" with ID %d has been successfully created.', $email, $newUser->getId()));

        return Command::SUCCESS;
    }
}
