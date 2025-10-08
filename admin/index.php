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
        .export-btn { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px; }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1>Administration - Formation Entrepreneuriat</h1>
        <a href="?logout=1" class="logout">Déconnexion</a>
        <div style="clear: both;"></div>
    </div>

    <div class="admin-container">
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
                <h2>Progression des utilisateurs</h2>
                <button class="export-btn" onclick="exportData()">Exporter CSV</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Nom complet</th>
                        <th>Email</th>
                        <th>Progression</th>
                        <th>Leçons terminées</th>
                        <th>Dernière activité</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
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
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($username); ?></td>
                            <td><?php echo htmlspecialchars($fullName ?: 'Non renseigné'); ?></td>
                            <td><?php echo htmlspecialchars($email ?: 'Non renseigné'); ?></td>
                            <td>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo $completionRate; ?>%"></div>
                                </div>
                                <small><?php echo $completionRate; ?>%</small>
                            </td>
                            <td><?php echo $completedCount; ?> / 77</td>
                            <td><?php echo date('d/m/Y H:i', strtotime($lastActivity)); ?></td>
                            <td>
                                <span class="user-detail" onclick="showUserDetail('<?php echo $username; ?>')">Voir détail</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
            if (event.target == modal) {
                modal.style.display = 'none';
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