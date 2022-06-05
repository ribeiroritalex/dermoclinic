<?php
session_start();
require_once '../../models/user.php';
require_once '../../config/db_connection.php';
require_once '../../config/jwt.php';
require_once '../../functions/session_functions.php';
require_once '../../functions/questions_functions.php';

use UserNS\User;

$session_is_valid = validateSessionToken();
if ($session_is_valid && $_SESSION["user_role"] !=  User::$USER_ROLE_PATIENT) {
    onSessionRedirect();
}

$listQuestions = getQuestions(0);
requestAppointment($listQuestions);

foreach ($listQuestions as $question) {
    fromQuestionToInputLine($question);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>DermoClinic | Pedir Marcação</title>
</head>

<body>
    <section class="vh-100">
        <div class="container py-5 h-100" style="background-color: teal;">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card bg-dark " style="border-radius: 1rem;">
                        <div class="card-body p-5 text-center">
                            <div class="login100-form-title">
                                <img src="../assets/images/Logo2.svg" class="mb-5" alt="">
                                <h2 class="fw-bold mb-2 text-white text-uppercase">Login</h2>
                                <p class="text-white-50 mb-4">Insira o email e password.</p>
                                <p class="fw-bold text-white">


                                <form method="post" action="login.php">
                                    <?php

                                    if (isset($listQuestions)) {
                                        foreach ($listQuestions as $question) {
                                            fromQuestionToInputLine($question);
                                        }
                                    }

                                    ?>

                                    <input id="request_appointment" name="request_appointment" class="btn btn-outline-light btn-lg px-5 mb-4" type="submit" value="Login" />

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