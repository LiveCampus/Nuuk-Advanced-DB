<?php

namespace App\Service;

use App\Entity\Owner;
use App\Entity\Tamagotchi;
use App\Repository\OwnerRepository;
use Doctrine\ORM\EntityManagerInterface;

class OwnerService
{
    public function __construct(
        private readonly OwnerRepository        $ownerRepository,
        private readonly EntityManagerInterface $manager
    ) {}

    /**
     * Retourne l'utilisateur possédant le nom donné en paramètre
     * Si aucun n'existe, retourne null
     *
     * @param string $name
     * @return Owner|null
     */
    public function existingUser(string $name): ?Owner
    {
        return $this->ownerRepository->findOneBy(['name' => $name]);
    }

    /**
     * Créer un utilisateur avec son premier tamagotchi avec les noms donnés
     * Retourne l'ID de l'utilisateur crée
     *
     * @param string $username
     * @param string $firstTamagotchi
     * @return int
     */
    public function createUser(string $username, string $firstTamagotchi): int
    {
        $owner = new Owner();
        $owner->setName($username);

        $tamagotchi = new Tamagotchi();
        $tamagotchi
            ->setName($firstTamagotchi)
            ->setOwner($owner)
            ->setFirst(true)
        ;

        $this->manager->persist($owner);
        $this->manager->persist($tamagotchi);
        $this->manager->flush();

        return $owner->getId();
    }
}