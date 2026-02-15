<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20260215132101 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE weekly_reflections DROP joy');
        $this->addSql('ALTER TABLE weekly_reflections DROP is_joy_private');
        $this->addSql('ALTER TABLE weekly_reflections DROP difficulty');
        $this->addSql('ALTER TABLE weekly_reflections DROP is_difficulty_private');
        $this->addSql('ALTER TABLE weekly_reflections DROP achievement');
        $this->addSql('ALTER TABLE weekly_reflections DROP is_achievement_private');
        $this->addSql('ALTER TABLE weekly_reflections ALTER joy_text DROP DEFAULT');
        $this->addSql('ALTER TABLE weekly_reflections ALTER joy_is_private DROP DEFAULT');
        $this->addSql('ALTER TABLE weekly_reflections ALTER difficulty_text DROP DEFAULT');
        $this->addSql('ALTER TABLE weekly_reflections ALTER difficulty_is_private DROP DEFAULT');
        $this->addSql('ALTER TABLE weekly_reflections ALTER achievement_text DROP DEFAULT');
        $this->addSql('ALTER TABLE weekly_reflections ALTER achievement_is_private DROP DEFAULT');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE weekly_reflections ADD joy TEXT NOT NULL');
        $this->addSql('ALTER TABLE weekly_reflections ADD is_joy_private BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE weekly_reflections ADD difficulty TEXT NOT NULL');
        $this->addSql('ALTER TABLE weekly_reflections ADD is_difficulty_private BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE weekly_reflections ADD achievement TEXT NOT NULL');
        $this->addSql('ALTER TABLE weekly_reflections ADD is_achievement_private BOOLEAN NOT NULL');
        $this->addSql("ALTER TABLE weekly_reflections ALTER joy_text SET DEFAULT ''");
        $this->addSql('ALTER TABLE weekly_reflections ALTER joy_is_private SET DEFAULT false');
        $this->addSql("ALTER TABLE weekly_reflections ALTER difficulty_text SET DEFAULT ''");
        $this->addSql('ALTER TABLE weekly_reflections ALTER difficulty_is_private SET DEFAULT false');
        $this->addSql("ALTER TABLE weekly_reflections ALTER achievement_text SET DEFAULT ''");
        $this->addSql('ALTER TABLE weekly_reflections ALTER achievement_is_private SET DEFAULT false');
    }
}
