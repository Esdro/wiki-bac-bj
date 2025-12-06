# 📘 Interfaces TypeScript - Documentation Complète

## 📋 Vue d'ensemble

Ce dossier contient l'ensemble des interfaces TypeScript générées pour le backend Symfony de Wiki-BAC-BJ. Ces interfaces offrent un **typage complet** et une **vérification de type au moment de la compilation** pour toutes vos interactions API.

---

## 📂 Structure des fichiers

```
interfaces/
├── entities.ts                  # Toutes les interfaces d'entités
├── server-response-data.ts      # Wrappers de réponse API
├── index.ts                     # Export central
└── USAGE_EXAMPLES.ts            # Exemples de code (ce fichier)
```

---

## 🚀 Démarrage rapide

### Installation
Les interfaces sont déjà prêtes à l'emploi. Aucune installation supplémentaire requise !

### Import simple

```typescript
// Importer les interfaces dont vous avez besoin
import { IUser, IResource, ICreateResourceDto } from '@app/interfaces';

// Ou tout importer
import * as Models from '@app/interfaces';
```

---

## 📚 Catégories d'Interfaces

### 1️⃣ Interfaces Principales (Entities)

Les interfaces principales représentent les entités du backend :

| Interface | Description |
|-----------|-------------|
| `IUser` | Utilisateur du système |
| `IRole` | Rôle avec permissions |
| `IResource` | Ressource pédagogique (PDF, vidéo, etc.) |
| `ISubject` | Matière scolaire |
| `IChapter` | Chapitre d'une matière |
| `ISeries` | Série d'examen (BAC A4, BAC C, etc.) |
| `IExercise` | Exercice avec question/réponse |
| `ISolution` | Solution d'exercice |
| `IExamPaper` | Épreuve d'examen |
| `IRevisionSheet` | Fiche de révision |
| `IPracticeSession` | Séance de pratique |
| `IUserProgress` | Progression utilisateur |
| `IForumTopic` | Topic du forum |
| `IForumPost` | Post dans un forum |
| `IForumCategory` | Catégorie du forum |

### 2️⃣ Interfaces DTO (Data Transfer Object)

Pour créer/modifier les ressources :

```typescript
// Create DTOs
ICreateUserDto
ICreateResourceDto
ICreateForumTopicDto
// ... et bien d'autres

// Update DTOs
IUpdateUserDto
IUpdateResourceDto
IUpdateForumTopicDto
// ... et bien d'autres
```

### 3️⃣ Interfaces de Réponse API

Pour typer les réponses du serveur :

```typescript
// Réponse simple
IApiResponse<T>

// Réponse paginée
IPaginatedResponse<T>

// Erreur
IApiErrorResponse

// Réponse générique du serveur
ServerResponseData<T>
```

---

## 💡 Exemples d'utilisation

### Exemple 1 : Service HTTP TypeSafe

```typescript
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { IResource, IPaginatedResponse } from '@app/interfaces';
import { Observable } from 'rxjs';

@Injectable({ providedIn: 'root' })
export class ResourceService {
  constructor(private http: HttpClient) {}

  // ✅ Entièrement typé !
  getResources(): Observable<IPaginatedResponse<IResource>> {
    return this.http.get<IPaginatedResponse<IResource>>(
      'http://localhost:8000/api/resources'
    );
  }
}
```

### Exemple 2 : Composant avec Signals

```typescript
import { Component, signal } from '@angular/core';
import { IResource } from '@app/interfaces';
import { ResourceService } from './resource.service';

@Component({
  selector: 'app-resources',
  template: `
    <div *ngFor="let resource of resources()">
      <h3>{{ resource.title }}</h3>
      <p>{{ resource.description }}</p>
      <span>{{ resource.viewCount }} vues</span>
    </div>
  `
})
export class ResourcesComponent {
  resources = signal<IResource[]>([]);

  constructor(service: ResourceService) {
    service.getResources().subscribe(response => {
      this.resources.set(response.data);
    });
  }
}
```

### Exemple 3 : Formulaire Réactif avec DTO

```typescript
import { Component } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { ICreateResourceDto } from '@app/interfaces';
import { ResourceService } from './resource.service';

@Component({
  selector: 'app-create-resource'
})
export class CreateResourceComponent {
  form: FormGroup;

  constructor(
    fb: FormBuilder,
    private resourceService: ResourceService
  ) {
    this.form = fb.group({
      title: [''],
      description: [''],
      typeId: [''],
      subjectId: ['']
    });
  }

  submit(): void {
    // ✅ TypeScript vérifie que dto correspond à ICreateResourceDto
    const dto: ICreateResourceDto = this.form.value;
    this.resourceService.createResource(dto).subscribe(response => {
      console.log('Ressource créée:', response.data);
    });
  }
}
```

### Exemple 4 : Utilisation de Filtres Typés

```typescript
import { IResourceFilter } from '@app/interfaces';

// ✅ TypeScript vous guide avec l'autocomplétion
const filter: IResourceFilter = {
  subjectId: 'math-uuid',
  status: 'published',        // ✅ Vérification au moment du typage
  sortBy: 'viewCount',        // ✅ Vérification au moment du typage
  page: 1,
  pageSize: 20
};

this.resourceService.getResources(filter).subscribe(...);
```

---

## 🔧 Cas d'Usage Courants

### Récupérer tous les utilisateurs

```typescript
import { Observable } from 'rxjs';
import { IUser, IPaginatedResponse } from '@app/interfaces';

getUsers(): Observable<IPaginatedResponse<IUser>> {
  return this.http.get<IPaginatedResponse<IUser>>('/api/users');
}
```

### Créer un nouvel utilisateur

```typescript
import { ICreateUserDto, IApiResponse, IUser } from '@app/interfaces';

createUser(dto: ICreateUserDto): Observable<IApiResponse<IUser>> {
  return this.http.post<IApiResponse<IUser>>('/api/users', dto);
}
```

### Mettre à jour une ressource

```typescript
import { IUpdateResourceDto, IResource } from '@app/interfaces';

updateResource(id: string, dto: IUpdateResourceDto): Observable<IResource> {
  return this.http.patch<IResource>(`/api/resources/${id}`, dto);
}
```

### Récupérer les topics du forum

```typescript
import { IForumTopic, IForumTopicFilter } from '@app/interfaces';

getForumTopics(filter: IForumTopicFilter): Observable<IPaginatedResponse<IForumTopic>> {
  return this.http.get<IPaginatedResponse<IForumTopic>>('/api/forum/topics', {
    params: filter as any
  });
}
```

---

## ✨ Avantages du Typage Complet

### ✅ Autocomplétion IDE

```typescript
// L'IDE vous propose automatiquement les propriétés
const user: IUser = { /* ... */ };
user.email  // ✅ Suggestion
user.foo    // ❌ Erreur: 'foo' n'existe pas
```

### ✅ Vérification de Type au Compile

```typescript
const filter: IResourceFilter = {
  status: 'invalid'  // ❌ Erreur: 'invalid' n'est pas un ResourceStatus valide
};
```

### ✅ Documentation Intégrée

Chaque interface est documentée. Passez votre souris dessus dans VS Code !

### ✅ Refactoring en Sécurité

Si la structure d'une entité change, TypeScript vous prévient immédiatement où mettre à jour le code.

---

## 📊 Énums et Types Personnalisés

### Types de Statut

```typescript
type EntityStatus = 'active' | 'inactive' | 'draft' | 'published' | 'archived';
type UserStatus = 'active' | 'inactive' | 'banned' | 'pending';
type ResourceStatus = 'draft' | 'published' | 'archived' | 'pending_review';
type SolutionType = 'text' | 'image' | 'video' | 'document';
```

### Utilisation

```typescript
const resource: IResource = {
  // ...
  status: 'published'  // ✅ Correct
  // status: 'invalid'  // ❌ Erreur TypeScript
};
```

---

## 🎯 Bonnes Pratiques

### ✅ À Faire

```typescript
// ✅ Utiliser les interfaces dans vos services
getResource(id: string): Observable<IApiResponse<IResource>> {
  return this.http.get<IApiResponse<IResource>>(`/api/resources/${id}`);
}

// ✅ Typer vos signaux
resources = signal<IResource[]>([]);

// ✅ Utiliser les DTOs pour la création/mise à jour
createResource(dto: ICreateResourceDto) {
  return this.http.post<IApiResponse<IResource>>('/api/resources', dto);
}
```

### ❌ À Éviter

```typescript
// ❌ Utiliser 'any'
getResource(): Observable<any> { /* ... */ }

// ❌ Typage insuffisant
resources: any[] = [];

// ❌ Ne pas utiliser les DTOs
postData({ title, description }: any) { /* ... */ }
```

---

## 🔄 Mise à Jour des Interfaces

Quand le backend change, mettez à jour `entities.ts` en conséquence.

### Processus de mise à jour

1. L'équipe backend modifie une entité
2. Mettre à jour l'interface TypeScript correspondante dans `entities.ts`
3. TypeScript vous signalera tous les endroits du code à modifier
4. Commit et push !

---

## 📞 Support & Questions

Pour toute question sur les interfaces ou le typage TypeScript :

- Consultez les exemples dans `USAGE_EXAMPLES.ts`
- Vérifiez la structure dans `entities.ts`
- Utilisez l'autocomplétion IDE (Ctrl+Space ou Cmd+Space)

---

## 📈 Performance & Optimisation

Le typage TypeScript :
- ✅ **Zéro impact** sur la performance à l'exécution
- ✅ Réduit les bugs à la compilation
- ✅ Améliore la maintenabilité
- ✅ Facilite la collaboration en équipe

---

## 🎓 Ressources Supplémentaires

- [Documentation TypeScript Officielle](https://www.typescriptlang.org/docs/)
- [Angular Type Safety Guide](https://angular.io/guide/typed-forms)
- [Vos exemples locaux](./USAGE_EXAMPLES.ts)

---

**Généré pour Wiki-BAC-BJ** 🇧🇯 | **Frontend Angular 21** | **API Symfony 6+**
