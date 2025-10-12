<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251011201906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates initial database schema for workout tracking system including tables for training plans, current workouts, workout history, personal records and event store';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE available_training_plan (id UUID NOT NULL, name VARCHAR(255) NOT NULL, exercise_requirements JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE current_workout (id UUID NOT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, training_plan_id UUID DEFAULT NULL, performed_workout_sets JSON NOT NULL, completion_requirements JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN current_workout.started_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE event_store (id UUID NOT NULL, aggregate_id UUID NOT NULL, aggregate_type VARCHAR(255) NOT NULL, event_type VARCHAR(255) NOT NULL, payload JSON NOT NULL, version INT NOT NULL, occurred_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_aggregate_id ON event_store (aggregate_id)');
        $this->addSql('CREATE INDEX idx_aggregate_type ON event_store (aggregate_type)');
        $this->addSql('CREATE INDEX idx_occurred_at ON event_store (occurred_at)');
        $this->addSql('CREATE UNIQUE INDEX uniq_aggregate_version ON event_store (aggregate_id, version)');
        $this->addSql('COMMENT ON COLUMN event_store.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN event_store.aggregate_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN event_store.occurred_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE personal_record (id UUID NOT NULL, exercise_code VARCHAR(64) NOT NULL, max_weight DOUBLE PRECISION NOT NULL, unit VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6FB873862E30522A ON personal_record (exercise_code)');
        $this->addSql('CREATE TABLE workout_history (id UUID NOT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, completed_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, duration_minutes INT NOT NULL, total_sets_count INT NOT NULL, total_volume DOUBLE PRECISION NOT NULL, exercises JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN workout_history.started_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN workout_history.completed_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE available_training_plan');
        $this->addSql('DROP TABLE current_workout');
        $this->addSql('DROP TABLE event_store');
        $this->addSql('DROP TABLE personal_record');
        $this->addSql('DROP TABLE workout_history');
    }
}
