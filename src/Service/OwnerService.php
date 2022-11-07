<?php

namespace App\Service;

use App\Entity\Owner;
use App\Entity\Tamagotchi;
use App\Repository\OwnerRepository;
use App\Repository\TamagotchiRepository;
use Doctrine\ORM\EntityManagerInterface;

class OwnerService
{
    public function __construct(
        private readonly OwnerRepository        $ownerRepository,
        private readonly TamagotchiRepository   $tamagotchiRepository,
        private readonly EntityManagerInterface $manager
    ) {}

    /**
     * Retourne l'ID du propriétaire possédant le nom donné en paramètre
     * Si aucun n'existe, retourne null
     *
     * @param string $name
     * @return int|null
     */
    public function existingOwner(string $name): ?int
    {
        return $this->ownerRepository->getIdByName($name);
    }

    /**
     * Retourne vrai si le nom du tamagotchi donné du propriétaire donnée est son premier
     * Sinon retourne faux
     *
     * @param int $ownerId
     * @param string $tamagotchiName
     * @return bool
     */
    public function goodFirstTamagotchi(int $ownerId, string $tamagotchiName): bool
    {
        return (bool)$this->tamagotchiRepository->findFirstTamagotchiByName($ownerId, $tamagotchiName);
    }

    /**
     * Créer un propriétaire avec son premier tamagotchi avec les noms donnés
     * Retourne l'ID du propriétaire crée
     *
     * @param string $ownerName
     * @param string $firstTamagotchi
     * @return int
     */
    public function createOwner(string $ownerName, string $firstTamagotchi): int
    {
        return $this->ownerRepository->createOwnerWithFirstTamagotchi($ownerName, $firstTamagotchi);
    }
}