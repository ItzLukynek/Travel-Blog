<?php
session_start();

require_once '../../controllers/db/pdo.php'; 


if (isset($_POST['submit'])) {

    if(isset($_SESSION['auth']) && $_SESSION['auth'] <= 2 ){
        $_SESSION['login_error'] = 'Uživatel již přihlášen';
        header('Location: ../../index.php');
        exit();
    }

    if (isset($_POST['g-recaptcha-response'])) {

        if($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1'){
            $key = "6LfJ0lApAAAAAKFrax2tbpTbIKna0VwLKB9dDabj";
        }else{
            $key = "6Lebf3YpAAAAAPDQSktR5UWv6jsxr9lCe1w2kiZe";
        }
        $recaptcha_secret = $key; 
        $recaptcha_response = $_POST['g-recaptcha-response'];
        $recaptcha_url = "https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret}&response={$recaptcha_response}";
        $recaptcha_data = json_decode(file_get_contents($recaptcha_url));
    
        if (!$recaptcha_data->success) {
            $_SESSION['login_error'] = 'Ověření reCAPTCHA selhalo';
            header('Location: login.php?auth=register');
            exit;
        }
    }else{
        $_SESSION['login_error'] = 'Potvrďte reCAPTCHA.';
        header('Location: login.php?auth=register');
        exit;
    }
  
    //filter the data
    $first_name = filter_input(INPUT_POST, 'new_first_name', FILTER_SANITIZE_STRING);
    $last_name = filter_input(INPUT_POST, 'new_last_name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'new_email', FILTER_VALIDATE_EMAIL);
    $password = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_STRING);
    $confirm_password = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_STRING);

    if (!preg_match('/^.{1,250}$/', $first_name)) {
        $_SESSION['login_error'] = 'Jméno musí mít mezi 1-250 znaků.';
        header('Location: login.php?auth=register');
        exit();
    }
    
    if (!preg_match('/^.{1,250}$/', $last_name)) {
        $_SESSION['login_error'] = 'Příjmení musí mít mezi 1-250 znaků.';
        header('Location: login.php?auth=register');
        exit();
    }
    
    if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
        $_SESSION['login_error'] = 'Neplatná emailová adresa.';
        header('Location: login.php?auth=register');
        exit();
    }
    
    if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{4,32}$/', $password)) {
        $_SESSION['login_error'] = 'Neplatné heslo';
        header('Location: login.php?auth=register');
        exit();
    }
    
    if ($password !== $confirm_password) {
        $_SESSION['login_error'] = 'Hesla se neshodují.';
        header('Location: login.php?auth=register');
        exit();
    }
    //if something missing or wrong 
    if (!$first_name || !$last_name || !$email || !$password || !$confirm_password) {
        $_SESSION['login_error'] = 'Nesprávné údaje, zkuste to prosím znovu.';
        header('Location: login.php?auth=register');
        exit();
    }

    //find if the email wasnt used in other registration
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT * FROM user WHERE Email = :Email");
    $stmt->bindParam(':Email', $email);
    $stmt->execute();
    $result = $stmt->fetch();

    if($result){
        $_SESSION['login_error'] = 'Účet s tímto e-mailem již existuje.';
        header('Location: login.php?auth=register');
        exit();
    }

   

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    //save user to db and data to session
   try{
        $createdAt = date('d-m-y H:i:s');
        $updatedAt = date('d-m-y H:i:s');

        $stmt = $pdo->prepare("INSERT INTO user (LastName, FirstName, created_at, updated_at, SecurityLevel_id, Email, verified, password) VALUES (:last_name, :first_name,:createdAt,:updatedAt, :security_level, :email, 0, :password)");
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':createdAt', $createdAt, PDO::PARAM_STR);
        $stmt->bindParam(':updatedAt', $updatedAt, PDO::PARAM_STR);
        $security_level = 2; 
        $stmt->bindParam(':security_level', $security_level);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->execute();

        $stmt = $pdo->prepare("SELECT id FROM user WHERE Email = :Email");
        $stmt->bindParam(':Email', $email);
        $stmt->execute();
        $result = $stmt->fetch();

        $_SESSION['auth'] = 2;
        $_SESSION['user_name'] = $first_name . " ". $last_name;
        $_SESSION['user_id'] = $result['id'];

        $_SESSION['login_error'] = "Uživatel úspěšně zaregistrován";
        header('Location: ../../index.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['login_error'] = "Registrace nevyšla " . $e;
        header('Location: login.php?auth=register'); 
        exit();
    }
} else {
    $_SESSION['login_error'] = "Nezdařilo se odeslání formuláře ";
        
    header('Location: login.php?auth=register');
    exit();
}
?>
