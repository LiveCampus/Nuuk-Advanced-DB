<?php

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;

class OwnerRepository
{
    public function __construct(private readonly EntityManagerInterface $manager) {}

    public function getIdByName(string $name): ?int
    {
        return $this->manager
            ->createQuery("SELECT id FROM Owner WHERE name = :name")
            ->setParameter("name", $name)
            ->getFirstResult()
        ;
    }

    public function createOwnerWithFirstTamagotchi(string $ownerName, string $firstTamagotchi): int
    {
        $this->manager
            ->createQuery("INSERT INTO Owner VALUES (NULL, :name)")
            ->setParameter('name', $ownerName)
            ->execute()
        ;

        $ownerId = $this->manager->getConnection()->lastInsertId();

        $this->manager
            ->createQuery("INSERT INTO Tamagotchi (owner_id, name, first) VALUES (:owner, :name, true)")
            ->setParameters(['owner' => $ownerId, 'name' => $firstTamagotchi])
            ->execute()
        ;

        return $ownerId;
    }
}
