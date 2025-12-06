<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add slug columns to main entities
 */
final class Version20251205140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add slug VARCHAR(255) columns with unique constraints to chapters, forum_categories, resource_types, resources, series, subjects, and tags tables';
    }

    public function up(Schema $schema): void
    {
        // Add slug columns with NULL to allow partial unique index
        $this->addSql('ALTER TABLE chapters ADD COLUMN IF NOT EXISTS slug VARCHAR(255)');
        $this->addSql('ALTER TABLE forum_categories ADD COLUMN IF NOT EXISTS slug VARCHAR(255)');
        $this->addSql('ALTER TABLE resource_types ADD COLUMN IF NOT EXISTS slug VARCHAR(255)');
        $this->addSql('ALTER TABLE resources ADD COLUMN IF NOT EXISTS slug VARCHAR(255)');
        $this->addSql('ALTER TABLE series ADD COLUMN IF NOT EXISTS slug VARCHAR(255)');
        $this->addSql('ALTER TABLE subjects ADD COLUMN IF NOT EXISTS slug VARCHAR(255)');
        $this->addSql('ALTER TABLE tags ADD COLUMN IF NOT EXISTS slug VARCHAR(255)');
        
        // Create partial unique indexes (only on non-null values)
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_chapters_slug ON chapters (slug) WHERE slug IS NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_forum_categories_slug ON forum_categories (slug) WHERE slug IS NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_resource_types_slug ON resource_types (slug) WHERE slug IS NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_resources_slug ON resources (slug) WHERE slug IS NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_series_slug ON series (slug) WHERE slug IS NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_subjects_slug ON subjects (slug) WHERE slug IS NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_tags_slug ON tags (slug) WHERE slug IS NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // Drop unique indexes
        $this->addSql('DROP INDEX IF EXISTS UNIQ_chapters_slug');
        $this->addSql('DROP INDEX IF EXISTS UNIQ_forum_categories_slug');
        $this->addSql('DROP INDEX IF EXISTS UNIQ_resource_types_slug');
        $this->addSql('DROP INDEX IF EXISTS UNIQ_resources_slug');
        $this->addSql('DROP INDEX IF EXISTS UNIQ_series_slug');
        $this->addSql('DROP INDEX IF EXISTS UNIQ_subjects_slug');
        $this->addSql('DROP INDEX IF EXISTS UNIQ_tags_slug');
        
        // Drop slug columns
        $this->addSql('ALTER TABLE chapters DROP COLUMN IF EXISTS slug');
        $this->addSql('ALTER TABLE forum_categories DROP COLUMN IF EXISTS slug');
        $this->addSql('ALTER TABLE resource_types DROP COLUMN IF EXISTS slug');
        $this->addSql('ALTER TABLE resources DROP COLUMN IF EXISTS slug');
        $this->addSql('ALTER TABLE series DROP COLUMN IF EXISTS slug');
        $this->addSql('ALTER TABLE subjects DROP COLUMN IF EXISTS slug');
        $this->addSql('ALTER TABLE tags DROP COLUMN IF EXISTS slug');
    }
}
