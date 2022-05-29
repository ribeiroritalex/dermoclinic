<?php
session_start();
require_once '../models/user.php';
require_once '../config/db_connection.php';
require_once '../config/jwt.php';
require_once '../functions/session_functions.php';

use UserNS\User;

// function console_log($output, $with_script_tags = true)
// {
//     $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
//         ');';
//     if ($with_script_tags) {
//         $js_code = '<script>' . $js_code . '</script>';
//     }
//     echo $js_code;
// }

onSessionRedirect();
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/site.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>DermoClinic | Login</title>
</head>

<body>
    <section class="vh-100">
        <div class="container py-5 h-100" style="background-color: teal;">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card bg-dark " style="border-radius: 1rem;">
                        <div class="card-body p-5 text-center">
                            <div class="login100-form-title">
                                <img src="../assets/images/Logo2.svg" class="mb-5" alt="">
                                <h2 class="fw-bold mb-2 text-white text-uppercase">Login</h2>
                                <p class="text-white-50 mb-4">Insira o email e password.</p>
                                <p class="fw-bold text-white">
                                    <?php
                                    // console_log("started");
                                    if (isset($_REQUEST["login"])) {
                                        // console_log("is Set");
                                        if (isset($_REQUEST["user_email"]) and isset($_REQUEST["user_password"])) {
                                            // console_log($_REQUEST["user_email"]);
                                            // console_log($_REQUEST["user_password"]);

                                            $user_email = $_REQUEST["user_email"];
                                            $user_password = $_REQUEST["user_password"];


                                            if ($user_email != null and $user_email != "") {

                                                if ($user_password != null and $user_password != "") {
                                                    $login_success = login($user_email, $user_password);
                                                    if ($login_success) {
                                                        onSessionRedirect();
                                                    } else {
                                                        $errorMsg = "Email and password combination not found.";
                                                    }
                                                } else {
                                                    $errorMsg = "Please enter a password.";
                                                }
                                            } else {
                                                $errorMsg = "Please enter an email address.";
                                            }
                                        } else {
                                            $errorMsg = "Please enter both an email and password.";
                                        }
                                        if (isset($errorMsg) && $errorMsg != "") {
                                            echo $errorMsg;
                                        } else {
                                            echo "Success!";
                                        }
                                    }

                                    ?>

                                <form method="post" action="login.php">
                                    <div class="form-floating mb-4">

                                        <input type="email" class="form-control" id="user_email" name="user_email" aria-describedby="emailHelp" placeholder="Enter email" required>
                                        <label for="floatingInput">Email</label>
                                        <div class="valid-feedback">
                                            Looks good!
                                        </div>

                                    </div>
                                    <div class="form-floating mb-4">
                                        <input type="password" class="form-control" id="user_password" name="user_password" placeholder="Password">
                                        <label for="floatingPassword">Palavra-chave</label>
                                    </div>

                                    <input id="login" name="login" class="btn btn-outline-light btn-lg px-5 mb-4" type="submit" value="Login" />

                                </form>
                            </div>


                            <div>
                                <p class="mb-0 text-white-50">Don't have an account? <a href="../register/register.php" class="text-white-50 fw-bold">Sign
                                        Up</a>
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</body>
<script src="../js/sitejs.js"></script>

</html>