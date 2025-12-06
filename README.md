# Wiki-BAC-BJ

![Logo Wiki-BAC-BJ](https://i.ibb.co/YBY5wcVM/DALL-E-2025-03-29-15-56-57-A-modern-minimalistic-logo-for-Wiki-BAC-BJ-an-open-source-educational-pla.webp")

## 📚 Projet Open Source pour mutualiser les ressources sur le BAC au Bénin

Bonjour à toutes et à tous !

Ceci est un projet Open Source ayant pour but de **rendre l'examen du BAC plus facile au Bénin**. 

Si vous avez fréquenté au Bénin, vous savez à quel point trouver des épreuves des années antérieures, trouver des réponses à des tas de sujets, trouver des exercices adaptés aux cours donnés dans les lycées et collèges du Bénin est difficile. Alors **Wiki-BAC-BJ** viendra proposer des solutions à tous ces problèmes.

---

## 🔍 Notre vision

Wiki-BAC-BJ sera une plateforme collaborative où les élèves, enseignants, parents et toute personne souhaitant contribuer pourront partager des ressources éducatives liées au BAC béninois. Notre objectif est de démocratiser l'accès aux connaissances et de donner à chaque élève, quelle que soit sa situation géographique ou socio-économique, les mêmes chances de réussite à cet examen crucial.

## 🎯 Ce que nous proposons

### 1. Une banque d'épreuves complète

- **Épreuves du BAC** des 20 dernières années, classées par série, matière et année
- **Corrigés détaillés** pour chaque épreuve
- **Commentaires pédagogiques** sur les points importants

### 2. Des fiches de révision

- **Résumés de cours** par chapitre, conformes au programme national
- **Exercices types** avec solutions détaillées
- **Méthodologies** pour aborder chaque type d'épreuve

### 3. Un forum d'entraide

- **Questions/réponses** entre élèves
- **Conseils** d'anciens bacheliers
- **Interventions** d'enseignants volontaires

### 4. Ressources complémentaires

- **Conseils** pour gérer le stress
- **Techniques de mémorisation** efficace
- **Plannings de révision** adaptés à chaque série

---

## 🤝 Comment contribuer ?

Ce projet ne pourra réussir que grâce à la contribution de chacun. Voici comment vous pouvez nous aider :

1. **Partagez vos anciens documents** : épreuves, corrigés, fiches de révision
2. **Apportez votre expertise** : si vous êtes enseignant ou ancien élève
3. **Participez au développement** : si vous avez des compétences en développement web, design ou rédaction
4. **Faites passer le mot** : parlez de cette initiative autour de vous

---

## 🚀 Installation et Configuration

### Prérequis

- Docker et Docker Compose
- PHP 8.2+ (pour développement local)
- Node.js 18+ (pour le frontend)
- PostgreSQL 15+ (inclus dans Docker)

### Configuration des variables d'environnement

Les fichiers de configuration sensibles (`.env`, `.env.local`, etc.) **ne doivent JAMAIS être commités** dans le repository.

#### Backend (Symfony)

1. Copiez le fichier template :
```bash
cp backend/.env.example backend/.env.local
```

2. Configurez les variables dans `backend/.env.local` :
```env
APP_ENV=dev
APP_SECRET=your_secret_key_here
DATABASE_URL="postgresql://user:password@database:5432/wikibac"
```

> ⚠️ **IMPORTANT** : Chaque développeur doit avoir son propre fichier `.env.local` avec ses propres valeurs. Ces fichiers sont ignorés par git.

#### Frontend (Angular)

Les variables d'environnement du frontend sont gérées dans `frontend/src/environments/`.

### Démarrage avec Docker

```bash
# Lancer tous les services
docker-compose up -d

# Backend sera accessible à : http://localhost:8000
# Frontend sera accessible à : http://localhost:4200
```

---

## 📅 Feuille de route du projet

| Phase | Période | Objectifs |
|-------|---------|-----------|
| **Phase 1** | Avril-Juin 2025 | Collecte des ressources initiales et mise en place de la structure du site |
| **Phase 2** | Juillet-Septembre 2025 | Lancement de la version beta et premiers retours d'utilisateurs |
| **Phase 3** | Octobre-Décembre 2025 | Amélioration de la plateforme et enrichissement des contenus |
| **Phase 4** | Janvier 2026 | Lancement officiel pour les révisions du BAC 2026 |

## 🌟 Rejoignez-nous

Nous sommes convaincus que l'éducation est un droit fondamental et que le partage des connaissances est la clé pour améliorer notre système éducatif.

Pour participer ou en savoir plus:

- 📧 Contactez-nous: [contact@wiki-bac-bj.org](mailto:contact@wiki-bac-bj.org)
- 💬 Rejoignez notre groupe: [t.me/WikiBACBJ](https://t.me/WikiBACBJ)
- 🌐 Suivez-nous sur les réseaux sociaux: [@wikibacbj](https://twitter.com/wikibacbj)

**Ensemble, rendons le BAC plus accessible pour tous les étudiants béninois !**

---

> *"L'éducation est l'arme la plus puissante pour changer le monde."* - Nelson Mandela

---

### Contributeurs

- [Esdras Onionkiton](https://code-addict.net)
  
### Licence

Ce projet est sous licence [MIT](LICENSE)
