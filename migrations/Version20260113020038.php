<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Override;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260113020038 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<'SQL'
                CREATE TABLE mood_reflections (
                  id UUID NOT NULL,
                  date DATE NOT NULL,
                  type VARCHAR(255) NOT NULL,
                  text VARCHAR(255) NOT NULL,
                  PRIMARY KEY (id)
                )
                SQL
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_MOOD_REFLECTION_DATE ON mood_reflections (date)');
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE mood_reflections');
    }
}
