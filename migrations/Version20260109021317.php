<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20260109021317 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE calendar_days');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->addSql(
            <<<'SQL'
                CREATE TABLE calendar_days (
                  id UUID NOT NULL,
                  date DATE NOT NULL,
                  type VARCHAR(255) NOT NULL,
                  title VARCHAR(255) NOT NULL,
                  "time" TIME(0) WITHOUT TIME ZONE DEFAULT NULL,
                  PRIMARY KEY (id)
                )
                SQL
        );
    }
}
