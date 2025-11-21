# 🔧 Configuration CORS pour l'API - Guide étape par étape

## ⚠️ Pourquoi le problème persiste ?

**Le problème CORS ne peut PAS être résolu côté client (front-end).** C'est une restriction de sécurité du navigateur qui ne peut être contournée que par la configuration côté serveur (API).

## 📍 Où modifier la configuration ?

Vous devez modifier le code de votre **API Node.js** déployée sur Vercel à l'adresse :
`https://formations-creation-entreprise-admin-m0awuogka.vercel.app`

## 🚀 Solution : Modifier la configuration CORS de l'API

### Étape 1 : Accéder au code de l'API

1. Allez sur [Vercel Dashboard](https://vercel.com/dashboard)
2. Trouvez votre projet API : `formations-creation-entreprise-admin-m0awuogka`
3. Cliquez sur le projet
4. Allez dans l'onglet "Settings" > "Git" pour voir le dépôt Git
5. Clonez ou modifiez le code directement sur GitHub/GitLab

### Étape 2 : Localiser le fichier de configuration

Cherchez le fichier principal de l'API (généralement) :
- `server.js`
- `app.js`
- `index.js`
- `src/index.js`
- `src/server.js`

### Étape 3 : Ajouter/Modifier la configuration CORS

Trouvez où CORS est configuré et remplacez/modifiez-le :

#### Si CORS n'est pas encore configuré :

```javascript
// Au début du fichier, après les imports
const cors = require('cors');

// Configuration CORS
app.use(cors({
  origin: [
    'https://formation-entreprise.kevin-attallah.com',  // ⭐ VOTRE FRONT-END
    'https://formations-creation-entreprise-admin-m0awuogka.vercel.app',  // L'API elle-même
    'http://localhost:8000',  // Développement local
    'http://localhost:3000'   // Développement local
  ],
  credentials: true,
  methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
  allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With'],
  exposedHeaders: ['Content-Range', 'X-Content-Range'],
  maxAge: 86400 // Cache preflight requests for 24 hours
}));
```

#### Si CORS est déjà configuré :

Trouvez la ligne avec `origin:` et ajoutez votre domaine :

```javascript
// AVANT (exemple)
app.use(cors({
  origin: ['http://localhost:3000'],  // ❌ Manque votre domaine
  credentials: true
}));

// APRÈS
app.use(cors({
  origin: [
    'https://formation-entreprise.kevin-attallah.com',  // ✅ AJOUTEZ CETTE LIGNE
    'https://formations-creation-entreprise-admin-m0awuogka.vercel.app',
    'http://localhost:3000',
    'http://localhost:8000'
  ],
  credentials: true,
  methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
  allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With']
}));
```

### Étape 4 : Configuration avec variables d'environnement (Recommandé)

Pour une meilleure gestion, utilisez des variables d'environnement :

```javascript
const cors = require('cors');

// Liste des origines autorisées
const allowedOrigins = [
  'https://formation-entreprise.kevin-attallah.com',  // Production
  'https://formations-creation-entreprise-admi.vercel.app',
  ...(process.env.ALLOWED_ORIGINS ? process.env.ALLOWED_ORIGINS.split(',') : []),
  // Développement local
  'http://localhost:8000',
  'http://localhost:3000',
  'http://127.0.0.1:8000'
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
      console.warn(`⚠️ Origine non autorisée: ${origin}`);
      callback(new Error('Not allowed by CORS'));
    }
  },
  credentials: true,
  methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
  allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With'],
  exposedHeaders: ['Content-Range', 'X-Content-Range'],
  maxAge: 86400
}));
```

### Étape 5 : Vérifier que le package `cors` est installé

Dans le fichier `package.json` de l'API, vérifiez que `cors` est présent :

```json
{
  "dependencies": {
    "cors": "^2.8.5",
    // ... autres dépendances
  }
}
```

Si ce n'est pas le cas, installez-le :

```bash
npm install cors
```

### Étape 6 : Commiter et déployer

1. **Commiter les changements** :
   ```bash
   git add .
   git commit -m "Fix CORS: Add front-end domain to allowed origins"
   git push
   ```

2. **Vercel déploiera automatiquement** ou vous pouvez déclencher un déploiement manuel depuis le dashboard Vercel

### Étape 7 : Vérifier que ça fonctionne

1. Attendez quelques minutes que Vercel termine le déploiement
2. Testez dans la console du navigateur (F12) :

```javascript
fetch('https://formations-creation-entreprise-admin-m0awuogka.vercel.app/api/health', {
  method: 'GET',
  headers: {
    'Content-Type': 'application/json'
  }
})
.then(response => {
  console.log('✅ CORS configuré correctement !');
  console.log('Headers:', {
    'Access-Control-Allow-Origin': response.headers.get('Access-Control-Allow-Origin'),
    'Access-Control-Allow-Methods': response.headers.get('Access-Control-Allow-Methods')
  });
  return response.json();
})
.then(data => console.log('Réponse:', data))
.catch(error => console.error('❌ Erreur:', error));
```

3. Essayez de vous connecter depuis votre front-end

## 🔍 Vérification avec curl (optionnel)

Testez la requête preflight (OPTIONS) :

```bash
curl -X OPTIONS \
  -H "Origin: https://formation-entreprise.kevin-attallah.com" \
  -H "Access-Control-Request-Method: POST" \
  -H "Access-Control-Request-Headers: Content-Type,Authorization" \
  -v https://formations-creation-entreprise-admin-m0awuogka.vercel.app/api/auth/login
```

Vous devriez voir dans la réponse :
```
< Access-Control-Allow-Origin: https://formation-entreprise.kevin-attallah.com
< Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
< Access-Control-Allow-Headers: Content-Type, Authorization
```

## 🆘 Si vous n'avez pas accès au code de l'API

### Option 1 : Contacter le développeur de l'API

Envoyez-lui ce message :

```
Bonjour,

Je rencontre une erreur CORS lors de l'accès à l'API depuis mon front-end.

Front-end : https://formation-entreprise.kevin-attallah.com
API : https://formations-creation-entreprise-admin-m0awuogka.vercel.app

Pouvez-vous ajouter mon domaine dans la configuration CORS de l'API ?

Configuration nécessaire :
- Ajouter 'https://formation-entreprise.kevin-attallah.com' dans les origines autorisées
- Activer credentials: true
- Autoriser les méthodes : GET, POST, PUT, DELETE, OPTIONS
- Autoriser les headers : Content-Type, Authorization

Merci !
```

### Option 2 : Utiliser l'API PHP en attendant

En attendant que CORS soit configuré, le système utilisera automatiquement l'API PHP (`admin/api.php`) comme fallback. Cependant, certaines fonctionnalités (comme JWT) ne seront pas disponibles.

## 📝 Checklist de vérification

- [ ] J'ai accès au code de l'API sur Vercel/GitHub
- [ ] J'ai localisé le fichier de configuration (server.js, app.js, etc.)
- [ ] J'ai ajouté `'https://formation-entreprise.kevin-attallah.com'` dans les origines autorisées
- [ ] J'ai vérifié que `credentials: true` est activé
- [ ] J'ai vérifié que les méthodes HTTP sont autorisées
- [ ] J'ai vérifié que le package `cors` est installé
- [ ] J'ai commité et poussé les changements
- [ ] Vercel a déployé la nouvelle version
- [ ] J'ai testé la connexion depuis le front-end
- [ ] Ça fonctionne ! ✅

## 🐛 Problèmes courants

### "Le package cors n'est pas trouvé"
```bash
cd /chemin/vers/api
npm install cors
```

### "Les changements ne sont pas pris en compte"
- Vérifiez que vous avez bien commité et poussé les changements
- Vérifiez que Vercel a bien déployé (regardez les logs de déploiement)
- Attendez 1-2 minutes après le déploiement

### "L'erreur persiste après déploiement"
- Videz le cache du navigateur (Ctrl+Shift+Delete)
- Testez en navigation privée
- Vérifiez les logs de l'API sur Vercel pour voir les erreurs

## 📞 Besoin d'aide ?

Si vous avez besoin d'aide pour localiser ou modifier le code de l'API, n'hésitez pas à demander !

