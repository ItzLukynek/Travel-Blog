<?php
session_start();

// if user wants to show admin or register
$auth = isset($_GET['auth']) ? $_GET['auth'] : 'login';

// $alert = "";

// // alert if error
// if (isset($_SESSION['login_error'])) {
//     $alert = 'alert("' . $_SESSION['login_error'] . '")';
//     unset($_SESSION["login_error"]);
// }

//show error if any
if(isset($_SESSION['login_error'])){
    $modal_HTML = '<div class="message_box ">
    
            <div class="container ">
            <h4 id="modal_message" class="p-3 text-center">'.$_SESSION['login_error'].'</h4></div></div>';
    unset($_SESSION["login_error"]);
};

$html = "<h1>NO HTML</h1>";
if ($auth === 'admin') {
    $admin_url = "?auth=admin";
} else {
    $admin_url = "";
}
if($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1'){
    $sitekey = "6LfJ0lApAAAAAI-YcG6GmDTMqMlsF30DZfBrPoNl";
}else{
    $sitekey = "6Lebf3YpAAAAALW3SavAywEyk08PCvPXFgIG4UWc";
}

if ($auth === 'login' || $auth === 'admin') {
    $html = '
    <div class="login-container">
        <div class="login-card">
            <h2>Přihlášení</h2>
            <form action="login_process.php' . $admin_url . '" method="POST">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Heslo</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <input style="display:none;" type="text" name="auth" value="' . $auth . '">
                <div class="buttons-container">
                    <button name="submit" type="submit" class="mb-3">Přihlásit se</button>
                    <a href="login.php?auth=register">
                        <div class="button">Registrovat se</div>
                    </a>
                </div>
            </form>
        </div>
    </div>';
} elseif ($auth === 'register') {
    $html = '
    <div class="login-container">
        <div class="login-card">
            <h2>Registrovat se</h2>
            <form action="register_process.php" method="POST">
                <div class="form-group">
                    <label for="new_first_name">Jméno</label>
                    <input type="text" id="new_first_name" name="new_first_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="new_last_name">Příjmení</label>
                    <input type="text" id="new_last_name" name="new_last_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="new_email">E-mail:</label>
                    <input type="email" id="new_email" name="new_email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Heslo</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required 
                        pattern="(?=.*[A-Z])(?=.*\d).{4,}" title="Heslo musí obsahovat alespoň 4 znaky a z toho 1 velké písmeno a číslici">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Heslo znovu</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>
                <div class="buttons-container">
                    <div class="g-recaptcha" data-sitekey="'.$sitekey.'"></div>
                        <button name="submit" type="submit" class="btn btn-primary">Registrovat se</button>
                        <a href="login.php">
                            <div class="button">Přihlášení</div>
                        </a>
                        <h4>Kliknutím na tlačítko "Registrovat se" souhlasíte se shromažďováním a zpracováním Vašich osobních údajů.</h4>
                </div>
            </form>
        </div>
    </div>';
}


?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="../../styles/modal.css">
    <link rel="stylesheet" href="../../styles/themplate.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Přihlášení</title>
    <style>
       body {
    margin: 0;
    min-height:100vh;
   
}

main{
    width: 100%;
    background-image: url('../../public/images/designImages/forests.jpg'); 
    background-position: center;
    background-repeat: no-repeat;
    display: flex;
    justify-content: center;
    z-index: 1;
    backdrop-filter: blur(50px);
    min-height: 100vh;
}


.login-container {
     
    background-color: rgba(255, 255, 255, 0.8); 
    border-radius: 16px;
    padding: 60px !important;
    box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.2) !important;
   margin: 20px 5px;
    z-index: 100;
    width: 30vw;
    min-width: 250px;
    max-width: 800px;
    height: auto;
    
   
}
@media(width > 996px){
    .login-container{
        margin: 100px 20px;
        backdrop-filter: blur(10px);
    }
}
@media(width < 996px){
    .login-container{
        
    padding: 30px !important;
    }
}

h2 {
    text-align: center !important;
    color: #333 !important;
}

form {
    margin-top: 20px !important;
}

.form-group {
    margin-bottom: 20px !important;
}

label {
    display: block !important;
    font-size: 16px !important;
    margin-bottom: 8px !important;
    color: #555 !important;
}

input {
    width: 100% !important;
    padding: 20px 13px !important;
    font-size: 16px !important;
    border: 1px solid #ccc !important;
    border-radius: 12px !important;
    box-sizing: border-box !important;
}

.buttons-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  margin-top: 20px;
}

.g-recaptcha {
  margin-bottom: 20px;
}
button {
    width: 100% !important;
    padding: 15px !important;
    font-size: 16px !important;
    border: none !important;
    border-radius: 12px !important;
    cursor: pointer !important;
    background-color: rgb(1,1,1) !important;
    color: #fff !important;
    transition: background-color 0.5s !important;
    font-weight: 600;
    margin-bottom: 10px;
}
.button {
    width: 100% !important;
    padding: 15px 0px !important;
    font-size: 16px !important;
    border: none !important;
    border-radius: 12px !important;
    cursor: pointer !important;
    background-color: rgb(1,1,1) !important;
    color: #fff !important;
    transition: background-color 0.5s !important;
    font-weight: 600;
    margin-bottom: 10px;
    text-align: center;
}
.button:hover{
    font-size: 18px !important;
    background-color: rgba(1,1,1,0.4) !important;
    transition:0.3s !important;
}
button:hover{
    font-size: 18px !important;
    background-color: rgba(1,1,1,0.4) !important;
    transition:0.3s !important;
}
a{
    width: 100% !important;
    text-decoration: none !important;;
}



.message_box{

}

#modal_message{
    text-align:center !important;
}

#show_HTML_Modal{
    margin-left:0px !important;
}
.HTML_Modal_Overlay{
    justify-content:center !important;
}



    </style>
</head>
<body class="d-flex justify-content-center">
<div  class="HTML_Modal_Overlay d-flex flex-row  justify-content-center">
            <div  id="show_HTML_Modal" class="d-flex flex-row justify-content-center">
            <?php if(isset($modal_HTML)) echo $modal_HTML; ?>
            </div>
        </div>
    <main >
        
        <?php echo $html; ?>
    </main>
</body>

<script src="../Admin/js/modal.js"></script>
</html>
