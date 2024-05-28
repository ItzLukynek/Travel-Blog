<?php
session_start();

require_once '../../controllers/db/pdo.php';

$pdo = getPDO();

//user login setup
if(isset($_SESSION['user_name'])){
  $username = $_SESSION["user_name"];
  $userbuttons = '<a href="../../controllers/logout.php"><button class="login mr-2">Odhlásit se</button></a>';

}else{
  $username = "nepřihlášen" ;
  $userbuttons = '<a href="../Auth/login.php"><button class="login mr-2">Přihlásit</button></a>
           <a href="../Auth/login.php?auth=register"><button class="register mr-2">Registrovat se</button></a>';   
}


if(isset($_GET["destination"])){
  $Destination_id = $_GET['destination'];

  $destinationCheckStmt = $pdo->prepare("SELECT EnglishName,CzechName,Shortcut,id FROM destination WHERE id = :destinationId");
  $destinationCheckStmt->bindParam(':destinationId', $Destination_id, PDO::PARAM_STR);
  $destinationCheckStmt->execute();
  $d = $destinationCheckStmt->fetch();

}


?>


<!DOCTYPE html>
<html lang="cs">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>NomadicHornets</title>
        <link rel="stylesheet" href="blog.css">
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
        <div class="bg_pic">
          <div class="black_opacity">
            <div class="top_info align-items-baseline  d-flex flex-row justify-content-between">
            <div class="social">
                <ul>
                    <li>
                      <a class="facebook" target="_blank" href="https://www.facebook.com/">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <i class="fa fa-facebook" aria-hidden="true"></i>
                      </a>
                    </li>
                    <li>
                      <a class="twitter" target="_blank" href="https://twitter.com/">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <i class="fa-brands fa-x-twitter"></i>
                      </a>
                    </li>
                    
                    <li>
                      <a class="google" target="_blank" href="https://www.google.cz/">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <i class="fa fa-google-plus" aria-hidden="true"></i>
                      </a>
                    </li>
                  </ul>
            </div>
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
                        <div class="collapse navbar-collapse" id="navbarNav">
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
                    <div class="d-none searchBox">
                        <input class="searchInput"type="text" name="" placeholder="Search">
                        <button class="searchButton" href="#">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
            </div>
            <div>
            </div>
            <?php
                      if(isset($d)){
                       echo' <div class="textWrapper d-flex w-100 pl-4 pr-2 h-50 justify-content-center align-items-center">
                          <h1 class="sentenceDisplay">'.$d["CzechName"].'</h1>
                            </div>';
                      }else{
                        echo'<div class="textWrapper d-flex w-100 pl-4 pr-2 h-50 justify-content-center align-items-center">
                        <h1 id="sentenceDisplay"></h1>
                          </div>';
                      }

                      ?>
        
          </div>    
      </div>

      
      </header>
      <main>
        <div class="under_main justify-content-center d-flex flex-row">
          
            <div class="d-flex align-content-center flex-column ">
            <section class="search d-flex flex-column ">
                <?php
                if(!isset($d)){?>
                  <div class="mt-5 mb-5">
                    <div class="title_header" style="padding: 25px 0px;">
                        <h2 class="section_title reveal fade-bottom">Prozkoumejte naše nejnovější články</h2>
                    </div>
                </div>
                <?php
                }?>
                
                <div class="d-flex  flex-lg-row flex-column justify-content-lg-between align-items-center justify-content-center">
                  <div class="search-container  d-flex justify-content-between">
                      <div class="d-flex w-100  flex-row align-items-center">
                      <i class="search-icon fas fa-search"></i>
                      <input class="search-input" type="text" placeholder="Vyhledat...">
                      </div>
                      <button class="search-button">Vyhledat</button>
                  </div>
                  <div class="dropdown  mt-5 mt-lg-0 ml-0 ml-lg-3">
                <button class="dropbtn">Destinace</button>
                <div id="destinations" class="dropdown-content">
                 
                </div>
            </div>
                </div>
              
            </section>
            <section class="section ">
                
                  <div class="blogs">
                     
                  </div>
                  <div class="d-flex flex-row justify-content-center">
                  <button id="loadMoreBtn" class="loadMoreBtn "><h5>Zobrazit další</h5></button>
                  </div>
            </section>
            
           
        </div>
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
          <div class="social d-flex justify-content-center mt-5">
                <ul>
                    <li>
                      <a class="facebook" target="_blank" href="https://www.facebook.com/">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <i class="fa fa-facebook" aria-hidden="true"></i>
                      </a>
                    </li>
                    <li>
                      <a class="twitter" target="_blank" href="https://twitter.com/">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <i class="fa-brands fa-x-twitter"></i>
                      </a>
                    </li>
                    
                    <li>
                      <a class="google" target="_blank" href="https://www.google.cz/">
                        <span></span>
                        <span></span>
                        <span></span>
                        <span></span>
                        <i class="fa fa-google-plus" aria-hidden="true"></i>
                      </a>
                    </li>
                  </ul>
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
      <script src="js/wordDisplayer.js"></script>
      <script src="js/blog_funcionality.js"></script>
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