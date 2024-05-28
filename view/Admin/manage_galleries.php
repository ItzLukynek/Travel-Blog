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



try {
    $pdo = getPDO();
   
} catch (Exception $e) {
    
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
    <link rel="stylesheet" href="styles/blog_admin.css">
    <link rel="stylesheet" href="styles/gallery_admin.css">
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
                <li><a href="admin.php"><i class="fas fa-home"></i>Informace</a></li>
                <li><a  href="manage_blogs.php"><i class="fas fa-edit"></i>Správa blogů</a></li>
                <li><a class="active" href="manage_galleries.php"><i class="fas fa-image"></i>Správa galerií</a></li>
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
        <div class="ecard mb-3">
            <div class="ecard-body">
                <div class="d-flex flex-sm-row flex-column align-items-sm-baseline  justify-content-sm-between align-items-center">
                    <div class="card_title"><h1>Správa gallerii</h1></div>
                    <div class=""><div  class="add_blog"><button id="add_gallery"><i style="font-size:30px;" class="fa-solid fa-plus"></i></button></div></div>
                </div>
            </div>
        </div>
        <div class="ecard mb-3">
            <div class="ecard-body">
                 <div class="search-filter row mb-5 ">
                    <div class="col  col-md-8 col-sm-6 col-12">
                    <label for="">Vyhledat</label>
                    <input type="text" id="blogSearch" placeholder="Najít galerii...">
                    </div>
                    <div class="col col-md-4 col-sm-6 col-12">
                    <label for="">Filter</label>
                    <select id="filterSelect">
                        <option selected value="new">Nejnovější</option>
                        <option value="old">Nejstarší</option>
                    </select>
                    </div>
                </div>
                <div class="manage_blogs_header ">
                    <div class="row">
                        <div class="col col-lg-8 col-md-8 col-12"><label for="">Jméno</label></div>
                        <div class="col col-lg-4 col-md-4 col-12 text-center"><label >Nástroje</label></div>
                    </div>
                </div>
                <div class="manage_blogs" id="blogList">
                    
                </div>
            </div>
        </div>
        
        
           </div>
    </div>
        </div>
    </div>
    
    <script src="../../js/reveal.js"></script>
    <script src="js/menu.js"></script>
    <script src="js/modal.js"></script>
    <script src="js/gallery_showing.js"></script>
    <script>
        
    </script>
    

    
</body>
</html>