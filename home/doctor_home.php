<?php
session_start();
require_once '../models/user.php';

use UserNS\User;

require_once '../config/db_connection.php';
require_once '../config/jwt.php';
require_once '../functions/session_functions.php';

$session_is_valid = validateSessionToken();
if ($session_is_valid && $_SESSION["user_role"] != User::$USER_ROLE_DOCTOR) {
    onSessionRedirect();
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/site.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

    <title>DermoClinic</title>
</head>
<!--N-->

<body>
    <!--Navbar-->
    <nav class="navbar">
        <!--Logo-->
        <a class="navbar-brand" href="#">
            <img src="../assets/images/Logo.svg" width="170" height="56,6" alt="">
        </a>
        <ul class="nav-links">
            <li>
                <a href="check_appointment_requests/check_appointment_requests.php">Ver Pedidos de Consulta</a>
            </li>
            <li>
                <a href="appointment_list/appointment_list.php">Ver Marcações</a>
            </li>
            <li>
                <a href="appointment_history/appointment_history.php">Histórico</a>
            </li>
            <li>
                <form method="POST">
                    <button type="submit" class="btn btn-sm btn-outline-secondary" name="logout">Logout</button>
                </form>
            </li>
        </ul>
        <!--Burger 2-->
        <div class="burger">
            <div class="line1"></div>
            <div class="line2"></div>
            <div class="line3"></div>
        </div>
        </div>
    <main id="main">
       
    </main>
</body>
    </nav> 
    <section class="page-section" id="admin-dash">
            <h1 class="large text-muted">Bem-vindo, Doutor(a)!</h1>
        </section>
    <div class="banner-image">
        <div class="banner-text">
            <h1>DermoClinic</h1>
        </div>
   

</html>