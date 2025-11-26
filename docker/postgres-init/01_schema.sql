-- Initialisation de la base wikibac
-- NOTE: Le conteneur crée déjà la base POSTGRES_DB=wikibac
-- On supprime les lignes CREATE DATABASE / \c
-- wikibac si elles existent.
-- set collation_connection = 'utf8_general_ci';
SET search_path TO public;
SET client_encoding TO 'UTF8';
-- ============================================
-- ACTIVATION DE L'EXTENSION UUID
-- ============================================
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
-- ============================================
-- CRÉATION DES TYPES ENUM
-- ============================================
CREATE TYPE user_role_enum AS ENUM ('STUDENT', 'TEACHER', 'ADMIN');
CREATE TYPE user_status_enum AS ENUM ('active', 'inactive', 'banned');
CREATE TYPE resource_status_enum AS ENUM ('draft', 'review', 'published');
CREATE TYPE resource_type_enum AS ENUM (
    'exam_paper',
    'solution',
    'revision_sheet',
    'exercise'
);
CREATE TYPE progress_status_enum AS ENUM ('not_started', 'in_progress', 'completed');
-- ============================================
-- TABLES DE BASE (Core Data)
-- ============================================
CREATE TABLE roles (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(50) NOT NULL UNIQUE,
    permissions JSONB,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE users (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    full_name VARCHAR(100),
    role_id UUID,
    avatar_url VARCHAR(255),
    bio TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP,
    status user_status_enum NOT NULL DEFAULT 'active',
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE
    SET NULL
);
CREATE TABLE subjects (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(100) NOT NULL,
    code VARCHAR(10) NOT NULL UNIQUE,
    icon VARCHAR(255)
);
CREATE TABLE series (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    code VARCHAR(10) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    description TEXT
);
CREATE TABLE series_subjects (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    series_id UUID NOT NULL,
    subject_id UUID NOT NULL,
    coefficient DECIMAL(3, 1),
    FOREIGN KEY (series_id) REFERENCES series(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    UNIQUE(series_id, subject_id)
);
CREATE TABLE chapters (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    subject_id UUID NOT NULL,
    title VARCHAR(255) NOT NULL,
    order_num INTEGER NOT NULL,
    description TEXT,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
);
CREATE TABLE resource_types (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);
CREATE TABLE resources (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    title VARCHAR(255) NOT NULL,
    description TEXT,
    type_id UUID NOT NULL,
    subject_id UUID,
    chapter_id UUID,
    series_id UUID,
    year INTEGER,
    file_url VARCHAR(255),
    thumbnail_url VARCHAR(255),
    user_id UUID NOT NULL,
    status resource_status_enum NOT NULL DEFAULT 'draft',
    view_count INTEGER NOT NULL DEFAULT 0,
    download_count INTEGER NOT NULL DEFAULT 0,
    average_rating DECIMAL(3, 2),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (type_id) REFERENCES resource_types(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE
    SET NULL,
        FOREIGN KEY (chapter_id) REFERENCES chapters(id) ON DELETE
    SET NULL,
        FOREIGN KEY (series_id) REFERENCES series(id) ON DELETE
    SET NULL,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE tags (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(50) NOT NULL UNIQUE
);
CREATE TABLE resource_tags (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    resource_id UUID NOT NULL,
    tag_id UUID NOT NULL,
    FOREIGN KEY (resource_id) REFERENCES resources(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE,
    UNIQUE(resource_id, tag_id)
);
CREATE TABLE resource_ratings (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    resource_id UUID NOT NULL,
    user_id UUID NOT NULL,
    rating INTEGER NOT NULL CHECK (
        rating >= 1
        AND rating <= 5
    ),
    comment TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (resource_id) REFERENCES resources(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE(resource_id, user_id)
);
CREATE TABLE exam_papers (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    resource_id UUID NOT NULL UNIQUE,
    exam_type VARCHAR(50),
    duration_minutes INTEGER,
    FOREIGN KEY (resource_id) REFERENCES resources(id) ON DELETE CASCADE
);
CREATE TABLE solutions (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    resource_id UUID NOT NULL UNIQUE,
    exam_paper_id UUID,
    content_text TEXT,
    FOREIGN KEY (resource_id) REFERENCES resources(id) ON DELETE CASCADE,
    FOREIGN KEY (exam_paper_id) REFERENCES exam_papers(id) ON DELETE
    SET NULL
);
CREATE TABLE revision_sheets (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    resource_id UUID NOT NULL UNIQUE,
    content TEXT,
    FOREIGN KEY (resource_id) REFERENCES resources(id) ON DELETE CASCADE
);
CREATE TABLE exercises (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    resource_id UUID NOT NULL UNIQUE,
    question TEXT NOT NULL,
    answer TEXT,
    difficulty_level INTEGER CHECK (
        difficulty_level >= 1
        AND difficulty_level <= 5
    ),
    FOREIGN KEY (resource_id) REFERENCES resources(id) ON DELETE CASCADE
);
CREATE TABLE user_progress (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID NOT NULL,
    chapter_id UUID NOT NULL,
    status progress_status_enum NOT NULL DEFAULT 'not_started',
    confidence_level INTEGER CHECK (
        confidence_level >= 1
        AND confidence_level <= 5
    ),
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (chapter_id) REFERENCES chapters(id) ON DELETE CASCADE,
    UNIQUE(user_id, chapter_id)
);
CREATE TABLE practice_sessions (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID NOT NULL,
    subject_id UUID NOT NULL,
    start_time TIMESTAMP NOT NULL,
    end_time TIMESTAMP,
    duration_minutes INTEGER,
    resources_used JSONB,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
);
CREATE TABLE forum_categories (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(100) NOT NULL,
    description TEXT,
    parent_id UUID,
    order_num INTEGER NOT NULL,
    FOREIGN KEY (parent_id) REFERENCES forum_categories(id) ON DELETE CASCADE
);
CREATE TABLE forum_topics (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    category_id UUID NOT NULL,
    user_id UUID NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    view_count INTEGER NOT NULL DEFAULT 0,
    is_pinned BOOLEAN NOT NULL DEFAULT FALSE,
    is_locked BOOLEAN NOT NULL DEFAULT FALSE,
    last_post_id UUID,
    FOREIGN KEY (category_id) REFERENCES forum_categories(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE forum_posts (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    topic_id UUID NOT NULL,
    user_id UUID NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_solution BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (topic_id) REFERENCES forum_topics(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
-- Ajout de la contrainte last_post_id après création de forum_posts
ALTER TABLE forum_topics
ADD CONSTRAINT fk_forum_topics_last_post FOREIGN KEY (last_post_id) REFERENCES forum_posts(id) ON DELETE
SET NULL;
-- ============================================
-- INDEX POUR OPTIMISATION
-- ============================================
-- Index sur users
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_role_id ON users(role_id);
CREATE INDEX idx_users_status ON users(status);
-- Index sur resources
CREATE INDEX idx_resources_type_id ON resources(type_id);
CREATE INDEX idx_resources_subject_id ON resources(subject_id);
CREATE INDEX idx_resources_chapter_id ON resources(chapter_id);
CREATE INDEX idx_resources_series_id ON resources(series_id);
CREATE INDEX idx_resources_user_id ON resources(user_id);
CREATE INDEX idx_resources_status ON resources(status);
CREATE INDEX idx_resources_year ON resources(year);
CREATE INDEX idx_resources_created_at ON resources(created_at);
-- Index sur chapters
CREATE INDEX idx_chapters_subject_id ON chapters(subject_id);
CREATE INDEX idx_chapters_order_num ON chapters(order_num);
-- Index sur series_subjects
CREATE INDEX idx_series_subjects_series_id ON series_subjects(series_id);
CREATE INDEX idx_series_subjects_subject_id ON series_subjects(subject_id);
-- Index sur resource_tags
CREATE INDEX idx_resource_tags_resource_id ON resource_tags(resource_id);
CREATE INDEX idx_resource_tags_tag_id ON resource_tags(tag_id);
-- Index sur resource_ratings
CREATE INDEX idx_resource_ratings_resource_id ON resource_ratings(resource_id);
CREATE INDEX idx_resource_ratings_user_id ON resource_ratings(user_id);
-- Index sur user_progress
CREATE INDEX idx_user_progress_user_id ON user_progress(user_id);
CREATE INDEX idx_user_progress_chapter_id ON user_progress(chapter_id);
-- Index sur practice_sessions
CREATE INDEX idx_practice_sessions_user_id ON practice_sessions(user_id);
CREATE INDEX idx_practice_sessions_subject_id ON practice_sessions(subject_id);
CREATE INDEX idx_practice_sessions_start_time ON practice_sessions(start_time);
-- Index sur forum
CREATE INDEX idx_forum_topics_category_id ON forum_topics(category_id);
CREATE INDEX idx_forum_topics_user_id ON forum_topics(user_id);
CREATE INDEX idx_forum_topics_created_at ON forum_topics(created_at);
CREATE INDEX idx_forum_posts_topic_id ON forum_posts(topic_id);
CREATE INDEX idx_forum_posts_user_id ON forum_posts(user_id);
-- ============================================
-- FONCTIONS ET TRIGGERS
-- ============================================
-- Fonction pour mettre à jour updated_at automatiquement
CREATE OR REPLACE FUNCTION update_updated_at_column() RETURNS TRIGGER AS $$ BEGIN NEW.updated_at = CURRENT_TIMESTAMP;
RETURN NEW;
END;
$$ language 'plpgsql';
-- Triggers pour updated_at
CREATE TRIGGER update_resources_updated_at BEFORE
UPDATE ON resources FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_forum_topics_updated_at BEFORE
UPDATE ON forum_topics FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_forum_posts_updated_at BEFORE
UPDATE ON forum_posts FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_user_progress_updated_at BEFORE
UPDATE ON user_progress FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
-- Fonction pour mettre à jour average_rating
CREATE OR REPLACE FUNCTION update_resource_average_rating() RETURNS TRIGGER AS $$ BEGIN
UPDATE resources
SET average_rating = (
        SELECT AVG(rating)::DECIMAL(3, 2)
        FROM resource_ratings
        WHERE resource_id = COALESCE(NEW.resource_id, OLD.resource_id)
    )
WHERE id = COALESCE(NEW.resource_id, OLD.resource_id);
RETURN NULL;
END;
$$ language 'plpgsql';
-- Trigger pour mettre à jour average_rating automatiquement
CREATE TRIGGER update_avg_rating_on_insert
AFTER
INSERT ON resource_ratings FOR EACH ROW EXECUTE FUNCTION update_resource_average_rating();
CREATE TRIGGER update_avg_rating_on_update
AFTER
UPDATE ON resource_ratings FOR EACH ROW EXECUTE FUNCTION update_resource_average_rating();
CREATE TRIGGER update_avg_rating_on_delete
AFTER DELETE ON resource_ratings FOR EACH ROW EXECUTE FUNCTION update_resource_average_rating();
-- ============================================
-- COMMENTAIRES SUR LES TABLES
-- ============================================
COMMENT ON TABLE users IS 'Utilisateurs du système (étudiants, enseignants, admins)';
COMMENT ON TABLE roles IS 'Rôles avec permissions personnalisables';
COMMENT ON TABLE subjects IS 'Matières scolaires (Maths, PCT, Philo, etc.)';
COMMENT ON TABLE series IS 'Séries du BAC (A, B, C, D, etc.)';
COMMENT ON TABLE series_subjects IS 'Relation many-to-many entre séries et matières';
COMMENT ON TABLE chapters IS 'Chapitres spécifiques à chaque matière';
COMMENT ON TABLE resources IS 'Table centrale pour toutes les ressources';
COMMENT ON TABLE resource_types IS 'Types de ressources (épreuves, corrigés, fiches, exercices)';
COMMENT ON TABLE tags IS 'Tags pour catégoriser les ressources';
COMMENT ON TABLE resource_tags IS 'Liaison many-to-many entre ressources et tags';
COMMENT ON TABLE resource_ratings IS 'Évaluations des ressources par les utilisateurs';
COMMENT ON TABLE exam_papers IS 'Données spécifiques aux épreuves';
COMMENT ON TABLE solutions IS 'Données spécifiques aux corrigés';
COMMENT ON TABLE revision_sheets IS 'Données spécifiques aux fiches de révision';
COMMENT ON TABLE exercises IS 'Données spécifiques aux exercices';
COMMENT ON TABLE user_progress IS 'Suivi de la progression par chapitre';
COMMENT ON TABLE practice_sessions IS 'Sessions d''étude des utilisateurs';
COMMENT ON TABLE forum_categories IS 'Catégories du forum';
COMMENT ON TABLE forum_topics IS 'Sujets de discussion du forum';
COMMENT ON TABLE forum_posts IS 'Messages dans les discussions du forum';
-- ============================================
-- DONNÉES INITIALES
-- ============================================
-- Rôles par défaut
INSERT INTO roles (name, permissions)
VALUES (
        'student',
        '{"can_view": true, "can_comment": true, "can_upload": false}'::jsonb
    ),
    (
        'teacher',
        '{"can_view": true, "can_comment": true, "can_upload": true, "can_moderate": true}'::jsonb
    ),
    (
        'admin',
        '{"can_view": true, "can_comment": true, "can_upload": true, "can_moderate": true, "can_manage_users": true}'::jsonb
    );
-- Types de ressources
INSERT INTO resource_types (name, description)
VALUES ('exam_paper', 'Épreuve de BAC'),
    ('solution', 'Corrigé d''épreuve'),
    ('revision_sheet', 'Fiche de révision'),
    ('exercise', 'Exercice pratique');
-- Séries
INSERT INTO series (code, name, description)
VALUES ('A1', 'Série A1', 'Lettres et Sciences Humaines'),
    ('A2', 'Série A2', 'Lettres et Langues'),
    (
        'B',
        'Série B',
        'Sciences Économiques et Sociales'
    ),
    (
        'C',
        'Série C',
        'Mathématiques et Sciences Physiques'
    ),
    (
        'D',
        'Série D',
        'Mathématiques et Sciences de la Vie et de la Terre'
    );
-- Matières principales
INSERT INTO subjects (name, code, icon)
VALUES ('Mathématiques', 'MATH', '📐'),
    ('Physique-Chimie-Technologie', 'PCT', '⚛️'),
    ('Sciences de la Vie et de la Terre', 'SVT', '🧬'),
    ('Français', 'FR', '📖'),
    ('Philosophie', 'PHILO', '🤔'),
    ('Anglais', 'ANG', '🇬🇧'),
    ('Histoire-Géographie', 'HG', '🌍'),
    ('Éducation Civique et Morale', 'ECM', '⚖️');
-- Catégories du forum
INSERT INTO forum_categories (name, description, parent_id, order_num)
VALUES ('Général', 'Discussions générales', NULL, 1),
    ('Matières', 'Questions par matière', NULL, 2),
    (
        'Entraide',
        'Aide et support entre étudiants',
        NULL,
        3
    ),
    ('Annonces', 'Annonces officielles', NULL, 4);
-- Tags populaires
INSERT INTO tags (name)
VALUES ('Important'),
    ('Difficile'),
    ('Révision'),
    ('Exercices'),
    ('Corrigés détaillés'),
    ('Méthode'),
    ('Astuces'),
    ('Avant BAC'),
    ('Session normale'),
    ('Session rattrapage');