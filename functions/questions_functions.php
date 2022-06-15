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
    
    while ($aux = $result->fetch(PDO::FETCH_ASSOC)) {
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
    echo "<table>";
    echo "<tr>";
    echo "<td>";
    echo '<input type="checkbox" style="margin-right: 10; text-align: center; vertical-align: start;" name="' . ($question->question_id) . '" />';
    echo "</td>";
    echo '<td><p style="color: white;text-align: center; vertical-align: middle;margin:0 auto;" >' . ($question->description) . "</p></td>";
    echo "</tr>";
    echo "</table>";
}
function string_to_blob($str){
    $bin = "";
    for($i = 0, $j = strlen($str); $i < $j; $i++) 
    $bin .= decbin(ord($str[$i])) . " ";
    echo $bin;
  }
  function blob_to_string($bin){
    $char = explode(' ', $bin);
    $userStr = '';
    foreach($char as $ch) 
    $userStr .= chr(bindec($ch));
    return $userStr;
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
            // foreach($_FILES['image'] as $aa){
            //     echo "".$aa;
            // }
            $file_name = $_FILES['image']['name'];
            // $aux_explode = explode('.', $file_name);
            // $aux_end = end($aux_explode);
            // $file_ext = strtolower($aux_end);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            $file_size = $_FILES['image']['size'];
            $file_tmp = $_FILES['image']['tmp_name'];
            
            $type = pathinfo($file_tmp, PATHINFO_EXTENSION);
            
            $data = file_get_contents($file_tmp);
            $base64 = 'data:image/' . $type . ';base64, ' . base64_encode($data);
            
            if (in_array($file_ext, $allowed_ext) === false) {
                $errors[] = 'Extension not allowed';
            }

            if ($file_size > (4097152)) {
                $errors[] = 'File size must be under 4mb';
            }
            if (empty($errors)) {
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    if (move_uploaded_file($file_tmp, str_replace("/", "\\", $_SERVER['DOCUMENT_ROOT']).'\DermoClinic\images\\'.$file_name)) {
                        // echo "File Uploaded !";
                       }
                    
                } else {
                    if (move_uploaded_file($file_tmp, $_SERVER['DOCUMENT_ROOT'].'/DermoClinic/images/'.$file_name)) {
                        // echo "File Uploaded !";
                       }
                    
                }
                //    if (!move_uploaded_file(
                //     $_FILES['upfile']['tmp_name'],
                //     sprintf('./uploads/%s.%s',
                //         sha1_file($_FILES['upfile']['tmp_name']),
                //         $ext
                //     )
                // )) {
                //     throw new RuntimeException('Failed to move uploaded file.');
                // }
            } else {
                foreach ($errors as $error) {
                    //echo $error, '<br/>';
                }
            }
           
        }

        $appointment = new Appointment();
        $appointment->patient_id=$_SESSION["user_id"];

        $db = getDb();
        $insert_stmt = "INSERT INTO `appointment` (`patient_id`) VALUES ( ? );";
        $insert_stmt_res = $db->prepare($insert_stmt);
        $insert_stmt_res->execute([$appointment->patient_id]);
        
        $appointment->appointment_id = $db->lastInsertId();
       
        $screening = new Screening();
        $screening->appointment_id = $appointment->appointment_id;
        $screening->screening_type = 0;
        $screening->patient_id = $appointment->patient_id;

        
        $insert_stmt = "INSERT INTO `screening` (`screening_type`, `appointment_id`, `patient_id`) VALUES ( ?, ?, ? );";
        $insert_stmt_res = $db->prepare($insert_stmt);
        $insert_stmt_res->execute([$screening->screening_type, $appointment->appointment_id, $screening->patient_id]);
        
        $screening->screening_id = $db->lastInsertId();

        if(isset($base64) and $base64 != null){
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

        echo "Formulário submetido com sucesso!
        Pedido de marcação de consulta efetuado.";
        
        // header('Location: /DermoClinic');
    }
}

?>>