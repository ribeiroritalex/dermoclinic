<?php
session_start();
require_once '../../models/user.php';
require_once '../../config/db_connection.php';
require_once '../../config/jwt.php';
require_once '../../models/appointment.php';
require_once '../../models/question.php';
require_once '../../models/screening.php';
require_once '../../functions/session_functions.php';
require_once '../../functions/questions_functions.php';

use UserNS\User;

$session_is_valid = validateSessionToken();
if ($session_is_valid && $_SESSION["user_role"] !=  User::$USER_ROLE_PATIENT) {
    onSessionRedirect();
}

$listQuestions = getQuestions(0);
requestAppointment($listQuestions);

?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/site.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>DermoClinic | Pedir Marcação</title>
</head>

<body>
    <!--Navbar-->
    <nav class="navbar">
        <!--Logo-->
        <a class="navbar-brand" href="#">
            <img src="../../assets/images/logo.svg" width="170" height="56,6" alt="">
        </a>
        <ul class="nav-links">
            <li>
                <a href="../patient_home.php">Voltar à Dashboard</a>
            </li>
        </ul>
        <!--Burger 2-->
        <div class="burger">
            <div class="line1"></div>
            <div class="line2"></div>
            <div class="line3"></div>
        </div>
    </nav>
    <section class="vh-100">
        <div class="container py-5 h-100" style="background-color: SteelBlue;">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card bg-dark " style="border-radius: 1rem;">
                        <div class="card-body p-5 text-center">
                            <div class="login100-form-title">
                                <img src="../assets/images/Logo2.svg" class="mb-5" alt="">
                                <h2 class="fw-bold mb-2 text-white text-uppercase">Pedido de Marcação</h2>
                                <p class="text-white-50 mb-4">Para realizar um pedido de marcação de consulta, preencha por favor o seguinte questionário.</p>
                                <p class="fw-bold text-white">

                                    <form action="request_appointment.php" method="post" enctype="multipart/form-data" >
                                        
                                        <p class="text-white">Envie-nos um ficheiro (<4MB):    
                                        <input type="file" class="btn" style="color: orange;" name="image" id="image">
                                        
                                        <br>
                                    <br>
                                    <p class="text-white">Indique os seus sintomas e a região do corpo
                                 <?php

                                    if (isset($listQuestions)) {
                                        foreach ($listQuestions as $question) {
                                            fromQuestionToInputLine($question);
                                        }
                                    }

                                    ?>

                                    <input id="request_appointment" name="request_appointment" class="btn btn-outline-light btn-lg px-5 mb-4" type="submit" value="Submeter" />

                                </form>

                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</body>
<script src="../js/sitejs.js"></script>

</html>