# Schéma de Base de Données - Wiki BAC BJ

## Diagramme ER (Entity-Relationship)

```mermaid
erDiagram
    %% Entités principales
    User ||--o{ Resource : "crée"
    User ||--o{ ResourceRating : "note"
    User ||--o{ UserProgress : "progresse"
    User ||--o{ PracticeSession : "pratique"
    User ||--o{ ForumTopic : "crée"
    User ||--o{ ForumPost : "poste"
    User }o--|| Role : "a un"

    %% Matières et Séries
    Subject ||--o{ Chapter : "contient"
    Subject ||--o{ Resource : "possède"
    Subject ||--o{ PracticeSession : "pour"
    Subject ||--o{ SeriesSubject : "dans"
    
    Series ||--o{ SeriesSubject : "contient"
    Series ||--o{ Resource : "possède"

    %% Chapitres
    Chapter ||--o{ Resource : "contient"
    Chapter ||--o{ UserProgress : "suivi"

    %% Ressources
    Resource }o--|| ResourceType : "de type"
    Resource }o--o| Subject : "appartient"
    Resource }o--o| Chapter : "dans"
    Resource }o--o| Series : "pour"
    Resource }o--|| User : "créé par"
    Resource ||--o{ ResourceTag : "taggé"
    Resource ||--o{ ResourceRating : "noté"
    Resource ||--o| ExamPaper : "peut être"
    Resource ||--o| Solution : "peut être"
    Resource ||--o| RevisionSheet : "peut être"
    Resource ||--o| Exercise : "peut être"

    %% Tags
    Tag ||--o{ ResourceTag : "utilisé dans"

    %% Types de ressources
    ResourceType ||--o{ Resource : "type de"

    %% Forum
    ForumCategory ||--o{ ForumTopic : "contient"
    ForumTopic ||--o{ ForumPost : "contient"

    %% Tables
    User {
        uuid id PK
        string email UK
        string username UK
        string password_hash
        string full_name
        uuid role_id FK
        string avatar_url
        text bio
        datetime created_at
        datetime last_login
        string status
    }

    Role {
        uuid id PK
        string name UK
        text description
    }

    Subject {
        uuid id PK
        string name
        string code UK
        string icon
    }

    Chapter {
        uuid id PK
        uuid subject_id FK
        string title
        int order_num
        text description
    }

    Series {
        uuid id PK
        string code UK
        string name
        text description
    }

    SeriesSubject {
        uuid id PK
        uuid series_id FK
        uuid subject_id FK
        decimal coefficient
    }

    ResourceType {
        uuid id PK
        string name UK
        text description
    }

    Resource {
        uuid id PK
        string title
        text description
        uuid type_id FK
        uuid subject_id FK
        uuid chapter_id FK
        uuid series_id FK
        uuid user_id FK
        int year
        string file_url
        string thumbnail_url
        string status
        int view_count
        int download_count
        decimal average_rating
        datetime created_at
        datetime updated_at
    }

    Tag {
        uuid id PK
        string name UK
    }

    ResourceTag {
        uuid id PK
        uuid resource_id FK
        uuid tag_id FK
    }

    ResourceRating {
        uuid id PK
        uuid resource_id FK
        uuid user_id FK
        int rating
        text comment
        datetime created_at
    }

    ExamPaper {
        uuid id PK
        uuid resource_id FK
        string session
        int duration
    }

    Solution {
        uuid id PK
        uuid resource_id FK
        text content
    }

    RevisionSheet {
        uuid id PK
        uuid resource_id FK
        text content
    }

    Exercise {
        uuid id PK
        uuid resource_id FK
        text question
        text answer
        int difficulty_level
    }

    UserProgress {
        uuid id PK
        uuid user_id FK
        uuid chapter_id FK
        boolean is_completed
        datetime completed_at
    }

    PracticeSession {
        uuid id PK
        uuid user_id FK
        uuid subject_id FK
        decimal score
        int total_questions
        int correct_answers
        datetime created_at
    }

    ForumCategory {
        uuid id PK
        string name UK
        text description
    }

    ForumTopic {
        uuid id PK
        uuid category_id FK
        uuid user_id FK
        string title
        text content
        datetime created_at
        datetime updated_at
        int view_count
        boolean is_pinned
        boolean is_locked
    }

    ForumPost {
        uuid id PK
        uuid topic_id FK
        uuid user_id FK
        text content
        datetime created_at
        datetime updated_at
    }
```

## Structure de la Base de Données

### 🔐 Gestion des Utilisateurs
- **User** : Utilisateurs de la plateforme
- **Role** : Rôles/permissions des utilisateurs

### 📚 Structure Académique
- **Subject** : Matières (Math, Physique, etc.)
- **Chapter** : Chapitres dans chaque matière
- **Series** : Séries du BAC (A, C, D, etc.)
- **SeriesSubject** : Table de liaison entre séries et matières (avec coefficient)

### 📄 Gestion des Ressources
- **Resource** : Ressource centrale (peut être exam, solution, exercice, etc.)
- **ResourceType** : Types de ressources (Examen, Corrigé, Fiche, etc.)
- **Tag** : Tags pour catégoriser les ressources
- **ResourceTag** : Table de liaison ressources-tags

### 📝 Types Spécifiques de Ressources
- **ExamPaper** : Sujets d'examens
- **Solution** : Corrigés d'examens
- **RevisionSheet** : Fiches de révision
- **Exercise** : Exercices avec questions/réponses

### ⭐ Interactions Utilisateurs
- **ResourceRating** : Notes et commentaires sur les ressources
- **UserProgress** : Suivi de progression par chapitre
- **PracticeSession** : Sessions de pratique/quiz

### 💬 Forum
- **ForumCategory** : Catégories du forum
- **ForumTopic** : Sujets de discussion
- **ForumPost** : Messages dans les discussions

## Relations Principales

### 1️⃣ Un utilisateur peut :
- Créer plusieurs ressources
- Noter plusieurs ressources
- Suivre sa progression sur plusieurs chapitres
- Participer à plusieurs sessions de pratique
- Créer et répondre dans le forum

### 2️⃣ Une matière (Subject) :
- Contient plusieurs chapitres
- Est associée à plusieurs séries (via SeriesSubject)
- Possède plusieurs ressources

### 3️⃣ Une ressource (Resource) :
- Est créée par un utilisateur
- Appartient à un type spécifique
- Peut être liée à une matière, un chapitre et/ou une série
- Peut être un examen, une solution, une fiche ou un exercice
- Peut avoir plusieurs tags et notes

### 4️⃣ Le forum :
- Organisé en catégories
- Les catégories contiennent des topics
- Les topics contiennent des posts
- Chaque topic/post est créé par un utilisateur

## Cardinalités Importantes

- **1:N** (One-to-Many) : Un sujet a plusieurs chapitres
- **N:M** (Many-to-Many) : Matières ↔ Séries (via SeriesSubject)
- **N:M** : Ressources ↔ Tags (via ResourceTag)
- **1:1** : Resource ↔ ExamPaper/Solution/RevisionSheet/Exercise

## Clés et Contraintes

- 🔑 Toutes les tables utilisent des **UUID v7** comme clés primaires
- 🔒 Contraintes d'unicité sur :
  - User: email, username
  - Subject: code
  - Series: code
  - ResourceType: name
  - Tag: name
  - ForumCategory: name
  - SeriesSubject: (series_id, subject_id)
  - ResourceTag: (resource_id, tag_id)

## Gestion des Suppressions (ON DELETE)

- **CASCADE** : La suppression est propagée (ex: suppression d'un user supprime ses resources)
- **SET NULL** : La clé étrangère est mise à NULL (ex: suppression d'un subject n'efface pas les resources)
