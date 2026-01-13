<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260113000941 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE weekly_reflection (
              id UUID NOT NULL,
              date DATE NOT NULL,
              joy TEXT NOT NULL,
              is_joy_private BOOLEAN NOT NULL,
              difficulty TEXT NOT NULL,
              is_difficulty_private BOOLEAN NOT NULL,
              achievement TEXT NOT NULL,
              is_achievement_private BOOLEAN NOT NULL,
              PRIMARY KEY (id)
            )
        SQL);
        $this->addSql('CREATE UNIQUE INDEX UNIQ_WEEKLY_REFLECTION_DATE ON weekly_reflection (date)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE weekly_reflection');
    }
}
