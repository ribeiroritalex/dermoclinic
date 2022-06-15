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

# validar login token
$session_is_valid = validateSessionToken();


# tem que ser doctor ou patient dado que esta página se adapta ao perfil
if ($session_is_valid && ($_SESSION["user_role"] !=  User::$USER_ROLE_DOCTOR and $_SESSION["user_role"] !=  User::$USER_ROLE_PATIENT)) {
    onSessionRedirect();
    return;
}

$is_doctor = $_SESSION["user_role"] ==  User::$USER_ROLE_DOCTOR;

# carrega consultas passadas deste utilizador
$listAppointments = getAppointmentFinished($is_doctor);

if (isset($_GET["open"])) {
    $show_appointment = $_GET["open"];
}
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
    <title>DermoClinic | Histórico</title>
</head>

<body>
    <!--Navbar-->
    <nav class="navbar">
        <!--Logo-->
        <a class="navbar-brand" href="#">
            <img src="../../assets/images/Logo.svg" width="170" height="56,6" alt="">
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
                                <h2 class="fw-bold mb-2 text-white text-uppercase">Histórico de Consultas</h2>
                                <p class="text-white-50 mb-4">Abaixo encontram-se todas as marcações de consultas passadas.</p>
                                <p class="fw-bold text-white">

                                    <?php
                                    if (isset($listAppointments)) {
                                        // transformar appointment em html para apresentar os seus dados
                                        fromAppointmentsFinishedToInputLine($is_doctor, $listAppointments);
                                    }
                                    ?>

                            </div>


                        </div>
                        <div class="card-body p-5 text-center">
                            <?php
                            if (isset($show_appointment)) {

                                foreach ($listAppointments as $app) {
                                    if ($app->appointment_id == $show_appointment) {
                                        $appointment = $app;
                                        break;
                                    }
                                }
                                if (!isset($appointment)) {
                                    echo "<p style='color: white;'>Not found!</p>";
                                    return;
                                }

                                echo '<table style="color: white;border-collapse:collapse;border-spacing:0;width:100%;" class="tg"><thead><tr><th style="border-color:inherit;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;font-weight:normal;overflow:hidden;padding:10px 5px;text-align:center;vertical-align:top;word-break:normal"><strong>Observações da Consulta</strong></th></tr></thead>';
                                echo '<tbody><td style="border-color:inherit;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;overflow:hidden;padding:10px 5px;text-align:right;vertical-align:top;word-break:normal">';
                                echo '' . ($appointment->observations) . '</td>';
                                echo '</tbody></table><br/><br/>';

                                echo '<table style="width:100%;color: white;border-collapse:collapse;border-spacing:0" class="tg"><thead><tr><th style="border-color:inherit;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;font-weight:normal;overflow:hidden;padding:10px 5px;text-align:center;vertical-align:top;word-break:normal"><strong>Tensão Arterial (mm Hg)</strong></th><th style="border-color:inherit;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;font-weight:normal;overflow:hidden;padding:10px 5px;text-align:center;vertical-align:top;word-break:normal"><strong>Peso (kg)</strong></th><th style="border-color:inherit;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;font-weight:normal;overflow:hidden;padding:10px 5px;text-align:center;vertical-align:top;word-break:normal"> <strong>Altura (cm)</strong></th></thead><tbody>';
                                echo '<tr>';
                                echo '<td style="border-color:inherit;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;overflow:hidden;padding:10px 5px;text-align:right;vertical-align:top;word-break:normal">';
                                echo '' . $appointment->bloodPressure;
                                echo '</td>';
                                echo '<td style="border-color:inherit;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;overflow:hidden;padding:10px 5px;text-align:right;vertical-align:top;word-break:normal">';
                                echo '' . $appointment->weight;
                                echo '</td>';
                                echo '<td style="border-color:inherit;border-style:solid;border-width:1px;font-family:Arial, sans-serif;font-size:14px;overflow:hidden;padding:10px 5px;text-align:right;vertical-align:top;word-break:normal">';
                                echo '' . $appointment->height;
                                echo '</td>';
                                echo '</tbody></table><br/><br/>';

                                if (isset($appointment->prescriptions)) {
                                    echo '<h3 style="color:white;">Prescrições</h3>;
                                    <div class="container"><div class="row clearfix"><div class="col-md-12 column">
                                            <table class="table table-bordered table-hover" id="tab_logic">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" style="color: white;">
                                                            Prescrição
                                                        </th>
                                                        <th class="text-center" style="color: white;">
                                                            Quantidade
                                                        </th>
                                                        <th class="text-center" style="color: white;">
                                                            Duração (Dias)
                                                        </th>
                                                        <th class="text-center" style="color: white;">
                                                            Observações
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>';

                                    foreach ($appointment->prescriptions as $prescription) {
                                        echo '<tr>';
                                        echo '<td style="color:white;">' . ($prescription->description) . '</td>';
                                        echo '<td style="color:white;">' . ($prescription->quantity) . '</td>';
                                        echo '<td style="color:white;">' . ($prescription->duration) . '</td>';
                                        echo '<td style="color:white;">' . ($prescription->observation) . '</td>';
                                        echo '</tr>';
                                    }
                                    echo '</tbody>
                                            </table>
                                        </div>
                                    </div>';
                                } else {
                                    echo "<p class=\"text-white\">-- Sem prescrições adicionados --";
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