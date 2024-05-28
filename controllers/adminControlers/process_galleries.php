<?php
session_start();
require_once '../db/pdo.php';

$pdo = getPDO();

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'get_galleries':
            try {
                $post_data = json_decode(file_get_contents('php://input'), true);
                $filter = isset($post_data['filter']) ? $post_data['filter'] : ''; 
        
                if ($filter === 'old') {
                    $stmt = $pdo->prepare("SELECT id, created_at, updated_at, active, User_id, name FROM photogallery ORDER BY STR_TO_DATE(created_at, '%d-%m-%y %H:%i:%s') DESC");
                } else if ($filter === 'new') {
                    $stmt = $pdo->prepare("SELECT id, created_at, updated_at, active, User_id, name FROM photogallery ORDER BY STR_TO_DATE(created_at, '%d-%m-%y %H:%i:%s') ASC");
                } else if ($filter === 'id') {
                    $id = $post_data['galleryId'];
                    $stmt = $pdo->prepare("SELECT id, created_at, updated_at, active, User_id, name FROM photogallery WHERE id = :id");
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                } else {
                      $stmt = $pdo->prepare("SELECT id, created_at, updated_at, active, User_id, name FROM photogallery");
                }
        
                $stmt->execute();
                $galleries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
                if ($galleries) {
                    echo json_encode($galleries);
                    exit();
                } else {
                    echo json_encode(array());
                    exit();
                }
            } catch (Exception $e) {
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
                exit();
            }
            break;
        case 'activate_gallery':
            try {
                if (!isset($_SESSION['user_id']) || !isset($_SESSION['auth']) || $_SESSION['auth'] > 1) {
                    echo json_encode(array('error' => 'Na toto nemá uživatel práva'));
                    exit();
                }
                
        
                $post_data = json_decode(file_get_contents('php://input'), true);
                $galleryId = $post_data['galleryId'];
        
                $stmt = $pdo->prepare("SELECT active FROM photogallery WHERE id = :galleryId");
                $stmt->bindParam(':galleryId', $galleryId, PDO::PARAM_INT);
                $stmt->execute();
                $active = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Neg value for block/unblock
                $active = !$active['active'];
        
                $stmt = $pdo->prepare("UPDATE photogallery SET active = :active WHERE id = :galleryId");
                $stmt->bindParam(':active', $active, PDO::PARAM_INT);
                $stmt->bindParam(':galleryId', $galleryId, PDO::PARAM_INT);
                $stmt->execute();
        
                echo json_encode(['message' => "Galérie stav změněn"]);
                exit();
            } catch (PDOException $e) {
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
                exit();
            }
            break;
        case 'get_gallery_photos':
            try {
                $post_data = json_decode(file_get_contents('php://input'), true);
                $galleryId = $post_data['galleryId'];

                $stmt = $pdo->prepare("SELECT id, url, created_at, updated_at, active FROM photogalleryphotos WHERE PhotoGallery_id = :galleryId");
                $stmt->bindParam(':galleryId', $galleryId, PDO::PARAM_INT);
                $stmt->execute();
                $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($photos) {
                    echo json_encode($photos);
                    exit();
                } else {
                    echo json_encode([]);
                    exit();
                }
            } catch (Exception $e) {
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
                exit();
            }

        case 'remove_gallery':
            try {
                if (!isset($_SESSION['user_id']) || !isset($_SESSION['auth']) || $_SESSION['auth'] > 1) {
                    echo json_encode(array('error' => 'Na toto nemá uživatel práva'));
                    exit();
                }

                $post_data = json_decode(file_get_contents('php://input'), true);
                $galleryId = $post_data['galleryId'];

                $stmtGetPhotoUrls = $pdo->prepare("SELECT url FROM photogalleryphotos WHERE PhotoGallery_id = :galleryId");
                $stmtGetPhotoUrls->bindParam(':galleryId', $galleryId, PDO::PARAM_INT);
                $stmtGetPhotoUrls->execute();
                $photoUrls = $stmtGetPhotoUrls->fetchAll(PDO::FETCH_COLUMN);

                $stmtDeletePhotos = $pdo->prepare("DELETE FROM photogalleryphotos WHERE PhotoGallery_id = :galleryId");
                $stmtDeletePhotos->bindParam(':galleryId', $galleryId, PDO::PARAM_INT);
                $stmtDeletePhotos->execute();

                $stmtDeleteGallery = $pdo->prepare("DELETE FROM photogallery WHERE id = :galleryId");
                $stmtDeleteGallery->bindParam(':galleryId', $galleryId, PDO::PARAM_INT);
                $stmtDeleteGallery->execute();

                foreach ($photoUrls as $url) {
                    $filename = basename($url);
                    $filePath = '../../public/images/galleryImages/' . $galleryId . '/' . $filename;
                    unlink($filePath);
                }

                echo json_encode(['message' => 'Gallery removed successfully']);
                exit();
            } catch (Exception $e) {
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
                exit();
            }

            break;

        case 'remove_photo':
            try {
                if (!isset($_SESSION['user_id']) || !isset($_SESSION['auth']) || $_SESSION['auth'] > 1) {
                    echo json_encode(array('error' => 'Na toto nemá uživatel práva'));
                    exit();
                }

                $post_data = json_decode(file_get_contents('php://input'), true);
                $photoId = $post_data['photoId'];

                $stmtGetPhotoUrl = $pdo->prepare("SELECT url, PhotoGallery_id FROM photogalleryphotos WHERE id = :photoId");
                $stmtGetPhotoUrl->bindParam(':photoId', $photoId, PDO::PARAM_INT);
                $stmtGetPhotoUrl->execute();
                $photoData = $stmtGetPhotoUrl->fetch(PDO::FETCH_ASSOC);

                $stmtDeletePhoto = $pdo->prepare("DELETE FROM photogalleryphotos WHERE id = :photoId");
                $stmtDeletePhoto->bindParam(':photoId', $photoId, PDO::PARAM_INT);
                $stmtDeletePhoto->execute();

                try {
                    $galleryId = $photoData['PhotoGallery_id'];
                    $filename = basename($photoData['url']);
                    $filePath = "../../public/images/galleryImages/$galleryId/$filename";
                    unlink($filePath);
                    echo json_encode(['message' => $filePath]);
                    exit();
                } catch (Exception $e) {
                    echo json_encode(['error' => 'Nepodařilo se smazat přímo soubor']);
                    exit();
                }

                
            } catch (Exception $e) {
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
                exit();
            }

            break;

            case 'add_photos_to_gallery':
                try {
                    if (!isset($_SESSION['user_id']) || !isset($_SESSION['auth']) || $_SESSION['auth'] > 1) {
                        echo json_encode(array('error' => 'Na toto nemá uživatel práva'));
                        exit();
                    }
            
                    $galleryId = $_POST['galleryId'];
            
                    
                    if (empty($galleryId) || !is_numeric($galleryId)) {
                        echo json_encode(['error' => 'Špatné id gallerie']);
                        exit();
                    }
            
                    // Check if files are uploaded
                    if (empty($_FILES['photos']['name']) || count($_FILES['photos']['name']) === 0) {
                        echo json_encode(['error' => 'Nenahrané žádné fotky']);
                        exit();
                    }
            
                    $photoUrls = [];
            
                    // Loop through each uploaded file
                    foreach ($_FILES['photos']['tmp_name'] as $index => $tmpName) {
                        // validate
                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mime = finfo_file($finfo, $tmpName);
            
                        if (strpos($mime, 'image') === false) {
                            echo json_encode(['error' => 'Špatný formát']);
                            exit();
                        }
            
                        // move file
                        $destinationPath = "../../public/images/galleryImages/$galleryId/";
                        $imageName = $_FILES['photos']['name'][$index];
                        $targetPath = $destinationPath . $imageName;
            
                        if (!move_uploaded_file($tmpName, $targetPath)) {
                            echo json_encode(['error' => 'Nepodařilo se uložit soubor']);
                            exit();
                        }
            
                        $photoUrls[] = "../../public/images/galleryImages/$galleryId/$imageName";
                    }
            
                    // image URLs into the database
                    foreach ($photoUrls as $url) {
                        $stmt = $pdo->prepare("INSERT INTO photogalleryphotos (url, PhotoGallery_id, created_at) VALUES (:url, :galleryId, NOW())");
                        $stmt->bindParam(':url', $url, PDO::PARAM_STR);
                        $stmt->bindParam(':galleryId', $galleryId, PDO::PARAM_INT);
                        $stmt->execute();
                    }
            
                    echo json_encode(['message' => 'Fotky úspěšně přidány']);
                    exit();
                } catch (Exception $e) {
                    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
                    exit();
                }
            
                break;
            case 'add_gallery':
                try {
                    if (!isset($_SESSION['user_id']) || !isset($_SESSION['auth']) || $_SESSION['auth'] > 1) {
                        echo json_encode(array('error' => 'Na toto nemá uživatel práva'));
                        exit();
                    }
                    
                    $userId = $_SESSION['user_id'];
            
                    $galleryName = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
            
                    if (empty($galleryName) || $galleryName == "") {
                        echo json_encode(['error' => 'Zadejte vhodné jméno']);
                        exit();
                    }

                    // Check upload_max_filesize
                    $maxFileSize = ini_get('upload_max_filesize');
                    $uploadLimit = 500 * 1024 * 1024; // 500 MB
                    if ($_SERVER['CONTENT_LENGTH'] > $uploadLimit) {
                        echo json_encode(['error' => 'Překročena maximální velikost souboru (' . $maxFileSize . ')']);
                        exit();
                    }

                    // Check max_file_uploads
                    $maxFileUploads = ini_get('max_file_uploads');
                    $totalFiles = count($_FILES['galleryPhotos']['name']);
                    if ($totalFiles > $maxFileUploads) {
                        echo json_encode(['error' => 'Překročen maximální počet souborů (' . $maxFileUploads . ')']);
                        exit();
                    }
            
                    $createdAt = date('d-m-y H:i:s');
                    $updatedAt = date('d-m-y H:i:s');
            
                    $stmt = $pdo->prepare("INSERT INTO photogallery (name, created_at, User_id, updated_at, active) VALUES (:galleryName, :createdAt, :userId, :updatedAt, 1)");
                    $stmt->bindParam(':galleryName', $galleryName, PDO::PARAM_STR);
                    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                    $stmt->bindParam(':createdAt', $createdAt, PDO::PARAM_STR);
                    $stmt->bindParam(':updatedAt', $updatedAt, PDO::PARAM_STR);
                    $stmt->execute();
            
                    $galleryId = $pdo->lastInsertId();
            
                    // create a directory 
                    $galleryDirectory = "../../public/images/galleryImages/$galleryId/";
                    if (!file_exists($galleryDirectory)) {
                        if (!mkdir($galleryDirectory, 0777, true)) {
                            echo json_encode(['error' => 'Nepodařilo se vytvořit složku pro galerii']);
                            exit();
                        }
                    }
            
                    if (isset($_FILES["galleryPhotos"])) {
                        $imageUrls = [];
                        foreach ($_FILES['galleryPhotos']['tmp_name'] as $index => $tmpName) {
                            // verify that the file is an image
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            $mime = finfo_file($finfo, $tmpName);
            
                            if (strpos($mime, 'image') === false) {
                                echo json_encode(['error' => 'Špatný formát souboru']);
                                exit();
                            }
            
                            $imageName = $_FILES['galleryPhotos']['name'][$index];
                            $targetPath = $galleryDirectory . $imageName;
            
                            move_uploaded_file($tmpName, $targetPath);
            
                            // save url
                            $imageUrls[] = "../../public/images/galleryImages/$galleryId/$imageName";
                        }
                    } else {
                        echo json_encode(['error' => 'Nebyly vybrány žádné obrázky']);
                        exit();
                    }
            
                    // Insert image URLs into the database
                    foreach ($imageUrls as $imageUrl) {
                        $stmt = $pdo->prepare("INSERT INTO photogalleryphotos (url, PhotoGallery_id, created_at, updated_at, active) VALUES (:imageUrl, :galleryId, NOW(), NOW(), 1)");
                        $stmt->bindParam(':imageUrl', $imageUrl, PDO::PARAM_STR);
                        $stmt->bindParam(':galleryId', $galleryId, PDO::PARAM_INT);
                        $stmt->execute();
                    }
            
                    echo json_encode(['message' => 'Galerie přidána úspěšně']);
                    exit();
                } catch (Exception $e) {
                    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
                    exit();
                }
            
                break;
            case 'edit_gallery':
                try {
                    if (!isset($_SESSION['user_id']) || !isset($_SESSION['auth']) || $_SESSION['auth'] > 1) {
                        echo json_encode(array('error' => 'Na toto nemá uživatel práva'));
                        exit();
                    }
    
                    $post_data = json_decode(file_get_contents('php://input'), true);
    
                    $galleryId = $post_data['galleryId'];
                    $galleryname = filter_var($post_data['name'], FILTER_SANITIZE_STRING);

            
                    if (empty($galleryname) || $galleryname == "") {
                        echo json_encode(['error' => 'Zadejte vhodné jméno']);
                        exit();
                    }
                    $stmt = $pdo->prepare("UPDATE photogallery SET name = :Name WHERE id = :Id");
                    $stmt->bindParam(':Name', $galleryname, PDO::PARAM_STR);
                    $stmt->bindParam(':Id', $galleryId, PDO::PARAM_STR);
                    
                    $stmt->execute();
    
                    echo json_encode(['message' => 'Galerie aktualizována úspěšně']);
                    exit();
                } catch (PDOException $e) {
                    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
                    exit();
                }
                
                break;
            
        default:
            echo 'Invalid action';
            break;
    }
}
?>
