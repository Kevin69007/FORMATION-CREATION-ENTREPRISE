<?php
// admin/api.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gérer les requêtes OPTIONS pour CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Créer le dossier data s'il n'existe pas
$dataDir = 'data';
if (!file_exists($dataDir)) {
    mkdir($dataDir, 0777, true);
}

$dataFile = $dataDir . '/user_progress.json';

function loadUserData() {
    global $dataFile;
    if (file_exists($dataFile)) {
        $content = file_get_contents($dataFile);
        return json_decode($content, true) ?: [];
    }
    return [];
}

function saveUserData($data) {
    global $dataFile;
    return file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
}

function logActivity($message) {
    $logFile = 'data/activity.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

// Traitement des requêtes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid JSON data']);
        exit;
    }
    
    $action = $data['action'] ?? '';
    
    switch ($action) {
        case 'update_progress':
            $username = $data['username'] ?? '';
            $progress = $data['progress'] ?? [];
            $timestamp = $data['timestamp'] ?? date('c');
            
            if (!$username) {
                http_response_code(400);
                echo json_encode(['error' => 'Username required']);
                exit;
            }
            
            // Charger les données existantes
            $userData = loadUserData();
            
            // Mettre à jour les données de l'utilisateur
            if (!isset($userData[$username])) {
                $userData[$username] = [
                    'first_login' => $timestamp,
                    'progress' => [],
                    'session_count' => 0
                ];
            }
            
            $userData[$username]['progress'] = $progress;
            $userData[$username]['last_activity'] = $timestamp;
            $userData[$username]['session_count'] = ($userData[$username]['session_count'] ?? 0) + 1;
            
            // Calculer les statistiques
            $completedLessons = count(array_filter($progress, function($lesson) {
                return $lesson['completed'] ?? false;
            }));
            $userData[$username]['completed_lessons'] = $completedLessons;
            $userData[$username]['completion_rate'] = round(($completedLessons / 77) * 100, 1); // 77 = total des leçons
            
            // Sauvegarder
            if (saveUserData($userData)) {
                logActivity("Progress updated for user: $username (Completed: $completedLessons/77)");
                echo json_encode([
                    'success' => true,
                    'message' => 'Progress updated successfully',
                    'stats' => [
                        'completed_lessons' => $completedLessons,
                        'completion_rate' => $userData[$username]['completion_rate']
                    ]
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to save data']);
            }
            break;
            
        case 'user_login':
            $username = $data['username'] ?? '';
            $timestamp = $data['timestamp'] ?? date('c');
            
            if (!$username) {
                http_response_code(400);
                echo json_encode(['error' => 'Username required']);
                exit;
            }
            
            $userData = loadUserData();
            
            if (!isset($userData[$username])) {
                $userData[$username] = [
                    'first_login' => $timestamp,
                    'progress' => [],
                    'session_count' => 0,
                    'firstName' => '',
                    'lastName' => '',
                    'email' => ''
                ];
                logActivity("New user registered: $username");
            }
            
            $userData[$username]['last_login'] = $timestamp;
            $userData[$username]['session_count'] = ($userData[$username]['session_count'] ?? 0) + 1;
            
            saveUserData($userData);
            logActivity("User login: $username");
            
            echo json_encode([
                'success' => true,
                'message' => 'Login recorded',
                'user_data' => $userData[$username]
            ]);
            break;
            
        case 'update_profile':
            $username = $data['username'] ?? '';
            $userData = $data['userData'] ?? [];
            $timestamp = $data['timestamp'] ?? date('c');
            
            if (!$username) {
                http_response_code(400);
                echo json_encode(['error' => 'Username required']);
                exit;
            }
            
            $allUserData = loadUserData();
            
            if (!isset($allUserData[$username])) {
                $allUserData[$username] = [
                    'first_login' => $timestamp,
                    'progress' => [],
                    'session_count' => 0
                ];
            }
            
            // Mettre à jour les informations du profil
            if (isset($userData['firstName'])) {
                $allUserData[$username]['firstName'] = $userData['firstName'];
            }
            if (isset($userData['lastName'])) {
                $allUserData[$username]['lastName'] = $userData['lastName'];
            }
            if (isset($userData['email'])) {
                $allUserData[$username]['email'] = $userData['email'];
            }
            
            $allUserData[$username]['last_activity'] = $timestamp;
            
            if (saveUserData($allUserData)) {
                logActivity("Profile updated for user: $username");
                echo json_encode([
                    'success' => true,
                    'message' => 'Profile updated successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to save profile data']);
            }
            break;
            
        case 'create_student':
            $studentData = $data['studentData'] ?? [];
            $timestamp = date('c');
            
            // Validation des champs requis
            $requiredFields = ['username', 'firstName', 'lastName', 'email', 'password'];
            $missingFields = [];
            foreach ($requiredFields as $field) {
                if (empty($studentData[$field])) {
                    $missingFields[] = $field;
                }
            }
            
            if (!empty($missingFields)) {
                http_response_code(400);
                echo json_encode(['error' => 'Champs manquants: ' . implode(', ', $missingFields)]);
                exit;
            }
            
            // Validation de l'email
            if (!filter_var($studentData['email'], FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(['error' => 'Adresse email invalide']);
                exit;
            }
            
            // Validation du mot de passe
            if (strlen($studentData['password']) < 6) {
                http_response_code(400);
                echo json_encode(['error' => 'Le mot de passe doit contenir au moins 6 caractères']);
                exit;
            }
            
            $allUserData = loadUserData();
            
            // Vérifier si l'utilisateur existe déjà
            if (isset($allUserData[$studentData['username']])) {
                http_response_code(400);
                echo json_encode(['error' => 'Ce nom d\'utilisateur existe déjà']);
                exit;
            }
            
            // Vérifier si l'email existe déjà
            foreach ($allUserData as $user) {
                if (isset($user['email']) && $user['email'] === $studentData['email']) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Cette adresse email est déjà utilisée']);
                    exit;
                }
            }
            
            // Créer le nouvel étudiant
            $allUserData[$studentData['username']] = [
                'firstName' => trim($studentData['firstName']),
                'lastName' => trim($studentData['lastName']),
                'email' => trim($studentData['email']),
                'password' => password_hash($studentData['password'], PASSWORD_DEFAULT),
                'created_at' => $timestamp,
                'enrollment_date' => $studentData['enrollmentDate'] ?? $timestamp,
                'first_login' => null,
                'last_activity' => null,
                'progress' => [],
                'session_count' => 0,
                'completed_lessons' => 0,
                'completion_rate' => 0
            ];
            
            if (saveUserData($allUserData)) {
                logActivity("New student created: {$studentData['username']} ({$studentData['firstName']} {$studentData['lastName']})");
                echo json_encode([
                    'success' => true,
                    'message' => 'Compte étudiant créé avec succès',
                    'student' => [
                        'username' => $studentData['username'],
                        'firstName' => $studentData['firstName'],
                        'lastName' => $studentData['lastName'],
                        'email' => $studentData['email']
                    ]
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erreur lors de la sauvegarde des données. Vérifiez les permissions du dossier data/']);
            }
            break;
            
        case 'update_student':
            $username = $data['username'] ?? '';
            $studentData = $data['studentData'] ?? [];
            $timestamp = date('c');
            
            if (!$username) {
                http_response_code(400);
                echo json_encode(['error' => 'Nom d\'utilisateur requis']);
                exit;
            }
            
            $allUserData = loadUserData();
            
            if (!isset($allUserData[$username])) {
                http_response_code(404);
                echo json_encode(['error' => 'Étudiant non trouvé']);
                exit;
            }
            
            // Mettre à jour les informations de l'étudiant
            if (isset($studentData['firstName'])) {
                $allUserData[$username]['firstName'] = $studentData['firstName'];
            }
            if (isset($studentData['lastName'])) {
                $allUserData[$username]['lastName'] = $studentData['lastName'];
            }
            if (isset($studentData['email'])) {
                $allUserData[$username]['email'] = $studentData['email'];
            }
            if (isset($studentData['enrollmentDate'])) {
                $allUserData[$username]['enrollment_date'] = $studentData['enrollmentDate'];
            }
            
            $allUserData[$username]['last_activity'] = $timestamp;
            
            if (saveUserData($allUserData)) {
                logActivity("Student updated: $username");
                echo json_encode([
                    'success' => true,
                    'message' => 'Étudiant mis à jour avec succès'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erreur lors de la mise à jour']);
            }
            break;
            
        case 'delete_student':
            $username = $data['username'] ?? '';
            
            if (!$username) {
                http_response_code(400);
                echo json_encode(['error' => 'Nom d\'utilisateur requis']);
                exit;
            }
            
            $allUserData = loadUserData();
            
            if (!isset($allUserData[$username])) {
                http_response_code(404);
                echo json_encode(['error' => 'Étudiant non trouvé']);
                exit;
            }
            
            // Supprimer l'étudiant
            unset($allUserData[$username]);
            
            if (saveUserData($allUserData)) {
                logActivity("Student deleted: $username");
                echo json_encode([
                    'success' => true,
                    'message' => 'Étudiant supprimé avec succès'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erreur lors de la suppression']);
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
}

// Traitement des requêtes GET (pour l'admin)
elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'get_all_users':
            $userData = loadUserData();
            echo json_encode($userData);
            break;
            
        case 'get_user':
            $username = $_GET['username'] ?? '';
            if (!$username) {
                http_response_code(400);
                echo json_encode(['error' => 'Username required']);
                exit;
            }
            
            $userData = loadUserData();
            if (isset($userData[$username])) {
                echo json_encode($userData[$username]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
            }
            break;
            
        case 'get_stats':
            $userData = loadUserData();
            $totalUsers = count($userData);
            $activeUsers = 0;
            $totalCompletions = 0;
            
            foreach ($userData as $user) {
                $completedLessons = $user['completed_lessons'] ?? 0;
                if ($completedLessons > 0) {
                    $activeUsers++;
                }
                $totalCompletions += $completedLessons;
            }
            
            $avgCompletion = $totalUsers > 0 ? round($totalCompletions / ($totalUsers * 77) * 100, 1) : 0;
            
            echo json_encode([
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'average_completion' => $avgCompletion,
                'total_lessons' => 77
            ]);
            break;
            
        case 'export_csv':
            $userData = loadUserData();
            
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="formation_progress_' . date('Y-m-d') . '.csv"');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Utilisateur', 'Prenom', 'Nom', 'Email', 'Premiere_Connexion', 'Derniere_Activite', 'Sessions', 'Lecons_Terminees', 'Pourcentage_Completion']);
            
            foreach ($userData as $username => $data) {
                fputcsv($output, [
                    $username,
                    $data['firstName'] ?? '',
                    $data['lastName'] ?? '',
                    $data['email'] ?? '',
                    $data['first_login'] ?? '',
                    $data['last_activity'] ?? '',
                    $data['session_count'] ?? 0,
                    $data['completed_lessons'] ?? 0,
                    ($data['completion_rate'] ?? 0) . '%'
                ]);
            }
            
            fclose($output);
            exit;
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
}

else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>