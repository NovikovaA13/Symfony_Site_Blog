<?php

namespace App\Application\Command;

use Doctrine\ORM\EntityManagerInterface;

class UserManager
{
    /**
     * @var EntityManagerInterface;
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    public function recordEvent(string $username, string $data)
    {
        $sql = 'INSERT INTO events (username, data, isRead)
                VALUES (:username, :data, 0)';

        $this->em->getConnection()
             ->prepare($sql)
             ->execute([
                 'username' => $username,
                 'data' => $data
             ]);

    }
}