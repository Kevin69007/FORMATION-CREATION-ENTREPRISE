<?php
// admin/index.php
session_start();

// Vérification de l'authentification admin
$admin_password = 'admin2024'; // Changez ce mot de passe

if (!isset($_SESSION['admin_logged_in'])) {
    if (isset($_POST['password']) && $_POST['password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        // Afficher le formulaire de connexion admin
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Administration - Formation</title>
            <style>
                body { font-family: Arial, sans-serif; background: #f5f5f5; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
                .admin-login { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); max-width: 400px; width: 100%; }
                .admin-login h1 { color: #2c3e50; margin-bottom: 20px; text-align: center; }
                .form-group { margin-bottom: 20px; }
                .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
                .form-group input { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 4px; font-size: 16px; }
                .login-btn { width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer; }
                .login-btn:hover { background: #0056b3; }
                .error { color: #dc3545; margin-top: 10px; }
            </style>
        </head>
        <body>
            <div class="admin-login">
                <h1>Administration</h1>
                <form method="POST">
                    <div class="form-group">
                        <label for="password">Mot de passe administrateur</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="login-btn">Se connecter</button>
                    <?php if (isset($_POST['password']) && $_POST['password'] !== $admin_password): ?>
                        <div class="error">Mot de passe incorrect</div>
                    <?php endif; ?>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Fonctions utilitaires
function loadUserData() {
    $dataFile = 'data/user_progress.json';
    if (file_exists($dataFile)) {
        return json_decode(file_get_contents($dataFile), true) ?: [];
    }
    return [];
}

function getProgressStats($userData) {
    $stats = [
        'total_users' => count($userData),
        'active_users' => 0,
        'completion_rates' => []
    ];
    
    foreach ($userData as $username => $data) {
        $progress = $data['progress'] ?? [];
        $completedLessons = count(array_filter($progress, function($lesson) {
            return $lesson['completed'] ?? false;
        }));
        
        $completionRate = $completedLessons > 0 ? round(($completedLessons / 77) * 100, 1) : 0; // 77 = nombre total de leçons
        $stats['completion_rates'][$username] = $completionRate;
        
        if ($completionRate > 0) {
            $stats['active_users']++;
        }
    }
    
    return $stats;
}

$userData = loadUserData();
$stats = getProgressStats($userData);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Formation Entrepreneuriat</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8f9fa; }
        .admin-header { background: #2c3e50; color: white; padding: 20px; }
        .admin-header h1 { font-size: 24px; }
        .admin-header .logout { float: right; background: #dc3545; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; }
        .admin-container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        .stat-number { font-size: 36px; font-weight: bold; color: #007bff; margin-bottom: 5px; }
        .stat-label { color: #6c757d; font-size: 14px; }
        .users-table { background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden; }
        .table-header { background: #f8f9fa; padding: 20px; border-bottom: 1px solid #dee2e6; }
        .table-header h2 { color: #2c3e50; }
        table { width: 100%; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #dee2e6; }
        th { background: #f8f9fa; font-weight: 600; color: #2c3e50; }
        .progress-bar { width: 100px; height: 20px; background: #e9ecef; border-radius: 10px; overflow: hidden; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, #dc3545 0%, #ffc107 50%, #28a745 100%); transition: width 0.3s ease; }
        .user-detail { cursor: pointer; color: #007bff; }
        .user-detail:hover { text-decoration: underline; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: white; margin: 5% auto; padding: 20px; width: 80%; max-width: 800px; border-radius: 8px; max-height: 80vh; overflow-y: auto; }
        .close { float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close:hover { color: #dc3545; }
        .lesson-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px; margin-top: 20px; }
        .lesson-item { padding: 10px; border-radius: 6px; font-size: 14px; }
        .lesson-completed { background: #d4edda; color: #155724; }
        .lesson-pending { background: #f8d7da; color: #721c24; }
        .create-student-btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px; font-weight: 600; }
        .create-student-btn:hover { background: #0056b3; }
        .export-btn { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px; }
        
        /* Styles pour le formulaire de création d'étudiant */
        .create-student-section { background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 30px; overflow: hidden; }
        .section-header { background: #f8f9fa; padding: 20px; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center; }
        .section-header h2 { color: #2c3e50; margin: 0; }
        .toggle-form-btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; }
        .toggle-form-btn:hover { background: #0056b3; }
        .create-form { padding: 30px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-weight: 600; color: #2c3e50; margin-bottom: 5px; }
        .form-group input { padding: 12px; border: 2px solid #e1e5e9; border-radius: 6px; font-size: 16px; transition: border-color 0.3s ease; }
        .form-group input:focus { outline: none; border-color: #007bff; }
        .form-actions { display: flex; gap: 15px; justify-content: flex-end; margin-top: 30px; }
        .btn-primary { background: #007bff; color: white; padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .btn-primary:hover { background: #0056b3; }
        .btn-secondary { background: #6c757d; color: white; padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .btn-secondary:hover { background: #545b62; }
        
        /* Styles pour le tableau amélioré */
        .table-actions { display: flex; gap: 10px; }
        .refresh-btn { background: #17a2b8; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .refresh-btn:hover { background: #138496; }
        .table-container { overflow-x: auto; }
        .student-row:hover { background-color: #f8f9fa; }
        .progress-container { display: flex; align-items: center; gap: 10px; }
        .progress-text { font-weight: 600; color: #2c3e50; }
        .lesson-count { font-weight: 600; color: #28a745; }
        .action-buttons { display: flex; gap: 5px; flex-wrap: wrap; }
        .btn-view, .btn-detail, .btn-edit, .btn-delete { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600; }
        .btn-view { background: #007bff; color: white; }
        .btn-detail { background: #6c757d; color: white; }
        .btn-edit { background: #ffc107; color: #212529; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-view:hover, .btn-detail:hover, .btn-edit:hover, .btn-delete:hover { opacity: 0.8; }
        
        /* Styles pour le modal du dashboard étudiant */
        .student-dashboard-modal .modal-content { width: 95%; max-width: 1400px; max-height: 90vh; }
        .large-modal { width: 95%; max-width: 1400px; }
        .student-dashboard-header { background: #f8f9fa; padding: 20px; border-bottom: 1px solid #dee2e6; margin-bottom: 20px; }
        .student-dashboard-header h2 { color: #2c3e50; margin: 0 0 15px 0; }
        .student-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
        .student-info-item { background: white; padding: 15px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .student-info-item h4 { color: #2c3e50; margin: 0 0 5px 0; font-size: 14px; }
        .student-info-item p { color: #6c757d; margin: 0; font-size: 16px; font-weight: 600; }
        .student-dashboard-content { padding: 20px; }
        
        /* Styles pour la simulation du dashboard étudiant */
        .student-dashboard-simulation { background: #f8f9fa; border-radius: 8px; padding: 20px; }
        .dashboard-header { text-align: center; margin-bottom: 30px; }
        .dashboard-header h3 { color: #2c3e50; margin-bottom: 10px; }
        .dashboard-header p { color: #6c757d; }
        .dashboard-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .dashboard-stats .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; }
        .dashboard-stats .stat-card h4 { color: #2c3e50; margin-bottom: 15px; }
        .dashboard-stats .stat-card .progress-bar { margin: 10px 0; }
        .dashboard-stats .stat-card .stat-number { font-size: 24px; font-weight: bold; color: #007bff; }
        .modules-overview { margin-bottom: 30px; }
        .modules-overview h4 { color: #2c3e50; margin-bottom: 20px; }
        .modules-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px; }
        .module-card { background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .module-card h5 { color: #007bff; margin-bottom: 10px; }
        .module-card p { color: #6c757d; font-size: 14px; margin-bottom: 15px; }
        .module-progress { display: flex; align-items: center; gap: 10px; }
        .module-progress .progress-bar { flex: 1; }
        .module-progress span { font-size: 12px; color: #6c757d; }
        .recent-activity h4 { color: #2c3e50; margin-bottom: 20px; }
        .activity-list { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .activity-item { padding: 10px 0; border-bottom: 1px solid #e9ecef; color: #6c757d; }
        .activity-item:last-child { border-bottom: none; }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>Administration - Formation Entrepreneuriat</h1>
        <a href="?logout=1" class="logout">Déconnexion</a>
        <div style="clear: both;"></div>
    </div>

    <div class="admin-container">
        <!-- Formulaire de création d'étudiant (masqué par défaut) -->
        <div class="create-student-section" id="createStudentSection" style="display: none;">
            <div class="section-header">
                <h2>👨‍🎓 Créer un nouveau compte étudiant</h2>
                <button class="toggle-form-btn" onclick="toggleCreateForm()">− Masquer</button>
            </div>
            <div class="create-form" id="createForm">
                <form id="studentForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">Prénom *</label>
                            <input type="text" id="firstName" name="firstName" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Nom *</label>
                            <input type="text" id="lastName" name="lastName" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="username">Nom d'utilisateur *</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Mot de passe *</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Confirmer le mot de passe *</label>
                            <input type="password" id="confirmPassword" name="confirmPassword" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="enrollmentDate">Date d'inscription *</label>
                            <input type="date" id="enrollmentDate" name="enrollmentDate" required>
                        </div>
                        <div class="form-group">
                            <!-- Champ vide pour l'alignement -->
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" onclick="resetForm()" class="btn-secondary">Annuler</button>
                        <button type="submit" class="btn-primary">Créer le compte</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistiques globales -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_users']; ?></div>
                <div class="stat-label">Utilisateurs inscrits</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['active_users']; ?></div>
                <div class="stat-label">Utilisateurs actifs</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total_users'] > 0 ? round(array_sum($stats['completion_rates']) / $stats['total_users'], 1) : 0; ?>%</div>
                <div class="stat-label">Progression moyenne</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">13</div>
                <div class="stat-label">Modules disponibles</div>
            </div>
        </div>

        <!-- Tableau des utilisateurs -->
        <div class="users-table">
            <div class="table-header">
                <h2>📊 Liste des étudiants</h2>
                <div class="table-actions">
                    <button class="create-student-btn" onclick="toggleCreateForm()">👨‍🎓 Nouvel étudiant</button>
                    <button class="refresh-btn" onclick="refreshStudents()">🔄 Actualiser</button>
                    <button class="export-btn" onclick="exportData()">📥 Exporter CSV</button>
                </div>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Nom d'utilisateur</th>
                            <th>Nom complet</th>
                            <th>Email</th>
                            <th>Date de création</th>
                            <th>Progression</th>
                            <th>Leçons terminées</th>
                            <th>Dernière activité</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="studentsTableBody">
                        <?php foreach ($userData as $username => $data): ?>
                            <?php
                            $progress = $data['progress'] ?? [];
                            $completedCount = count(array_filter($progress, function($lesson) {
                                return $lesson['completed'] ?? false;
                            }));
                            $completionRate = $stats['completion_rates'][$username] ?? 0;
                            $lastActivity = $data['last_activity'] ?? 'Jamais';
                            $firstName = $data['firstName'] ?? '';
                            $lastName = $data['lastName'] ?? '';
                            $email = $data['email'] ?? '';
                            $fullName = trim($firstName . ' ' . $lastName);
                            $creationDate = $data['enrollment_date'] ?? $data['first_login'] ?? $data['created_at'] ?? 'N/A';
                            ?>
                            <tr class="student-row" data-username="<?php echo htmlspecialchars($username); ?>">
                                <td><strong><?php echo htmlspecialchars($username); ?></strong></td>
                                <td><?php echo htmlspecialchars($fullName ?: 'Non renseigné'); ?></td>
                                <td><?php echo htmlspecialchars($email ?: 'Non renseigné'); ?></td>
                                <td><?php echo $creationDate !== 'N/A' ? date('d/m/Y', strtotime($creationDate)) : 'N/A'; ?></td>
                                <td>
                                    <div class="progress-container">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?php echo $completionRate; ?>%"></div>
                                        </div>
                                        <small class="progress-text"><?php echo $completionRate; ?>%</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="lesson-count"><?php echo $completedCount; ?> / 77</span>
                                </td>
                                <td><?php echo $lastActivity !== 'Jamais' ? date('d/m/Y H:i', strtotime($lastActivity)) : 'Jamais'; ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-view" onclick="viewStudentDashboard('<?php echo $username; ?>')" title="Voir le dashboard">
                                            👁️ Dashboard
                                        </button>
                                        <button class="btn-detail" onclick="showUserDetail('<?php echo $username; ?>')" title="Détails">
                                            📋 Détails
                                        </button>
                                        <button class="btn-edit" onclick="editStudent('<?php echo $username; ?>')" title="Modifier">
                                            ✏️ Modifier
                                        </button>
                                        <button class="btn-delete" onclick="deleteStudent('<?php echo $username; ?>')" title="Supprimer">
                                            🗑️ Supprimer
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal pour les détails utilisateur -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle">Détails utilisateur</h2>
            <div id="modalContent"></div>
        </div>
    </div>

    <!-- Modal pour le dashboard étudiant -->
    <div id="studentDashboardModal" class="modal student-dashboard-modal">
        <div class="modal-content large-modal">
            <span class="close" onclick="closeStudentDashboard()">&times;</span>
            <div class="student-dashboard-header">
                <h2 id="studentDashboardTitle">Dashboard de l'étudiant</h2>
                <div class="student-info" id="studentInfo"></div>
            </div>
            <div id="studentDashboardContent" class="student-dashboard-content">
                <!-- Le contenu du dashboard sera chargé ici -->
            </div>
        </div>
    </div>

    <script>
        const userData = <?php echo json_encode($userData); ?>;

        function showUserDetail(username) {
            const user = userData[username];
            if (!user) return;

            document.getElementById('modalTitle').textContent = `Progression de ${username}`;
            
            let content = `<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">`;
            content += `<div><strong>Prénom:</strong> ${user.firstName || 'Non renseigné'}</div>`;
            content += `<div><strong>Nom:</strong> ${user.lastName || 'Non renseigné'}</div>`;
            content += `<div><strong>Email:</strong> ${user.email || 'Non renseigné'}</div>`;
            content += `<div><strong>Première connexion:</strong> ${user.first_login ? new Date(user.first_login).toLocaleDateString() : 'N/A'}</div>`;
            content += `<div><strong>Dernière activité:</strong> ${user.last_activity ? new Date(user.last_activity).toLocaleString() : 'N/A'}</div>`;
            content += `<div><strong>Nombre de sessions:</strong> ${user.session_count || 0}</div>`;
            content += `</div>`;
            
            content += `<h3>Progression par leçon:</h3>`;
            content += `<div class="lesson-grid">`;

            const progress = user.progress || {};
            for (let moduleId = 1; moduleId <= 13; moduleId++) {
                // Vous devrez adapter selon votre structure exacte
                const moduleLessons = getModuleLessons(moduleId);
                moduleLessons.forEach(lessonId => {
                    const lessonKey = `module_${moduleId}_lesson_${lessonId}`;
                    const isCompleted = progress[lessonKey] && progress[lessonKey].completed;
                    const completedDate = isCompleted ? new Date(progress[lessonKey].completedAt).toLocaleDateString() : '';
                    
                    content += `<div class="lesson-item ${isCompleted ? 'lesson-completed' : 'lesson-pending'}">
                        Module ${moduleId} - Leçon ${lessonId}
                        ${isCompleted ? `<br><small>Terminé le ${completedDate}</small>` : '<br><small>Non terminé</small>'}
                    </div>`;
                });
            }

            content += `</div>`;
            document.getElementById('modalContent').innerHTML = content;
            document.getElementById('userModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('userModal').style.display = 'none';
        }

        function exportData() {
            // Utiliser l'API backend pour l'export
            window.open('api.php?action=export_csv', '_blank');
        }

        function getModuleLessons(moduleId) {
            // Structure simplifiée - vous pouvez l'adapter selon vos besoins
            const moduleLessons = {
                1: [1, 2, 3, 4, 5, 6, 7, 8],
                2: [1, 2, 3, 4, 5, 6],
                3: [1, 2, 3, 4, 5, 6, 7],
                4: [1, 2, 3, 4, 5],
                5: [1, 2, 3, 4],
                6: [1, 2, 3, 4, 5],
                7: [1, 2, 3, 4],
                8: [1, 2, 3, 4],
                9: [1, 2, 3, 4],
                10: [1, 2, 3, 4, 5],
                11: [1, 2, 3, 4],
                12: [1, 2, 3, 4, 5],
                13: [1]
            };
            return moduleLessons[moduleId] || [];
        }

        // Fermer le modal en cliquant en dehors
        window.onclick = function(event) {
            const modal = document.getElementById('userModal');
            const studentModal = document.getElementById('studentDashboardModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
            if (event.target == studentModal) {
                studentModal.style.display = 'none';
            }
        }

        // Fonctions pour le formulaire de création d'étudiant
        function toggleCreateForm() {
            const section = document.getElementById('createStudentSection');
            const btn = document.querySelector('.create-student-btn');
            const toggleBtn = document.querySelector('.toggle-form-btn');
            
            if (section.style.display === 'none') {
                section.style.display = 'block';
                btn.textContent = '− Masquer';
                if (toggleBtn) toggleBtn.textContent = '− Masquer';
                // Définir la date d'aujourd'hui par défaut
                document.getElementById('enrollmentDate').value = new Date().toISOString().split('T')[0];
            } else {
                section.style.display = 'none';
                btn.textContent = '👨‍🎓 Nouvel étudiant';
                if (toggleBtn) toggleBtn.textContent = '+ Nouvel étudiant';
            }
        }

        function resetForm() {
            document.getElementById('studentForm').reset();
            document.getElementById('createStudentSection').style.display = 'none';
            document.querySelector('.create-student-btn').textContent = '👨‍🎓 Nouvel étudiant';
            const toggleBtn = document.querySelector('.toggle-form-btn');
            if (toggleBtn) toggleBtn.textContent = '+ Nouvel étudiant';
        }

        // Gestion du formulaire de création d'étudiant
        document.getElementById('studentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const studentData = {
                firstName: formData.get('firstName'),
                lastName: formData.get('lastName'),
                email: formData.get('email'),
                username: formData.get('username'),
                password: formData.get('password'),
                confirmPassword: formData.get('confirmPassword'),
                enrollmentDate: formData.get('enrollmentDate')
            };

            // Validation
            if (studentData.password !== studentData.confirmPassword) {
                alert('Les mots de passe ne correspondent pas');
                return;
            }

            if (studentData.password.length < 6) {
                alert('Le mot de passe doit contenir au moins 6 caractères');
                return;
            }

            // Vérifier si l'utilisateur existe déjà
            if (userData[studentData.username]) {
                alert('Ce nom d\'utilisateur existe déjà');
                return;
            }

            // Créer le compte étudiant
            createStudentAccount(studentData);
        });

        function createStudentAccount(studentData) {
            fetch('api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'create_student',
                    studentData: studentData
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Compte étudiant créé avec succès !');
                    resetForm();
                    refreshStudents();
                } else {
                    alert('Erreur lors de la création du compte: ' + (data.error || 'Erreur inconnue'));
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la création du compte');
            });
        }

        function refreshStudents() {
            location.reload();
        }

        function viewStudentDashboard(username) {
            const user = userData[username];
            if (!user) {
                alert('Utilisateur non trouvé');
                return;
            }

            document.getElementById('studentDashboardTitle').textContent = `Dashboard de ${username}`;
            
            // Afficher les informations de l'étudiant
            const studentInfo = document.getElementById('studentInfo');
            studentInfo.innerHTML = `
                <div class="student-info-item">
                    <h4>Nom complet</h4>
                    <p>${user.firstName || ''} ${user.lastName || ''}</p>
                </div>
                <div class="student-info-item">
                    <h4>Email</h4>
                    <p>${user.email || 'Non renseigné'}</p>
                </div>
                <div class="student-info-item">
                    <h4>Date de création</h4>
                    <p>${user.first_login ? new Date(user.first_login).toLocaleDateString() : 'N/A'}</p>
                </div>
                <div class="student-info-item">
                    <h4>Dernière activité</h4>
                    <p>${user.last_activity ? new Date(user.last_activity).toLocaleString() : 'Jamais'}</p>
                </div>
                <div class="student-info-item">
                    <h4>Progression</h4>
                    <p>${user.completion_rate || 0}%</p>
                </div>
                <div class="student-info-item">
                    <h4>Leçons terminées</h4>
                    <p>${user.completed_lessons || 0} / 77</p>
                </div>
            `;

            // Charger le contenu du dashboard (simulation du dashboard étudiant)
            loadStudentDashboardContent(username, user);
            
            document.getElementById('studentDashboardModal').style.display = 'block';
        }

        function loadStudentDashboardContent(username, user) {
            const content = document.getElementById('studentDashboardContent');
            
            // Simuler le contenu du dashboard étudiant
            content.innerHTML = `
                <div class="student-dashboard-simulation">
                    <div class="dashboard-header">
                        <h3>🎓 Formation Entrepreneuriat - Vue Étudiant</h3>
                        <p>Simulation du dashboard tel que l'étudiant le voit</p>
                    </div>
                    
                    <div class="dashboard-stats">
                        <div class="stat-card">
                            <h4>Progression Globale</h4>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: ${user.completion_rate || 0}%"></div>
                            </div>
                            <p>${user.completion_rate || 0}% complété</p>
                        </div>
                        <div class="stat-card">
                            <h4>Leçons Terminées</h4>
                            <p class="stat-number">${user.completed_lessons || 0} / 77</p>
                        </div>
                        <div class="stat-card">
                            <h4>Modules Disponibles</h4>
                            <p class="stat-number">13</p>
                        </div>
                    </div>

                    <div class="modules-overview">
                        <h4>📚 Modules de Formation</h4>
                        <div class="modules-grid">
                            ${generateModulesOverview(user.progress || {})}
                        </div>
                    </div>

                    <div class="recent-activity">
                        <h4>📈 Activité Récente</h4>
                        <div class="activity-list">
                            ${generateRecentActivity(user)}
                        </div>
                    </div>
                </div>
            `;
        }

        function generateModulesOverview(progress) {
            const modules = [
                { id: 1, title: "Mettre ses compétences au service de son projet", lessons: 8 },
                { id: 2, title: "Connaître son marché pour mieux vendre", lessons: 6 },
                { id: 3, title: "Définir les besoins et la rentabilité du projet", lessons: 7 },
                { id: 4, title: "Choisir une structure juridique appropriée", lessons: 5 },
                { id: 5, title: "Comprendre les différents régimes fiscaux", lessons: 4 },
                { id: 6, title: "Connaître les principales aides à la création d'entreprise", lessons: 5 },
                { id: 7, title: "Où s'adresser pour déclarer son entreprise", lessons: 4 },
                { id: 8, title: "Atouts de la reprise d'entreprise", lessons: 4 },
                { id: 9, title: "Obtenir les premières informations sur les structures juridiques", lessons: 4 },
                { id: 10, title: "Trouver ses clients en étudiant son marché", lessons: 5 },
                { id: 11, title: "Valoriser son offre et choisir son circuit de distribution", lessons: 4 },
                { id: 12, title: "Cibler les actions commerciales adaptées à ses clients", lessons: 5 },
                { id: 13, title: "Études de cas", lessons: 1 }
            ];

            return modules.map(module => {
                let completedLessons = 0;
                for (let i = 1; i <= module.lessons; i++) {
                    const lessonKey = `module_${module.id}_lesson_${i}`;
                    if (progress[lessonKey] && progress[lessonKey].completed) {
                        completedLessons++;
                    }
                }
                const progressPercent = Math.round((completedLessons / module.lessons) * 100);
                
                return `
                    <div class="module-card">
                        <h5>Module ${module.id}</h5>
                        <p>${module.title}</p>
                        <div class="module-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: ${progressPercent}%"></div>
                            </div>
                            <span>${completedLessons}/${module.lessons} leçons</span>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function generateRecentActivity(user) {
            const activities = [];
            
            if (user.last_activity) {
                activities.push(`<div class="activity-item">Dernière connexion: ${new Date(user.last_activity).toLocaleString()}</div>`);
            }
            
            if (user.completed_lessons > 0) {
                activities.push(`<div class="activity-item">${user.completed_lessons} leçons terminées</div>`);
            }
            
            if (user.session_count) {
                activities.push(`<div class="activity-item">${user.session_count} sessions de formation</div>`);
            }
            
            return activities.length > 0 ? activities.join('') : '<div class="activity-item">Aucune activité récente</div>';
        }

        function closeStudentDashboard() {
            document.getElementById('studentDashboardModal').style.display = 'none';
        }

        function editStudent(username) {
            const user = userData[username];
            if (!user) return;
            
            // Remplir le formulaire avec les données existantes
            document.getElementById('firstName').value = user.firstName || '';
            document.getElementById('lastName').value = user.lastName || '';
            document.getElementById('email').value = user.email || '';
            document.getElementById('username').value = username;
            document.getElementById('username').readOnly = true;
            document.getElementById('enrollmentDate').value = user.enrollment_date ? user.enrollment_date.split('T')[0] : '';
            
            // Afficher le formulaire
            document.getElementById('createStudentSection').style.display = 'block';
            document.querySelector('.create-student-btn').textContent = '− Masquer';
            const toggleBtn = document.querySelector('.toggle-form-btn');
            if (toggleBtn) toggleBtn.textContent = '− Masquer';
            
            // Changer le bouton de soumission
            const submitBtn = document.querySelector('#studentForm button[type="submit"]');
            submitBtn.textContent = 'Mettre à jour';
            submitBtn.onclick = function(e) {
                e.preventDefault();
                updateStudent(username);
            };
        }

        function updateStudent(username) {
            const formData = new FormData(document.getElementById('studentForm'));
            const studentData = {
                firstName: formData.get('firstName'),
                lastName: formData.get('lastName'),
                email: formData.get('email'),
                username: formData.get('username'),
                enrollmentDate: formData.get('enrollmentDate')
            };

            fetch('api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'update_student',
                    username: username,
                    studentData: studentData
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Étudiant mis à jour avec succès !');
                    resetForm();
                    refreshStudents();
                } else {
                    alert('Erreur lors de la mise à jour: ' + (data.error || 'Erreur inconnue'));
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la mise à jour');
            });
        }

        function deleteStudent(username) {
            if (confirm(`Êtes-vous sûr de vouloir supprimer l'étudiant "${username}" ?`)) {
                fetch('api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'delete_student',
                        username: username
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Étudiant supprimé avec succès !');
                        refreshStudents();
                    } else {
                        alert('Erreur lors de la suppression: ' + (data.error || 'Erreur inconnue'));
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la suppression');
                });
            }
        }
    </script>
</body>
</html>

<?php
// Gestion de la déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ../index.html');
    exit;
}
?>