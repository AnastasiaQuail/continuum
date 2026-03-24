<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Override;

final class Version20260317141801 extends AbstractMigration
{
    #[Override]
    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<'SQL'
                CREATE TABLE chat (
                  id UUID NOT NULL,
                  user1_id UUID NOT NULL,
                  user2_id UUID NOT NULL,
                  last_message_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                  PRIMARY KEY (id)
                )
                SQL
        );
        $this->addSql('CREATE INDEX IDX_659DF2AA56AE248B ON chat (user1_id)');
        $this->addSql('CREATE INDEX IDX_659DF2AA441B8B65 ON chat (user2_id)');
        $this->addSql(
            <<<'SQL'
                CREATE TABLE messages (
                  id UUID NOT NULL,
                  chat_id UUID NOT NULL,
                  sender_id UUID NOT NULL,
                  content TEXT NOT NULL,
                  created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                  read_at TIMESTAMP(0) WITHOUT TIME ZONE,
                  PRIMARY KEY (id)
                )
                SQL
        );
        $this->addSql('CREATE INDEX IDX_DB021E961A9A7125 ON messages (chat_id)');
        $this->addSql('CREATE INDEX IDX_DB021E96F624B39D ON messages (sender_id)');
        $this->addSql(
            <<<'SQL'
                ALTER TABLE chat
                ADD CONSTRAINT FK_659DF2AA56AE248B FOREIGN KEY (user1_id) REFERENCES "users" (id) NOT DEFERRABLE
                SQL
        );
        $this->addSql(
            <<<'SQL'
                ALTER TABLE chat
                ADD CONSTRAINT FK_659DF2AA441B8B65 FOREIGN KEY (user2_id) REFERENCES "users" (id) NOT DEFERRABLE
                SQL
        );
        $this->addSql(
            <<<'SQL'
                ALTER TABLE messages
                ADD CONSTRAINT FK_DB021E961A9A7125 FOREIGN KEY (chat_id) REFERENCES chat (id) NOT DEFERRABLE
                SQL
        );
        $this->addSql(
            <<<'SQL'
                ALTER TABLE messages
                ADD CONSTRAINT FK_DB021E96F624B39D FOREIGN KEY (sender_id) REFERENCES "users" (id) NOT DEFERRABLE
                SQL
        );
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE chat DROP CONSTRAINT FK_659DF2AA56AE248B');
        $this->addSql('ALTER TABLE chat DROP CONSTRAINT FK_659DF2AA441B8B65');
        $this->addSql('ALTER TABLE messages DROP CONSTRAINT FK_DB021E961A9A7125');
        $this->addSql('ALTER TABLE messages DROP CONSTRAINT FK_DB021E96F624B39D');
        $this->addSql('DROP TABLE chat');
        $this->addSql('DROP TABLE messages');
    }
}
