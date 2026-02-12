<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Override;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260123020752 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<'SQL'
                CREATE TABLE exercises (
                  id UUID NOT NULL,
                  name VARCHAR(255) NOT NULL,
                  exercise_group VARCHAR(255) NOT NULL,
                  PRIMARY KEY (id)
                )
                SQL
        );
        $this->addSql(
            <<<'SQL'
                CREATE TABLE workout_exercises (
                  id UUID NOT NULL,
                  workout_id UUID NOT NULL,
                  exercise_id UUID NOT NULL,
                  order_index SMALLINT NOT NULL,
                  PRIMARY KEY (id)
                )
                SQL
        );
        $this->addSql('CREATE INDEX IDX_2D7B2EC5A6CCCFC9 ON workout_exercises (workout_id)');
        $this->addSql('CREATE INDEX IDX_2D7B2EC5E934951A ON workout_exercises (exercise_id)');
        $this->addSql(
            <<<'SQL'
                CREATE TABLE workout_sets (
                  id UUID NOT NULL,
                  workout_exercise_id UUID NOT NULL,
                  order_index SMALLINT NOT NULL,
                  weight INT NOT NULL,
                  reps INT NOT NULL,
                  is_warmup BOOLEAN NOT NULL,
                  performed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                  PRIMARY KEY (id)
                )
                SQL
        );
        $this->addSql('CREATE INDEX IDX_EC0346ADE435DB6B ON workout_sets (workout_exercise_id)');
        $this->addSql(
            <<<'SQL'
                CREATE TABLE workouts (
                  id UUID NOT NULL,
                  date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
                  PRIMARY KEY (id)
                )
                SQL
        );
        $this->addSql(
            <<<'SQL'
                ALTER TABLE workout_exercises
                ADD CONSTRAINT FK_2D7B2EC5A6CCCFC9 FOREIGN KEY (workout_id) REFERENCES workouts (id) NOT DEFERRABLE
                SQL
        );
        $this->addSql(
            <<<'SQL'
                ALTER TABLE workout_exercises
                ADD CONSTRAINT FK_2D7B2EC5E934951A FOREIGN KEY (exercise_id) REFERENCES exercises (id) NOT DEFERRABLE
                SQL
        );
        $this->addSql(
            <<<'SQL'
                ALTER TABLE workout_sets
                ADD CONSTRAINT FK_EC0346ADE435DB6B FOREIGN KEY (workout_exercise_id) REFERENCES workout_exercises (id) NOT DEFERRABLE
                SQL
        );
    }

    #[Override]
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE workout_exercises DROP CONSTRAINT FK_2D7B2EC5A6CCCFC9');
        $this->addSql('ALTER TABLE workout_exercises DROP CONSTRAINT FK_2D7B2EC5E934951A');
        $this->addSql('ALTER TABLE workout_sets DROP CONSTRAINT FK_EC0346ADE435DB6B');
        $this->addSql('DROP TABLE exercises');
        $this->addSql('DROP TABLE workout_exercises');
        $this->addSql('DROP TABLE workout_sets');
        $this->addSql('DROP TABLE workouts');
    }
}
