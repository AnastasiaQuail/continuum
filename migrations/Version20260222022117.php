<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20260222022117 extends AbstractMigration
{
    #[Override]
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE body_measurements ALTER fat_deurenberg TYPE REAL');
        $this->addSql('ALTER TABLE body_measurements ALTER fat_us_navy TYPE REAL');
        $this->addSql('ALTER TABLE body_measurements ALTER weight TYPE REAL');
        $this->addSql('ALTER TABLE body_measurements ALTER neck TYPE REAL');
        $this->addSql('ALTER TABLE body_measurements ALTER chest TYPE REAL');
        $this->addSql('ALTER TABLE body_measurements ALTER shoulders TYPE REAL');
        $this->addSql('ALTER TABLE body_measurements ALTER waist TYPE REAL');
        $this->addSql('ALTER TABLE body_measurements ALTER flexed_biceps TYPE REAL');
        $this->addSql('ALTER TABLE body_measurements ALTER hips TYPE REAL');
        $this->addSql('ALTER TABLE body_measurements ALTER thigh TYPE REAL');
        $this->addSql('ALTER TABLE body_measurements ALTER calf TYPE REAL');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE body_measurements ALTER fat_deurenberg TYPE INT');
        $this->addSql('ALTER TABLE body_measurements ALTER fat_us_navy TYPE INT');
        $this->addSql('ALTER TABLE body_measurements ALTER weight TYPE INT');
        $this->addSql('ALTER TABLE body_measurements ALTER neck TYPE INT');
        $this->addSql('ALTER TABLE body_measurements ALTER chest TYPE INT');
        $this->addSql('ALTER TABLE body_measurements ALTER shoulders TYPE INT');
        $this->addSql('ALTER TABLE body_measurements ALTER waist TYPE INT');
        $this->addSql('ALTER TABLE body_measurements ALTER flexed_biceps TYPE INT');
        $this->addSql('ALTER TABLE body_measurements ALTER hips TYPE INT');
        $this->addSql('ALTER TABLE body_measurements ALTER thigh TYPE INT');
        $this->addSql('ALTER TABLE body_measurements ALTER calf TYPE INT');
    }
}
