<?php



require_once '../../controllers/db/pdo.php';
session_start();



if (isset($_POST['submit'])) {

    if(isset($_SESSION['auth']) && $_SESSION['auth'] <= 2 ){
        $_SESSION['login_error'] = 'Uživatel již přihlášen';
        header('Location: ../../index.php');
        exit();
    }
   
    $email = $_POST['email'];
    $password = $_POST['password'];
    $auth = $_POST['auth'];

    //filter data    

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $password = htmlspecialchars($password, ENT_QUOTES, 'UTF-8');
    //check the validation of user credentials

    if (!$email || $email == "" || empty($password) ) {
        $_SESSION['login_error'] = "E-mail nebo heslo nebylo zadáno správně";
        header('Location: login.php'); 
        exit();
    }
    $pdo = getPDO();
    //find email
    try {
        
        $stmt = $pdo->prepare("SELECT blocked,password FROM user WHERE Email = :Email");
        $stmt->bindParam(':Email', $email);
        $stmt->execute();
        $result = $stmt->fetch();
    } catch (Exception $e) {
        
    }

    //check if user exist
    if(empty($result)){
        $_SESSION['login_error'] = "Uživatel nenalezen";
        header('Location: login.php'); 
        exit();
    }

    if($result['blocked'] == 1){
        $_SESSION['login_error'] = "Uživatel byl zablokován";
        header('Location: login.php'); 
        exit();
    }

    
    if(isset($_COOKIE['attempt_count']) && $_COOKIE['attempt_count'] >= 5){
        $_SESSION['login_error'] = "Dosažen maximální počet pokusů na přihlášení, zkuste znovu za 2 hodiny";
        header('Location: login.php'); 
        exit();
    }


    if(!isset($_COOKIE['attempt_count'])){
        setcookie('attempt_count', 1, time() + 7200, '/');
    }else{
        setcookie('attempt_count', $_COOKIE['attempt_count'] + 1, time() + 7200, '/');
    }

    //test password and log in the user
    if ($result && password_verify($password, $result['password'])) {

        $stmt = $pdo->prepare("SELECT SecurityLevel_id,FirstName,LastName,id FROM user WHERE Email = :Email");
        $stmt->bindParam(':Email', $email);
        $stmt->execute();
        $result = $stmt->fetch();

        $_SESSION['auth'] = $result['SecurityLevel_id'];
        $_SESSION['user_name']= $result['FirstName']. " ". $result['LastName'];
        $_SESSION['user_id'] = $result['id'];

        if($auth === 'admin' ){
            header('Location: ../Admin/'); 
        }

        if(isset($_GET["auth"]) && $_GET["auth"] === "admin"){
            header('Location: ../Admin/admin.php');
            exit();
        }
        $_SESSION['login_error'] = "Uživatel úspěšně přihlášen";
        header('Location: ../../index.php');
        exit();
    } else {
        $countattepmt = 5 - $_COOKIE['attempt_count'];
        $_SESSION['login_error'] = "E-mail nebo heslo se neshodují, prosím zkuste to znovu<br>Pokusů zbývá: " . $countattepmt;
        header('Location: login.php'); 
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}
?>
