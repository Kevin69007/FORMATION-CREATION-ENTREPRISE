// admin/admin-api.js
// API JavaScript pour remplacer admin/api.php

class AdminAPI {
    constructor() {
        this.dataKey = 'user_progress_data';
        this.activityKey = 'activity_log';
    }

    // Charger les données utilisateur depuis le localStorage
    loadUserData() {
        const storedData = localStorage.getItem(this.dataKey);
        if (storedData) {
            try {
                return JSON.parse(storedData);
            } catch (e) {
                console.error('Erreur lors du parsing des données utilisateur:', e);
                return {};
            }
        }
        return {};
    }

    // Sauvegarder les données utilisateur dans le localStorage
    saveUserData(data) {
        try {
            localStorage.setItem(this.dataKey, JSON.stringify(data));
            return true;
        } catch (e) {
            console.error('Erreur lors de la sauvegarde des données:', e);
            return false;
        }
    }

    // Logger une activité
    logActivity(message) {
        const timestamp = new Date().toISOString();
        const logEntry = `[${timestamp}] ${message}`;
        
        const existingLog = localStorage.getItem(this.activityKey) || '';
        const newLog = existingLog + logEntry + '\n';
        
        try {
            localStorage.setItem(this.activityKey, newLog);
        } catch (e) {
            console.error('Erreur lors de l\'enregistrement de l\'activité:', e);
        }
    }

    // Mettre à jour la progression d'un utilisateur
    updateProgress(username, progress, timestamp = null) {
        if (!username) {
            throw new Error('Nom d\'utilisateur requis');
        }

        const userData = this.loadUserData();
        const now = timestamp || new Date().toISOString();

        if (!userData[username]) {
            userData[username] = {
                first_login: now,
                progress: {},
                session_count: 0,
                firstName: '',
                lastName: '',
                email: ''
            };
        }

        userData[username].progress = progress;
        userData[username].last_activity = now;
        userData[username].session_count = (userData[username].session_count || 0) + 1;

        // Calculer les statistiques
        const completedLessons = Object.values(progress).filter(lesson => lesson.completed).length;
        userData[username].completed_lessons = completedLessons;
        userData[username].completion_rate = Math.round((completedLessons / 77) * 100 * 10) / 10; // 77 = total des leçons

        if (this.saveUserData(userData)) {
            this.logActivity(`Progress updated for user: ${username} (Completed: ${completedLessons}/77)`);
            return {
                success: true,
                message: 'Progress updated successfully',
                stats: {
                    completed_lessons: completedLessons,
                    completion_rate: userData[username].completion_rate
                }
            };
        } else {
            throw new Error('Failed to save data');
        }
    }

    // Enregistrer une connexion utilisateur
    userLogin(username, timestamp = null) {
        if (!username) {
            throw new Error('Nom d\'utilisateur requis');
        }

        const userData = this.loadUserData();
        const now = timestamp || new Date().toISOString();

        if (!userData[username]) {
            userData[username] = {
                first_login: now,
                progress: {},
                session_count: 0,
                firstName: '',
                lastName: '',
                email: ''
            };
            this.logActivity(`New user registered: ${username}`);
        }

        userData[username].last_login = now;
        userData[username].session_count = (userData[username].session_count || 0) + 1;

        this.saveUserData(userData);
        this.logActivity(`User login: ${username}`);

        return {
            success: true,
            message: 'Login recorded',
            user_data: userData[username]
        };
    }

    // Mettre à jour le profil d'un utilisateur
    updateProfile(username, profileData, timestamp = null) {
        if (!username) {
            throw new Error('Nom d\'utilisateur requis');
        }

        const userData = this.loadUserData();
        const now = timestamp || new Date().toISOString();

        if (!userData[username]) {
            userData[username] = {
                first_login: now,
                progress: {},
                session_count: 0
            };
        }

        // Mettre à jour les informations du profil
        if (profileData.firstName !== undefined) {
            userData[username].firstName = profileData.firstName;
        }
        if (profileData.lastName !== undefined) {
            userData[username].lastName = profileData.lastName;
        }
        if (profileData.email !== undefined) {
            userData[username].email = profileData.email;
        }

        userData[username].last_activity = now;

        if (this.saveUserData(userData)) {
            this.logActivity(`Profile updated for user: ${username}`);
            return {
                success: true,
                message: 'Profile updated successfully'
            };
        } else {
            throw new Error('Failed to save profile data');
        }
    }

    // Créer un compte étudiant
    createStudent(studentData) {
        const requiredFields = ['username', 'firstName', 'lastName', 'email', 'password'];
        const missingFields = requiredFields.filter(field => !studentData[field]);
        
        if (missingFields.length > 0) {
            throw new Error('Tous les champs sont requis');
        }

        const userData = this.loadUserData();
        const timestamp = new Date().toISOString();

        // Vérifier si l'utilisateur existe déjà
        if (userData[studentData.username]) {
            throw new Error('Ce nom d\'utilisateur existe déjà');
        }

        // Créer le nouvel étudiant
        userData[studentData.username] = {
            firstName: studentData.firstName,
            lastName: studentData.lastName,
            email: studentData.email,
            password: btoa(studentData.password), // Simple encoding (not secure for production)
            created_at: timestamp,
            first_login: null,
            last_activity: null,
            progress: {},
            session_count: 0,
            completed_lessons: 0,
            completion_rate: 0
        };

        if (this.saveUserData(userData)) {
            this.logActivity(`New student created: ${studentData.username} (${studentData.firstName} ${studentData.lastName})`);
            return {
                success: true,
                message: 'Compte étudiant créé avec succès',
                student: {
                    username: studentData.username,
                    firstName: studentData.firstName,
                    lastName: studentData.lastName,
                    email: studentData.email
                }
            };
        } else {
            throw new Error('Erreur lors de la création du compte');
        }
    }

    // Mettre à jour un étudiant
    updateStudent(username, studentData) {
        if (!username) {
            throw new Error('Nom d\'utilisateur requis');
        }

        const userData = this.loadUserData();
        const timestamp = new Date().toISOString();

        if (!userData[username]) {
            throw new Error('Étudiant non trouvé');
        }

        // Mettre à jour les informations de l'étudiant
        if (studentData.firstName !== undefined) {
            userData[username].firstName = studentData.firstName;
        }
        if (studentData.lastName !== undefined) {
            userData[username].lastName = studentData.lastName;
        }
        if (studentData.email !== undefined) {
            userData[username].email = studentData.email;
        }

        userData[username].last_activity = timestamp;

        if (this.saveUserData(userData)) {
            this.logActivity(`Student updated: ${username}`);
            return {
                success: true,
                message: 'Étudiant mis à jour avec succès'
            };
        } else {
            throw new Error('Erreur lors de la mise à jour');
        }
    }

    // Supprimer un étudiant
    deleteStudent(username) {
        if (!username) {
            throw new Error('Nom d\'utilisateur requis');
        }

        const userData = this.loadUserData();

        if (!userData[username]) {
            throw new Error('Étudiant non trouvé');
        }

        // Supprimer l'étudiant
        delete userData[username];

        if (this.saveUserData(userData)) {
            this.logActivity(`Student deleted: ${username}`);
            return {
                success: true,
                message: 'Étudiant supprimé avec succès'
            };
        } else {
            throw new Error('Erreur lors de la suppression');
        }
    }

    // Obtenir les statistiques
    getStats() {
        const userData = this.loadUserData();
        const totalUsers = Object.keys(userData).length;
        let activeUsers = 0;
        let totalCompletions = 0;

        Object.values(userData).forEach(user => {
            const completedLessons = user.completed_lessons || 0;
            if (completedLessons > 0) {
                activeUsers++;
            }
            totalCompletions += completedLessons;
        });

        const avgCompletion = totalUsers > 0 ? Math.round((totalCompletions / (totalUsers * 77)) * 100 * 10) / 10 : 0;

        return {
            total_users: totalUsers,
            active_users: activeUsers,
            average_completion: avgCompletion,
            total_lessons: 77
        };
    }

    // Exporter les données en CSV
    exportCSV() {
        const userData = this.loadUserData();
        const headers = ['Utilisateur', 'Prenom', 'Nom', 'Email', 'Premiere_Connexion', 'Derniere_Activite', 'Sessions', 'Lecons_Terminees', 'Pourcentage_Completion'];
        let csv = headers.join(',') + '\n';
        
        Object.entries(userData).forEach(([username, data]) => {
            const row = [
                username,
                data.firstName || '',
                data.lastName || '',
                data.email || '',
                data.first_login || '',
                data.last_activity || '',
                data.session_count || 0,
                data.completed_lessons || 0,
                (data.completion_rate || 0) + '%'
            ];
            csv += row.join(',') + '\n';
        });
        
        return csv;
    }

    // Obtenir un utilisateur spécifique
    getUser(username) {
        const userData = this.loadUserData();
        return userData[username] || null;
    }

    // Obtenir tous les utilisateurs
    getAllUsers() {
        return this.loadUserData();
    }
}

// Créer une instance globale de l'API
window.adminAPI = new AdminAPI();