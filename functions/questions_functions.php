<?php

use QuestionNS\Question;

function getQuestions($screening_type) {

    $select_stmt="SELECT * FROM `questions` as q WHERE q.screening_type=:stype;";

    $db=getDb();
    $result=$db->prepare($select_stmt);
    $result->bindParam(":stype",$screening_type);
    $result->execute();
}

?>