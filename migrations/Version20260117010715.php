<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20260117010715 extends AbstractMigration
{
    #[Override]
    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<'SQL'
                CREATE TABLE body_measurements (
                  id UUID NOT NULL,
                  datetime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                  fat_deurenberg INT NOT NULL,
                  fat_us_navy INT DEFAULT NULL,
                  age INT NOT NULL,
                  height INT NOT NULL,
                  weight INT NOT NULL,
                  neck INT DEFAULT NULL,
                  chest INT DEFAULT NULL,
                  shoulders INT DEFAULT NULL,
                  waist INT DEFAULT NULL,
                  flexed_biceps INT DEFAULT NULL,
                  hips INT DEFAULT NULL,
                  thigh INT DEFAULT NULL,
                  calf INT DEFAULT NULL,
                  PRIMARY KEY (id)
                )
                SQL
        );
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE body_measurements');
    }
}
