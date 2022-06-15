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

$appointment_id = $_GET["appointment_id"];

if (!isset($appointment_id)) return;
$listAppointments = getAppointments();
$user_screening = checkScreening($listAppointments);

foreach ($listAppointments as $app) {
    if ($app->appointment_id == $appointment_id) {
        $appointment = $app;
        break;
    }
}

$result = finishAppointment();

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
    <script src="https://code.jquery.com/jquery-3.2.1.js"></script>
    <title>DermoClinic | Consulta</title>
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
                                <?php if (isset($result) && $result) : ?>
                                    <h2 class="fw-bold mb-2 text-white text-uppercase">Sucesso!</h2>
                                    <p class="text-white-50 mb-4">Consulta terminada, pode sair desta página.</p>
                                    <br />
                                    <a href="../appointment_list/appointment_list.php" class="btn btn-secondary">Voltar</a>
                                    
                                <?php else : ?>
                                    <?php
                                    if (isset($listAppointments) and isset($appointment)) {
                                        echo "<h2 class=\"fw-bold mb-2 text-white text-uppercase\">Paciente: " . ($appointment->patient_name) . "</h2>";
                                        echo "<p class=\"text-white-50 mb-4\">Preencha o formulário abaixo antes de finalizar a consulta.</p>";
                                    } else {
                                        echo "<h2 class=\"fw-bold mb-2 text-white text-uppercase\">Não foi encontrada consulta com este Id</h2>";
                                        echo "<a href=\"../appointment_list/appointment_list.php\" class=\"btn btn-secondary\">Voltar</a>";
                                    }
                                    ?>

                                    <p class="fw-bold text-white">

                                        <?php if (isset($appointment) and isset($listAppointments)) : ?>
                                    <form method="post" action="do_appointment.php?appointment_id=<?php echo "" . ($appointment->appointment_id) ?>">>
                                        <input hidden="true" type="text" class="form-control" name="appointment_id" id="appointment_id" value="<?php echo "" . ($appointment->appointment_id) ?>"></input>
                                        <p style="color: white;">Altura (cm)</p>
                                        <input type="number" class="form-control" name="height" id="height" placeholder="Altura (cm)" min="1" , max="300"></input>
                                        <p style="color: white;">Peso (kg)</p>
                                        <input type="number" class="form-control" name="weight" id="weight" placeholder="Peso (kg)" step="0.1" min="1" , max="400"></input>
                                        <p style="color: white;">Pressão arterial (mm Hg)</p>
                                        <input type="text" class="form-control" name="bloodPressure" id="bloodPressure" placeholder="Pressão arterial (mmHg)"></input>
                                        <br />
                                        <p style="color: white;">Observações da Consulta</p>
                                        <textarea class="form-control" style="width:100%;" id="observations" name="observations" rows="8" placeholder="Insira aqui as observações da consulta"></textarea>
                                        <br />
                                        <br />
                                        <div class="text-center">
                                            <h3 style="color:white;">Prescrição</h3>
                                            <p style="color:white;">Adicione abaixo as prescrições necessárias.</p>
                                        </div>
                                        <hr>

                                        <div class="container">
                                            <div class="row clearfix">
                                                <div class="col-md-12 column">
                                                    <table class="table table-bordered table-hover" id="tab_logic">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center" style="color: white;">
                                                                    #
                                                                </th>
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
                                                        <tbody>
                                                            <tr id='prescr0'>
                                                                <td style='color: white;'>
                                                                    1
                                                                </td>
                                                                <td>
                                                                    <input type="text" name='description_0' placeholder='Descrição' class="form-control" />
                                                                </td>
                                                                <td>
                                                                    <input type="number" name='quantity_0' placeholder='Quantidade' class="form-control" />
                                                                </td>
                                                                <td>
                                                                    <input type="number" name='duration_0' placeholder='Duração' class="form-control" />
                                                                </td>
                                                                <td>
                                                                    <input type="text" name='observations_0' placeholder='Observações' class="form-control" />
                                                                </td>
                                                            </tr>
                                                            <tr id='prescr1'></tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <br />

                                            <a id="add_row" class="btn btn-secondary btn-lg pull-left">+ Adicionar Linha</a>
                                            <br />
                                            <br />
                                            <br />
                                            <br />
                                            <button id="finishAppointment" name="finishAppointment" class="btn btn-primary btn-lg pull-left">Finalizar Consulta</button>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>


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
                                    echo '<div><p class="text-white"><string>Image Attached</strong></p><img  style="display:block; width:600px;" src="' . ($user_screening->imageAttached) . '" alt="Attached Image" /></div>';
                                } else {
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
<script>
    $(document).ready(function() {
        var i = 1;
        $("#add_row").click(function() {
            /* $('tr').find('input') .prop('disabled',true)*/
            $('#prescr' + i).html("<td style='color: white;'>" + (i + 1) + "</td><td><input type='text' name='description_" + i + "'  placeholder='Descrição' class='form-control input-md'/></td><td><input type='number' name='quantity_" + i + "' placeholder='Quantidades' class='form-control input-md'/></td><td><input type='number' name='duration_" + i + "' placeholder='Duração' class='form-control input-md'/></td><td><input type='text' name='observations_" + i + "' placeholder='Observações' class='form-control input-md'/></td>");

            $('#tab_logic').append('<tr id="prescr' + (i + 1) + '"></tr>');
            i++;
        });
    });
</script>
<script src="../js/sitejs.js"></script>

</html>