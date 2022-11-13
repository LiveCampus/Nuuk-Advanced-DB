<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221108194047 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Génère toutes les procédures/déclencheurs/vues de la base de données';
    }

    public function up(Schema $schema): void
    {
        $difficulty = self::NORMAL;
        
        // Procédure pour créer un utilisateur et son premier tamagotchi
        $this->addSql("CREATE PROCEDURE CREATE_ACCOUNT(IN username VARCHAR(255), tamagotchiName VARCHAR(255))
            BEGIN
                DECLARE idOwner VARCHAR(255)

                INSERT INTO Owner (name) VALUES (username)
                SELECT id INTO idOwner
                FROM Owner
                WHERE name = username;
                INSERT INTO Tamagotchi (owner_id, name, first) VALUES (idOwner, tamagocthiName, 1)
            END
        ");

        // Procédure pour créer un tamagotchi d'un utilisateur existant
        $this->addSql("CREATE PROCEDURE CREATE_TAMAGOCHI(IN tamagotchiName VARCHAR(255), ownerId VARCHAR(255))
            BEGIN
                INSERT INTO Tamagotchi (owner_id, name) VALUES (ownerID, tamagotchiName)
            END
        ");

        //Procédures pour créer une ligne dans la table action

        //Eat
        $this->addSql("CREATE PROCEDURE EAT(IN tamagotchiId INT)
            BEGIN
                INSERT INTO Action (id_tamagotchi, title) VALUES (tamagotchiId, \"Eat\")
            END
        ");
        //Drink
        $this->addSql("CREATE PROCEDURE DRINK(IN tamagotchiId INT)
            BEGIN
                INSERT INTO Action (id_tamagotchi, title) VALUES (tamagotchiId, \"Drink\")
            END
        ");
        //Bedtime
        $this->addSql("CREATE PROCEDURE BEDTIME(IN tamagotchiId INT)
            BEGIN
                INSERT INTO Action (id_tamagotchi, title) VALUES (tamagotchiId, \"Bedtime\")
            END
        ");
        //Enjoy
        $this->addSql("CREATE PROCEDURE ENJOY(IN tamagotchiId INT)
            BEGIN
                INSERT INTO Action (id_tamagotchi, title) VALUES (tamagotchiId, \"Enjoy\")
            END
        ");

        //Fonctions qui retourne le niveau du tamagotchi
        $this->addSql("CREATE FUNCTION LEVEL(tamagotchiId INT)
            RETURNS INT NOT DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE lvl INT;

                SELECT level INTO lvl
                FROM Tamagotchi
                WHERE id = tamagotchiId;

                RETURN lvl;
            END
        ");

        //Fonctions qui retourne l'état de santé du tamagotchi
        $this->addSql("CREATE FUNCTION IS_ALIVE(tamagotchiId INT)
            RETURNS INT NOT DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE health INT;

                SELECT alive INTO health
                FROM Tamagotchi
                WHERE id = tamagotchiId;

                RETURN health;
            END
        ");

        //Trigger qui change les valeurs du tamagotchi
        $this->addSql("CREATE OR REPLACE TRIGGER action AFTER INSERT ON Action

            SELECT A.id, A.id_tamagotchi, A.title, T.hunger, T.thirst, T.sleep, T.boredom, T.level
            FROM Action A
            JOIN Tamagotchi T ON A.id_tamagotchi = T.id
            WHERE A.id = MAX(A.id);
            CASE title
                WHEN \"Eat\" THEN 
                    UPDATE Tamagotchi SET T.hunger = MIN(T.hunger + T.level + 30, 100), T.drink = MAX(T.thirst - T.level + 10, 0), T.sleep = MAX(T.sleep - T.level + 5, 0), T.boredom = MAX(T.boredom - T.level + 5, 0) 
                    WHERE T.id = A.id_tamagotchi
                WHEN \"Drink\" THEN 
                    UPDATE Tamagotchi SET T.hunger = MAX(T.hunger - T.level + 10, 0), T.drink = MIN(T.thirst + T.level + 30, 100), T.sleep = MAX(T.sleep - T.level + 5, 0), T.boredom = MAX(T.boredom - T.level + 5, 0) 
                    WHERE T.id = A.id_tamagotchi
                WHEN \"Bedtime\" THEN 
                    UPDATE Tamagotchi SET T.hunger = MAX(T.hunger - T.level + 10, 0), T.drink = MAX(T.thirst - T.level + 15, 0), T.sleep = MIN(T.sleep + T.level + 30, 100), T.boredom = MAX(T.boredom - T.level + 15, 0) 
                    WHERE T.id = A.id_tamagotchi
                WHEN \"Enjoy\" THEN 
                    UPDATE Tamagotchi SET T.hunger = MAX(T.hunger + T.level + 5, 0), T.drink = MAX(T.thirst - T.level + 5, 0), T.sleep = MAX(T.sleep - T.level + 5, 0), T.boredom = MIN(T.boredom + T.level + 15, 100) 
                    WHERE T.id = A.id_tamagotchi
        ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
