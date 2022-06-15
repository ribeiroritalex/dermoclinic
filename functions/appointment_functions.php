<?php

use AppointmentNS\Appointment;

class AppReq
{
    public int $patient_id;
    public int $appointment_id;
    public int $screening_id;
    public string $patient_name;
    public DateTime $screening_date;
}

class Prescription
{
    public int $id;
    public string $observation;
    public string $description;
    public string $quantity;
    public string $duration;
}

class AppFinish
{
    public int $patient_id;
    public int $appointment_id;
    public string $observations;
    public float $height;
    public float $weight;
    public string $bloodPressure;
    public  $prescriptions = [];
}


class AppointmentAndOtherData
{
    public int $patient_id;
    public int $appointment_id;
    public int $screening_id;
    public string $patient_name;
    public DateTime $appointment_date;
    public DateTime $screening_date;
    public string $height;
    public string $weight;
    public string $bloodPressure;
    public string $observations;
    public string $doctor_name;
    public $prescriptions = [];
}

class UserScreening
{

    public $answers = [];
    public String $user_name;
    public String $date;
    public $imageAttached;
}
class UserScreeningAnswers
{

    public int $question_id;
    public String $description;
    public String $answer;
}

// Função para retornar os pedidos de consultas para se poder fazer a marcação.
function getAppointmentRequests()
{

    $select_stmt = "SELECT a.appointment_id , u.user_id , u.name , s.screening_id , s.screening_date FROM `appointment` as a, `users` as u, `screening` as s WHERE a.doctor_id is NULL and a.appointment_id = s.appointment_id and a.patient_id = u.user_id  ORDER BY s.screening_date;";

    $db = getDb();
    $result = $db->prepare($select_stmt);
    $result->execute();

    $toReturn = [];

    while ($aux = $result->fetch(PDO::FETCH_ASSOC)) {
        $appointment = new AppReq();
        $appointment->appointment_id = $aux["appointment_id"];
        $appointment->patient_id = $aux["user_id"];
        $appointment->patient_name = $aux["name"];
        $appointment->screening_id = $aux["screening_id"];
        $appointment->screening_date = DateTime::createFromFormat("Y-m-d H:i:s", $aux["screening_date"]);

        array_push($toReturn, $appointment);
    }
    return $toReturn;
}

function addToDBFinishAppointment(AppFinish $app): bool
{
    $toReturn = true;
    $db = getDb();

    // INSERT Prescriptions in DB    
    $insert_stmt = "INSERT INTO `prescription` (`appointment_id`, `prescription_description`, `prescription_observations`, `quantity`, `duration`) VALUES ( ?, ?, ?, ?, ? );";
    $insert_stmt_res = $db->prepare($insert_stmt);
    foreach ($app->prescriptions as $prescription) {
        $toReturn = $toReturn & $insert_stmt_res->execute([$app->appointment_id, $prescription->description, $prescription->observation, $prescription->quantity, $prescription->duration]);
    }

    // INSERT Diagnostic in DB
    $insert_stmt = "INSERT INTO `diagnostic` (`appointment_id`, `blood_pressure`, `height`, `weight`, `observations`) VALUES ( ?, ?, ?, ?, ? );";
    $insert_stmt_res = $db->prepare($insert_stmt);
    $toReturn = $toReturn & $insert_stmt_res->execute([$app->appointment_id, $app->bloodPressure, $app->height, $app->weight, $app->observations]);

    return $toReturn;
}

// Função que termina um appointment, apenas o médico através do botão finishAppointment poderá terminar a consulta.
function finishAppointment(): bool
{
    $toReturn = false;

    if (isset($_REQUEST["finishAppointment"])) {
        $observations = $_REQUEST["observations"];
        $height = $_REQUEST["height"];
        $weight = $_REQUEST["weight"];
        $bloodPressure = $_REQUEST["bloodPressure"];
        $appointment_id = $_REQUEST["appointment_id"];
        $prescriptions = [];
        $count = 0;

        // Como pode haver várias prescrições, enquanto esitver definida uma variavel _XX então é porque ainda temos prescrições
        while (isset($_REQUEST["description_" . $count])) {
            $pres = new Prescription();
            $pres->description = $_REQUEST["description_" . $count];
            $pres->duration = $_REQUEST["duration_" . $count];
            $pres->quantity = $_REQUEST["quantity_" . $count];
            $pres->observation = $_REQUEST["observations_" . $count];
            array_push($prescriptions, $pres);
            $count++;
        }


        // criar classe com apenas os dados que queremos guardar da consulta
        $app = new AppFinish();
        $app->appointment_id = $appointment_id;
        $app->bloodPressure = $bloodPressure;
        $app->height = $height;
        $app->weight = $weight;
        $app->observations = $observations;
        $app->prescriptions = $prescriptions;

        $toReturn = addToDBFinishAppointment($app);
    }

    return $toReturn;
}


// Retorna as consultas que já foram realizadas -> já têm diagnostico
// se for médico vai buscar pelo doctor_id, se não pelo patient_id
function getAppointmentFinished(bool $is_doctor)
{

    $db = getDb();

    // Buscar os appointments mais dados de screening e diagnostico.
    // Sabemos que o appointment já foi realizado porque temos entrada no diagnistico
    //  + Paciente vai devolver o nome do médico
    //  + Médico vai devolver o nome do paciente
    //  + vai buscar os dados com doctor_id ou patient_id dependendo do valor de $is_doctor
    if ($is_doctor) {
        $select_stmt = "SELECT a.appointment_id, 
                        a.appointment_date,
                        u.user_id,
                        u.name,
                        d.blood_pressure,
                        d.height,
                        d.weight,
                        d.observations
                    FROM `appointment` as a,
                        `users` as u,
                        `diagnostic` as d
                    WHERE a.doctor_id=:docId
                        and a.patient_id=u.user_id 
                        and a.appointment_id in (select appointment_id from `diagnostic`)
                    ORDER BY a.appointment_date;";
        $result = $db->prepare($select_stmt);
        $result->bindParam(":docId", $_SESSION['user_id']);
    } else {
        $select_stmt = "SELECT a.appointment_id, 
                        a.appointment_date,
                        u.user_id,
                        u.name,
                        d.blood_pressure,
                        d.height,
                        d.weight,
                        d.observations
                    FROM `appointment` as a,
                        `users` as u,
                        `diagnostic` as d
                    WHERE a.patient_id=:patId
                        and a.doctor_id=u.user_id
                        and a.appointment_id in (select appointment_id from `diagnostic`)
                    ORDER BY a.appointment_date;";
        $result = $db->prepare($select_stmt);
        $result->bindParam(":patId", $_SESSION['user_id']);
    }

    $result->execute();

    $toReturn = [];

    while ($aux = $result->fetch(PDO::FETCH_ASSOC)) {
        $already = false;
        foreach($toReturn as $a){
            if($a->appointment_id == $aux["appointment_id"]){
                $already = true;
                break;
            }
        }
        if($already) continue;
        // Criar e preencher Appointment
        $appointment = new AppointmentAndOtherData();
        $appointment->appointment_id = $aux["appointment_id"];
        $appointment->patient_id = $aux["user_id"];
        $appointment->patient_name = $aux["name"];
        $appointment->doctor_name = $aux["name"];
        $appointment->appointment_date  = DateTime::createFromFormat("Y-m-d H:i:s", $aux["appointment_date"]);
        $appointment->height = $aux["height"];
        $appointment->weight = $aux["weight"];
        $appointment->bloodPressure = $aux["blood_pressure"];
        $appointment->observations = $aux["observations"];

        // Em cada resultado encontrado, vamos buscar as prescrições do mesmo.
        $select_stmt_prescriptions = "SELECT appointment_id, 
            prescription_id,
            prescription_description,
            prescription_observations,
            quantity,
            duration
        FROM 
            `prescription`
        WHERE
            appointment_id=:appid;";

        $prescrRes = $db->prepare($select_stmt_prescriptions);
        $prescrRes->bindParam(':appid', $appointment->appointment_id);
        $prescrRes->execute();

        while ($presc = $prescrRes->fetch(PDO::FETCH_ASSOC)) {
            // Criar e preencher Prescription()
            $prescription = new Prescription();
            $prescription->prescription_id = $presc["prescription_id"];
            $prescription->description = $presc["prescription_description"];
            $prescription->observation = $presc["prescription_observations"];
            $prescription->quantity = $presc["quantity"];
            $prescription->duration = $presc["duration"];
            // Associar ao Appointment
            array_push($appointment->prescriptions, $prescription);
        }

        // Adicionar appointment à lista a retornar
        array_push($toReturn, $appointment);
    }

    // Retornar lista com os appointments para este user
    return $toReturn;
}

// Retorna todos as consultas de um médico por ordem de proximidade (data).
function getAppointments()
{

    $select_stmt = "SELECT a.appointment_id, a.appointment_date,s.screening_date, u.user_id , u.name , s.screening_id FROM `appointment` as a, `users` as u, `screening` as s WHERE a.doctor_id=:docId and a.appointment_id = s.appointment_id and a.patient_id = u.user_id and a.appointment_id not in (select appointment_id from `diagnostic`) ORDER BY a.appointment_date;";

    $db = getDb();
    $result = $db->prepare($select_stmt);
    $result->bindParam(":docId", $_SESSION['user_id']);
    $result->execute();

    $toReturn = [];

    while ($aux = $result->fetch(PDO::FETCH_ASSOC)) {
        $appointment = new AppointmentAndOtherData();
        $appointment->appointment_id = $aux["appointment_id"];
        $appointment->patient_id = $aux["user_id"];
        $appointment->patient_name = $aux["name"];
        $appointment->screening_id = $aux["screening_id"];
        $appointment->appointment_date  = DateTime::createFromFormat("Y-m-d H:i:s", $aux["appointment_date"]);
        $appointment->screening_date  = DateTime::createFromFormat("Y-m-d H:i:s", $aux["screening_date"]);

        array_push($toReturn, $appointment);
    }
    return $toReturn;
}

// transforma uma lista de pedidos de consulta consultas numa tabela formatada com os seus parametros para apresentação.
// possibilita escolher a data para marcação da consulta
function fromAppointmentRequestToInputLine($appointments): void
{
    if (isset($appointments) and count($appointments) > 0) {
        echo "<form><table style=\"width:100%;\"> <tr style=\"color: white; \"> <th> <div style=\"margin-left:10;margin-right:10;\">Nome do Utente</div> </th> <th> <div style=\"margin-left:10;margin-right:10;\">Data do pedido </th> </div> <th> <div style=\"margin-left:10;margin-right:10;\">Data da marcação</div> </th> <th> <div style=\"margin-left:10;margin-right:10;\">Confirmar Marcação</div> </th> <th> <div style=\"margin-left:10;margin-right:10;\">Ver Screening</div> </th> </tr>";
        foreach ($appointments as $appointment) {
            echo "<tr style=\"color: white;\">";
            echo "<td><p style=\"color: white;text-align: center; vertical-align: middle;margin:0 auto;\" >" . ($appointment->patient_name) . "</p></td>";
            echo "<td><p style=\"color: white;text-align: center; vertical-align: middle;margin:0 auto;\" >" . (($appointment->screening_date)->format("Y-m-d H:i")) . "</p></td>";
            echo "<td><input my-datetime-format=\"DD/MM/YYYY, hh:mm:ss\" style=\"color: black;text-align: center; vertical-align: middle;margin:0 auto;\" type=\"datetime-local\" name=" . ($appointment->appointment_id) . " id=\"appointmentTime\"/></td>";
            echo "<td><input style=\"display: block; margin: auto;\" class=\"btn btn-sm btn-primary\" type=\"submit\" name=\"scheduleAppointment_" . ($appointment->appointment_id) . "\" id=\"scheduleAppointment\" value=\"Agendar\"/></td>";
            echo "<td><input style=\"display: block; margin: auto;\" class=\"btn btn-sm btn-outline-secondary\" type=\"submit\" name=\"check_data_" . ($appointment->appointment_id) . "\" id=\"checkData\" value=\"Ver\"/></td>";
            echo "</tr>";
        }
        echo "</table></form>";
    } else {
        echo "<p class=\"text-white\">Não existem pedidos de consulta para aprovar.</p>";
    }
}

// transforma uma lista de consultas finalizadas numa tabela formatada com os seus parametros para apresentação
function fromAppointmentsFinishedToInputLine(bool $is_doctor, $appointments): void
{
    if (isset($appointments) and count($appointments) > 0) {
        echo "<table style='width:100%;'> <tr style=\"color: white; \"> <th> <div style=\"margin-left:10;margin-right:10;\">Nome do "
            . ($is_doctor ? "Utente" : "Médico") // Se for médico apresenta nome do utente e vice-versa 
            . "</div> </th> <th> <div style=\"margin-left:10;margin-right:10;\">Data da Consulta </th> </div> <th> <div style=\"margin-left:10;margin-right:10;\">Ver Dados da Consulta</div> </th></tr>";
        foreach ($appointments as $appointment) {
            echo "<tr style=\"color: white;\">";
            echo "<td><p style=\"color: white;text-align: center; vertical-align: middle;margin:0 auto;\" >" . ($is_doctor ? $appointment->patient_name : $appointment->doctor_name) . "</p></td>";
            echo "<td><p style=\"color: white;text-align: center; vertical-align: middle;margin:0 auto;\" >" . (($appointment->appointment_date)->format("Y-m-d H:i")) . "</p></td>";
            echo "<td><a style=\"display: block; margin: auto;\" class=\"btn btn-sm btn-outline-secondary\" href=\"appointment_history.php?open=" . ($appointment->appointment_id) . "\">Mostrar Dados</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class=\"text-white\">Não existe histórico de consultas.</p>";
    }
}


// transforma uma lista de consultas numa tabela formatada com os seus parametros para apresentação
function fromAppointmentsToInputLine($appointments): void
{
    if (isset($appointments) and count($appointments) > 0) {
        echo "<form><table style='width:100%;'> <tr style=\"color: white; \"> <th> <div style=\"margin-left:10;margin-right:10;\">Nome do Utente</div> </th> <th> <div style=\"margin-left:10;margin-right:10;\">Data da Consulta </th> </div> <th> <div style=\"margin-left:10;margin-right:10;\">Ver Screening</div> </th><th> <div style=\"margin-left:10;margin-right:10;\">Iniciar Consulta</div> </th> </tr>";
        foreach ($appointments as $appointment) {
            echo "<tr style=\"color: white;\">";
            echo "<td><p style=\"color: white;text-align: center; vertical-align: middle;margin:0 auto;\" >" . ($appointment->patient_name) . "</p></td>";
            echo "<td><p style=\"color: white;text-align: center; vertical-align: middle;margin:0 auto;\" >" . (($appointment->appointment_date)->format("Y-m-d H:i")) . "</p></td>";
            echo "<td><input style=\"display: block; margin: auto;\" class=\"btn btn-sm btn-outline-secondary\" type=\"submit\" name=\"check_data_" . ($appointment->appointment_id) . "\" id=\"checkData\" value=\"Ver\"/></td>";
            if ($appointment->appointment_date < (new DateTime())) {
                echo "<td><a style=\"display: block; margin: auto; height: 30px;\" class=\"btn btn-sm btn-primary\" href=\"../do_appointment/do_appointment.php?appointment_id=" . ($appointment->appointment_id) . "\" id=\"appointmentStart\">Iniciar Consulta</a></td>";
            } else {
                $diff = $appointment->appointment_date->diff(new DateTime());
                echo "<td><p style=\"color: white;text-align: center; vertical-align: middle;margin:0 auto;\" >Em: " . ($diff->d) . " dias, " . ($diff->h) . "horas e " . ($diff->i) . "mins  </p></td>";
            }
            echo "</tr>";
        }
        echo "</table></form>";
    } else {
        echo "<p class=\"text-white\">Não consultas marcadas.</p>";
    }
}


// Vai buscar o screening do pedido de consulta selecionado pelo utilizador.
function checkScreening($appointments)
{

    foreach ($appointments as $appRequest) {
        if (isset($_REQUEST["check_data_" . ($appRequest->appointment_id)])) {

            $db = getDb();
            $select_stmt = "SELECT u.`name`, sq.`question_id`, q.`description`, sq.`answer` FROM `questions` as q, `users` as u, `screening_questions` as sq WHERE u.`user_id`=:userId and sq.`screening_id`=:screeningId and q.`question_id` = sq.`question_id`;";
            $select_stmt_res = $db->prepare($select_stmt);
            $select_stmt_res->bindParam(":screeningId", $appRequest->screening_id);
            $select_stmt_res->bindParam(":userId", $appRequest->patient_id);
            $select_stmt_res->execute();

            $user_screening = new UserScreening();
            $user_screening->date = ($appRequest->screening_date)->format("c");

            while ($questionRaw = $select_stmt_res->fetch(PDO::FETCH_ASSOC)) {

                $question = new UserScreeningAnswers();
                $user_screening->user_name = $questionRaw["name"];
                $question->question_id = $questionRaw["question_id"];
                $question->description = $questionRaw["description"];
                $question->answer = $questionRaw["answer"];

                array_push($user_screening->answers, $question);
            }

            $select_stmt_2 = "SELECT * FROM `screening_images` WHERE `screening_id`=:screeningId;";
            $select_stmt_res_2 = $db->prepare($select_stmt_2);
            $select_stmt_res_2->bindParam(":screeningId", $appRequest->screening_id);
            $select_stmt_res_2->execute();

            // por enquanto vai ter apenas uma mas está preparada pra ter mais
            while ($imageRaw = $select_stmt_res_2->fetch(PDO::FETCH_ASSOC)) { 
                $user_screening->imageAttached = $imageRaw["image_blob"];
            }

            return $user_screening;
        }
    }
}

// agenda uma consulta com base numa data inserida pelo médico.
function scheduleAppointment($appointments): bool
{
    if (!isset($_SESSION)) {
        echo "No Session!";
        return false;
    }
    foreach ($appointments as $appRequest) {
        if (isset($_REQUEST["scheduleAppointment_" . ($appRequest->appointment_id)]) and isset($_REQUEST[($appRequest->appointment_id)])) {
            $chosenDate = $_REQUEST[($appRequest->appointment_id)];

            $appointment = new Appointment();
            $appointment->appointment_id = $appRequest->appointment_id;
            $appointment->patient_id = $appRequest->patient_id;
            $appointment->doctor_id = $_SESSION["user_id"];
            $datetime = DateTime::createFromFormat("Y-m-d\TH:i", "" . $chosenDate);
            $appointment->appointment_date = $datetime;

            $db = getDb();
            $update_stmt = "UPDATE `appointment` SET appointment_date = ?, doctor_id = ? WHERE `appointment_id` = ?;";
            $update_stmt_res = $db->prepare($update_stmt);
            $result = $update_stmt_res->execute([($appointment->appointment_date)->format("c"), $appointment->doctor_id, $appointment->appointment_id]);

            if ($result) {
                return true;
            }
        }
    }
    return false;
}
