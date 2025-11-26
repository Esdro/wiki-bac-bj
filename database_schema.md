# Database Schema Design - Wiki-BAC-BJ

Based on the requirements in `README.md`, here is the proposed database schema.

## Entity-Relationship Diagram

```mermaid
erDiagram
    USER ||--o{ EXAM_PAPER : uploads
    USER ||--o{ SOLUTION : uploads
    USER ||--o{ REVISION_SHEET : creates
    USER ||--o{ EXERCISE : creates
    USER ||--o{ FORUM_THREAD : starts
    USER ||--o{ FORUM_POST : writes

    SUBJECT ||--o{ EXAM_PAPER : has
    SUBJECT ||--o{ CHAPTER : contains
    
    SERIES ||--o{ EXAM_PAPER : has
    
    CHAPTER ||--o{ REVISION_SHEET : contains
    CHAPTER ||--o{ EXERCISE : contains

    EXAM_PAPER ||--o{ SOLUTION : has

    FORUM_THREAD ||--o{ FORUM_POST : contains

    USER {
        int id PK
        string email
        string password
        string username
        string role "STUDENT, TEACHER, ADMIN"
        datetime created_at
    }

    SUBJECT {
        int id PK
        string name "Maths, PCT, etc."
        string code
    }

    SERIES {
        int id PK
        string name "A, B, C, D"
        string description
    }

    CHAPTER {
        int id PK
        string title
        int subject_id FK
    }

    EXAM_PAPER {
        int id PK
        string title
        int year
        string file_url
        string status "PENDING, APPROVED"
        int subject_id FK
        int series_id FK
        int uploader_id FK
        datetime created_at
    }

    SOLUTION {
        int id PK
        string title
        string content_text
        string file_url
        int exam_paper_id FK
        int author_id FK
        datetime created_at
    }

    REVISION_SHEET {
        int id PK
        string title
        string content
        string file_url
        int chapter_id FK
        int author_id FK
        datetime created_at
    }

    EXERCISE {
        int id PK
        string question
        string answer
        int chapter_id FK
        int author_id FK
        datetime created_at
    }

    FORUM_THREAD {
        int id PK
        string title
        string content
        int author_id FK
        datetime created_at
        boolean is_solved
    }

    FORUM_POST {
        int id PK
        string content
        int thread_id FK
        int author_id FK
        datetime created_at
        boolean is_accepted_answer
    }
```

## Table Descriptions

### Core Data
- **USER**: Stores all registered users (Students, Teachers, Admins).
- **SUBJECT**: School subjects (e.g., Mathematics, Physics, Philosophy).
- **SERIES**: BAC Series (e.g., A1, A2, B, C, D).
- **CHAPTER**: Specific chapters within a subject to categorize revisions and exercises.

### Resources
- **EXAM_PAPER (Epreuves)**: The core resource. Linked to a Subject, Series, and Year. Has a status for moderation.
- **SOLUTION (Corrigés)**: Solutions linked to a specific Exam Paper. Can be text or a file.
- **REVISION_SHEET (Fiches)**: Educational content linked to a specific Chapter.
- **EXERCISE**: Practice questions linked to a Chapter.

### Community
- **FORUM_THREAD**: Discussion topics created by users.
- **FORUM_POST**: Replies to threads.
