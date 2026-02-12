<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260113021730 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE reflection_moods');
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            <<<'SQL'
                CREATE TABLE reflection_moods (
                  id UUID NOT NULL,
                  date DATE NOT NULL,
                  type VARCHAR(255) NOT NULL,
                  text VARCHAR(255) NOT NULL,
                  PRIMARY KEY (id)
                )
                SQL
        );
        $this->addSql('CREATE UNIQUE INDEX uniq_identifier_date ON reflection_moods (date)');
    }
}
