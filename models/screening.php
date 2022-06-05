<?php
namespace ScreeningNS;

use DateTime;

class ScreeningImage{

    public int $image_id;
    public int $screening_id;
    public int $image_blob;

}


class Screening{

    public int $screening_id;
    public int $screening_type;
    public int $appointment_id;
    public int $patient_id;
    public DateTime $screening_date;

}

class ScreeningQuestion{

    public int $question_id;
    public int $screening_id;
    public int $screening_type;
    public string $answer;

}


?>