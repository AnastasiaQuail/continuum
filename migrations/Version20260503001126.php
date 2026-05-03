<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20260503001126 extends AbstractMigration
{
    #[Override]
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exercises ALTER is_active DROP DEFAULT');
        $this->addSql("ALTER TABLE workout_exercises ADD description VARCHAR(255) DEFAULT '' NOT NULL");
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exercises ALTER is_active SET DEFAULT true');
        $this->addSql('ALTER TABLE workout_exercises DROP description');
    }
}
