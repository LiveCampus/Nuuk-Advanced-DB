<?php

namespace App\Repository;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;


class TamagotchiRepository
{
    public function __construct(private readonly EntityManagerInterface $manager) {}

    /**
     * @throws Exception
     */
    public function findFirstTamagotchiByName(int $ownerId, string $tamagotchiName): ?int
    {
        return $this->manager->getConnection()
            ->prepare("SELECT id FROM Tamagotchi WHERE owner_id = :owner AND name = :tamagotchi AND first = 1")
            ->executeQuery(['owner' => $ownerId, 'tamagotchi' => $tamagotchiName])
            ->fetchOne()
        ;
    }

    /**
     * @throws Exception
     */
    public function findOneById(int $id, int $ownerId): array
    {
        return $this->manager->getConnection()
            ->prepare("SELECT * FROM Tamagotchi WHERE id = :id AND owner_id = :owner")
            ->executeQuery(['id' => $id, 'owner' => $ownerId])
            ->fetchAssociative()
        ;
    }

    /**
     * @throws Exception
     */
    public function findAliveTamagotchis(int $ownerId): array
    {
        return $this->manager->getConnection()
            ->prepare("SELECT id, name, first, hunger, thirst, sleep, boredom FROM Tamagotchi WHERE owner_id = :owner AND alive = 1")
            ->executeQuery(['owner' => $ownerId])
            ->fetchAllAssociative()
        ;
    }

    /**
     * @throws Exception
     */
    public function findDeadTamagotchis(int $ownerId): array
    {
        return $this->manager->getConnection()
            ->prepare("SELECT id, name, created_at, died_on, level, first FROM Tamagotchi WHERE owner_id = :owner AND alive = 0")
            ->executeQuery(['owner' => $ownerId])
            ->fetchAllAssociative()
        ;
    }

    /**
     * @throws Exception
     */
    public function createTamagotchi(int $ownerId, string $name): void
    {
      $this->manager->getConnection()
        ->prepare("CALL CREATE_TAMAGOCHI(:name ,:owner)")
        ->executeQuery(["name" => $name, "owner" => $ownerId])
      ;
    }

    /**
     * @throws Exception
     */
    public function updateTamagotchi(int $id, string $name, int $ownerId): void
    {
        $this->manager->getConnection()
            ->prepare("UPDATE Tamagotchi SET name = :name WHERE id = :id AND owner_id = :owner")
            ->executeQuery(["name" => $name, "id" => $id, 'owner' => $ownerId])
        ;
    }

    /**
     * @throws Exception
     */
    public function removeTamagotchi(int $id, int $ownerId): void
    {
        $this->manager->getConnection()
            ->prepare("DELETE FROM Tamagotchi WHERE id = :id AND owner_id = :owner")
            ->executeQuery(["id" => $id, 'owner' => $ownerId])
        ;
    }

    /**
     * @throws Exception
     */
    public function killTamagotchi(int $id, int $ownerId): void
    {
        $this->manager->getConnection()
            ->prepare("UPDATE Tamagotchi SET alive = false, died_on = CURRENT_TIMESTAMP WHERE id = :id AND owner_id = :owner")
            ->executeQuery(["id" => $id, 'owner' => $ownerId])
        ;
    }

    /**
     * @throws Exception
     */
    public function eat(int $id, int $ownerId): bool
    {
        $tamagotchi = $this->findOneById($id, $ownerId);

        if ($tamagotchi["hunger"] < 80) {
            $this->manager->getConnection()
                ->prepare("CALL eat(:id)")
                ->executeQuery(["id" => $id])
            ;
            $this->addAction($id, $ownerId);

            return true;
        }

        return false;
    }

    /**
     * @throws Exception
     */
    public function drink(int $id, int $ownerId): bool
    {
        $tamagotchi = $this->findOneById($id, $ownerId);

        if ($tamagotchi["thirst"] < 80) {
            $this->manager->getConnection()
                ->prepare("CALL drink(:id)")
                ->executeQuery(["id" => $id])
            ;
            $this->addAction($id, $ownerId);

            return true;
        }

        return false;
    }

    /**
     * @throws Exception
     */
    public function sleep(int $id, int $ownerId): bool
    {
        $tamagotchi = $this->findOneById($id, $ownerId);

        if ($tamagotchi["sleep"] < 80) {
            $this->manager->getConnection()
                ->prepare("CALL bedtime(:id)")
                ->executeQuery(["id" => $id])
            ;
            $this->addAction($id, $ownerId);

            return true;
        }

        return false;
    }

    /**
     * @throws Exception
     */
    public function play(int $id, int $ownerId): bool
    {
        $tamagotchi = $this->findOneById($id, $ownerId);

        if ($tamagotchi["boredom"] < 80) {
            $this->manager->getConnection()
                ->prepare("CALL enjoy(:id)")
                ->executeQuery(["id" => $id])
            ;
            $this->addAction($id, $ownerId);

            return true;
        }

        return false;
    }

    /**
     * @throws Exception
     */
    private function addAction(int $id, int $ownerId): void {
        $tamagotchi = $this->findOneById($id, $ownerId);

        $nbrOfActions = $tamagotchi["actions_count"] + 1;
        $level = $tamagotchi["level"];

        if ($nbrOfActions >= 10) {
            $nbrOfActions = 0;
            $level = $tamagotchi["level"] + 1;
        }

        $this->manager->getConnection()
            ->prepare("UPDATE Tamagotchi SET level = :level, actions_count = :actions WHERE id = :id AND owner_id = :owner")
            ->executeQuery([
                "level" => $level,
                "actions" => $nbrOfActions,
                "id" => $id,
                'owner' => $ownerId
            ])
        ;
    }
}
