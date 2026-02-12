<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20260129152057 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users ADD latitude NUMERIC(9, 6) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE users ADD longitude NUMERIC(9, 6) DEFAULT 0 NOT NULL');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "users" DROP latitude');
        $this->addSql('ALTER TABLE "users" DROP longitude');
    }
}
