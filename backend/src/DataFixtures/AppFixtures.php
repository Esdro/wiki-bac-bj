<?php

namespace App\DataFixtures;

use App\Entity\Chapter;
use App\Entity\ExamPaper;
use App\Entity\Exercise;
use App\Entity\ForumCategory;
use App\Entity\ForumPost;
use App\Entity\ForumTopic;
use App\Entity\PracticeSession;
use App\Entity\Resource;
use App\Entity\ResourceRating;
use App\Entity\ResourceTag;
use App\Entity\ResourceType;
use App\Entity\RevisionSheet;
use App\Entity\Role;
use App\Entity\Series;
use App\Entity\SeriesSubject;
use App\Entity\Solution;
use App\Entity\Subject;
use App\Entity\Tag;
use App\Entity\User;
use App\Entity\UserProgress;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // ===== RÔLES =====
        $adminRole = (new Role())
            ->setName('ROLE_ADMIN')
            ->setPermissions(['can_read', 'can_write', 'can_delete', 'can_manage_users']);
        $manager->persist($adminRole);

        $teacherRole = (new Role())
            ->setName('ROLE_TEACHER')
            ->setPermissions(['can_read', 'can_write', 'can_create_resources']);
        $manager->persist($teacherRole);

        $studentRole = (new Role())
            ->setName('ROLE_STUDENT')
            ->setPermissions(['can_read']);
        $manager->persist($studentRole);

        // ===== UTILISATEURS =====
        $adminUser = (new User())
            ->setEmail('admin@example.com')
            ->setUsername('admin')
            ->setFullName('Administrateur Système')
            ->setPassword($this->passwordHasher->hashPassword(new User(), 'admin123'))
            ->setRole($adminRole)
            ->setStatus('active')
            ->setAvatarUrl('https://via.placeholder.com/150?text=Admin')
            ->setBio('Je suis l\'administrateur du système');
        $manager->persist($adminUser);

        $teacher1 = (new User())
            ->setEmail('teacher1@example.com')
            ->setUsername('teacher_jean')
            ->setFullName('Jean Dupont')
            ->setPassword($this->passwordHasher->hashPassword(new User(), 'teacher123'))
            ->setRole($teacherRole)
            ->setStatus('active')
            ->setAvatarUrl('https://via.placeholder.com/150?text=Teacher1')
            ->setBio('Professeur de mathématiques');
        $manager->persist($teacher1);

        $teacher2 = (new User())
            ->setEmail('teacher2@example.com')
            ->setUsername('teacher_marie')
            ->setFullName('Marie Martin')
            ->setPassword($this->passwordHasher->hashPassword(new User(), 'teacher123'))
            ->setRole($teacherRole)
            ->setStatus('active')
            ->setAvatarUrl('https://via.placeholder.com/150?text=Teacher2')
            ->setBio('Professeur de français');
        $manager->persist($teacher2);

        $student1 = (new User())
            ->setEmail('student1@example.com')
            ->setUsername('student_alice')
            ->setFullName('Alice Bernard')
            ->setPassword($this->passwordHasher->hashPassword(new User(), 'student123'))
            ->setRole($studentRole)
            ->setStatus('active')
            ->setAvatarUrl('https://via.placeholder.com/150?text=Student1')
            ->setBio('Élève en classe de Terminale');
        $manager->persist($student1);

        $student2 = (new User())
            ->setEmail('student2@example.com')
            ->setUsername('student_bob')
            ->setFullName('Bob Leclerc')
            ->setPassword($this->passwordHasher->hashPassword(new User(), 'student123'))
            ->setRole($studentRole)
            ->setStatus('active')
            ->setAvatarUrl('https://via.placeholder.com/150?text=Student2')
            ->setBio('Élève passionné par les sciences');
        $manager->persist($student2);

        // ===== TYPES DE RESSOURCES =====
        $videoType = (new ResourceType())
            ->setName('Vidéo');
        $manager->persist($videoType);

        $documentType = (new ResourceType())
            ->setName('Document PDF');
        $manager->persist($documentType);

        $articleType = (new ResourceType())
            ->setName('Article');
        $manager->persist($articleType);

        // ===== MATIÈRES =====
        $mathSubject = (new Subject())
            ->setName('Mathématiques')
            ->setCode('MATH');
        $manager->persist($mathSubject);

        $frenchSubject = (new Subject())
            ->setName('Français')
            ->setCode('FR');
        $manager->persist($frenchSubject);

        $scienceSubject = (new Subject())
            ->setName('Sciences')
            ->setCode('SCI');
        $manager->persist($scienceSubject);

        // ===== CHAPITRES =====
        $chapter1 = (new Chapter())
            ->setSubject($mathSubject)
            ->setTitle('Fonctions polynômes')
            ->setOrderNum(1)
            ->setDescription('Étude complète des fonctions polynômes du premier et second degré');
        $manager->persist($chapter1);

        $chapter2 = (new Chapter())
            ->setSubject($mathSubject)
            ->setTitle('Trigonométrie')
            ->setOrderNum(2)
            ->setDescription('Trigonométrie: sinus, cosinus, tangente');
        $manager->persist($chapter2);

        $chapter3 = (new Chapter())
            ->setSubject($frenchSubject)
            ->setTitle('Le Romantisme')
            ->setOrderNum(1)
            ->setDescription('Littérature du 19ème siècle');
        $manager->persist($chapter3);

        // ===== SÉRIES =====
        $series1 = (new Series())
            ->setCode('TLE-S')
            ->setName('Terminale S')
            ->setDescription('Classe de Terminale Scientifique');
        $manager->persist($series1);

        $series2 = (new Series())
            ->setCode('TLE-L')
            ->setName('Terminale L')
            ->setDescription('Classe de Terminale Littéraire');
        $manager->persist($series2);

        // ===== SÉRIES-MATIÈRES =====
        $ss1 = (new SeriesSubject())
            ->setSeries($series1)
            ->setSubject($mathSubject);
        $manager->persist($ss1);

        $ss2 = (new SeriesSubject())
            ->setSeries($series1)
            ->setSubject($scienceSubject);
        $manager->persist($ss2);

        $ss3 = (new SeriesSubject())
            ->setSeries($series2)
            ->setSubject($frenchSubject);
        $manager->persist($ss3);

        // ===== ÉTIQUETTES =====
        $tag1 = (new Tag())->setNameWithSlug('Important');
        $manager->persist($tag1);

        $tag2 = (new Tag())->setNameWithSlug('Difficile');
        $manager->persist($tag2);

        $tag3 = (new Tag())->setNameWithSlug('Examen');
        $manager->persist($tag3);

        // ===== RESSOURCES =====
        $resource1 = (new Resource())
            ->setTitle('Introduction aux Polynômes')
            ->setDescription('Vidéo pédagogique sur les polynômes de base')
            ->setType($videoType)
            ->setSubject($mathSubject)
            ->setChapter($chapter1)
            ->setSeries($series1)
            ->setYear(2024)
            ->setFileUrl('https://example.com/video1.mp4')
            ->setUser($teacher1);
        $manager->persist($resource1);

        $resource2 = (new Resource())
            ->setTitle('Dérivées et étude de fonction')
            ->setDescription('Document complet sur les dérivées')
            ->setType($documentType)
            ->setSubject($mathSubject)
            ->setChapter($chapter1)
            ->setSeries($series1)
            ->setYear(2024)
            ->setFileUrl('https://example.com/derivees.pdf')
            ->setUser($teacher1);
        $manager->persist($resource2);

        $resource3 = (new Resource())
            ->setTitle('Guide Complet du Romantisme')
            ->setDescription('Article détaillé sur le mouvement romantique')
            ->setType($articleType)
            ->setSubject($frenchSubject)
            ->setChapter($chapter3)
            ->setSeries($series2)
            ->setYear(2024)
            ->setFileUrl('https://example.com/romantisme.html')
            ->setUser($teacher2);
        $manager->persist($resource3);

        // ===== RESSOURCES-ÉTIQUETTES =====
        $rt1 = (new ResourceTag())
            ->setResource($resource1)
            ->setTag($tag1);
        $manager->persist($rt1);

        $rt2 = (new ResourceTag())
            ->setResource($resource2)
            ->setTag($tag2);
        $manager->persist($rt2);

        $rt3 = (new ResourceTag())
            ->setResource($resource3)
            ->setTag($tag3);
        $manager->persist($rt3);

        // ===== ÉVALUATIONS DE RESSOURCES =====
        $rating1 = (new ResourceRating())
            ->setResource($resource1)
            ->setUser($student1)
            ->setRating(5)
            ->setComment('Excellente vidéo, très claire et complète!');
        $manager->persist($rating1);

        $rating2 = (new ResourceRating())
            ->setResource($resource2)
            ->setUser($student2)
            ->setRating(4)
            ->setComment('Très utile, mais quelques exemples de plus auraient été bienvenus');
        $manager->persist($rating2);

        // ===== EXERCICES =====
        $exercise1 = (new Exercise())
            ->setResource($resource1)
            ->setQuestion('Qu\'est-ce qu\'un polynôme de degré 2?')
            ->setAnswer('Un polynôme de degré 2 est une expression de la forme ax² + bx + c, où a ≠ 0.')
            ->setDifficultyLevel(1);
        $manager->persist($exercise1);

        $exercise2 = (new Exercise())
            ->setResource($resource2)
            ->setQuestion('Quelle est la dérivée de f(x) = 3x² + 2x + 1?')
            ->setAnswer('f\'(x) = 6x + 2')
            ->setDifficultyLevel(2);
        $manager->persist($exercise2);

        // Flush pour générer les IDs
        $manager->flush();

        // ===== SOLUTIONS =====
        $solution1 = (new Solution())
            ->setResource($resource1)
            ->setContentText('Un polynôme de degré 2, aussi appelé fonction quadratique, est une fonction f(x) = ax² + bx + c où a, b et c sont des constantes réelles et a ≠ 0.');
        $manager->persist($solution1);

        // ===== EXAMENS =====
        $examResource = (new Resource())
            ->setTitle('Bac Blanc Mathématiques 2024')
            ->setDescription('Sujet du Bac Blanc')
            ->setType($documentType)
            ->setSubject($mathSubject)
            ->setYear(2024)
            ->setFileUrl('https://example.com/bacblanc2024.pdf')
            ->setUser($teacher1);
        $manager->persist($examResource);
        
        $manager->flush();
        
        $examPaper1 = (new ExamPaper())
            ->setResource($examResource)
            ->setExamType('Bac Blanc')
            ->setDurationMinutes(180);
        $manager->persist($examPaper1);

        // ===== FEUILLES DE RÉVISION =====
        $revisionResource = (new Resource())
            ->setTitle('Résumé Polynômes')
            ->setDescription('Feuille de révision')
            ->setType($articleType)
            ->setSubject($mathSubject)
            ->setUser($teacher1);
        $manager->persist($revisionResource);
        
        $manager->flush();
        
        $revisionSheet1 = (new RevisionSheet())
            ->setResource($revisionResource)
            ->setContent('# Résumé des Polynômes\n\n- Définition\n- Propriétés\n- Exercices pratiques');
        $manager->persist($revisionSheet1);

        // ===== SÉANCES DE PRATIQUE =====
        $practiceSession1 = (new PracticeSession())
            ->setUser($student1)
            ->setSubject($mathSubject)
            ->setStartTime(new \DateTimeImmutable('2024-12-01 14:00:00'))
            ->setDurationMinutes(60)
            ->setResourcesUsed(['resource1', 'resource2']);
        $manager->persist($practiceSession1);

        $practiceSession2 = (new PracticeSession())
            ->setUser($student2)
            ->setSubject($frenchSubject)
            ->setStartTime(new \DateTimeImmutable('2024-12-02 15:30:00'))
            ->setDurationMinutes(45)
            ->setResourcesUsed(['resource3']);
        $manager->persist($practiceSession2);

        // ===== CATÉGORIES DE FORUM =====
        $forumCategory1 = (new ForumCategory())
            ->setName('Mathématiques')
            ->setDescription('Discussions sur les mathématiques')
            ->setOrderNum(1);
        $manager->persist($forumCategory1);

        $forumCategory2 = (new ForumCategory())
            ->setName('Général')
            ->setDescription('Discussions générales')
            ->setOrderNum(2);
        $manager->persist($forumCategory2);

        $manager->flush();

        // ===== SUJETS DE FORUM =====
        $forumTopic1 = (new ForumTopic())
            ->setTitle('Comment résoudre une équation du second degré?')
            ->setCategory($forumCategory1)
            ->setUser($student1)
            ->setContent('J\'ai du mal à comprendre les équations du second degré. Quelqu\'un peut m\'aider?');
        $manager->persist($forumTopic1);

        $forumTopic2 = (new ForumTopic())
            ->setTitle('Bienvenue sur le forum')
            ->setCategory($forumCategory2)
            ->setUser($adminUser)
            ->setContent('Bienvenue sur notre forum pédagogique! N\'hésitez pas à poser vos questions.');
        $manager->persist($forumTopic2);
        
        $manager->flush();

        // ===== MESSAGES DE FORUM =====
        $forumPost1 = (new ForumPost())
            ->setTopic($forumTopic1)
            ->setUser($teacher1)
            ->setContent('Pour résoudre une équation ax² + bx + c = 0, tu peux utiliser la formule du discriminant: Δ = b² - 4ac');
        $manager->persist($forumPost1);

        $forumPost2 = (new ForumPost())
            ->setTopic($forumTopic1)
            ->setUser($student2)
            ->setContent('Merci beaucoup pour cette explication! C\'est très clair maintenant.');
        $manager->persist($forumPost2);

        // ===== PROGRESSIONS UTILISATEUR =====
        $userProgress1 = (new UserProgress())
            ->setUser($student1)
            ->setChapter($chapter1)
            ->setStatus('in_progress')
            ->setConfidenceLevel(4);
        $manager->persist($userProgress1);

        $userProgress2 = (new UserProgress())
            ->setUser($student2)
            ->setChapter($chapter3)
            ->setStatus('in_progress')
            ->setConfidenceLevel(3);
        $manager->persist($userProgress2);

        $manager->flush();
    }
}
