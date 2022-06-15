<?php

use UserNS\User;

// Requirements:
// MUST have run session_start() before!
// MUST have called: require_once '../config/db_connection.php';
// MUST have called: require_once '../models/user.php';
function login($user_email, $user_password): bool
{
    if (isset($_REQUEST["login"])) {
        $db = getDb();
        // Vai buscar o utilizador
        $select_stmt = $db->prepare("SELECT `user_id`,`email`,`pwd`,`role` from users WHERE email=:uemail");
        // Injeta o valor do email
        $select_stmt->bindParam(":uemail", $user_email);
        $select_stmt->execute();
        $result = $select_stmt->fetch(PDO::FETCH_ASSOC);
        // Itera os resultados
        while ($result) {
            $dbuser_id = $result["user_id"];
            $dbuser_email = $result["email"];
            $dbuser_password = $result["pwd"];
            $dbuser_role = $result["role"];
            // Validar hash da password da DB com a que o user introduziu no formulário de login
            $pwd_check = password_verify($user_password, $dbuser_password);
            if ($pwd_check) {
                // Se validar com a hash, guarda os dados na sessão
                $_SESSION["user_id"] = $dbuser_id;
                $_SESSION["user_role"] = $dbuser_role;
                $_SESSION["user_email"] = $dbuser_email;
                // Gera login token que guarda uma validade e é associado só a este user (email)
                $_SESSION["user_loginToken"] = generateLoginToken($dbuser_email);
                return true;
            }
        }
    }
    return false;
}

// Registers the user, with it's data.
function register($user_email, $user_password, $user_name, $user_gender): bool
{
    if (isset($_REQUEST["register"])) {
        $db = getDb();
        // Insere utilizador diretamente na base de dados
        $insert_stmt = $db->prepare("INSERT INTO users (`name`,email,`role`,gender,pwd) VALUES(?, ? , ?, ?, ?);");
        $result = $insert_stmt->execute([$user_name, $user_email, User::$USER_ROLE_PATIENT, $user_gender, password_hash($user_password, PASSWORD_DEFAULT)]);
        // se o execute() retornar "false" então apresentamos erro no ecrã
        return $result;
    }
    return false;
}

// Deletes the user sessiona and redirects to home page.
function logout($returnTo = "/DermoClinic"): bool
{
    if (isset($_REQUEST["logout"])) {
        // _logout quando carregamos no botão de logout na UI
        _logout($returnTo);
    }
    return false;
}
function _logout($returnTo = "/DermoClinic")
{
    // destruir objeto $_SESSION
    $result = session_destroy();
    // Voltar para um URL especificado
    header("Location: $returnTo");
    return $result;
}

// Generates a login token to validate user session through the site.
function generateLoginToken($user_email): string
{
    $jwt = (new JWT());
    $payload = [
        'id' => '1',
        'email' => $user_email,
        'iss' => 'jwt.local',
        'aud' => 'example.com'
    ];
    return $jwt->generate($payload);
}

// Checks if the user session is valid.
function validateSessionToken(): bool
{
    if (isset($_SESSION) && isset($_SESSION["user_loginToken"])) {
        $jwt = (new JWT());
        $is_valid = $jwt->is_valid($_SESSION["user_loginToken"]);
        if ($is_valid) {
            return true;
        }
    }
    // If no session or invalid session, logout:
    _logout();
    return false;
}

// Redirects on home, login and registration to the user home, if session exists. 
// Esta função com alguma lógica adicional consegue garantir o redirecionamento correto dos utilizadores para a dashboard correta.
function onSessionRedirect(): void
{
    if (!isset($_SESSION["user_role"])) {
        return;
    }
    switch ($_SESSION["user_role"]) {
        case User::$USER_ROLE_DOCTOR:
            header("Location: /DermoClinic/home/doctor_home.php");
            break;
        case User::$USER_ROLE_PATIENT:
            header("Location: /DermoClinic/home/patient_home.php");
            break;
        case User::$USER_ROLE_ADMIN:
            header("Location: /DermoClinic/home/admin_home.php");
            break;
        default:
            return;
    }
}
logout();
