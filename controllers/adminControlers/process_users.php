<?php
session_start();
require_once '../db/pdo.php';

$pdo = getPDO();

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'get_users':
            //get all the users
            try {
                $stmt = $pdo->prepare("SELECT u.id, u.FirstName, u.LastName, u.SecurityLevel_id, u.blocked, u.Email, u.verified, COUNT(DISTINCT bl.id) AS likeCount, COUNT(DISTINCT bc.id) AS commentCount FROM user u LEFT JOIN bloglikes bl ON u.id = bl.user_id LEFT JOIN blogcomments bc ON u.id = bc.User_id GROUP BY u.id, u.FirstName, u.LastName, u.SecurityLevel_id, u.blocked, u.Email, u.verified");
            
                $stmt->execute();
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
                if ($users) {
                    echo json_encode($users);
                } else {
                    echo json_encode(['error' => 'Nepodařilo se získat uživatele ']);
                    exit();
                }
            } catch (PDOException $e) {
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
            }
            
            break;

            case 'get_user':
                try {
                    //get only one user
                    $post_data = json_decode(file_get_contents('php://input'), true);
                    $userId = $post_data['userId'];

                    $stmt = $pdo->prepare("SELECT id, FirstName, LastName, SecurityLevel_id, blocked, Email, verified,updated_at FROM user WHERE id = :userId");
                
                    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                    $stmt->execute();
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                    if ($user) {
                        echo json_encode($user);
                    } else {
                        echo json_encode(['error' => 'Nepodařilo se získat uživatele ']);
                        exit();
                    }
                } catch (PDOException $e) {
                    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
                }
                
                break;

        case 'update_user':
            try {

                //update user

                if (!isset($_SESSION['user_id']) || !isset($_SESSION['auth']) || $_SESSION['auth'] != 0) {
                    echo json_encode(array('error' => 'Na toto nemá uživatel práva'));
                    exit();
                }

                $post_data = json_decode(file_get_contents('php://input'), true);

                $userId = $post_data['id'];
                $firstName = $post_data['FirstName'];
                $lastName = $post_data['LastName'];
                $Email = $post_data['Email'];
                $securityLevelId = $post_data['SecurityLevel_id'];

                $stmt = $pdo->prepare("UPDATE user SET FirstName = :firstName, LastName = :lastName,Email = :Email, SecurityLevel_id = :securityLevelId WHERE id = :userId");
                $stmt->bindParam(':firstName', $firstName, PDO::PARAM_STR);
                $stmt->bindParam(':lastName', $lastName, PDO::PARAM_STR);
                $stmt->bindParam(':Email', $Email, PDO::PARAM_STR);
                $stmt->bindParam(':securityLevelId', $securityLevelId, PDO::PARAM_INT);
                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                $stmt->execute();

                echo json_encode(['message' => 'Uživatel aktualizován úspěšně']);
            } catch (PDOException $e) {
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
            }
            break;

        case 'block_user':
            try {
                //block user
                if (!isset($_SESSION['user_id']) || !isset($_SESSION['auth']) || $_SESSION['auth'] != 0) {
                    echo json_encode(array('error' => 'Na toto nemá uživatel práva'));
                    exit();
                }
                $post_data = json_decode(file_get_contents('php://input'), true);

                $userId = $post_data['userId'];

                $stmt = $pdo->prepare("SELECT blocked FROM user WHERE id = :userId");
                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                $stmt->execute();
                $blocked = $stmt->fetch(PDO::FETCH_ASSOC);
                //negging the value for block/unblock
                $blocked = !$blocked['blocked'];
                $stmt = $pdo->prepare("UPDATE user SET blocked = :blocked WHERE id = :userId");
                $stmt->bindParam(':blocked', $blocked, PDO::PARAM_INT);
                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                $stmt->execute();

                echo json_encode(['message' => "Uživateluv stav změněn"]);
            } catch (PDOException $e) {
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
            }
            break;

        case 'delete_user':
            try {
                //delete user
                if (!isset($_SESSION['user_id']) || !isset($_SESSION['auth']) || $_SESSION['auth'] != 0) {
                    echo json_encode(array('error' => 'Na toto nemá uživatel práva'));
                    exit();
                }
                $post_data = json_decode(file_get_contents('php://input'), true);

                $userId = $post_data['userId'];
                //delete likes,comments and everything the user could have created
                    $stmtLikes = $pdo->prepare("DELETE FROM bloglikes WHERE user_id = :userId");
                    $stmtLikes->bindParam(':userId', $userId, PDO::PARAM_INT);
                    $stmtLikes->execute();

                    $stmtComments = $pdo->prepare("DELETE FROM blogcomments WHERE User_id = :userId");
                    $stmtComments->bindParam(':userId', $userId, PDO::PARAM_INT);
                    $stmtComments->execute();

                    $stmtBlogs = $pdo->prepare("DELETE FROM blogs WHERE User_id = :userId");
                    $stmtBlogs->bindParam(':userId', $userId, PDO::PARAM_INT);
                    $stmtBlogs->execute();

                    $stmtUser = $pdo->prepare("DELETE FROM user WHERE id = :userId");
                    $stmtUser->bindParam(':userId', $userId, PDO::PARAM_INT);
                    $stmtUser->execute();

                echo json_encode(['message' => 'Uživatel smazán úspěšně']);
            } catch (PDOException $e) {
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
            }
            break;

        case 'approve_user':
            try {
                //approve user 
                if (!isset($_SESSION['user_id']) || !isset($_SESSION['auth']) || $_SESSION['auth'] != 0) {
                    echo json_encode(array('error' => 'Na toto nemá uživatel práva'));
                    exit();
                }
                $post_data = json_decode(file_get_contents('php://input'), true);

                $userId = $post_data['userId'];

                $stmt = $pdo->prepare("UPDATE user SET verified = 1 WHERE id = :userId");
                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                $stmt->execute();

                echo json_encode(['message' => 'Uživatel schválen úspěšně']);
            } catch (PDOException $e) {
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
            }
            break;

        default:
            echo 'Invalid action';
            break;
    }
}
?>
