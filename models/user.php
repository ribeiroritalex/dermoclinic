<?php
namespace UserNS;
use DateTime;
class User{
    // USER ROLES
    public static int $USER_ROLE_DOCTOR = 0;
    public static int $USER_ROLE_PATIENT = 1;
    public static int $USER_ROLE_ADMIN = 2;
    // GENDERS
    public static int $GENDER_FEMALE = 0;
    public static int $GENDER_MALE = 1;
    public static int $GENDER_OTHER = 2;

    public String $name;
    public String $email;
    public String $password;
    public int $role;
    public string $nif;
    public String $license_id;
    public String $phone;    
    public String $login_token;    
    public int $gender;    
    public DateTime $birthdate;
}


?>