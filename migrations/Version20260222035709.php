<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20260222035709 extends AbstractMigration
{
    #[Override]
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE users ALTER latitude TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE users ALTER latitude DROP DEFAULT');
        $this->addSql('ALTER TABLE users ALTER longitude TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE users ALTER longitude DROP DEFAULT');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "users" ALTER latitude TYPE NUMERIC(9, 6)');
        $this->addSql('ALTER TABLE "users" ALTER latitude SET DEFAULT \'0\'');
        $this->addSql('ALTER TABLE "users" ALTER longitude TYPE NUMERIC(9, 6)');
        $this->addSql('ALTER TABLE "users" ALTER longitude SET DEFAULT \'0\'');
    }
}
