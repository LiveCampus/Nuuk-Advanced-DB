<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20221111171642 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Créer la table Action nécessaire';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE Action (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            id_tamagotchi INT UNSIGNED NOT NULL,
            title VARCHAR(7) NOT NULL
        )");
        $this->addSql("ALTER TABLE Action ADD CONSTRAINT FK_TamagotchiId FOREIGN KEY (id_tamagotchi) REFERENCES Tamagotchi(id)");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE Action DROP FOREIGN KEY FK_Tamagotchi");
        $this->addSql("DROP TABLE Action");
    }
}
