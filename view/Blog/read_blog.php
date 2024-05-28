<?php
session_start();

require_once '../../controllers/db/pdo.php';
$pdo = getPDO();
$blog = null;


include '../../controllers/logic/message_controller.php';

function n12br($string) {
  return str_replace(["\r\n", "\r", "\n"], '<br>', $string);
}
try {
    $stmt = $pdo->prepare("SELECT name,active FROM settings");
    $stmt->execute();
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
   
} catch (Exception$e) {
    $settings = false;
}

if(isset($_GET['blog_id'])){
    $blog_id = $_GET['blog_id'];

     try {
      
      $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = :blog_id");
      $stmt->bindParam(':blog_id', $blog_id, PDO::PARAM_INT);
      $stmt->execute();

      $blog = $stmt->fetch(PDO::FETCH_ASSOC);

        if($blog){
            $id = $blog['id'];
            $blog_title = $blog['desc'];
            $blog_text = $blog['text'];
            $User_id = $blog['User_id'];
            $created_at = $blog['created_at'];
            $updated_at = $blog['updated_at'];
            $active = $blog['active'];
            $Destination_id = $blog['Destination_id'];
            $concept = $blog['concept'];

            $created_at = substr($created_at,0,8);
            $created_at = str_replace("-", "/", $created_at);

            $stmt = $pdo->prepare("SELECT id,FirstName, LastName FROM user");
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $author = "";
            if($users){
              foreach($users as $user){
                if($user["id"] === $User_id){
                  $author .= $user["FirstName"].' '.$user["LastName"];
                }
              }
            }

            try {
              $folder_Path = "../../public/images/db_images/blogs/" . $blog_id;
              $image_Urls = array();

              if (is_dir($folder_Path)) {
                  $folder = opendir($folder_Path);
  
                  while (($file = readdir($folder)) !== false) {
                      if (is_file($folder_Path . '/' . $file) && preg_match('/\.(jpg|jpeg|png)$/', $file)) {
                          $image_Urls[] = $folder_Path . '/' . $file;
                      }
                  }
  
                  closedir($folder);

                  $gallery = '<ul class="row galleryblog d-flex justify-content-center">';
                  foreach($image_Urls as $image_Url){
                    $gallery .= '
                    <li class="col col-12 col-sm-6 col-md-4 col-lg-3">
                   
                      <img data-enlargable src="'. $image_Url .'" alt="">
                  
                  </li>';
                  }
                  
                  $gallery .= '</ul>';
              }
            } catch (Exception $e) {
              $gallery = "<h3 class='text-center'>Obrázky nenalezeny</h3>";
            }

            try {
                    
                    if($settings[0]['active'] == 0){
                      $stmt = $pdo->prepare("SELECT * FROM blogcomments WHERE Blogs_id = :blog_id AND active = 1 ORDER BY id DESC");
                      $stmt->bindParam(':blog_id', $id, PDO::PARAM_INT);
                      $stmt->execute();
                      $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    }else{
                      $comments = false;
                    }

                    

                    $commentsHTML = "";
                    if(isset($comments[0])){
                      foreach ($comments as $comment) {

                        $stmt = $pdo->prepare("SELECT LastName,FirstName FROM user WHERE id = :user_id");
                        $stmt->bindParam(':user_id', $comment['User_id'], PDO::PARAM_INT);
                        $stmt->execute();
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);

                        $deleteIcon = ($_SESSION['user_id'] == $comment['User_id']) ? '<div><i class="fas fa-trash-alt delete-comment" data-comment-id="' . $comment['id'] . '"></i></div>' : '';

                        $commentHTML = '
                            <div class="comment">
                                <div class="comment-header d-flex justify-content-between w-100 align-items-center">
                                    <div class="comment-author">' . $user["FirstName"] . " " . $user["LastName"] . '</div>
                                    ' . $deleteIcon . '
                                </div>
                                <div class="comment-date">' . substr($comment['created_at'], 0, 8) . '</div>
                                <div class="comment-content">' . $comment['text'] . '</div>
                            </div>';

                        $commentsHTML .= $commentHTML;
                    }
                    }else{
                      $commentsHTML = "<h5 class='text-center'>Zatím zde nejsou žádné komentáře</h5>";
            
                    }
            } catch (Exception $e) {
              $commentsHTML = "<h3 class='text-center'>Komentáře nejsou dostupné</h3>";
            }

            
        } else{
          $blog_title = "Blog nenalezen";
        }
     } catch (Exception $e) {
      $blog_title = "Blog nenalezen";
      $blog = false;
     }
}else{
  $blog_title = "Blog nenalezen";
}

try {
  function hasUserLikedBlog( $blogId, $userId) {
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT COUNT(id) FROM bloglikes WHERE blogs_id = :blog_id AND user_id = :user_id");
    $stmt->bindParam(':blog_id', $blogId, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn() > 0;
}

function getBlogLikesCount( $blogId) {
  $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT COUNT(id) FROM bloglikes WHERE blogs_id = :blog_id");
    $stmt->bindParam(':blog_id', $blogId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn();
}
if(isset($blog_id)){
  
  if (isset($_SESSION['user_id']) ) {
    $userId = $_SESSION['user_id'];
    $hasLiked = hasUserLikedBlog($blog_id, $userId);

    $likesCount = getBlogLikesCount($blog_id);
    if(!$hasLiked){
        $likeSection = '
      <div class="like-section">
          <button class="like-button" id="likeButton">To se mi líbí</button>
          <span class="likes-count"><i class="ml-1 mr-2 fa-solid fa-thumbs-up"></i>'.$likesCount.'</span>
      </div>
      ';
    }else{
      $likeSection = '
      <div class="like-section">
          <button class="like-button disabled" id="likeButton" >To se mi líbí</button>
          <span class="likes-count"><i class="ml-1 mr-2 fa-solid fa-thumbs-up"></i>'.$likesCount.'</span>
      </div>
      ';
    }
  }else{

  $likesCount = getBlogLikesCount($blog_id);
  $likeSection = '
      <div class="like-section">
          <a href="../Auth/login.php"><button class="like-button" id="likeButton">Přihlásit se</button></a>
          <span class="likes-count"><i class="ml-1 mr-2 fa-solid fa-thumbs-up"></i>'.$likesCount.'</span>
      </div>
      ';
  }
}
} catch (Exception $e) {
  
}


//user login setup
if(isset($_SESSION['user_name'])){
  $username = $_SESSION["user_name"];
  $userbuttons = '<a href="../../controllers/logout.php"><button class="login mr-2">Odhlásit se</button></a>';

}else{
  $username = "nepřihlášen" ;
  $userbuttons = '<a href="../Auth/login.php"><button class="login mr-2">Přihlásit</button></a>
           <a href="../Auth/login.php?auth=register"><button class="register mr-2">Registrovat se</button></a>';   
}



?>

<!DOCTYPE html>
<html lang="cs">
<head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>NomadicHornets</title>
        
        <link rel="stylesheet" href="../../styles/modal.css">
        <link rel="stylesheet" href="read_blog.css">
        <link rel="stylesheet" href="../../styles/themplate.css">
        <script src="https://kit.fontawesome.com/8cb19e38e8.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="../../styles/animations.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
          <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    </head>
<body>
<header>
    <div class="user d-flex flex-sm-row justify-content-sm-between justify-content-center flex-column">
            <div class=" ml-3 d-flex h-100 h-sm-75 align-items-center">
            <div class="d-flex flex-row align-items-center">
            <i id="usericon" class="ml-1 fa-solid fa-user"></i>
            <h5 class="ml-1"><?php echo $username?></h5>
            </div>
            </div>
            <div class="d-flex flex-row">
            <?php echo $userbuttons?>
            </div>
        </div>
        <div class="bg_pic_blog">
          <div class="read_black_opacity ">
            <div class="top_info align-items-baseline  d-flex flex-row justify-content-end">
                
                <div id="normalnav" class="navbar_container">
                  <div class="hamburger">
                        <input type="checkbox" id="active">
                      <label for="active" class="menu-btn"><span></span></label>
                      <label for="active" class="close"></label>
                      <div class="wrapper d-flex flex-column justify-content-center align-items-center">
                        
                  <a  href="../../index.php">Home</a>
                  <a href="blog.php">Blog</a>
                  <a href="../../view/About/about.html">O nás</a>
                  <a href="../Photogallery/gallery.php">Fotogalerie</a>
                  
              </div>
                  </div>
                  <nav class="navbar navbar-expand-lg " id="custom-navbar">
                    <div class="container-fluid">
                        <button class="navbar-toggler hamburger-menu" type="button" aria-expanded="false" aria-label="Toggle navigation">
                            <div class="menu-icon"></div>
                        </button>
                        <div  class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link" href="../../index.php">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active " href="blog.php">Blog</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="../../view/About/about.html">O nás</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="../Photogallery/gallery.php">Fotogalerie</a>
                                </li>
                                
                            </ul>
                        </div>
                    </div>
                </nav>
                
                
                </div>
                <div id="scrollernav" class="d-none fixed-top  fade-top scnav">
                    <div class="d-flex flex-row fix_nav justify-content-center align-items-center ">
                        
                            <a class="nav-link" href="../../index.php">Home</a>
                        
                            <a class="active nav-link" href="blog.php">Blog</a>
                        
                            <a class="nav-link" href="../../view/About/about.html">O nás</a>
                        
                            <a class="nav-link" href="../Photogallery/gallery.php">Fotogalerie</a>
                        
                            
                    </div>
                    
                </div>
                
            </div>
            <div class="bg_text pl-5 d-flex flex-row justify-content-start align-items-center ">
            <h1 id=""><?php echo $blog_title;?></h1>
            </div>
              </div>
        </div>
          

      
      </header>
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
      <main class="d-flex flex-column justify-content-center align-items-center">
       <?php
       if($blog){       
        ?>
        <div class="read_blog d-flex flex-column align-items-center justify-content-center">
          <div class="read_blog_title_image">
            <img src="../../public/images/db_images/blogs/<?php echo $blog_id;?>/img-title.jpg" alt="">
            <div class="blog_info d-flex flex-row justify-content-between">
            <h5><?php echo $author;?></h5>
            <h5><?php echo $created_at;?></h5>
          </div>
          </div>
          
          <div class="read_blog_title ">
            <h1><?php echo $blog_title?></h1>
          </div>
          <div class="read_blog_text">
            <p><?php echo n12br($blog_text)?></p>
          </div>
          <?php
          if(isset($settings[3]["active"]) && !$settings[3]["active"]){
          ?>
          <div class="read_blog_likes">
          <?php echo $likeSection;?>
          </div>
          <?php
          }
          ?>
          
          <div class="read_blog_gallery mt-5">
          <h2 class="text-center mb-3">Galerie</h2>
            <?php echo $gallery;?>
          </div>
          
          <?php
          if(isset($settings[0]["active"]) && !$settings[0]["active"]){
            if(isset($_SESSION['auth']) && $_SESSION['auth'] <= 2 ){
            ?>
            <h2 class="text-center mb-3">Komentáře</h2>
            <div class="comment-form">
                <div class="comment-form-inputs d-flex flex-column align-items-end justify-content-center">
                    <input class="w-100" type="text" id="comment" name="comment" placeholder="Váš komentář" required>
                    <div class="d-flex flex-row">
                        <input class="add-comment-button" type="button" value="Přidat komentář">
                        <input class="grey_button cancel-comment-button" type="button" value="Zrušit">
                    </div>
                </div>
            </div>
            <?php
            }else{?>
                <div class="register_comment">
                  <h3>Pro přidání komentáře se musíte přihlásit</h3>
                  <a href="../Auth/login.php"><button class="login mr-2">Přihlásit</button></a>
                  <a href="../Auth/login.php?auth=register"><button class="register mr-2">Registrovat se</button></a>
                </div>
                <h2 class="text-center mb-3">Komentáře</h2>
            <?php
            }?>
            <div class="comments">
              <?php echo $commentsHTML?>
            </div>
          </div>
        <?php }else{?>
            <h3 class="text-center mb-3">Komentáře jsou vypnuté</h3>
            <?php } ?>
       <?php
       }?>
      </main>
      <footer>
        <div class="footer_content">
          <div class="footer_menu flex-lg-row d-flex flex-column">
            
            <a class="nav-link" href="../../index.php">Home</a>
                          
            <a class="nav-link" href="../Blog/blog.php">Blog</a>
        
            <a class="nav-link" href="about.html">O nás</a>
        
            <a class="nav-link" href="../Photogallery/gallery.php">Fotogalerie</a>
        
  
            <a class="nav-link" href="../Legal/zasady.html">Zásady ochrany osobních údajů</a>
        
          </div>
          <div class="d-flex flex-column justify-content-center container-fluid align-items-center">
            <h2  class="text-center">
              Copyright 2024 © NomadicHornets
            </h2>
            <h3>
              Tvůrce webu: <a target="_blank" href="https://www.wdkorba.cz/">Korba Lukáš</a>
            </h3>
          </div>
        </div>
      </footer>
      <script src="../../js/navVisibility.js"></script>
      <script src="../../js/reveal.js"></script>
      <script src="../../js/img.js"></script>
      <script src="../../js/hamburgerMenu.js"></script>
      <script src="js/comment_funcionality.js"></script>
      <script src="js/modal.js"></script>
      <script src="../../js/cookies.js"></script>
    <?php
    if (!isset($_SESSION['cookieAccepted']) || $_SESSION['cookieAccepted'] !== true) {
      echo('<div id="cookieBanner" class="d-flex flex-md-row flex-column align-items-md-center justify-content-md-between justify-content-center">
      <p id="cookieText">Tato webová stránka používá soubory cookie k zajištění nejlepšího zážitku při používání našich webových stránek.
      </p>
      <div class="d-flex flex-sm-row flex-column "><button id="acceptButton" class="mr-sm-3 mr-0">Přijmout nutné</button><button id="acceptAllButton">Přijmout vše</button></div>
    </div>');
    }
    ?>
      
</body>
</html>