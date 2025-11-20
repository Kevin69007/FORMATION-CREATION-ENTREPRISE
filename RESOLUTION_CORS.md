# 🔧 Résolution des erreurs CORS

## Problème

Vous rencontrez une erreur CORS lors de la connexion à l'API externe :
```
Access to fetch at 'http://localhost:3000/api/auth/login' from origin 'null' has been blocked by CORS policy
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

**Vérification :** L'API doit avoir une configuration CORS similaire à :

```javascript
const cors = require('cors');
app.use(cors({
  origin: ['http://localhost:8000', 'http://localhost:8080', 'http://127.0.0.1:8000'],
  credentials: true
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

```javascript
// server.js ou app.js
const cors = require('cors');

app.use(cors({
  origin: function (origin, callback) {
    // Autoriser les origines locales
    const allowedOrigins = [
      'http://localhost:8000',
      'http://localhost:8080',
      'http://127.0.0.1:8000',
      'http://127.0.0.1:8080'
    ];
    
    // En développement, autoriser toutes les origines
    if (!origin || allowedOrigins.indexOf(origin) !== -1 || process.env.NODE_ENV === 'development') {
      callback(null, true);
    } else {
      callback(new Error('Not allowed by CORS'));
    }
  },
  credentials: true,
  methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
  allowedHeaders: ['Content-Type', 'Authorization']
}));
```

## 💡 Alternative : Utiliser l'ancienne API PHP

Si l'API externe n'est pas accessible, le système basculera automatiquement vers l'ancienne API PHP (`admin/api.php`). Cependant, pour utiliser l'API externe avec JWT, vous devez résoudre le problème CORS.

