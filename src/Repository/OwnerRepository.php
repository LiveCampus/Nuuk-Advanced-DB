<?php

namespace App\Repository;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

class OwnerRepository
{
    public function __construct(private readonly EntityManagerInterface $manager) {}

    /**
     * @throws Exception
     */
    public function getIdByName(string $name): ?int
    {
        return $this->manager->getConnection()
            ->prepare("SELECT id FROM Owner WHERE name = :name")
            ->executeQuery(['name' => $name])
            ->fetchOne()
        ;
    }

    /**
     * @throws Exception
     */
    public function createOwnerWithFirstTamagotchi(string $ownerName, string $firstTamagotchi): int
    {
        $this->manager->getConnection()
            ->prepare("INSERT INTO Owner VALUES (NULL, :name)")
            ->executeQuery(['name' => $ownerName])
        ;

        $ownerId = $this->manager->getConnection()->lastInsertId();

        $this->manager->getConnection()
            ->prepare("INSERT INTO Tamagotchi (owner_id, name, first) VALUES (:owner, :name, true)")
            ->executeQuery(['owner' => $ownerId, 'name' => $firstTamagotchi])
        ;

        return $ownerId;
    }
}
