<?php

namespace App\Service;

use App\Entity\Tamagotchi;
use App\Entity\User;
use App\Repository\OwnerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class UserService
{
    public function __construct(
        private readonly OwnerRepository        $userRepository,
        private readonly EntityManagerInterface $manager
    ) {}

    /**
     * Retourne l'utilisateur possédant le nom donné en paramètre
     * Si aucun n'existe, retourne null
     *
     * @param string $name
     * @return User|null
     */
    public function existingUser(string $name): ?User
    {
        return $this->userRepository->findOneBy(['name' => $name]);
    }

    /**
     * Créer un utilisateur avec son premier tamagotchi avec les noms donnés
     * Retourne l'ID de l'utilisateur crée
     *
     * @param string $username
     * @param string $firstTamagotchi
     * @return Uuid
     */
    public function createUser(string $username, string $firstTamagotchi): Uuid
    {
        $user = new User();
        $user->setName($username);

        $tamagotchi = new Tamagotchi();
        $tamagotchi
            ->setName($firstTamagotchi)
            ->setOwner($user)
            ->setFirst(true)
        ;

        $this->manager->persist($user);
        $this->manager->persist($tamagotchi);
        $this->manager->flush();

        return $user->getId();
    }
}