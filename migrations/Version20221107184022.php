<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Faker\Factory;

final class Version20221107184022 extends AbstractMigration
{
    public CONST NUMBER_OWNERS      = 2;
    public CONST NUMBER_TAMAGOTCHIS = 3;

    public function getDescription(): string
    {
        return 'Génère des données aléatoires pour remplir les tables';
    }

    public function up(Schema $schema): void
    {
        $generator = Factory::create("fr-FR");

        for ($i = 0; $i < self::NUMBER_OWNERS; $i++) {
            /* Génère le nom de l'utilisateur aléatoirement */
            $username = $generator->firstName();

            /* Créer l'utilisateur */
            $this->connection->executeQuery("INSERT INTO Owner VALUES (NULL, :username)", ["username" => $username]);
            $userId = $this->connection->fetchFirstColumn('SELECT LAST_INSERT_ID()')[0];

            for ($j = 0; $j < self::NUMBER_TAMAGOTCHIS; $j++) {
                /* Génère le nom du tamagotchi aléatoirement */
                $tamagotchiName = $generator->firstName();

                /* Créer les tamagotchis de l'utilisateur */
                $this->connection->executeQuery("INSERT INTO Tamagotchi (owner_id, name, first) VALUES (:user, :name, :first)", [
                    "user" => $userId,
                    "name" => $tamagotchiName,
                    "first" => $j == 0 ? 1 : 0
                ]);
            }
        }
    }

    public function down(Schema $schema): void
    {
        $this->connection->executeQuery("DELETE FROM Tamagotchi");
        $this->connection->executeQuery("DELETE FROM Owner");
    }
}
