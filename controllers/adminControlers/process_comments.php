<?php
session_start();

require_once '../db/pdo.php';

include '../../controllers/logic/message_controller.php';


//method for correct showing of text
function n12br($string)
{
    return str_replace(["\r\n", "\r", "\n"], '<br>', $string);
}
//functiion for getting data if user already liked blog
function hasUserLikedBlog( $blogId, $userId) {
     try {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT COUNT(id) FROM bloglikes WHERE blogs_id = :blog_id AND user_id = :user_id");
        $stmt->bindParam(':blog_id', $blogId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
     } catch (Exception $e) {
        return null;
     }
  }

if (isset($_GET['action'])) {
    $pdo = getPDO();

   

    switch ($_GET['action']) {
        case 'get_all_comments':
            try {
                //get comments
                if (!isset($_SESSION['user_id']) || !isset($_SESSION['auth']) || $_SESSION['auth'] > 1) {
                        echo json_encode(array('error' => 'Na toto nemá uživatel práva'));
                        exit();
                    }
                $stmt = $pdo->prepare("SELECT id,text,active,blocked,created_at,User_id,Blogs_id FROM blogcomments WHERE blocked = 1 OR active = 1");
                $stmt->execute();
                $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $response = array();

                foreach ($comments as $comment) {
                    $response[] = array(
                        'id' => $comment['id'],
                        'text' => $comment['text'],
                        'active' => $comment['active'],
                        'blocked' => $comment['blocked'],
                        'created_at' => $comment['created_at'],
                        'User_id' => $comment['User_id'],
                        'Blogs_id' => $comment['Blogs_id'],
                    );
                }

                echo json_encode($response);

            } catch (PDOException $e) {
                echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
            }
            break;
        case 'get_blocked_comments':
            try {
                //get blocked comments
                if (!isset($_SESSION['user_id']) || !isset($_SESSION['auth']) || $_SESSION['auth'] > 1) {
                        echo json_encode(array('error' => 'Na toto nemá uživatel práva'));
                        exit();
                    }
                $stmt = $pdo->prepare("SELECT id,text,active,blocked,created_at,User_id,Blogs_id FROM blogcomments WHERE blocked = 1");
                $stmt->execute();
                $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $response = array();

                foreach ($comments as $comment) {
                    $response[] = array(
                        'id' => $comment['id'],
                        'text' => $comment['text'],
                        'active' => $comment['active'],
                        'blocked' => $comment['blocked'],
                        'created_at' => $comment['created_at'],
                        'User_id' => $comment['User_id'],
                        'Blogs_id' => $comment['Blogs_id'],
                    );
                }

                echo json_encode($response);

            } catch (PDOException $e) {
                echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
            }
            break;

        case 'get_active_comments':
            try {
                // Get active comments
                $stmt = $pdo->prepare("SELECT id,text,active,blocked,created_at,User_id,Blogs_id FROM blogcomments WHERE active = 1");
                $stmt->execute();
                $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $response = array();

                foreach ($comments as $comment) {
                    $response[] = array(
                        'id' => $comment['id'],
                        'text' => $comment['text'],
                        'active' => $comment['active'],
                        'blocked' => $comment['blocked'],
                        'created_at' => $comment['created_at'],
                        'User_id' => $comment['User_id'],
                        'Blogs_id' => $comment['Blogs_id'],
                    );
                }

                echo json_encode($response);

            } catch (PDOException $e) {
                echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
            }
            break;

        case 'get_not_active_not_blocked_comments':
            try {
                $stmt = $pdo->prepare("SELECT id,text,active,blocked,created_at,User_id,Blogs_id FROM blogcomments WHERE active = 0 AND blocked = 0");
                $stmt->execute();
                $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $response = array();

                foreach ($comments as $comment) {
                    $response[] = array(
                        'id' => $comment['id'],
                        'text' => $comment['text'],
                        'active' => $comment['active'],
                        'blocked' => $comment['blocked'],
                        'created_at' => $comment['created_at'],
                        'User_id' => $comment['User_id'],
                        'Blogs_id' => $comment['Blogs_id'],
                    );
                }

                echo json_encode($response);

            } catch (PDOException $e) {
                echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
            }
            break;

        case 'approve_comment':
            try {

                //aprove comment

                if (!isset($_SESSION['user_id']) || !isset($_SESSION['auth']) || $_SESSION['auth'] != 0) {
                    echo json_encode(array('error' => 'Na toto nemá uživatel práva'));
                    exit();
                }

                $post_data = json_decode(file_get_contents('php://input'), true);
                $commentId = $post_data['comment_id'];

                $stmt = $pdo->prepare("UPDATE blogcomments SET active = 1 WHERE id = :commentId");
                $stmt->bindParam(':commentId', $commentId, PDO::PARAM_INT);
                $stmt->execute();

                echo json_encode(array('message' => 'Comment approved successfully'));

            } catch (PDOException $e) {
                echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
            }
            break;

        case 'block_comment':
            try {
                //block comment
                if (!isset($_SESSION['user_id']) || !isset($_SESSION['auth']) || $_SESSION['auth'] != 0) {
                    echo json_encode(array('error' => 'Na toto nemá uživatel práva'));
                    exit();
                }

                $post_data = json_decode(file_get_contents('php://input'), true);
                $commentId = $post_data['comment_id'];

                $stmt = $pdo->prepare("SELECT blocked FROM blogcomments WHERE id = :commentId");
                $stmt->bindParam(':commentId', $commentId, PDO::PARAM_INT);
                $stmt->execute();
                $blocked = $stmt->fetch(PDO::FETCH_ASSOC);
                //negging the value for blocking/unblocking
                $blocked = !$blocked['blocked'];

                $stmt = $pdo->prepare("UPDATE blogcomments SET blocked = :blocked WHERE id = :commentId");
                $stmt->bindParam(':commentId', $commentId, PDO::PARAM_INT);
                $stmt->bindParam(':blocked', $blocked);
                $stmt->execute();

                echo json_encode(array('message' => 'Comment blocked/unblocked successfully'));

            } catch (PDOException $e) {
                echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
            }
            break;

        case 'remove_comment':
            try {
                //remove comment
                if (!isset($_SESSION['user_id']) || !isset($_SESSION['auth']) || $_SESSION['auth'] > 2) {
                    echo json_encode(array('error' => 'Na toto nemá uživatel práva'));
                    exit();
                }

                if($_SESSION['auth'] === 0){
                    //if admin, can remove any comment
                    $post_data = json_decode(file_get_contents('php://input'), true);
                    $commentId = $post_data['comment_id'];
    
                    $stmt = $pdo->prepare("DELETE FROM blogcomments WHERE id = :commentId");
                    $stmt->bindParam(':commentId', $commentId, PDO::PARAM_INT);
                    $stmt->execute();
    
                    echo json_encode(array('message' => 'Comment removed successfully'));
                    exit();
                }else{
                    //user, can remove only own comment
                    $post_data = json_decode(file_get_contents('php://input'), true);
                    $commentId = $post_data['comment_id'];

                    $stmt = $pdo->prepare("SELECT User_id FROM blogcomments WHERE id = :commentId");
                    $stmt->bindParam(':commentId', $commentId, PDO::PARAM_INT);
                    $stmt->execute();
                    $userId = $stmt->fetch(PDO::FETCH_ASSOC)['User_id'];
                    //validate the same user
                    if($userId === $_SESSION['user_id']){
                        $stmt = $pdo->prepare("DELETE FROM blogcomments WHERE id = :commentId");
                        $stmt->bindParam(':commentId', $commentId, PDO::PARAM_INT);
                        $stmt->execute();

                        
                        echo json_encode(array('message' => 'Comment removed successfully'));
                        exit();
                    }else{
                        echo json_encode(array('error' => 'Na toto nemá uživatel práva'));
                        exit();
                    }
                    

                exit();
                }

            } catch (PDOException $e) {
                echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
                exit();
            }
            break;

            case 'add_comment':
                try {
                    //adding comment
                    if (!isset($_SESSION['user_id']) || !isset($_SESSION['auth']) || $_SESSION['auth'] > 2) {
                        echo json_encode(array('error' => 'Na toto nemá uživatel práva' ));
                        exit();
                    }
            
                    $post_data = json_decode(file_get_contents('php://input'), true);
            
                    $text = filter_var($post_data['text'], FILTER_SANITIZE_STRING);
                    $userId = $_SESSION['user_id'];
                    $blogsId = $post_data['Blogs_id'];

                    $name = "validate_comments";

                    $stmt = $pdo->prepare("SELECT active FROM settings WHERE name = :name");
                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->execute();
                    $settings = $stmt->fetch(PDO::FETCH_ASSOC);

                    //if settings are set, filter the text of the comment
                    if($settings['active'] == "1"){
                        
                        $phrases = $pdo->query("SELECT phrase FROM validations")->fetchAll(PDO::FETCH_COLUMN);
                
                        $text = str_replace($phrases, '******', $text);
                    }

                    
                    $name = "aprove_comments";

                    $stmt = $pdo->prepare("SELECT active FROM settings WHERE name = :name");
                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->execute();
                    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
                    //by settings adjust the comment activity
                    if($settings['active'] == "1"){
                        
                        $createdAt = date('d-m-y H:i:s');
            
                        $stmt = $pdo->prepare("INSERT INTO blogcomments (text, active, blocked, created_at, updated_at, User_id, Blogs_id) 
                                            VALUES (:text, 0, 0, :createdAt, :updatedAt, :userId, :blogsId)");
                        $stmt->bindParam(':text', $text, PDO::PARAM_STR);
                        $stmt->bindParam(':updatedAt', $createdAt, PDO::PARAM_STR);
                        $stmt->bindParam(':createdAt', $createdAt, PDO::PARAM_STR);
                        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                        $stmt->bindParam(':blogsId', $blogsId, PDO::PARAM_INT);
                        $stmt->execute();

                        $_SESSION['message'] = "Komentář bude vidět až ho schválí administrátor";
                        exit();
                    }


                    $createdAt = date('d-m-y H:i:s');
            
                    $stmt = $pdo->prepare("INSERT INTO blogcomments (text, active, blocked, created_at, updated_at, User_id, Blogs_id) 
                                          VALUES (:text, 1, 0, :createdAt, :updatedAt, :userId, :blogsId)");
                    $stmt->bindParam(':text', $text, PDO::PARAM_STR);
                    $stmt->bindParam(':updatedAt', $createdAt, PDO::PARAM_STR);
                    $stmt->bindParam(':createdAt', $createdAt, PDO::PARAM_STR);
                    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                    $stmt->bindParam(':blogsId', $blogsId, PDO::PARAM_INT);
                    $stmt->execute();

                    
                    
            
                    echo json_encode(array('message' => 'Komentář úspěšně přidán'));
                    exit();
            
                } catch (PDOException $e) {
                    echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
                    exit();
                }
                break;
        case 'add_like':
            try {
                //add like
                if (!isset($_SESSION['user_id'])) {
                    echo json_encode(array('error' => 'Uživatel není přihlášen'));
                    exit();
                }
                $post_data = json_decode(file_get_contents('php://input'), true);
            
                $userId = $_SESSION['user_id'];
                $blogsId = $post_data['Blogs_id'];
        
                if (hasUserLikedBlog($blogsId, $userId)) {
                    echo json_encode(array('error' => 'Uživatel již lajkoval tento blog'));
                    exit();
                }
        
                $stmt = $pdo->prepare("INSERT INTO bloglikes (User_id, Blogs_id) VALUES (:userId, :blogsId)");
                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                $stmt->bindParam(':blogsId', $blogsId, PDO::PARAM_INT);
                $stmt->execute();
        
                echo json_encode(array('message' => 'Líbí se úspěšně přidáno'));
                exit();
            } catch (PDOException $e) {
                echo json_encode(array('error' => 'Chyba v databázi: ' . $e->getMessage()));
                exit();
            }
            break;
            
        default:
            echo json_encode(array('error' => 'Invalid action'));
            break;
    }
}
?>
