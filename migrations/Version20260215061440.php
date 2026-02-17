<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20260215061440 extends AbstractMigration
{
    #[Override]
    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE weekly_reflections ADD joy_text TEXT DEFAULT '' NOT NULL");
        $this->addSql('ALTER TABLE weekly_reflections ADD joy_is_private BOOLEAN DEFAULT false NOT NULL');
        $this->addSql("ALTER TABLE weekly_reflections ADD difficulty_text TEXT DEFAULT '' NOT NULL");
        $this->addSql('ALTER TABLE weekly_reflections ADD difficulty_is_private BOOLEAN DEFAULT false NOT NULL');
        $this->addSql("ALTER TABLE weekly_reflections ADD achievement_text TEXT DEFAULT '' NOT NULL");
        $this->addSql('ALTER TABLE weekly_reflections ADD achievement_is_private BOOLEAN DEFAULT false NOT NULL');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE weekly_reflections DROP joy_text');
        $this->addSql('ALTER TABLE weekly_reflections DROP joy_is_private');
        $this->addSql('ALTER TABLE weekly_reflections DROP difficulty_text');
        $this->addSql('ALTER TABLE weekly_reflections DROP difficulty_is_private');
        $this->addSql('ALTER TABLE weekly_reflections DROP achievement_text');
        $this->addSql('ALTER TABLE weekly_reflections DROP achievement_is_private');
    }
}
