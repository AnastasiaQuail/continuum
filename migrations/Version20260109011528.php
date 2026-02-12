<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260109011528 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<'SQL'
                CREATE TABLE calendar_events (
                  id UUID NOT NULL,
                  datetime TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                  format VARCHAR(255) NOT NULL,
                  type VARCHAR(255) NOT NULL,
                  title VARCHAR(255) NOT NULL,
                  PRIMARY KEY (id)
                )
                SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE calendar_events');
    }
}
