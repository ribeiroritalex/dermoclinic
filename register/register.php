<?php
session_start();
require_once '../models/user.php';
require_once '../config/db_connection.php';
require_once '../config/jwt.php';
require_once '../functions/session_functions.php';

use UserNS\User;

function console_log($output, $with_script_tags = true)
{
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
        ');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}
onSessionRedirect();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/site.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>DermoClinic | Register</title>
</head>

<body>

    <section class="vh-100 gradient-custom">
      
            <div class="row d-flex justify-content-center align-items-center" style="margin-top: 25px;margin-bottom: 25px;">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card bg-dark " style="border-radius: 2rem;">
                        <div class="card-body p-5 text-center">

                            <div class="mb-md-5 mt-md-2 mx-md-5 pb-5">
                                <img src="../assets/images/Logo2.svg" class="mb-5" alt="">
                                <h2 class="fw-bold mb-2 text-white text-uppercase  mb-4">Registo</h2>
                                <div>
                                    <p class="fw-bold text-white">

                                        <?php

                                        if (isset($_REQUEST["register"])) {
                                            console_log("register");

                                            $user_name = $_REQUEST["name"];
                                            $user_email = $_REQUEST["email"];
                                            $user_gender = $_REQUEST["gender"];
                                            $user_password = $_REQUEST["password"];
                                            $user_confirm_password = $_REQUEST["rpassword"];

                                            $errorMsg = "";
                                            if ($user_email == null or $user_email == "") {
                                                $errorMsg = "Por favor, insira o seu endereço de email.";
                                            } else
                if ($user_password == null or $user_password == "") {
                                                $errorMsg = "Por favor, insira a password.";
                                            } else
                if ($user_password != $user_confirm_password) {
                                                $errorMsg = "As passwords não coincidem!";
                                            } else
                if ($user_gender == null or $user_gender == "") {
                                                $errorMsg = "Por favor, insira o género.";
                                            } else
                if ($user_name == null or $user_name == "") {
                                                $errorMsg = "Por favor, insira o seu nome.";
                                            }
                                            console_log("errorMsg");
                                            console_log($errorMsg);

                                            if ($errorMsg == "") {
                                                try {
                                                    console_log("register");
                                                    
                                                    $result = register($user_email, $user_password, $user_name, $user_gender);
                                                    console_log($result);

                                                    if ($result) {
                                                        $errorMsg = "Registo efetuado com sucesso!";

                                                    } else {
                                                        $errorMsg = "Algo correu mal...";
                                                    }
                                                } catch (PDOException $e) {
                                                    $e->getMessage();
                                                }
                                            }
                                            
                                            //header('Location: /DermoClinic/login/login.php');
                                            echo $errorMsg;
                                           
                                        }
                                        ?>


                                    </p>
                                </div>
                                <form method="post" action="register.php">
                                    <div class="form-floating mb-4">
                                        <input class="form-control" type="text" name="name" placeholder="Full Name" required>
                                        <label for="floatingInput">Nome</label>
                                    </div>
                                    <div class="form-floating mb-4">
                                        <input class="form-control" type="email" name="email" placeholder="E-mail Address" required>
                                        <label for="floatingEmail">E-mail</label>
                                    </div>
                                    <div class="form-floating mb-4">
                                        <select class="form-select" id="gender" name="gender" required>
                                            <option value="0">Masculino</option>
                                            <option value="1">Feminino</option>
                                            <option value="2">Outro</option>
                                        </select>
                                        <label for="gender">Género</label>
                                    </div>
                                    <div class="form-floating mb-4">
                                        <input class="form-control" type="password" name="password" placeholder="Password" required>
                                        <label for="floatingInput">Password</label>
                                    </div>
                                    <div class="form-floating mb-4">
                                        <input class="form-control" type="password" name="rpassword" placeholder="Repetir Password" required>
                                        <label for="floatingInput">Repetir Password</label>
                                    </div>

                                    <input class="btn btn-outline-light btn-lg px-5" name="register" type="submit" value="Registar" />
                                </form>
                                <br/>
                                <a href="terms_and_conditions.php" style="color: white;">Ao realizar o registo está a concordar com estes Termos e Condições.</a> 
                            </div>

                            <div>
                                <p class="mb-0 text-white-50">Já está registado? <a href="../login/login.php" class="text-white-50 fw-bold">Login</a>
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>