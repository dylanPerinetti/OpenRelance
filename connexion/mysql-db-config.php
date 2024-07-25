<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'OpenRelance');

// Utilisateurs et mots de passe
define('DB_USER_ADD', 'user_add');
define('DB_PASS_ADD', 'password_add');

define('DB_USER_READ', 'user_read');
define('DB_PASS_READ', 'password_read');

define('DB_USER_MODIFY', 'user_modify');
define('DB_PASS_MODIFY', 'password_modify');

// Fonction pour obtenir la connexion à la base de données
function get_db_connection($role = 'read') {
    $host = DB_HOST;
    $dbname = DB_NAME;

    switch ($role) {
        case 'add':
            $user = DB_USER_ADD;
            $pass = DB_PASS_ADD;
            break;
        case 'modify':
            $user = DB_USER_MODIFY;
            $pass = DB_PASS_MODIFY;
            break;
        case 'read':
        default:
            $user = DB_USER_READ;
            $pass = DB_PASS_READ;
            break;
    }
    
    try {
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, $user, $pass, $options);
        log_nominal("Connection established for role: $role");
        return $pdo;
    } catch (PDOException $e) {
        log_error("Connection failed: " . $e->getMessage());
        exit;
    }
}

// Fonction pour log les erreurs
function log_error($message) {
    $date = date('Y-m-d H:i:s');
    $log_message = "[$date] $message" . PHP_EOL;
    $log_file = '../log/error/' . date('Y-m') . '.log';
    create_log_directory(dirname($log_file));
    error_log($log_message, 3, $log_file);
}

// Fonction pour log les connexions nominales
function log_nominal($message) {
    $date = date('Y-m-d H:i:s');
    $log_message = "[$date] $message" . PHP_EOL;
    $log_file = '../log/nominal/' . date('Y-m') . '.log';
    create_log_directory(dirname($log_file));
    error_log($log_message, 3, $log_file);
}

// Fonction pour créer le répertoire de log si nécessaire
function create_log_directory($directory) {
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }
}
?>
