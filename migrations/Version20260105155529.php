<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20260105155529 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<'SQL'
                CREATE TABLE "user" (
                  id UUID NOT NULL,
                  email VARCHAR(180) NOT NULL,
                  password VARCHAR(255) NOT NULL,
                  status INT NOT NULL,
                  roles JSON NOT NULL,
                  created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                  updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                  last_visited_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                  PRIMARY KEY (id)
                )
                SQL
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE "user"');
    }
}
