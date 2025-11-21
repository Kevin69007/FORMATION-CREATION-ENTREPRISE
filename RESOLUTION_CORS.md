# 🔧 Résolution des erreurs CORS

## Problème

Vous rencontrez une erreur CORS lors de la connexion à l'API externe.

### En développement local :
```
Access to fetch at 'http://localhost:3000/api/auth/login' from origin 'null' has been blocked by CORS policy
```

### En production :
```
Access to fetch at 'https://formations-creation-entreprise-admi.vercel.app/api/auth/login' 
from origin 'https://formation-entreprise.kevin-attallah.com' has been blocked by CORS policy: 
Response to preflight request doesn't pass access control check: 
No 'Access-Control-Allow-Origin' header is present on the requested resource.
```

## Causes possibles

### 1. Fichier HTML ouvert directement (file://)

Si vous ouvrez `index.html` directement depuis l'explorateur de fichiers (double-clic), l'origine sera `null` et les requêtes CORS seront bloquées.

**Solution :** Utilisez le serveur HTTP fourni :

```bash
# Windows
start-server.bat

# Ou PowerShell
.\start-server.ps1
```

Puis accédez à `http://localhost:8000` (ou le port indiqué)

### 2. API externe non démarrée

L'API externe doit être démarrée sur `http://localhost:3000`.

**Solution :** Dans le dossier de l'API externe :

```bash
npm run dev
```

Vérifiez que l'API répond sur `http://localhost:3000/api/health`

### 3. Configuration CORS de l'API externe

L'API externe doit être configurée pour accepter les requêtes depuis votre origine.

**⚠️ IMPORTANT EN PRODUCTION :** L'API doit autoriser votre domaine front-end.

**Vérification :** L'API doit avoir une configuration CORS similaire à :

#### Pour le développement local :
```javascript
const cors = require('cors');
app.use(cors({
  origin: ['http://localhost:8000', 'http://localhost:8080', 'http://127.0.0.1:8000'],
  credentials: true
}));
```

#### Pour la production :
```javascript
const cors = require('cors');
app.use(cors({
  origin: [
    'https://formation-entreprise.kevin-attallah.com',  // Front-end en production
    'https://formations-creation-entreprise-admi.vercel.app',  // API elle-même si nécessaire
    'http://localhost:8000',  // Pour le développement local
    'http://localhost:3000'   // Pour le développement local
  ],
  credentials: true,
  methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
  allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With']
}));
```

## ✅ Vérification étape par étape

1. **Démarrer l'API externe**
   ```bash
   cd /chemin/vers/api-externe
   npm run dev
   ```
   Vérifiez : `http://localhost:3000/api/health` doit répondre

2. **Démarrer le serveur front-end**
   ```bash
   start-server.bat
   ```
   Vérifiez : `http://localhost:8000` doit afficher la page de connexion

3. **Accéder via HTTP**
   - ✅ Utilisez : `http://localhost:8000`
   - ❌ N'utilisez PAS : `file:///C:/xampp/htdocs/.../index.html`

4. **Vérifier la console du navigateur**
   - Ouvrez les outils de développement (F12)
   - Onglet Console
   - Vérifiez qu'il n'y a plus d'erreurs CORS

## 🐛 Dépannage

### L'API externe ne répond pas

1. Vérifiez que Node.js est installé : `node --version`
2. Vérifiez que les dépendances sont installées : `npm install`
3. Vérifiez les logs de l'API pour voir les erreurs

### Le serveur front-end ne démarre pas

1. Vérifiez que le port 8000 (ou 8080) n'est pas déjà utilisé
2. Vérifiez les permissions PowerShell si nécessaire
3. Essayez de changer le port dans `start-server.ps1`

### Erreur CORS persiste

1. Vérifiez que vous accédez bien via HTTP (pas file://)
2. Vérifiez que l'API externe accepte les requêtes depuis votre origine
3. Vérifiez les headers CORS dans la réponse de l'API :
   - `Access-Control-Allow-Origin` doit être présent
   - `Access-Control-Allow-Methods` doit inclure POST, GET, etc.

## 📝 Configuration recommandée

### Pour l'API externe (si vous avez accès au code)

#### Configuration complète avec gestion développement/production :

```javascript
// server.js ou app.js
const cors = require('cors');

// Liste des origines autorisées
const allowedOrigins = [
  // Production
  'https://formation-entreprise.kevin-attallah.com',
  'https://formations-creation-entreprise-admi.vercel.app',
  // Développement local
  'http://localhost:8000',
  'http://localhost:8080',
  'http://127.0.0.1:8000',
  'http://127.0.0.1:8080',
  'http://localhost:3000'
];

app.use(cors({
  origin: function (origin, callback) {
    // Autoriser les requêtes sans origine (Postman, curl, etc.)
    if (!origin) {
      callback(null, true);
      return;
    }
    
    // Vérifier si l'origine est autorisée
    if (allowedOrigins.indexOf(origin) !== -1) {
      callback(null, true);
    } else {
      // En développement, autoriser toutes les origines
      if (process.env.NODE_ENV === 'development') {
        callback(null, true);
      } else {
        console.warn(`⚠️ Origine non autorisée: ${origin}`);
        callback(new Error('Not allowed by CORS'));
      }
    }
  },
  credentials: true,
  methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
  allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With'],
  exposedHeaders: ['Content-Range', 'X-Content-Range'],
  maxAge: 86400 // Cache preflight requests for 24 hours
}));
```

#### Configuration simple (toutes origines autorisées - pour test uniquement) :

```javascript
const cors = require('cors');
app.use(cors({
  origin: '*',  // ⚠️ À utiliser uniquement pour les tests
  credentials: false,
  methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
  allowedHeaders: ['Content-Type', 'Authorization']
}));
```

### 🔍 Vérification de la configuration CORS

Pour vérifier que CORS est correctement configuré, testez avec curl :

```bash
# Test de la requête preflight (OPTIONS)
curl -X OPTIONS \
  -H "Origin: https://formation-entreprise.kevin-attallah.com" \
  -H "Access-Control-Request-Method: POST" \
  -H "Access-Control-Request-Headers: Content-Type,Authorization" \
  -v https://formations-creation-entreprise-admi.vercel.app/api/auth/login

# Vous devriez voir dans la réponse :
# Access-Control-Allow-Origin: https://formation-entreprise.kevin-attallah.com
# Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
# Access-Control-Allow-Headers: Content-Type, Authorization
```

## 🚀 Configuration en production

### URLs de production

- **Front-end :** `https://formation-entreprise.kevin-attallah.com`
- **API :** `https://formations-creation-entreprise-admi.vercel.app/api`

### Étapes pour résoudre CORS en production

1. **Accéder au code de l'API** sur Vercel ou votre dépôt Git
2. **Modifier la configuration CORS** pour inclure votre domaine front-end
3. **Redéployer l'API** sur Vercel
4. **Vérifier** que les headers CORS sont présents dans les réponses

### Test rapide

Ouvrez la console du navigateur (F12) et testez :

```javascript
fetch('https://formations-creation-entreprise-admi.vercel.app/api/health')
  .then(r => {
    console.log('Headers CORS:', {
      'Access-Control-Allow-Origin': r.headers.get('Access-Control-Allow-Origin'),
      'Access-Control-Allow-Methods': r.headers.get('Access-Control-Allow-Methods')
    });
  })
  .catch(e => console.error('Erreur:', e));
```

## 💡 Alternative : Utiliser l'ancienne API PHP

Si l'API externe n'est pas accessible, le système basculera automatiquement vers l'ancienne API PHP (`admin/api.php`). Cependant, pour utiliser l'API externe avec JWT, vous devez résoudre le problème CORS.

## 📞 Support

Si le problème persiste après avoir configuré CORS :

1. Vérifiez les logs de l'API sur Vercel
2. Vérifiez la console du navigateur pour les erreurs détaillées
3. Testez l'API directement avec Postman ou curl
4. Vérifiez que l'API est bien déployée et accessible

