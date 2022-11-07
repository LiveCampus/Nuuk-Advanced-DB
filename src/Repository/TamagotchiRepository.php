<?php

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;


class TamagotchiRepository
{
    public function __construct(private readonly EntityManagerInterface $manager) {}

    public function findFirstTamagotchiByName(int $ownerId, string $tamagotchiName): ?int
    {
        return $this->manager
            ->createQuery("SELECT id FROM Tamagotchi WHERE owner_id = :owner AND name = :tamagotchi AND alive = 1")
            ->setParameters(['owner' => $ownerId, 'name' => $tamagotchiName])
            ->getFirstResult()
        ;
    }

    public function findAliveTamagotchis(int $ownerId): array
    {
        return $this->manager
            ->createQuery("SELECT id, name, first, hunger, thirst, sleep, boredom FROM Tamagotchi WHERE owner_id = :owner AND alive = 1")
            ->setParameter('owner', $ownerId)
            ->getArrayResult()
        ;
    }

    public function findDeadTamagotchis(int $ownerId): array
    {
        return $this->manager
            ->createQuery("SELECT id, name, created_at, level FROM Tamagotchi WHERE owner_id = :owner AND alive = 0")
            ->setParameter('owner', $ownerId)
            ->getArrayResult()
            ;
    }
}
