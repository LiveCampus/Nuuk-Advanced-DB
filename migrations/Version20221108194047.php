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
        // Procédure pour créer un utilisateur et son premier tamagotchi
        $this->addSql(
            "CREATE PROCEDURE create_account(username VARCHAR(255), tamagotchiName VARCHAR(255))
            BEGIN
                DECLARE idOwner VARCHAR(255);

                INSERT INTO Owner (name) VALUES (username);
                SELECT id INTO idOwner
                FROM Owner
                WHERE name = username;
                INSERT INTO Tamagotchi (owner_id, name, first) VALUES (idOwner, tamagocthiName, 1);
            END;
        ");

        // Procédure pour créer un tamagotchi d'un utilisateur existant
        $this->addSql("CREATE PROCEDURE create_tamagotchi(IN tamagotchiName VARCHAR(255), ownerId VARCHAR(255))
            BEGIN
                INSERT INTO tamagotchi (owner_id, name) VALUES (ownerID, tamagotchiName);
            END;
        ");

        //Procédures pour créer une ligne dans la table action

        //Eat
        $this->addSql("CREATE PROCEDURE eat(IN tamagotchiId INT)
            BEGIN
                INSERT INTO Action (id_tamagotchi, title) VALUES (tamagotchiId, \"Eat\");
            END;
        ");
        //Drink
        $this->addSql("CREATE PROCEDURE drink(IN tamagotchiId INT)
            BEGIN
                INSERT INTO Action (id_tamagotchi, title) VALUES (tamagotchiId, \"Drink\");
            END;
        ");
        //Bedtime
        $this->addSql("CREATE PROCEDURE bedtime(IN tamagotchiId INT)
            BEGIN
                INSERT INTO Action (id_tamagotchi, title) VALUES (tamagotchiId, \"Bedtime\");
            END;
        ");
        //Enjoy
        $this->addSql("CREATE PROCEDURE enjoy(IN tamagotchiId INT)
            BEGIN
                INSERT INTO Action (id_tamagotchi, title) VALUES (tamagotchiId, \"Enjoy\");
            END;
        ");

        //Fonctions qui retourne le niveau du tamagotchi
        $this->addSql("CREATE FUNCTION level(tamagotchiId INT)
            RETURNS INT NOT DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE lvl INT;

                SELECT level INTO lvl
                FROM Tamagotchi
                WHERE id = tamagotchiId;

                RETURN lvl;
            END;
        ");

        //Fonctions qui retourne l'état de santé du tamagotchi
        $this->addSql("CREATE FUNCTION is_alive(tamagotchiId INT)
            RETURNS INT NOT DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE health INT;

                SELECT alive INTO health
                FROM Tamagotchi
                WHERE id = tamagotchiId;

                RETURN health;
            END;
        ");

        //Trigger qui change les valeurs du tamagotchi
        $this->addSql("CREATE TRIGGER eat AFTER INSERT ON Action
        FOR EACH ROW
            UPDATE Tamagotchi SET hunger = IF(hunger + level + 30 > 100, 100, hunger + level + 30), thirst = IF(thirst - (level + 10) < 0, 0, thirst - (level + 10)), sleep = IF(sleep - (level + 5) < 0, 0, sleep - (level + 5)), boredom = IF(boredom - (level + 5) < 0, 0, boredom - (level + 5))
            WHERE id = (SELECT id_tamagotchi 
            FROM Action  
            WHERE title = \"Eat\" AND id =(SELECT MAX(id) FROM action));
        ");
        $this->addSql("CREATE TRIGGER drink AFTER INSERT ON Action
        FOR EACH ROW
            UPDATE Tamagotchi SET hunger = IF(hunger - (level + 10) < 0, 0, hunger - (level + 10)), thirst = IF(thirst + level + 30 > 100, 100, thirst + level + 10), sleep = IF(sleep - (level + 5) < 0, 0, sleep - (level + 5)), boredom = IF(boredom - (level + 5) < 0, 0, boredom - (level + 5))
            WHERE id = (SELECT id_tamagotchi 
            FROM Action  
            WHERE title = \"Drink\" AND id =(SELECT MAX(id) FROM action));
        ");
        $this->addSql("CREATE TRIGGER sleep AFTER INSERT ON Action
        FOR EACH ROW
            UPDATE Tamagotchi SET hunger = IF(hunger - (level + 10) < 0, 0, hunger - (level + 10)), thirst = IF(thirst - (level + 15) < 0, 0, thirst - (level + 15)), sleep = IF(sleep + level + 30 > 100, 100, sleep + level + 30), boredom = IF(boredom - (level + 15) < 0, 0, boredom - (level + 15))
            WHERE id = (SELECT id_tamagotchi 
            FROM Action  
            WHERE title = \"Bedtime\" AND id =(SELECT MAX(id) FROM action));
        ");
        $this->addSql("CREATE TRIGGER enjoy AFTER INSERT ON Action
        FOR EACH ROW
            UPDATE Tamagotchi SET hunger = IF(hunger - (level + 5) < 0, 0, hunger - (level + 5)), thirst = IF(thirst - (level + 5) < 0, 0, thirst - (level + 5)), sleep = IF(sleep - (level + 5) < 0, 0, sleep - (level + 5)), boredom = IF(boredom + level + 15 > 100, 100, boredom + level + 15)
            WHERE id = (SELECT id_tamagotchi 
            FROM Action  
            WHERE title = \"Enjoy\" AND id =(SELECT MAX(id) FROM action));
        ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
