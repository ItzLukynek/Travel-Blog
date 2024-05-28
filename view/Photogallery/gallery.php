<?php
session_start();

require_once '../../controllers/db/pdo.php';
$pdo = getPDO();

try {
  $stmt = $pdo->query("SELECT User_id,active,updated_at,created_at,name,id FROM photogallery WHERE active = 1");
$galleries = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
  $error = $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="cs">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>NomadicHornets</title>
        <link rel="stylesheet" href="galery_style.css">
        <link rel="stylesheet" href="../../styles/themplate.css">
        <script src="https://kit.fontawesome.com/8cb19e38e8.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="../../styles/animations.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>

<!-- Popper JS -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    </head>
<body>
    <div class="preload d-none">
      <img src="../../public/images/designImages/photogallerycanyon.jpg" alt="Your Image" class="d-none" preload>
      <img src="../../public/images/designImages/photoshoot.jpg" alt="">
    </div>
    <header>
    
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
                  <a href="../Blog/blog.php">Blog</a>
                  <a href="../About/about.html">O nás</a>
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
                                    <a class="nav-link  " href="../Blog/blog.php">Blog</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="../About/about.html">O nás</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" href="../Photogallery/gallery.php">Fotogalerie</a>
                                </li>
                                
                            </ul>
                        </div>
                    </div>
                </nav>
                
                
                </div>
                <div id="scrollernav" class="d-none fixed-top  fade-top scnav">
                    <div class="d-flex flex-row fix_nav justify-content-center align-items-center ">
                        
                            <a class="nav-link" href="../../index.php">Home</a>
                        
                            <a class=" nav-link" href="../Blog/blog.php">Blog</a>
                        
                            <a class="nav-link" href="../About/about.html">O nás</a>
                        
                            <a class="active nav-link" href="../Photogallery/gallery.php">Fotogalerie</a>
                        
                        
                    </div>
                    
                </div>
                    
            </div>
            <div>
            </div>
            <div class="textWrapper d-flex w-100 pl-4 pr-2 h-50 justify-content-center align-items-center">
                        <h1 id="sentenceDisplay"></h1>
                          </div>
                
        
          </div>    
      </div>

      
      </header>
      <main>
        <div class="under_main justify-content-center d-flex flex-row">
          
            <div class="d-flex align-content-center flex-column ">
            
            <section class="section mb-5">
                
                        <div class="galleries">
                                    <?php
                                    $isleft = true;
                                    foreach ($galleries as $gallery): ?>
                                
                                        <div class="gallery-item  <?php echo $isleft === true ? 'left reveal fade-left' : 'reveal fade-right';?>">
                                            <div class="gallery-title">
                                                <h2><?php echo htmlspecialchars($gallery['name']); ?></h2>
                                            </div>
                                            <div data-interval="false" id="carousel<?php echo $gallery['id']; ?>" class="carousel <?php echo $isleft === true ? 'left' : '';?> slide" data-ride="carousel">
                                       
                                                <ul class="carousel-indicators">
                                                  
                                                    <?php
                                                    $stmtPhotos = $pdo->prepare("SELECT id,url FROM photogalleryphotos WHERE PhotoGallery_id = :galleryId");
                                                    $stmtPhotos->bindParam(':galleryId', $gallery['id'], PDO::PARAM_INT);
                                                    $stmtPhotos->execute();
                                                    $photos = $stmtPhotos->fetchAll(PDO::FETCH_ASSOC);

                                                    foreach ($photos as $index => $photo): ?>
                                                        <li data-target="#carousel<?php echo $gallery['id']; ?>" data-slide-to="<?php echo $index; ?>" <?php echo $index === 0 ? 'class="active"' : ''; ?>></li>
                                                    <?php endforeach; ?>
                                                </ul>

                                                <div class="carousel-inner">
                                                    <?php foreach ($photos as $index => $photo): ?>
                                                        <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                                            <img data-enlargable src="<?php echo htmlspecialchars($photo['url']); ?>" alt="<?php echo htmlspecialchars($gallery['name']); ?>">
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>

                                                <a class="carousel-control-prev" href="#carousel<?php echo $gallery['id']; ?>" data-slide="prev">
                                                    <span class="carousel-control-prev-icon"></span>
                                                </a>
                                                <a class="carousel-control-next" href="#carousel<?php echo $gallery['id']; ?>" data-slide="next">
                                                    <span class="carousel-control-next-icon"></span>
                                                </a>
                                            </div>
                                        </div>
                                        
                                        
                                  
                                  <?php
                                  $isleft = !$isleft;
                                endforeach; ?>
                        </div>
                  <?php
                  if(count($galleries) > 3){
                    ?>
                    <div class="d-flex flex-row justify-content-center">
                  <button id="loadMoreBtn" class="loadMoreBtn "><h5>Zobrazit další</h5></button>
                  </div>
                    
                    <?php
                  }
                  ?>
            </section>
            
           
        </div>
        
      </main>
      <section class="about">
            <div class="black_opacity">
              <div class="under_about row d-flex justify-content-md-center">
                <div class="col col-md-1 col-0 d-none d-md-block d-lg-none"></div>
                <div class="col col-md-9 col-12"><h1 class="about_title reveal text-center fade-left">Chcete vidět více fotek?</h1></div>
                <div class="col col-md-2 col-0 d-none d-md-block ">
                </div>
                <div class="col-md-7 col col-0 d-none d-md-block "></div>
                <div class="reveal fade-bottom col col-md-5 col-12 d-flex flex-row justify-content-center">
                <a href="view/About/about.html"><button class="red_styled_btn ">Navštivte náš FB</button></a>
                </div>
                
              
            </div>
            </div>
        </section>
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
      <script src="js/gallery_funcionality.js"></script>
      <script src="js/slider.js"></script>
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