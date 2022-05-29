<?php
function getDb(){
    $db_host="localhost";
    $db_user="testeuser";
    $db_password="password";
    $db_name="teste";

    $db = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $db;
}
?>