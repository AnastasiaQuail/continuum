<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Override;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260106232845 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<'SQL'
                CREATE TABLE calendar_days (
                  id UUID NOT NULL,
                  date DATE NOT NULL,
                  type VARCHAR(255) NOT NULL,
                  title VARCHAR(255) NOT NULL,
                  PRIMARY KEY (id)
                )
                SQL
        );
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE calendar_days');
    }
}
