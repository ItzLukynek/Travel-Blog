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
    $sql = "SELECT id, CzechName FROM destination ORDER BY CzechName ASC";
    $stmt = $pdo->query($sql);

    $options = "";
    $first = true;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if($first){
            $first = false;
        }else{
            $options .= '<option value="' . $row['id'] . '">' . $row['CzechName'] . '</option>';
        }
    }
} catch (\Throwable $th) {
    $options = '<option value="">Nelze načíst destinace</option>';
    
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
                <li><a class="active" href="manage_blogs.php"><i class="fas fa-edit"></i>Správa blogů</a></li>
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
        <div class="ecard mb-3">
            <div class="ecard-body">
                <div class="d-flex flex-sm-row flex-column align-items-sm-baseline  justify-content-sm-between align-items-center">
                    <div class="card_title"><h1>Správa blogů</h1></div>
                    <div class=""><div class="add_blog"><a href="#write_blog"><button>Napsat blog</button></a></div></div>
                </div>
            </div>
        </div>
        <div class="ecard mb-3">
            <div class="ecard-body">
                 <div class="search-filter row mb-5 ">
                    <div class="col  col-md-8 col-sm-6 col-12">
                    <label for="">Vyhledat</label>
                    <input type="text" id="blogSearch" placeholder="Najít blog...">
                    </div>
                    <div class="col col-md-4 col-sm-6 col-12">
                    <label for="">Filter</label>
                    <select id="filterSelect">
                        <option value="new">Nejnovější</option>
                        <option value="old">Nejstarší</option>
                        <option value="likes">Nejlajkovanější</option>
                        <option value="comments">Nejkomentovanější</option>
                        <option value="alphabet">Abecedně</option>
                    </select>
                    </div>
                </div>
                <div class="manage_blogs_header ">
                    <div class="row">
                        <div class="col col-lg-6 col-md-7 col-12"><label for="">Název</label></div>
                        <div class="col col-lg-1 col-md-1 col-3 text-center"><label><i class="fa-solid fa-thumbs-up"></i></label></div>
                        <div class="col col-lg-1 col-md-1 col-3 text-center"><label><i class="fa-regular fa-comment-dots"></i></label></div>
                        <div class="col col-lg-4 col-md-3 col-12 text-center"><label >Nástroje</label></div>
                    </div>
                </div>
                <div class="manage_blogs" id="blogList">
                    
                </div>
            </div>
        </div>
        <div class="ecard" >
            <div class="ecard-body">
                <form action="../../controllers/adminControlers/process_blogs.php?action=post_blog" method="POST" enctype="multipart/form-data">
                    <div class="row flex-column flex-lg-row d-flex" >
                        <div class="col-12 col-lg-9 d-flex flex-column mb-3 mb-lg-0" id="write_blog">
                            <label for="blogTitle">Nadpis:</label>
                            <input  value="<?php
                                if(isset($_SESSION['concept-title'])){
                                    echo($_SESSION['concept-title']);
                                    unset($_SESSION['concept-title']);
                                }
                                ?>" type="text" id="blogTitle" name="blogTitle" class="" required>
                        </div>
                        <div class="col-12 col-lg-3 d-flex flex-column">
                            <label for="selectCountry">Vybrat zemi:</label>
                            <select id="selectCountry" name="selectCountry" class="">
                                <option value="0">Vybrat zemi</option>
                                <?php 
                                if ($options) {
                                    echo $options;
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="blog_images row mt-3">
                        <div class="form-group col-md-6 col-12 col">
                            <label for="titlePhotoInput">Nahrajte ůvodní fotku</label>
                            <input type="file" class="form-control-file" id="titlePhotoInput" accept="image/*" name="titlePhoto" >
                        </div>
                        <div class="form-group col-md-6 col-12 col">
                            <label for="galleryPhotosInput">Nahrajte fotky do galerie</label>
                            <input type="file" class="form-control-file " id="galleryPhotosInput" accept="image/*" name="galleryPhotos[]" multiple >
                        </div>
                    </div>
                    <label for="blogText">Text:</label>
                    <textarea id="blogText" name="blogText" rows="15" class="form-control" style="resize: none; height: 100%;" required><?php if(isset($_SESSION['concept-text'])){ echo(trim($_SESSION['concept-text']));unset($_SESSION['concept-text']);}?></textarea>
                               
                    <div  class="d-flex flex-column flex-md-row ">
                        <button   id="save_concept" type="button" class="mr-md-4 mt-2 mb-md-0  mb-4 btn btn-secondary">Uložit kocept textu</button>
                        <button  id="show_concept" type="button" class="btn mt-2 mb-md-0  mb-4 btn-secondary show_concept">Načíst koncept</button>
                        <button name="post_blog" type="submit" class=" ml-md-4 mt-2 mb-md-0 mb-0 btn btn-success">Zvěřejnit blog</button>
                    </div>
                    <input value="<?php if(isset($_SESSION['blog_id'])){echo $_SESSION['blog_id'];unset($_SESSION['blog_id']);} ?>" id="blog_id" type="text" style="display:none;" name="blog_id">
                </form>
            </div>
        </div>
        
        
        
           </div>
    </div>
        </div>
    </div>
    
    <script src="../../js/reveal.js"></script>
    <script src="js/menu.js"></script>
    <script src="js/modal.js"></script>
    <script src="js/blog_buttons.js"></script>
    <script src="js/manage_table.js"></script>
    <script src="js/modal.js"></script>
    <script>
        
    </script>
    

    
</body>
</html>