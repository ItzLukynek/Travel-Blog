<?php
session_start();
require_once '../../controllers/db/pdo.php'; 


//check if auth is good
if(!isset($_SESSION['auth']) || $_SESSION['auth'] > 0){
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

} catch (Exception $e) {
    $options = '<option value="">Nelze načíst destinace</option>';
    
}
try {
    $pdo = getPDO();
    $sql = "SELECT id,name,active FROM settings";
    $stmt = $pdo->query($sql);
    

    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $sql = "SELECT id, phrase FROM validations";
    $stmt = $pdo->query($sql);

    $phrases = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
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
    <link rel="stylesheet" href="styles/settings_admin.css">
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
                <li><a href="manage_galleries.php"><i class="fas fa-image"></i>Správa galerii</a></li>
                <?php
                if($auth === 0){
                    echo('<li><a href="manage_comments.php"><i class="fas fa-comment"></i>Správa komentářů</a></li>
                    <li><a href="manage_users.php"><i class="fas fa-users"></i>Správa uživatelů</a></li>
                    <li><a class="active" href="settings.php"><i class="fas fa-cog"></i>Nastavení</a></li>');
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
                <div class="ecard">
                    <div class="ecard-body">
                        <div class="card_title">
                            <h1>Nastavení</h1>
                        </div>
                    </div>
                </div>
                    <div class="ecard mt-3">
                        <div class="ecard-body">
                            <h5 class="text-center">Základní nastavení</h5>
                            
                        </div>
                        <div class="settings-container">
                            <div class="setting-box d-flex flex-row align-items-center justify-content-between">
                                <h5>Filtrovat komentáře</h5>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="validate_comments" <?php echo $settings[2]["active"] ? 'checked' : ''; ?>>
                                    <label class="custom-control-label" for="validate_comments"></label>
                                </div>
                            </div>
                        </div>
                        <div class="settings-container">
                            <div class="setting-box d-flex flex-row align-items-center justify-content-between">
                                <h5>Potvrzování komentářů</h5>
                                <div class="custom-control custom-switch ">
                                    <input type="checkbox" class="custom-control-input" id="aprove_comments" <?php echo $settings[1]["active"] ? 'checked' : ''; ?>>
                                    <label class="custom-control-label" for="aprove_comments"></label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="settings-container">
                            <div class="setting-box d-flex flex-row align-items-center justify-content-between">
                                <h5>Vypnout lajky</h5>
                                <div class="custom-control custom-switch ">
                                    <input type="checkbox" class="custom-control-input" id="turn_off_likes" <?php echo $settings[3]["active"]? 'checked' : ''; ?>>
                                    <label class="custom-control-label" for="turn_off_likes"></label>
                                </div>
                            </div>
                        </div>
                        <div class="settings-container">
                            <div class="setting-box d-flex flex-row align-items-center justify-content-between">
                                <h5>Vypnout komentáře</h5>
                                <div class="custom-control custom-switch ">
                                    <input type="checkbox" class="custom-control-input" id="turn_off_comments" <?php echo $settings[0]["active"] ? 'checked' : ''; ?>>
                                    <label class="custom-control-label" for="turn_off_comments"></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ecard mt-3">
                        <div class="ecard-body">
                                <h5 class="text-center">Seznam zakázaných slov</h5>
                                <p class="text-center"> (oddělovat čárkou, bez mezer)</p>
                                <div class="settings-container">
                                    <div class="setting-box d-flex flex-row align-items-center">
                                        <textarea id="allowed_words" rows="6"><?php
                                            foreach ($phrases as $phrase) {
                                                echo $phrase['phrase'] . ',';
                                            }
                                            ?></textarea>
                                    </div>
                                </div>
                               <div class="d-flex flex-row justify-content-end">
                               <button class="btn btn-secondary w-25 mr-5" id="save_allowed_words">Uložit</button>
                                    
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
    <script src="js/settings_funcionality.js"></script>
    <script>
        
    </script>
    

    
</body>
</html>