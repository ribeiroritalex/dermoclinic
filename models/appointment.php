<?php
namespace AppointmentNS;

use DateTime;

class Appointment{

    public int $appointment_id;
    public int $patient_id;
    public int $doctor_id;
    public DateTime $appointment_date;

}


?>