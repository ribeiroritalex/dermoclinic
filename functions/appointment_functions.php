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

class AppointmentDTO
{
    public int $patient_id;
    public int $appointment_id;
    public int $screening_id;
    public string $patient_name;
    public DateTime $appointment_date;
    public DateTime $screening_date;
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

function getAppointments()
{

    $select_stmt = "SELECT a.appointment_id ,a.appointment_date,s.screening_date, u.user_id , u.name , s.screening_id FROM `appointment` as a, `users` as u, `screening` as s WHERE a.doctor_id=:docId and a.appointment_id = s.appointment_id and a.patient_id = u.user_id ORDER BY a.appointment_date;";

    $db = getDb();
    $result = $db->prepare($select_stmt);
    $result->bindParam(":docId", $_SESSION['user_id']);
    $result->execute();

    $toReturn = [];

    while ($aux = $result->fetch(PDO::FETCH_ASSOC)) {
        $appointment = new AppointmentDTO();
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

function fromAppointmentRequestToInputLine($appointments): void
{
    if (isset($appointments) and count($appointments) > 0) {
        echo "<form><table> <tr style=\"color: white; \"> <th> <div style=\"margin-left:10;margin-right:10;\">Nome do Utente</div> </th> <th> <div style=\"margin-left:10;margin-right:10;\">Data do pedido </th> </div> <th> <div style=\"margin-left:10;margin-right:10;\">Data da marcação</div> </th> <th> <div style=\"margin-left:10;margin-right:10;\">Confirmar Marcação</div> </th> <th> <div style=\"margin-left:10;margin-right:10;\">Ver Screening</div> </th> </tr>";
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
    }else{
        echo "<p class=\"text-white\">Não existem pedidos de consulta para aprovar.</p>";
    }
}

function fromAppointmentsToInputLine($appointments): void
{
    if (isset($appointments) and count($appointments) > 0) {
        echo "<form><table> <tr style=\"color: white; \"> <th> <div style=\"margin-left:10;margin-right:10;\">Nome do Utente</div> </th> <th> <div style=\"margin-left:10;margin-right:10;\">Data da Consulta </th> </div> <th> <div style=\"margin-left:10;margin-right:10;\">Ver Screening</div> </th> </tr>";
        foreach ($appointments as $appointment) {
            echo "<tr style=\"color: white;\">";
            echo "<td><p style=\"color: white;text-align: center; vertical-align: middle;margin:0 auto;\" >" . ($appointment->patient_name) . "</p></td>";
            echo "<td><p style=\"color: white;text-align: center; vertical-align: middle;margin:0 auto;\" >" . (($appointment->appointment_date)->format("Y-m-d H:i")) . "</p></td>";
            echo "<td><input style=\"display: block; margin: auto;\" class=\"btn btn-sm btn-outline-secondary\" type=\"submit\" name=\"check_data_" . ($appointment->appointment_id) . "\" id=\"checkData\" value=\"Ver\"/></td>";
            echo "</tr>";
        }
        echo "</table></form>";
    }else{
        echo "<p class=\"text-white\">Não existem pedidos de consulta para aprovar.</p>";
    }
}

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
            
            while ($imageRaw = $select_stmt_res_2->fetch(PDO::FETCH_ASSOC)) {
                $user_screening->imageAttached = $imageRaw["image_blob"];
            }
            
            return $user_screening;
        }
    }
}

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
