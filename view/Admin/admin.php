<?php
session_start();
require_once '../../controllers/db/pdo.php'; 


//check if auth is good
if(!isset($_SESSION['auth']) || $_SESSION['auth'] > 1){
    header('Location: ../Auth/login.php?auth=admin');
    exit();
}

include '../../controllers/logic/message_controller.php';

$auth = $_SESSION['auth'];
$role = $auth ? "Blogger" : "Admin";
$user_name = $_SESSION['user_name'];


    $pdo = getPDO();
    

try {
    $userCountStmt = $pdo->prepare("SELECT COUNT(*) as user_count FROM user WHERE SecurityLevel_id = 2");
    $userCountStmt->execute();
    $userCountResult = $userCountStmt->fetch(PDO::FETCH_ASSOC);
    $userCount = $userCountResult['user_count'];

    $blogCommentsCountStmt = $pdo->query("SELECT COUNT(*) as blog_comments_count FROM blogcomments");
    $blogCommentsCountResult = $blogCommentsCountStmt->fetch(PDO::FETCH_ASSOC);
    $blogCommentsCount = $blogCommentsCountResult['blog_comments_count'];


    $blogLikesCountStmt = $pdo->query("SELECT COUNT(*) as blog_likes_count FROM bloglikes");
    $blogLikesCountResult = $blogLikesCountStmt->fetch(PDO::FETCH_ASSOC);
    $blogLikesCount = $blogLikesCountResult['blog_likes_count'];
} catch (PDOException $e) {
    $userCount = "Nelze načíst";
    $blogCommentsCount = "Nelze načíst";
    $blogLikesCount = "Nelze načíst";
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="admin_themeplate.css">
    <link rel="stylesheet" href="../../styles/themplate.css">
    <script src="https://kit.fontawesome.com/8cb19e38e8.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../../styles/animations.css">
    <link rel="stylesheet" href="../../styles/modal.css">
    <link rel="stylesheet" href="styles/info_admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
</head>
<body>
    <div class="header">
        
            <div class="user-info">
                <div class="user-role"><?php echo $role ?></div>
                <div class="user-name"><?php echo $user_name ?></div>
            </div>
            <div class="menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    <div class="admin-panel">
    
    <div id="slide"class="sidebar ">
            <div class="closener" style="display:none;">
                <div class="close_button">
                    <i class="fas fa-xmark"></i>
                </div>
            </div>
            <div class="user-info">
                <div class="user-role"><?php echo $role?></div>
                <div class="user-name"><?php echo $user_name?></div>
            </div>
            
            <ul class="menu d-flex flex-column">
                <li><a class="active" href="admin.php"><i class="fas fa-home"></i>Informace</a></li>
                <li><a  href="manage_blogs.php"><i class="fas fa-edit"></i>Správa blogů</a></li>
                <li><a href="manage_galleries.php"><i class="fas fa-image"></i>Správa galerii</a></li>
                <?php
                if($auth === 0){
                    echo('<li><a href="manage_comments.php"><i class="fas fa-comment"></i>Správa komentářů</a></li>
                    <li><a href="manage_users.php"><i class="fas fa-users"></i>Správa uživatelů</a></li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i>Nastavení</a></li>');
                }?>
                <li><a href="../../controllers/logout.php"><i class="fas fa-user"></i>Odhlásit se</a></li>
                
            </ul>
    </div>
        <div  class="HTML_Modal_Overlay  justify-content-center">
            <div  id="show_HTML_Modal" class="d-flex justify-content-center">
                <?php
                if(isset($modal_HTML)){
                    echo $modal_HTML;
                    unset($modal_HTML) ; 
                }
                ?>
            </div>
        </div>
        <div class="content" >

                <div class="container-fluid">
                    <div class="ecard mb-3">
                        <div class="ecard-body">
                            <div class="d-flex flex-row justify-content-center align-items-center">
                                <div class="card_title"><h1>Informační panel</h1></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                            <div class="col-4">
                                <div class="icard card-user">
                                    <div class="icard-header">
                                        <h2>Počet uživatelů</h2>
                                    </div>
                                    <div class="icard-content ">
                                        <h2><?php echo $userCount; ?></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="icard card-comments">
                                    <div class="icard-header">
                                        <h2>Počet komentářů</h2>
                                    </div>
                                    <div class="icard-content">
                                        <h2><?php echo $blogCommentsCount; ?></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="icard card-likes">
                                    <div class="icard-header">
                                        <h2>Počet lajků</h2>
                                    </div>
                                    <div class="icard-content">
                                        <h2><?php echo $blogLikesCount; ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                   


                </div> 


            <!--End-Content -->
        </div>
    
    
    <script src="../../js/reveal.js"></script>
    <script src="js/menu.js"></script>
    <script src="js/modal.js"></script>
    <script>
        
    </script>
    

    
</body>
</html>