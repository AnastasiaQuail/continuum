<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20260113022848 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<'SQL'
                CREATE TABLE "users" (
                  id UUID NOT NULL,
                  email VARCHAR(180) NOT NULL,
                  password VARCHAR(255) NOT NULL,
                  status INT NOT NULL,
                  roles JSON NOT NULL,
                  created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                  updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                  last_visited_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                  timezone VARCHAR(64) NOT NULL,
                  PRIMARY KEY (id)
                )
                SQL
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_USERS_EMAIL ON "users" (email)');
        $this->addSql('DROP TABLE "user"');
    }

    #[Override]
    public function down(Schema $schema): void
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
                  timezone VARCHAR(64) NOT NULL,
                  PRIMARY KEY (id)
                )
                SQL
        );
        $this->addSql('CREATE UNIQUE INDEX uniq_identifier_email ON "user" (email)');
        $this->addSql('DROP TABLE "users"');
    }
}
