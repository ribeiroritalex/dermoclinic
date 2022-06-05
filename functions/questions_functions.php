<?php

use AppointmentNS\Appointment;
use ScreeningNS\Screening;
use ScreeningNS\ScreeningImage;
use ScreeningNS\ScreeningQuestion;
use QuestionNS\Question;

function getQuestions($screening_type)
{

    $select_stmt = "SELECT * FROM `questions` as q WHERE q.screening_type=:stype;";

    $db = getDb();
    $result = $db->prepare($select_stmt);
    $result->bindParam(":stype", $screening_type);
    $result->execute();


    $toReturn = [];
    $aux = $result->fetch(PDO::FETCH_ASSOC);
    while ($aux) {
        $question = new Question();
        $question->description = $aux["description"];
        $question->screening_type = $aux["screening_type"];
        $question->question_id = $aux["question_id"];

        array_push($toReturn, $question);
    }
    return $toReturn;
}

function fromQuestionToInputLine($question) : void
{
    echo "<div>";
    echo "<h3>" . ($question->description) . "</h3>";
    echo '<input type="checkbox" name="' . ($question->question_id) . '" />';
    echo "</div>";
}

function requestAppointment($questionsList)
{
    if(!isset($_SESSION)){
        echo "No Session!";
        return;
    }
    if (isset($_REQUEST["request_appointment"])) {
        if (isset($_FILES['image'])) {
            $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');
            $file_name = $_FILES['image']['name'];
            $file_ext = strtolower(end(explode('.', $file_name)));

            $file_size = $_FILES['image']['size'];
            $file_tmp = $_FILES['image']['tmp_name'];
            $type = pathinfo($file_tmp, PATHINFO_EXTENSION);
            $data = file_get_contents($file_ext);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

            if (in_array($file_ext, $allowed_ext) === false) {
                $errors[] = 'Extension not allowed';
            }

            if ($file_size > (2097152*2)) {
                $errors[] = 'File size must be under 4mb';
            }
            if (empty($errors)) {
                if (move_uploaded_file($file_tmp, 'images/' . $file_name)); {
                    echo 'File uploaded';
                }
            } else {
                foreach ($errors as $error) {
                    echo $error, '<br/>';
                }
            }
            
        }

        $appointment = new Appointment();
        $appointment->patient_id=$_SESSION["user_email"];

        $db = getDb();
        $insert_stmt = "INSERT INTO `appointment` (`patient_id`) VALUES ( ? );";
        $insert_stmt_res = $db->prepare($insert_stmt);
        $insert_stmt_res->execute([$appointment->patient_id]);
        
        $appointment->appointment_id = $db->lastInsertId();
       
        $screening = new Screening();
        $screening->appointment_id = $appointment->appointment_id;
        $screening->screening_type = 0;
        $screening->patient_id = $appointment->patient_id;
        $screening->screening_date = new DateTime();

        
        $insert_stmt = "INSERT INTO `screening` (`screening_type`, `appointment_id`, `patient_id`, `screening_date`) VALUES ( ?, ?, ?, ? );";
        $insert_stmt_res = $db->prepare($insert_stmt);
        $insert_stmt_res->execute([$screening->screening_type, $appointment->appointment_id, $screening->patient_id, $screening->screening_date]);
        
        $screening->screening_id = $db->lastInsertId();

        if(isset($base64)){
            $screening_image = new ScreeningImage();
            $screening_image->screening_id = $screening->screening_id;
            $screening_image->image_blob = $base64;
            
            $insert_stmt = "INSERT INTO `screening_images` (`screening_id`, `image_blob`) VALUES ( ?, ? );";
            $insert_stmt_res = $db->prepare($insert_stmt);
            $insert_stmt_res->execute([$screening_image->screening_id, $screening_image->image_blob]);
        }

        
        foreach ($questionsList as $question) {
            $screening_question = new ScreeningQuestion();
            $screening_question->question_id = $question->question_id;
            $screening_question->screening_id = $screening->screening_id;
            $screening_question->screening_type = $screening->screening_type;
            if(isset($_REQUEST["" . ($question->question_id)])){
                $screening_question->answer = "Sim.";
            }else{
                $screening_question->answer = "Não.";
            }
            $insert_stmt = "INSERT INTO `screening_questions` (`question_id`, `screening_id`, `screening_type`, `answer`) VALUES ( ?, ?, ?, ? );";
            $insert_stmt_res = $db->prepare($insert_stmt);
            $insert_stmt_res->execute([$screening_question->question_id, $screening_question->screening_id, $screening_question->screening_type, $screening_question->answer]);
        }
    }
}

?>>