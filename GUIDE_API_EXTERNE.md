# Guide d'utilisation de l'API externe

Ce guide explique comment le front-end utilise l'API externe Node.js selon la documentation Postman.

## 🔧 Configuration

L'API externe doit être démarrée sur `http://localhost:3000` avant d'utiliser l'application.

### Démarrage de l'API

```bash
npm run dev
```

## 📁 Fichiers modifiés

### 1. `api-client.js`
Client API créé pour faciliter les appels à l'API externe. Il fournit des méthodes pour :
- Authentification (login, register, getCurrentUser)
- Gestion des utilisateurs (getAllUsers, createStudent, getUser, updateProfile)
- Progression (updateProgress, getProgress)

### 2. `index.html`
- Modifié pour utiliser `/api/auth/login` au lieu de `admin/api.php`
- Sauvegarde le token JWT dans localStorage
- Gère les rôles ADMIN et STUDENT

### 3. `script.js`
- `syncLessonToAPI()` : Synchronise une leçon individuelle avec l'API
- `sendProgressToServer()` : Adapté pour utiliser l'API externe avec fallback vers l'ancienne API PHP
- `markLessonAsCompleted()` : Utilise maintenant l'API externe
- `markLessonAsIncomplete()` : Utilise maintenant l'API externe

### 4. `lesson.html`
- `markAsCompleted()` : Adapté pour utiliser l'API externe
- Ajout du script `api-client.js`

### 5. `dashboard.html`
- Ajout du script `api-client.js`

## 🔐 Authentification

### Connexion

L'application utilise maintenant `/api/auth/login` :

```javascript
const data = await window.apiClient.login(username, password);
// Sauvegarde automatique du token dans localStorage
localStorage.setItem('token', data.token);
```

### Token JWT

Le token est automatiquement inclus dans toutes les requêtes authentifiées via le header :
```
Authorization: Bearer <token>
```

## 📊 Progression

### Mise à jour d'une leçon

Chaque leçon est synchronisée individuellement avec l'API :

```javascript
await window.apiClient.updateProgress({
    moduleId: 'module1',
    lessonId: 'lesson1',
    completed: true,
    timeSpent: 3600
});
```

### Synchronisation globale

La fonction `sendProgressToServer()` synchronise toutes les leçons du localStorage avec l'API externe.

## 🔄 Fallback

Si l'API externe n'est pas disponible, le système utilise automatiquement l'ancienne API PHP (`admin/api.php`) comme fallback.

## 📝 Endpoints utilisés

### Authentification
- `POST /api/auth/login` - Connexion
- `POST /api/auth/register` - Inscription (disponible via apiClient)
- `GET /api/auth/me` - Obtenir l'utilisateur connecté (disponible via apiClient)

### Progression
- `POST /api/progress` - Mettre à jour la progression
- `GET /api/progress` - Obtenir la progression de l'utilisateur connecté

### Utilisateurs (pour admin)
- `GET /api/users` - Liste tous les utilisateurs
- `POST /api/users` - Créer un étudiant
- `GET /api/users/:username` - Obtenir un utilisateur
- `PUT /api/users/:username/profile` - Mettre à jour le profil

## 🐛 Dépannage

### L'API externe ne répond pas

1. Vérifiez que l'API est démarrée : `npm run dev`
2. Vérifiez que l'URL est correcte : `http://localhost:3000`
3. Vérifiez la console du navigateur pour les erreurs CORS

### Erreur 401 (Unauthorized)

1. Vérifiez que vous êtes connecté
2. Vérifiez que le token est présent dans localStorage
3. Vérifiez que le token n'est pas expiré

### La progression ne se sauvegarde pas

1. Vérifiez que `api-client.js` est chargé
2. Vérifiez la console pour les erreurs
3. Le système utilisera automatiquement l'ancienne API PHP en fallback

## 💡 Utilisation du client API

### Exemple : Obtenir l'utilisateur actuel

```javascript
try {
    const user = await window.apiClient.getCurrentUser();
    console.log('Utilisateur:', user);
} catch (error) {
    console.error('Erreur:', error);
}
```

### Exemple : Mettre à jour le profil

```javascript
try {
    const result = await window.apiClient.updateProfile('username', {
        firstName: 'Jean',
        lastName: 'Dupont',
        email: 'jean.dupont@example.com'
    });
    console.log('Profil mis à jour:', result);
} catch (error) {
    console.error('Erreur:', error);
}
```

### Exemple : Obtenir la progression

```javascript
try {
    const progress = await window.apiClient.getProgress();
    console.log('Progression:', progress);
} catch (error) {
    console.error('Erreur:', error);
}
```

## 🔒 Sécurité

- Le token JWT est stocké dans localStorage
- Le token est automatiquement inclus dans toutes les requêtes authentifiées
- Les tokens expirent après 7 jours (configurable dans l'API)
- En cas d'expiration, l'utilisateur doit se reconnecter

