<?php
session_start();
require_once '../../models/user.php';
require_once '../../config/db_connection.php';
require_once '../../config/jwt.php';
require_once '../../models/appointment.php';
require_once '../../models/question.php';
require_once '../../models/screening.php';
require_once '../../functions/session_functions.php';
require_once '../../functions/appointment_functions.php';

use UserNS\User;

$session_is_valid = validateSessionToken();
if ($session_is_valid && $_SESSION["user_role"] !=  User::$USER_ROLE_DOCTOR) {
    onSessionRedirect();
}

$listAppointments = getAppointments();
$user_screening = checkScreening($listAppointments);

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
    <title>DermoClinic | Marcações</title>
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
                <div class="col-12 col-md-8 col-lg-6 col-xl-9">
                    <div class="card bg-dark " style="border-radius: 1rem;">
                        <div class="card-body p-5 text-center">
                            <div class="login100-form-title">
                                <img src="../assets/images/Logo2.svg" class="mb-5" alt="">
                                <h2 class="fw-bold mb-2 text-white text-uppercase">Consultas</h2>
                                <p class="text-white-50 mb-4">Abaixo encontram-se todas as marcações de consultas.</p>
                                <p class="fw-bold text-white">

                                    <?php
                                    if (isset($listAppointments)) {
                                        fromAppointmentsToInputLine($listAppointments);
                                    }
                                    ?>

                            </div>


                        </div>
                        <div class="card-body p-5 text-center">
                            <?php
                            if (isset($user_screening)) {
                                echo '<table style="color: white;border-collapse:collapse;border-spacing:0" class="tg"><thead><tr><th style="border-color:inherit;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;font-weight:normal;overflow:hidden;padding:10px 5px;text-align:center;vertical-align:top;word-break:normal"><strong>Nome Paciente: </strong>' . ($user_screening->user_name) . '</th><th style="border-color:inherit;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;font-weight:normal;overflow:hidden;padding:10px 5px;text-align:center;vertical-align:top;word-break:normal"><strong>Data Pedido: </strong>' . ($user_screening->date) . '</th></tr></thead><tbody><tr><td style="border-color:inherit;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;overflow:hidden;padding:10px 5px;text-align:center;vertical-align:top;word-break:normal"><strong>Perguntas</strong></td><td style="border-color:inherit;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;overflow:hidden;padding:10px 5px;text-align:center;vertical-align:top;word-break:normal"><strong>Respostas</strong></td></tr>';
                                foreach (($user_screening->answers) as $answer) {

                                    echo '<tr>';
                                    echo '<td style="border-color:inherit;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;overflow:hidden;padding:10px 5px;text-align:right;vertical-align:top;word-break:normal">';
                                    echo $answer->description;
                                    echo '</td>';
                                    echo '<td style="border-color:inherit;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;overflow:hidden;padding:10px 5px;text-align:center;vertical-align:top;word-break:normal">' . $answer->answer . '</td>';
                                    echo '</tr>';
                                }
                                echo '</tbody></table><br/><br/>';

                                if (isset($user_screening->imageAttached)) {
                                    echo '<div><p class="text-white"><string>Image Attached</strong></p><img  style="display:block; width:600px;" src="'.($user_screening->imageAttached).'" alt="Attached Image" /></div>';
                                }else{
                                    echo "<p class=\"text-white\">-- Sem documentos adicionados --";
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</body>
<script src="../js/sitejs.js"></script>

</html>