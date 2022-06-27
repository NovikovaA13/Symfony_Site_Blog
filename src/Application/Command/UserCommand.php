<?php

namespace App\Application\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Application\Command\UserManager;


class UserCommand extends Command
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        parent::__construct();
        $this->userManager = $userManager;
    }

    protected function configure()
    {
        $this->setName('user:create')
             ->setDescription('Crete a test user');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->userManager->recordEvent('Event', 'is executed');
    }

}