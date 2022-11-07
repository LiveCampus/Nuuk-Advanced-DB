<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221107181232 extends AbstractMigration
{
    public const EASY = 100;
    public const NORMAL = 70;
    public const HARD = 40;

    public function getDescription(): string
    {
        return 'Créer les 2 tables (Owner et Tamagotchi) nécessaires avec la difficulté sélectionnée.';
    }

    public function up(Schema $schema): void
    {
        /* Changer la constante pour choisir une autre difficulté (par défaut : NORMAL) */
        $difficulty = self::NORMAL;

        $this->addSql("CREATE TABLE Owner (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, 
            name VARCHAR(255) NOT NULL 
       )");
        $this->addSql("CREATE TABLE Tamagotchi (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, 
            owner_id INT UNSIGNED NOT NULL, name VARCHAR(255) NOT NULL, 
            hunger INT NOT NULL DEFAULT $difficulty, 
            thirst INT NOT NULL DEFAULT $difficulty, 
            sleep INT NOT NULL DEFAULT $difficulty, 
            boredom INT NOT NULL DEFAULT $difficulty, 
            level INT NOT NULL DEFAULT 1, 
            actions_count INT NOT NULL DEFAULT 0, 
            alive TINYINT NOT NULL DEFAULT 1, 
            first TINYINT NOT NULL DEFAULT 0, 
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
            updated_at DATETIME
        )");
        $this->addSql("ALTER TABLE Tamagotchi ADD CONSTRAINT FK_OwnerId FOREIGN KEY (owner_id) REFERENCES Owner(id)");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE Tamagotchi DROP FOREIGN KEY FK_OwnerId");
        $this->addSql("DROP TABLE Tamagotchi");
        $this->addSql("DROP TABLE Owner");
    }
}
