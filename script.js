// Configuration des données de formation
const formationData = {
    modules: [
        {
            id: 1,
            title: "Mettre ses compétences au service de son projet",
            lessons: [
                { id: 1, title: "À quoi vous attendre dans ce premier module", type: "lesson" },
                { id: 2, title: "Comment lancer son projet d'entreprise sans se louper", type: "lesson" },
                { id: 3, title: "Présentation des compétences requises pour être un chef d'entreprise", type: "lesson" },
                { id: 4, title: "Comprendre l'importance des compétences entrepreneuriales", type: "lesson" },
                { id: 5, title: "Analyse des compétences clés pour gérer une entreprise", type: "lesson" },
                { id: 6, title: "SMART Goals", type: "lesson" },
                { id: 7, title: "Synthèse", type: "lesson" },
                { id: 8, title: "Fiches Complémentaires", type: "pdf" }
            ]
        },
        {
            id: 2,
            title: "Connaître son marché pour mieux vendre",
            lessons: [
                { id: 1, title: "Analyse du Marché", type: "lesson" },
                { id: 2, title: "Analyse de la clientèle et de la concurrence", type: "lesson" },
                { id: 3, title: "Mise en place de stratégies basées sur la connaissance du marché", type: "lesson" },
                { id: 4, title: "Utilisation des documents commerciaux pour mieux vendre", type: "lesson" },
                { id: 5, title: "Customer Journey Mapping", type: "lesson" },
                { id: 6, title: "Fiches Complémentaires", type: "pdf" }
            ]
        },
        {
            id: 3,
            title: "Définir les besoins et la rentabilité du projet",
            lessons: [
                { id: 1, title: "Définir les besoins et la rentabilité du projet", type: "lesson" },
                { id: 2, title: "Comprendre et structurer son projet avec le Business Model Canva", type: "lesson" },
                { id: 3, title: "Élaboration d'un compte de résultat", type: "lesson" },
                { id: 4, title: "Création d'un plan de financement", type: "lesson" },
                { id: 5, title: "Présentation des outils de financement en partenariat avec un banquier", type: "lesson" },
                { id: 6, title: "Plan financier & Lean Canvas", type: "lesson" },
                { id: 7, title: "Fiches Complémentaires", type: "pdf" }
            ]
        },
        {
            id: 4,
            title: "Choisir une structure juridique appropriée",
            lessons: [
                { id: 1, title: "Introduction aux structures juridiques", type: "lesson" },
                { id: 2, title: "Présentation des différentes formes juridiques", type: "lesson" },
                { id: 3, title: "Comparaison des avantages et limites des structures juridiques", type: "lesson" },
                { id: 4, title: "Sélection de la structure juridique adaptée au projet", type: "lesson" },
                { id: 5, title: "Fiches Complémentaires", type: "pdf" }
            ]
        },
        {
            id: 5,
            title: "Comprendre les différents régimes fiscaux",
            lessons: [
                { id: 1, title: "Les régimes fiscaux des entreprises", type: "lesson" },
                { id: 2, title: "Présentation des divers régimes fiscaux", type: "lesson" },
                { id: 3, title: "Comparaison des avantages et limites des régimes fiscaux", type: "lesson" },
                { id: 4, title: "Sélection du régime fiscal approprié à l'entreprise", type: "lesson" }
            ]
        },
        {
            id: 6,
            title: "Connaître les principales aides à la création d'entreprise",
            lessons: [
                { id: 1, title: "Introduction aux aides à la création d'entreprise", type: "lesson" },
                { id: 2, title: "Identification des aides à la création nationales, régionales, territoriales", type: "lesson" },
                { id: 3, title: "Application des aides en fonction du statut personnel", type: "lesson" },
                { id: 4, title: "Maximisation des avantages des principales aides", type: "lesson" },
                { id: 5, title: "Fiches Complémentaires", type: "pdf" }
            ]
        },
        {
            id: 7,
            title: "Où s'adresser pour déclarer son entreprise",
            lessons: [
                { id: 1, title: "Centre de Formalités des Entreprises (CFE)", type: "lesson" },
                { id: 2, title: "Présentation du centre de formalités des entreprises", type: "lesson" },
                { id: 3, title: "Explication des modalités de fonctionnement administratif", type: "lesson" },
                { id: 4, title: "Connaissances des conséquences administratives de l'immatriculation", type: "lesson" }
            ]
        },
        {
            id: 8,
            title: "Atouts de la reprise d'entreprise",
            lessons: [
                { id: 1, title: "Moins de risques que la création", type: "lesson" },
                { id: 2, title: "Spécificités de la reprise d'entreprise", type: "lesson" },
                { id: 3, title: "Outils disponibles pour trouver une entreprise à reprendre", type: "lesson" },
                { id: 4, title: "Évaluation des avantages de la reprise par rapport à la création", type: "lesson" }
            ]
        },
        {
            id: 9,
            title: "Obtenir les premières informations sur les structures juridiques",
            lessons: [
                { id: 1, title: "Comprendre les statuts juridiques", type: "lesson" },
                { id: 2, title: "Compréhension des mécanismes financiers de base", type: "lesson" },
                { id: 3, title: "Mise en place d'une organisation administrative et comptable efficace", type: "lesson" },
                { id: 4, title: "Connaissance du calendrier des déclarations et des télédéclarations", type: "lesson" }
            ]
        },
        {
            id: 10,
            title: "Trouver ses clients en étudiant son marché",
            lessons: [
                { id: 1, title: "Identifier votre marché cible", type: "lesson" },
                { id: 2, title: "Étude approfondie du marché cible", type: "lesson" },
                { id: 3, title: "Stratégies pour se démarquer des concurrents", type: "lesson" },
                { id: 4, title: "Création d'une identité commerciale distinctive", type: "lesson" },
                { id: 5, title: "Comprendre les attentes clients avec le Value Proposition Canvas", type: "lesson" }
            ]
        },
        {
            id: 11,
            title: "Valoriser son offre et choisir son circuit de distribution",
            lessons: [
                { id: 1, title: "Comprendre la Valeur de son Offre", type: "lesson" },
                { id: 2, title: "Prospection et valorisation de l'offre commerciale", type: "lesson" },
                { id: 3, title: "Création d'outils de communication adaptés à l'entreprise", type: "lesson" },
                { id: 4, title: "Détermination du juste prix et du coût de revient", type: "lesson" }
            ]
        },
        {
            id: 12,
            title: "Cibler les actions commerciales adaptées à ses clients",
            lessons: [
                { id: 1, title: "Comprendre ses Clients", type: "lesson" },
                { id: 2, title: "Identification des meilleures actions commerciales adaptées", type: "lesson" },
                { id: 3, title: "Création de stratégies pour fidéliser la clientèle", type: "lesson" },
                { id: 4, title: "Élaboration d'un plan d'actions commerciales personnalisées", type: "lesson" },
                { id: 5, title: "Avant de vous lancer : erreurs classiques à éviter", type: "lesson" }
            ]
        },
        {
            id: 13,
            title: "Études de cas",
            lessons: [
                { id: 1, title: "Études de Cas", type: "pdf" }
            ]
        }
    ]
};

// Données des leçons avec contenu (fallback)
const lessonContent = {
    1: {
        1: {
            title: "À quoi vous attendre dans ce premier module",
            moduleTitle: "Module 1 : Mettre ses compétences au service de son projet",
            content: `
                <h1>🎯 À quoi vous attendre dans ce premier module</h1>
                <p>Bienvenue dans ce premier module de formation ! Vous allez découvrir les fondations essentielles de l'entrepreneuriat.</p>
                <div style="background: #f0f8ff; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <h3>💡 Conseil pratique</h3>
                    <p>Prenez des notes pendant cette formation et n'hésitez pas à mettre en pause pour réfléchir aux applications concrètes dans votre projet.</p>
                </div>
            `
        }
    }
};

// Fonction pour télécharger les PDFs
function downloadPDF(pdfPath) {
    const link = document.createElement('a');
    link.href = pdfPath;
    link.download = pdfPath.split('/').pop();
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showNotification('Téléchargement en cours...');
}

// Fonctions utilitaires
function checkAuth() {
    if (localStorage.getItem('isLoggedIn') !== 'true') {
        window.location.href = 'index.html';
    }
}

function generateNavigation() {
    const navigationElement = document.getElementById('navigation');
    if (!navigationElement) return;
    
    let navigationHTML = '';
    
    formationData.modules.forEach(module => {
        const isActive = hasActiveLesson(module.id);
        
        navigationHTML += `
            <div class="nav-section ${isActive ? 'active' : ''}" id="module-${module.id}">
                <div class="nav-section-header" onclick="toggleModule(${module.id})">
                    <span class="nav-section-title">${module.title}</span>
                    <span class="nav-arrow">▶</span>
                </div>
                <div class="nav-items">
        `;
        
        module.lessons.forEach(lesson => {
            const lessonKey = `module_${module.id}_lesson_${lesson.id}`;
            const progress = JSON.parse(localStorage.getItem('lessonProgress') || '{}');
            const isCompleted = progress[lessonKey] && progress[lessonKey].completed;
            const isCurrentLesson = isCurrentLessonActive(module.id, lesson.id);
            
            const lessonUrl = `lesson.html?module=${module.id}&lesson=${lesson.id}`;
            
            navigationHTML += `
                <a href="${lessonUrl}" class="nav-item ${isCurrentLesson ? 'active' : ''} ${isCompleted ? 'completed' : ''}">
                    <span class="nav-item-icon">${isCompleted ? '✓' : '○'}</span>
                    ${lesson.title}
                </a>
            `;
        });
        
        navigationHTML += `
                </div>
            </div>
        `;
    });
    
    navigationElement.innerHTML = navigationHTML;
}

function hasActiveLesson(moduleId) {
    const urlParams = new URLSearchParams(window.location.search);
    const currentModule = parseInt(urlParams.get('module'));
    return currentModule === moduleId;
}

function isCurrentLessonActive(moduleId, lessonId) {
    const urlParams = new URLSearchParams(window.location.search);
    const currentModule = parseInt(urlParams.get('module'));
    const currentLesson = parseInt(urlParams.get('lesson'));
    return currentModule === moduleId && currentLesson === lessonId;
}

function toggleModule(moduleId) {
    const moduleElement = document.getElementById(`module-${moduleId}`);
    if (moduleElement) {
        moduleElement.classList.toggle('active');
    }
}

function updateProgress() {
    const progress = JSON.parse(localStorage.getItem('lessonProgress') || '{}');
    const totalLessons = getTotalLessonsCount();
    const completedLessons = Object.keys(progress).filter(key => progress[key].completed).length;
    const percentage = totalLessons > 0 ? Math.round((completedLessons / totalLessons) * 100) : 0;
    
    const progressPercent = document.getElementById('progressPercent');
    const progressFill = document.getElementById('progressFill');
    const completedLessonsElement = document.getElementById('completedLessons');
    const totalLessonsElement = document.getElementById('totalLessons');
    
    if (progressPercent) progressPercent.textContent = `${percentage}%`;
    if (progressFill) progressFill.style.width = `${percentage}%`;
    if (completedLessonsElement) completedLessonsElement.textContent = completedLessons;
    if (totalLessonsElement) totalLessonsElement.textContent = totalLessons;
}

function getTotalLessonsCount() {
    let total = 0;
    formationData.modules.forEach(module => {
        total += module.lessons.length;
    });
    return total;
}

function initializeDashboard() {
    generateNavigation();
    updateProgress();
    
    const username = localStorage.getItem('username') || 'Utilisateur';
    const userNameElement = document.getElementById('userName');
    const userAvatar = document.getElementById('userAvatar');
    
    if (userNameElement) userNameElement.textContent = username;
    if (userAvatar) userAvatar.textContent = username.charAt(0).toUpperCase();
}

function getLessonData(moduleId, lessonId) {
    const module = formationData.modules.find(m => m.id === moduleId);
    if (!module) {
        console.log(`Module ${moduleId} introuvable`);
        return null;
    }
    
    const lesson = module.lessons.find(l => l.id === lessonId);
    if (!lesson) {
        console.log(`Leçon ${lessonId} introuvable dans module ${moduleId}`);
        return null;
    }
    
    console.log(`Recherche contenu pour module ${moduleId}, leçon ${lessonId}`);
    
    // Essayer d'abord les modules chargés dynamiquement
    const moduleContentVar = `module${moduleId}Content`;
    console.log(`Vérification de window.${moduleContentVar}:`, typeof window[moduleContentVar]);
    
    if (window[moduleContentVar] && window[moduleContentVar][lessonId]) {
        console.log(`Contenu trouvé dans window.${moduleContentVar}`);
        const lessonData = window[moduleContentVar][lessonId];
        return {
            title: lessonData.title,
            moduleTitle: lessonData.moduleTitle,
            content: lessonData.content,
            type: lessonData.type || lesson.type
        };
    }
    
    // Fallback vers lessonContent
    if (lessonContent[moduleId] && lessonContent[moduleId][lessonId]) {
        console.log(`Contenu trouvé dans lessonContent`);
        const lessonData = lessonContent[moduleId][lessonId];
        return {
            title: lessonData.title,
            moduleTitle: lessonData.moduleTitle,
            content: lessonData.content,
            type: lessonData.type || lesson.type
        };
    }
    
    console.log(`Aucun contenu trouvé, utilisation du fallback`);
    
    // Fallback par défaut
    return {
        title: lesson.title,
        moduleTitle: `Module ${moduleId} : ${module.title}`,
        content: `
            <h1>${lesson.title}</h1>
            <p>Contenu de la leçon en cours de préparation...</p>
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3>📚 Cette leçon sera bientôt disponible</h3>
                <p>Nous travaillons actuellement sur le contenu de cette leçon. Elle sera disponible prochainement.</p>
            </div>
        `,
        type: lesson.type
    };
}

function getModuleData(moduleId) {
    return formationData.modules.find(m => m.id === moduleId);
}

function getPreviousLesson(currentModule, currentLesson) {
    if (currentLesson > 1) {
        const module = formationData.modules.find(m => m.id === currentModule);
        const prevLesson = module.lessons.find(l => l.id === currentLesson - 1);
        return {
            module: currentModule,
            lesson: currentLesson - 1,
            isPDF: prevLesson.type === 'pdf'
        };
    }
    
    if (currentModule > 1) {
        const prevModule = formationData.modules.find(m => m.id === currentModule - 1);
        if (prevModule) {
            const lastLesson = prevModule.lessons[prevModule.lessons.length - 1];
            return {
                module: currentModule - 1,
                lesson: lastLesson.id,
                isPDF: lastLesson.type === 'pdf'
            };
        }
    }
    
    return null;
}

function getNextLesson(currentModule, currentLesson) {
    const module = formationData.modules.find(m => m.id === currentModule);
    if (!module) return null;
    
    if (currentLesson < module.lessons.length) {
        const nextLesson = module.lessons.find(l => l.id === currentLesson + 1);
        return {
            module: currentModule,
            lesson: currentLesson + 1,
            isPDF: nextLesson.type === 'pdf'
        };
    }
    
    if (currentModule < formationData.modules.length) {
        const nextModule = formationData.modules.find(m => m.id === currentModule + 1);
        if (nextModule) {
            return {
                module: currentModule + 1,
                lesson: 1,
                isPDF: nextModule.lessons[0].type === 'pdf'
            };
        }
    }
    
    return null;
}

function sendProgressToServer() {
    const progress = localStorage.getItem('lessonProgress');
    const username = localStorage.getItem('username');
    
    if (!progress || !username) {
        console.log('Pas de progression ou nom d\'utilisateur à sauvegarder');
        return;
    }
    
    try {
        const progressData = JSON.parse(progress);
        
        fetch('admin/api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'update_progress',
                username: username,
                progress: progressData,
                timestamp: new Date().toISOString()
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Progression sauvegardée avec succès:', data);
            if (data.success) {
                console.log(`Leçons terminées: ${data.stats?.completed_lessons || 'N/A'}, Taux: ${data.stats?.completion_rate || 'N/A'}%`);
            }
        })
        .catch(error => {
            console.error('Erreur lors de la sauvegarde:', error);
            showNotification('Erreur lors de la sauvegarde de la progression', 'error');
        });
    } catch (error) {
        console.error('Erreur lors du parsing de la progression:', error);
    }
}

// Fonction pour marquer une leçon comme terminée avec timestamp
function markLessonAsCompleted(moduleId, lessonId) {
    const lessonKey = `module_${moduleId}_lesson_${lessonId}`;
    const progress = JSON.parse(localStorage.getItem('lessonProgress') || '{}');
    
    if (!progress[lessonKey]) {
        progress[lessonKey] = {};
    }
    
    progress[lessonKey].completed = true;
    progress[lessonKey].completedAt = new Date().toISOString();
    
    localStorage.setItem('lessonProgress', JSON.stringify(progress));
    
    // Mettre à jour l'affichage
    updateProgress();
    generateNavigation();
    
    // Sauvegarder sur le serveur
    sendProgressToServer();
    
    showNotification('Leçon marquée comme terminée !', 'success');
}

// Fonction pour marquer une leçon comme non terminée
function markLessonAsIncomplete(moduleId, lessonId) {
    const lessonKey = `module_${moduleId}_lesson_${lessonId}`;
    const progress = JSON.parse(localStorage.getItem('lessonProgress') || '{}');
    
    if (progress[lessonKey]) {
        progress[lessonKey].completed = false;
        delete progress[lessonKey].completedAt;
    }
    
    localStorage.setItem('lessonProgress', JSON.stringify(progress));
    
    // Mettre à jour l'affichage
    updateProgress();
    generateNavigation();
    
    // Sauvegarder sur le serveur
    sendProgressToServer();
    
    showNotification('Leçon marquée comme non terminée', 'success');
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    const bgColor = type === 'error' ? '#dc3545' : '#28a745';
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${bgColor};
        color: white;
        padding: 15px 20px;
        border-radius: 6px;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        max-width: 300px;
        word-wrap: break-word;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, type === 'error' ? 5000 : 3000);
}

function getUrlParameter(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}

// Fonction d'initialisation générale
function initializeFormation() {
    checkAuth();
    
    if (document.getElementById('navigation')) {
        generateNavigation();
        updateProgress();
        
        const username = localStorage.getItem('username') || 'Utilisateur';
        const userNameElement = document.getElementById('userName');
        const userAvatar = document.getElementById('userAvatar');
        
        if (userNameElement) userNameElement.textContent = username;
        if (userAvatar) userAvatar.textContent = username.charAt(0).toUpperCase();
    }
}

// Initialiser au chargement de la page
document.addEventListener('DOMContentLoaded', initializeFormation);