// API Client pour communiquer avec l'API externe Node.js
// Base URL de l'API externe
// Production: https://formations-creation-entreprise-admi.vercel.app
// Développement: http://localhost:3000

// Détection automatique de l'environnement
const isDevelopment = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
const API_BASE_URL = isDevelopment 
  ? 'http://localhost:3000/api'  // Développement local
  : 'https://formations-creation-entreprise-admi.vercel.app/api';  // Production

class APIClient {
  constructor() {
    this.baseURL = API_BASE_URL;
  }

  // Obtenir le token depuis localStorage
  getToken() {
    return localStorage.getItem('token');
  }

  /**
   * Normalise la réponse de l'API pour gérer différents formats
   * @param {object} data - Données brutes de l'API
   * @returns {object} Données normalisées avec success, error, etc.
   */
  normalizeResponse(data) {
    // Si data est déjà normalisé (a un champ success)
    if (data.success !== undefined) {
      return data;
    }

    // Si data a un token, c'est un succès
    if (data.token !== undefined) {
      return {
        success: true,
        ...data
      };
    }

    // Si data a un error, c'est un échec
    if (data.error !== undefined) {
      return {
        success: false,
        error: data.error,
        ...data
      };
    }

    // Si data est un tableau (liste d'utilisateurs par exemple), c'est un succès
    if (Array.isArray(data)) {
      return {
        success: true,
        data: data,
        users: data // Pour compatibilité
      };
    }

    // Si data est un objet avec des propriétés, considérer comme succès
    if (typeof data === 'object' && data !== null && Object.keys(data).length > 0) {
      // Vérifier si c'est une erreur (message d'erreur commun)
      if (data.message && (data.message.toLowerCase().includes('error') || data.message.toLowerCase().includes('erreur'))) {
        return {
          success: false,
          error: data.message,
          ...data
        };
      }
      // Sinon, considérer comme succès
      return {
        success: true,
        ...data
      };
    }

    // Par défaut, considérer comme succès
    return {
      success: true,
      data: data
    };
  }

  // Méthode générique pour les requêtes
  async request(endpoint, options = {}) {
    const url = `${this.baseURL}${endpoint}`;
    const token = this.getToken();

    const headers = {
      'Content-Type': 'application/json',
      ...options.headers
    };

    if (token) {
      headers['Authorization'] = `Bearer ${token}`;
    }

    try {
      const response = await fetch(url, {
        ...options,
        headers
      });

      // Vérifier si la réponse est OK avant de parser le JSON
      let data;
      try {
        data = await response.json();
      } catch (e) {
        // Si le parsing JSON échoue, créer un objet d'erreur
        throw new Error(`Erreur serveur (${response.status}): ${response.statusText}`);
      }

      if (!response.ok) {
        throw new Error(data.error || data.message || `Erreur ${response.status}: ${response.statusText}`);
      }

      // Normaliser la réponse pour gérer différents formats
      return this.normalizeResponse(data);
    } catch (error) {
      // Gestion spécifique des erreurs CORS
      if (error.message.includes('Failed to fetch') || error.message.includes('CORS') || error.message.includes('Access-Control-Allow-Origin')) {
        const currentOrigin = window.location.origin;
        const corsError = new Error(`Erreur CORS: L'API externe n'est pas accessible depuis ${currentOrigin}.\n\nVérifiez que:\n1. L'API est accessible sur https://formations-creation-entreprise-admi.vercel.app\n2. L'API accepte les requêtes CORS depuis votre origine: ${currentOrigin}\n3. La configuration CORS côté serveur inclut votre domaine dans les origines autorisées`);
        corsError.name = 'CORSError';
        throw corsError;
      }
      console.error('API Error:', error);
      throw error;
    }
  }

  // ==================== AUTHENTIFICATION ====================

  /**
   * Connexion d'un utilisateur
   * @param {string} username - Nom d'utilisateur ou email
   * @param {string} password - Mot de passe
   * @returns {Promise} Réponse avec token et informations utilisateur
   */
  async login(username, password) {
    // L'API attend 'email' et 'password', on envoie le username comme email
    // (l'API peut accepter soit un email soit un username selon sa configuration)
    return this.request('/auth/login', {
      method: 'POST',
      body: JSON.stringify({ email: username, password })
    });
  }

  /**
   * Inscription d'un nouvel utilisateur
   * @param {object} userData - Données de l'utilisateur
   * @returns {Promise} Réponse avec token et informations utilisateur
   */
  async register(userData) {
    return this.request('/auth/register', {
      method: 'POST',
      body: JSON.stringify(userData)
    });
  }

  /**
   * Obtenir les informations de l'utilisateur connecté
   * @returns {Promise} Informations de l'utilisateur
   */
  async getCurrentUser() {
    return this.request('/auth/me', {
      method: 'GET'
    });
  }

  // ==================== UTILISATEURS ====================

  /**
   * Liste tous les utilisateurs (Admin uniquement)
   * @returns {Promise} Liste des utilisateurs
   */
  async getAllUsers() {
    return this.request('/users', {
      method: 'GET'
    });
  }

  /**
   * Créer un étudiant (Admin uniquement)
   * @param {object} studentData - Données de l'étudiant
   * @returns {Promise} Réponse de création
   */
  async createStudent(studentData) {
    return this.request('/users', {
      method: 'POST',
      body: JSON.stringify(studentData)
    });
  }

  /**
   * Obtenir les informations d'un utilisateur par username
   * @param {string} username - Nom d'utilisateur
   * @returns {Promise} Informations de l'utilisateur
   */
  async getUser(username) {
    return this.request(`/users/${username}`, {
      method: 'GET'
    });
  }

  /**
   * Mettre à jour le profil d'un utilisateur
   * @param {string} username - Nom d'utilisateur
   * @param {object} profileData - Données du profil
   * @returns {Promise} Réponse de mise à jour
   */
  async updateProfile(username, profileData) {
    return this.request(`/users/${username}/profile`, {
      method: 'PUT',
      body: JSON.stringify(profileData)
    });
  }

  // ==================== PROGRESSION ====================

  /**
   * Mettre à jour la progression
   * @param {object} progressData - Données de progression
   * @param {string} progressData.moduleId - ID du module
   * @param {string} progressData.lessonId - ID de la leçon
   * @param {boolean} progressData.completed - Leçon terminée
   * @param {number} progressData.timeSpent - Temps passé en secondes
   * @returns {Promise} Réponse de mise à jour
   */
  async updateProgress(progressData) {
    return this.request('/progress', {
      method: 'POST',
      body: JSON.stringify(progressData)
    });
  }

  /**
   * Obtenir la progression
   * @param {string|null} username - Nom d'utilisateur (optionnel, admin uniquement)
   * @returns {Promise} Données de progression
   */
  async getProgress(username = null) {
    const endpoint = username ? `/progress?username=${username}` : '/progress';
    return this.request(endpoint, {
      method: 'GET'
    });
  }
}

// Créer une instance globale
window.apiClient = new APIClient();

