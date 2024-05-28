<?php
session_start();

require_once '../db/pdo.php';

function n12br($string) {
    return str_replace(["\r\n", "\r", "\n"], '<br>', $string);
}



//save blog data if some error so it load on the page again
function save_blog_info(){
    if(isset($_POST['selectCountry'])){
        $_SESSION['selectCountry'] = $_POST['selectCountry'];
    }
    if(isset($_POST['blogTitle'])){
        $_SESSION['concept-title'] = $_POST['blogTitle'];
    }
    if(isset($_POST['blogText'])){
        $_SESSION['concept-text'] = $_POST['blogText'];
    }
    if(isset($_POST['blog_id'])){
        $_SESSION['blog_id'] = $_POST['blog_id'];
    }
}

//for unseting the info
function unset_blog_info(){
        if(isset($_SESSION['selectCountry'])){
            unset($_SESSION['selectCountry']);
        }
        if(isset($_SESSION['concept-title'])){
            unset($_SESSION['concept-title']);
        }
        if(isset($_SESSION['concept-text'])){
            unset($_SESSION['concept-text']);
        }
        if(isset($_SESSION['blog_id'])){
            unset($_SESSION['blog_id']);
        }
}


//check action 
if (isset($_GET['action'])) {
    
    $pdo = getPDO();

    switch ($_GET['action']) {
        case 'get_concept':
            if (isset($_SESSION['user_id']) && isset($_SESSION['auth']) && $_SESSION['auth'] <= 1) {
                try {

                    //find user concepts
                    $userId = $_SESSION['user_id'];
                    $stmt = $pdo->prepare("SELECT * FROM blogs WHERE concept = 1 AND User_id = :user_id");
                    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                    $stmt->execute();
                    $concepts = $stmt->fetchAll(PDO::FETCH_ASSOC);


                    if($concepts){
                        //procces data
                        foreach ($concepts as $concept) {
                            $createdTimestamp = strtotime($concept['created_at']);
                            $formattedCreatedAt = date('d-m-y', $createdTimestamp);

                            $response[] = array(
                                'id' => $concept['id'],
                                'desc' => $concept['desc'],
                                'text' => $concept['text'],
                                'User_id' => $concept['User_id'],
                                'created_at' => $formattedCreatedAt,
                                'updated_at' => $concept['updated_at'],
                                'active' => $concept['active'],
                                'Destination_id' => $concept['Destination_id'],
                                'concept' => $concept['concept']
                            );
                        }
                    }else{
                        $response = array();
                    }

                    

                    echo json_encode($response);

                } catch (PDOException $e) {
                    echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
                }
            } else {
                echo json_encode(array('error' => 'User ID not set'));
            }
            break;

        case 'save_concept':
                if (isset($_SESSION['user_id']) && isset($_SESSION['auth']) && $_SESSION['auth'] <= 1) {
                    try {
                    // bcs using js frontend for fetch
                    $post_data = json_decode(file_get_contents('php://input'), true);

                    //set the post data to normal post
                    
                    $_POST = array_merge($_POST, $post_data);
                    
                    
                    //check if blog title is fine

                    if(isset($_POST['blogTitle']) && trim($_POST['blogTitle']) !== ""){
                        $title = filter_var($_POST['blogTitle'], FILTER_SANITIZE_STRING);
                        if($title === false){ 
                            $_SESSION["message"] = "Název obsahuje nevhodné znaky."; 
                            save_blog_info();
                            exit();
                        }
                    }else{
                        $_SESSION["message"] = "Chybí název blogu."; 
                        save_blog_info();
                        exit();
                    }
                    //check if blog text is fine

                    if(isset($_POST['blogText'])){
                        $blogText = filter_var($_POST['blogText'], FILTER_SANITIZE_STRING);
                        
                        if($blogText === false){
                            $_SESSION["message"] = "Text obsahuje nevhodné znaky."; 
                            save_blog_info();
                            exit();
                        }
                    }else{
                        $_SESSION["message"] = "Chybí text blogu."; 
                        save_blog_info();
                        exit();
                    }
                    //check selected country and if exists

                    if(isset($_POST['selectCountry']) && $_POST['selectCountry'] != "0"){
                        $destinationId = $post_data['selectCountry'];
                        if($destinationId === false){
                            $_SESSION["message"] = "Země obsahuje nevhodné znaky." . $_POST['selectCountry']; 
                            save_blog_info();
                            exit();
                        }
                        if(empty($destinationId)){
                            $_SESSION["message"] = "Chybí destinace"; 
                            save_blog_info();
                            exit();
                        }
                        //getting the destination
                        try {
                            $destinationCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM destination WHERE id = :destinationId");
                            $destinationCheckStmt->bindParam(':destinationId', $destinationId, PDO::PARAM_STR);
                            $destinationCheckStmt->execute();
                            $destinationCount = $destinationCheckStmt->fetchColumn();
                        } catch (PDOException $e) {
                            save_blog_info();
                            $_SESSION["message"] = "Nepodařilo se najít destinaci z databáze ". $e->getMessage();
                            exit();
                        }
                        //if it doesnt find destionation
                        if ($destinationCount === 0) {
                            $_SESSION["message"] = "Zvolená destinace neexistuje.";
                            save_blog_info();
                            exit();
                        }
                    }else{
                        $_SESSION["message"] = "Chybí destinace."; 
                        save_blog_info();
                       exit();
                    }

                        $blogText = n12br($blogText);
                        
                        if (isset($_POST['blog_id']) && $_POST['blog_id'] !== "") {
                            $updatedAt = date('d-m-y H:i:s');
                        
                            $stmt = $pdo->prepare("UPDATE blogs SET `desc` = :title, text = :text, Destination_id = :destinationId, active = 0, concept = 1 , updated_at = :updatedAt WHERE id = :blogId");
                            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
                            $stmt->bindParam(':text', $blogText, PDO::PARAM_STR);
                            $stmt->bindParam(':updatedAt', $updatedAt, PDO::PARAM_STR);
                            $stmt->bindParam(':blogId', $_POST['blog_id'], PDO::PARAM_INT);
                            $stmt->bindParam(':destinationId', $destinationId, PDO::PARAM_STR);
                            $stmt->execute();
                        
                            $_SESSION['type'] = "message";
                            $_SESSION["message"] = "Koncept byl uložen";
                            unset_blog_info();
                            exit();
                        }
                            
                            $userId = $_SESSION['user_id'];
                            $createdAt = date('d-m-y H:i:s');
                            $updatedAt = date('d-m-y H:i:s');
                            $active = 0; //set to not active
                            $concept = 1;// but only concept
                            $stmt = $pdo->prepare("INSERT INTO blogs (`desc`, text, User_id, created_at, updated_at, active, Destination_id, concept) VALUES (:title, :text, :userId, :createdAt, :updatedAt, :active, :destinationId, :concept)");
                            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
                            $stmt->bindParam(':text', $blogText, PDO::PARAM_STR);
                            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                            $stmt->bindParam(':createdAt', $createdAt, PDO::PARAM_STR);
                            $stmt->bindParam(':updatedAt', $updatedAt, PDO::PARAM_STR);
                            $stmt->bindParam(':active', $active, PDO::PARAM_INT);
                            $stmt->bindParam(':destinationId', $destinationId, PDO::PARAM_STR);
                            $stmt->bindParam(':concept', $concept, PDO::PARAM_INT);
                            $stmt->execute();
                            
                            $_SESSION['type'] = "message";
                            $_SESSION["message"] = "Koncept byl uložen";
                            unset_blog_info();
                            
    
                    } catch (PDOException $e) {
                        save_blog_info();
                        $_SESSION["message"] = "Nepodařilo se uložit koncept: " . $e->getMessage();
                        
                        exit();
                    }
                } else {
                        save_blog_info();
                        $_SESSION["message"] = "Je potřeba platné id";
                        exit();
                }
                break;
        case 'post_blog':
            if (isset($_SESSION['user_id']) && isset($_POST['post_blog']) && isset($_SESSION['auth']) && $_SESSION['auth'] <= 1) {
                try {
                    //check if blog title is fine

                    if(isset($_POST['blogTitle']) && trim($_POST['blogTitle']) !== ""){
                        $title = filter_input(INPUT_POST, 'blogTitle', FILTER_SANITIZE_STRING);
                        if(!$title){
                            $_SESSION["message"] = "Název obsahuje nevhodné znaky."; 
                            save_blog_info();
                            header("Location: ../../view/Admin/manage_blogs.php");
                            exit(); 
                        }
                    }else{
                        $_SESSION["message"] = "Chybí název blogu."; 
                        save_blog_info();
                        header("Location: ../../view/Admin/manage_blogs.php");
                        exit(); 
                    }
                    //check if blog text is fine

                    if(isset($_POST['blogText']) && trim($_POST['blogText']) !== ""){
                        $blogText = filter_input(INPUT_POST, 'blogText', FILTER_SANITIZE_STRING);
                        if(!$blogText){
                            $_SESSION["message"] = "Text obsahuje nevhodné znaky."; 
                            save_blog_info();
                            header("Location: ../../view/Admin/manage_blogs.php");
                            exit(); 
                        }
                    }else{
                        $_SESSION["message"] = "Chybí text blogu."; 
                        save_blog_info();
                        header("Location: ../../view/Admin/manage_blogs.php");
                        exit(); 
                    }
                    //check selected country and if exists

                    if(isset($_POST['selectCountry']) && $_POST['selectCountry'] != "0"){
                        $destinationId = filter_input(INPUT_POST, 'selectCountry', FILTER_SANITIZE_STRING);
                        if($destinationId === false){
                            $_SESSION["message"] = "Destinace obsahuje nevhodné znaky." . $_POST['selectCountry']; 
                            save_blog_info();
                            header("Location: ../../view/Admin/manage_blogs.php");
                            exit(); 
                        }
                        //getting the destination
                        try {
                            $destinationCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM destination WHERE id = :destinationId");
                            $destinationCheckStmt->bindParam(':destinationId', $destinationId, PDO::PARAM_STR);
                            $destinationCheckStmt->execute();
                            $destinationCount = $destinationCheckStmt->fetchColumn();
                        } catch (PDOException $e) {
                            save_blog_info();
                            $_SESSION["message"] = "Nepodařilo se najít destinaci z databáze ". $e->getMessage();
                            header("Location: ../../view/Admin/manage_blogs.php");
                            exit();
                        }
                        //if it doesnt find destionation
                        if ($destinationCount === 0) {
                            $_SESSION["message"] = "Zvolená destinace neexistuje.";
                            save_blog_info();
                            header("Location: ../../view/Admin/manage_blogs.php");
                            exit();
                        }
                    }else{
                        $_SESSION["message"] = "Chybí destinace."; 
                        save_blog_info();
                        header("Location: ../../view/Admin/manage_blogs.php");
                        exit(); 
                    }

                    try {

                        //set blog id or validate one
                        if (!isset($_POST['blog_id']) || $_POST['blog_id'] == "") {
                            $stmt = $pdo->prepare("SELECT MAX(id) AS max_id FROM blogs");
                            $stmt->execute();
                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            if ($result['max_id'] !== null) {
                                $blog_id = (int)$result['max_id'] + 1;
                            } else {
                                $blog_id = 1; 
                            }
                        } else {
                            $blog_id = filter_var($_POST['blog_id'], FILTER_VALIDATE_INT);
                            if ($blog_id === false || $blog_id < 1) {
                                $_SESSION["message"] = "Špatné blog id."; 
                                save_blog_info();
                                header("Location: ../../view/Admin/manage_blogs.php");
                                exit();  
                            }
                        }
                        //images funcionality
                        $imageFolder = '../../public/images/db_images/blogs/' . $blog_id;
    
                        if (!is_dir($imageFolder)) {
                            mkdir($imageFolder, 0755, true); 
                        }
    
                        if ($_FILES['titlePhoto']['error'] === UPLOAD_ERR_OK) {
                            $titlePhotoPath = $imageFolder . '/img-title.jpg'; 
                            move_uploaded_file($_FILES['titlePhoto']['tmp_name'], $titlePhotoPath);
                        }else{
                            $_SESSION["message"] = "Chybí úvodní fotka."; 
                                save_blog_info();
                                header("Location: ../../view/Admin/manage_blogs.php");
                                exit(); 
                        }
                        //galery photos for blog
                        if (!empty($_FILES['galleryPhotos']['name'][0])) {
                            foreach ($_FILES['galleryPhotos']['tmp_name'] as $key => $tmp_name) {
                                if ($_FILES['galleryPhotos']['error'][$key] === UPLOAD_ERR_OK) {
                                    $galleryPhotoPath = $imageFolder . '/img' . ($key + 1) . '.jpg'; 
                                    move_uploaded_file($tmp_name, $galleryPhotoPath);
                                   
                                }
                            }
                        }
                    } catch (Exception $e) {
                        $_SESSION["message"] = "Nepodařilo se uložit obrázky";
                        save_blog_info();
                        header("Location: ../../view/Admin/manage_blogs.php");
                        exit(); 
                    }
                    
                    $blogText = n12br($blogText);
                    
                    //if the blog id is set, it mean that it update already created blog in db
                    if(isset($_POST['blog_id']) && $_POST['blog_id'] !== ""){
                        $updatedAt = date('d-m-y H:i:s');
                        $active = 1; //set to visible to public
                        $concept = 0; 
                        $stmt = $pdo->prepare("UPDATE blogs SET `desc` = :title, text = :text, updated_at = :updatedAt, active = :active, Destination_id = :destinationId, concept = :concept WHERE id = :blog_id");
                        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
                        $stmt->bindParam(':text', $blogText, PDO::PARAM_STR);
                        $stmt->bindParam(':updatedAt', $updatedAt, PDO::PARAM_STR);
                        $stmt->bindParam(':active', $active, PDO::PARAM_INT);
                        $stmt->bindParam(':destinationId', $destinationId, PDO::PARAM_STR);
                        $stmt->bindParam(':concept', $concept, PDO::PARAM_INT);
                        $stmt->bindParam(':blog_id', $_POST['blog_id'], PDO::PARAM_INT);
                        $stmt->execute();
                        
                        $_SESSION['type'] = "message";
                        $_SESSION["message"] = "Blog byl zveřejněn";
                        unset_blog_info();
                        header("Location: ../../view/Admin/manage_blogs.php");
                        exit();
                    }
                        //if no blog id, it create a new one
                        $userId = $_SESSION['user_id'];
                        $createdAt = date('d-m-y H:i:s');
                        $updatedAt = date('d-m-y H:i:s');
                        $active = 1; //set to visible to public
                        $concept = 0; //reset concept
                        $stmt = $pdo->prepare("INSERT INTO blogs (id,`desc`, text, User_id, created_at, updated_at, active, Destination_id, concept) VALUES (:id,:title, :text, :userId, :createdAt, :updatedAt, :active, :destinationId, :concept)");
                        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
                        $stmt->bindParam(':text', $blogText, PDO::PARAM_STR);
                        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                        $stmt->bindParam(':createdAt', $createdAt, PDO::PARAM_STR);
                        $stmt->bindParam(':updatedAt', $updatedAt, PDO::PARAM_STR);
                        $stmt->bindParam(':active', $active, PDO::PARAM_INT);
                        $stmt->bindParam(':destinationId', $destinationId, PDO::PARAM_STR);
                        $stmt->bindParam(':concept', $concept, PDO::PARAM_INT);
                        $stmt->bindParam(':id', $blog_id, PDO::PARAM_INT);
                        $stmt->execute();
                        
                        $_SESSION['type'] = "message";
                        $_SESSION["message"] = "Blog byl zveřejněn";
                        unset_blog_info();
                        header("Location: ../../view/Admin/manage_blogs.php");
                        exit();
                    
                    
                    

                } catch (PDOException $e) {
                    $_SESSION["message"] = "Blog se nepodařilo uložit do databáze". $e->getMessage();
                    save_blog_info();
                    header("Location: ../../view/Admin/manage_blogs.php");
                    exit();
                }
            } else {
                $_SESSION["message"] = "Blog se nepodařilo uložit";
                save_blog_info();
                header("Location: ../../view/Admin/manage_blogs.php");
                exit();
            }
            break;
        case 'get_blogs':
            try {
                // bcs using js frontend for fetch
                $post_data = json_decode(file_get_contents('php://input'), true);

                //set the post data to normal post
                $_POST = array_merge($_POST, $post_data);

                if(isset($_POST['request'])){
                    $filter = $_POST['filter'];

                    if(isset($filter)){
                        switch ($filter) {
                            case 'active':
                                try {
                                    $stmt = $pdo->prepare("SELECT b.id,b.Destination_id, b.`desc`, b.text, b.User_id, b.created_at, b.active, COUNT(cl.id) AS comment_count, COUNT(lk.id) AS like_count FROM blogs AS b LEFT JOIN blogcomments AS cl ON b.id = cl.Blogs_id LEFT JOIN bloglikes AS lk ON b.id = lk.blogs_id WHERE b.concept = 0 AND b.active = 1 GROUP BY b.id ORDER BY STR_TO_DATE(b.created_at, '%d-%m-%y %H:%i:%s') DESC");
                                    $stmt->execute();
                                    $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                                    echo json_encode($blogs);
                                    exit();
                                } catch (Exception $e) {
                                    $_SESSION["message"] = "Nepodařilo se získat blogy z databáze" . $e->getMessage();
                                    echo json_encode(array());
                                    exit();
                                }
                                break;
                            case 'new':
                                try {
                                    $stmt = $pdo->prepare("SELECT b.id,b.Destination_id, b.`desc`, b.text, b.User_id, b.created_at, b.active, COUNT(cl.id) AS comment_count, COUNT(lk.id) AS like_count FROM blogs AS b LEFT JOIN blogcomments AS cl ON b.id = cl.Blogs_id LEFT JOIN bloglikes AS lk ON b.id = lk.blogs_id WHERE b.concept = 0 GROUP BY b.id ORDER BY STR_TO_DATE(b.created_at, '%d-%m-%y %H:%i:%s') DESC");
                                    $stmt->execute();
                                    $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                                    echo json_encode($blogs);
                                    exit();
                                } catch (Exception $e) {
                                    $_SESSION["message"] = "Nepodařilo se získat blogy z databáze" . $e->getMessage();
                                    echo json_encode(array());
                                    exit();
                                }
                                break;
                            case 'old':
                                try {
                                    $stmt = $pdo->prepare("SELECT b.id, b.`desc`, b.text, b.User_id, b.created_at, b.active, COUNT(cl.id) AS comment_count, COUNT(lk.id) AS like_count FROM blogs AS b LEFT JOIN blogcomments AS cl ON b.id = cl.Blogs_id LEFT JOIN bloglikes AS lk ON b.id = lk.blogs_id WHERE b.concept = 0 GROUP BY b.id ORDER BY STR_TO_DATE(b.created_at, '%d-%m-%y %H:%i:%s') ASC");
                                    $stmt->execute();
                                    $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                                    echo json_encode($blogs);
                                    exit();
                                } catch (Exception $e) {
                                    $_SESSION["message"] = "Nepodařilo se získat blogy z databáze" . $e->getMessage();
                                    echo json_encode(array());
                                    exit();
                                }
                                break;
                            case 'alphabet':
                                    try {
                                        $stmt = $pdo->prepare("SELECT b.id, b.`desc`, b.text, b.User_id, b.created_at,b.active, COUNT(cl.id) AS comment_count, COUNT(lk.id) AS like_count FROM blogs AS b LEFT JOIN blogcomments AS cl ON b.id = cl.Blogs_id LEFT JOIN bloglikes AS lk ON b.id = lk.blogs_id WHERE b.concept = 0  GROUP BY b.id ORDER BY b.`desc` ASC");
                                        $stmt->execute();
                                        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        echo json_encode($blogs);
                                        exit();
                                    } catch (Exception $e) {
                                        $_SESSION["message"] = "Nepodařilo se získat blogy z databáze". $e->getMessage();
                                        echo json_encode(array());
                                        exit();
                                    }
                                    break;
                                
                            case 'likes':
                                    try {
                                        $stmt = $pdo->prepare("SELECT b.id, b.`desc`, b.text, b.User_id, b.created_at,b.active, COUNT(cl.id) AS comment_count, COUNT(lk.id) AS like_count FROM blogs AS b LEFT JOIN blogcomments AS cl ON b.id = cl.Blogs_id LEFT JOIN bloglikes AS lk ON b.id = lk.blogs_id WHERE b.concept = 0 GROUP BY b.id ORDER BY like_count DESC");
                                        $stmt->execute();
                                        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        echo json_encode($blogs);
                                        exit();
                                    } catch (Exception $e) {
                                        $_SESSION["message"] = "Nepodařilo se získat blogy z databáze". $e->getMessage();
                                        echo json_encode(array());
                                        exit();
                                    }
                                    break;
                            case 'comments':
                                    try {
                                        $stmt = $pdo->prepare("SELECT b.id, b.`desc`, b.text, b.User_id, b.created_at,b.active, COUNT(cl.id) AS comment_count, COUNT(lk.id) AS like_count FROM blogs AS b LEFT JOIN blogcomments AS cl ON b.id = cl.Blogs_id LEFT JOIN bloglikes AS lk ON b.id = lk.blogs_id WHERE b.concept = 0 GROUP BY b.id ORDER BY comment_count DESC");
                                        $stmt->execute();
                                        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        echo json_encode($blogs);
                                        exit();
                                    } catch (Exception $e) {
                                        $_SESSION["message"] = "Nepodařilo se získat blogy z databáze". $e->getMessage();
                                        echo json_encode(array());
                                        exit();
                                    }
                                    break;
                            case 'id':

                                if (isset($_POST["blog_id"])) {
                                    $blog_id = $_POST["blog_id"];

                                    
                                    try {
                                        $stmt = $pdo->prepare("SELECT b.id, b.Destination_id, b.`desc`, b.text, u.FirstName, u.LastName, b.active, b.created_at, COUNT(cl.id) AS comment_count, COUNT(lk.id) AS like_count, d.CzechName AS DestinationName FROM blogs AS b LEFT JOIN blogcomments AS cl ON b.id = cl.Blogs_id LEFT JOIN bloglikes AS lk ON b.id = lk.blogs_id LEFT JOIN user AS u ON b.User_id = u.id LEFT JOIN destination AS d ON b.Destination_id = d.id WHERE b.id = :blog_id AND b.concept = 0 GROUP BY b.id ORDER BY STR_TO_DATE(b.created_at, '%a-%m-%d %H:%i:%s') DESC");
                                        $stmt->bindParam(':blog_id', $blog_id, PDO::PARAM_INT); 
                                        $stmt->execute();
                                        $blog = $stmt->fetch(PDO::FETCH_ASSOC);

                                        echo json_encode($blog);
                                        exit();
                                    } catch (Exception $e) {
                                        $_SESSION["message"] = "Nepodařilo se získat blog z databáze: " . $e->getMessage();
                                        echo json_encode(array());
                                        exit();
                                    }
                                } else {
                                    $_SESSION["message"] = "Nepodařilo se získat blog ";
                                    echo json_encode(array());
                                    exit();
                                }
                                break;
                            default:
                                exit();
                                break;
                        }
                    }
                }else{
                    $_SESSION["message"] = "Nepodařilo se získat blogy z databáze" ;
                    exit();
                }

            } catch (Exception $e) {
                    $_SESSION["message"] = "Nepodařilo se získat blogy z databáze". $e->getMessage();
                    echo json_encode(array());
                    exit();
            }
            break;
        case 'remove_blog':
            try {
                // bcs using js frontend for fetch
                $post_data = json_decode(file_get_contents('php://input'), true);


                if (isset($post_data['remove_id']) && isset($_SESSION['auth']) && $_SESSION['auth'] <= 1) {
                    $remove_Id = $post_data['remove_id'];
                    
                    $stmt = $pdo->prepare("DELETE FROM blogs WHERE id = :remove_id");
                    $stmt->bindParam(':remove_id', $remove_Id, PDO::PARAM_INT);
                    $stmt->execute();

                    $folder_Path = "../../public/images/db_images/blogs/" . $remove_Id ;

                    
                    if (is_dir($folder_Path)) {
                        //delete all files in folder
                        $files = glob($folder_Path . '/*');
                        foreach ($files as $file) {
                            if (is_file($file)) {
                                unlink($file);
                            }
                        }
                        //remove the empty folder
                        rmdir($folder_Path);
                    }

                    exit();
                } else {
                    $_SESSION["message"] = "Nepodařilo se smazat blog.";
                    exit();
                }
            } catch (Exception $e) {
                $_SESSION["message"] = "Nepodařilo se smazat blog.". $e->getMessage();
                echo json_encode(array());
                exit();
            }
            break;
        case 'update_blog':
                if(isset($_SESSION['auth']) && $_SESSION['auth'] <= 1){
                    try {

                        $title = filter_input(INPUT_POST, 'blogTitle', FILTER_SANITIZE_STRING);
                        $blogText = filter_input(INPUT_POST, 'blogText', FILTER_SANITIZE_STRING);
                        $destinationId = filter_input(INPUT_POST, 'selectCountry', FILTER_SANITIZE_STRING);
                        $blog_id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
                       
                        //validate data

                        if (empty($title) || empty($blogText) || empty($destinationId)) {
                            $_SESSION["message"] = "Chybí název, text nebo destinace.";
                            exit();
                        }
            
                        if ($blog_id === false || $blog_id < 1) {
                            $_SESSION["message"] = "Špatné blog id.";
                            exit();
                        }
            
                        $imageFolder = '../../public/images/db_images/blogs/' . $blog_id;
            
                        if (!is_dir($imageFolder)) {
                            mkdir($imageFolder, 0755, true);
                        }
                        //title photo
                        if(isset($_FILES['titlePhoto'])){
                            if ($_FILES['titlePhoto']['error'] === UPLOAD_ERR_OK) {
                                $titlePhotoPath = $imageFolder . '/img-title.jpg';
                                move_uploaded_file($_FILES['titlePhoto']['tmp_name'], $titlePhotoPath);
                            }
                        }
                        
            
                        
                        //gallery photos
                        if (!empty($_FILES['galleryPhotos']['name'][0])) {
                            $galleryPhotoCount = 0;
                            $files = glob($imageFolder . '/img*.jpg');
                            if ($files) {
                                $highestNumber = 0;
                                foreach ($files as $file) {
                                    $filename = basename($file);
                                    $parts = explode('.', $filename);
                                    $number = intval(str_replace('img', '', $parts[0]));
                                    if ($number > $highestNumber) {
                                        $highestNumber = $number;
                                    }
                                }
                                $galleryPhotoCount = $highestNumber;
                            }
                            foreach ($_FILES['galleryPhotos']['tmp_name'] as $key => $tmp_name) {
                                if ($_FILES['galleryPhotos']['error'][$key] === UPLOAD_ERR_OK) {
                                    $galleryPhotoCount++;
                                    $galleryPhotoPath = $imageFolder . '/img' . $galleryPhotoCount . '.jpg';
                                    move_uploaded_file($tmp_name, $galleryPhotoPath);
                                }
                            }
                        }               
            
                        $updatedAt = date('d-m-y H:i:s');
                        $active = $_POST['active']; // set to visible to the public
                        $concept = 0;

                        $blogText = n12br($blogText);
            
                        //update for the blog 
                        $stmt = $pdo->prepare("UPDATE blogs SET `desc` = :title, text = :blogText, updated_at = :updatedAt, active = :active, Destination_id = :destinationId, concept = :concept WHERE id = :blog_id");
                        $stmt->bindParam(':blog_id', $blog_id, PDO::PARAM_INT);
                    
        
                        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
                        $stmt->bindParam(':blogText', $blogText, PDO::PARAM_STR);
                        $stmt->bindParam(':updatedAt', $updatedAt, PDO::PARAM_STR);
                        $stmt->bindParam(':active', $active, PDO::PARAM_INT);
                        $stmt->bindParam(':destinationId', $destinationId, PDO::PARAM_STR);
                        $stmt->bindParam(':concept', $concept, PDO::PARAM_INT);
                        $stmt->execute();
            
                        $_SESSION['type'] = "message";
                        $_SESSION["message"] = "Blog byl uložen";
                        exit();
                    } catch (Exception $e) {
                        $_SESSION["message"] = "Nepodařilo se uložit blog: " . $e->getMessage();
                        exit();
                    }
                
                }
            break;
        
        case  'set_visibility_blog':
            try {
                $post_data = json_decode(file_get_contents('php://input'), true);
                //set active for a blog
                if(isset($post_data["blog_id"])  && isset($_SESSION['auth']) && $_SESSION['auth'] <= 1){
                    $blog_id = $post_data['blog_id'];

                    $stmt = $pdo->prepare("UPDATE blogs SET active = NOT active WHERE id = :blog_id");
                    $stmt->bindParam(':blog_id', $blog_id, PDO::PARAM_INT);
                    $stmt->execute();
                }else {
                    $_SESSION["message"] = "Nepodařilo se změnit viditelnost blogu.";
                    exit();
                }
            } catch (Exception $e) {
                $_SESSION["message"] = "Nepodařilo se změnit viditelnost blogu". $e->getMessage();
                echo json_encode(array());
                exit();
            }
                break;
        case 'get_img_urls':
                try {
                    $post_data = json_decode(file_get_contents('php://input'), true);
                    //get imgs of the blog if there are any
                    if (isset($post_data["blog_id"])) {
                        $blog_id = $post_data['blog_id'];
            
                        $folder_Path = "../../public/images/db_images/blogs/" . $blog_id;
                        $image_Urls = array();
                        //search the folders
                        if (is_dir($folder_Path)) {
                            $folder = opendir($folder_Path);
            
                            while (($file = readdir($folder)) !== false) {
                                if (is_file($folder_Path . '/' . $file) && preg_match('/\.(jpg|jpeg|png)$/', $file)) {
                                    $image_Urls[] = $folder_Path . '/' . $file;
                                }
                            }
            
                            closedir($folder);
                        }
    
                        echo json_encode($image_Urls);
                    } else {
                        $_SESSION["message"] = "Nepodařilo se změnit viditelnost blogu.";
                        exit();
                    }
                } catch (Exception $e) {
                    $_SESSION["message"] = "Nepodařilo se změnit viditelnost blogu" . $e->getMessage();
                    echo json_encode(array());
                    exit();
                }
                break;
            case 'remove_image':
                try {
                    $post_data = json_decode(file_get_contents('php://input'), true);
                    
                    if(isset($post_data['path'])  && isset($_SESSION['auth']) && $_SESSION['auth'] <= 1){
                        $path = $post_data['path'];
                        //this deletes the img url
                        unlink($path);
                        exit();
                    }else{
                    $_SESSION["message"] = "Nepodařilo se najít path k obrázku";
                    echo json_encode(array());
                    exit();
                    }

                } catch (Exception $e) {
                    $_SESSION["message"] = "Nepodařilo se vymazat obrázek" . $e->getMessage();
                    echo json_encode(array());
                    exit();
                }
                break;
            
            case 'get_active_destination':
                try {
                    $stmt = $pdo->prepare("SELECT DISTINCT d.id, d.CzechName FROM destination d JOIN blogs b ON d.id = b.Destination_id WHERE b.active = 1");
                    $stmt->execute();
                    $destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    //return active destination that have blogs
                    echo json_encode($destinations);
                    exit();
                } catch (Exception $e) {
                    $_SESSION["message"] = "Nepodařilo se dostat destinace" . $e->getMessage();
                    echo json_encode(array());
                    exit();
                }
                break;
        default:
            echo 'Invalid action';
            break;
    }
}



?>
