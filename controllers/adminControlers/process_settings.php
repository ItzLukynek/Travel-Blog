<?php
session_start();

require_once '../db/pdo.php';

include '../../controllers/logic/message_controller.php';

//method for correct showing of text
function n12br($string)
{
    return str_replace(["\r\n", "\r", "\n"], '<br>', $string);
}

if (isset($_GET['action'])) {
    $pdo = getPDO();

    switch ($_GET['action']) {
        case 'set_setting':
            try {
                //set the setting
                if (!isset($_SESSION['user_id']) || !isset($_SESSION['auth']) || $_SESSION['auth'] != 0) {
                    echo json_encode(array('error' => 'Na toto nemá uživatel práva'));
                    exit();
                }

                $post_data = json_decode(file_get_contents('php://input'), true);
                $name = $post_data['name'];
                $value = $post_data['value'];

                $stmt = $pdo->prepare("UPDATE settings SET active = :value  WHERE name = :name");
                $stmt->bindParam(':value', $value, PDO::PARAM_INT);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->execute();

                echo json_encode(array('message' => 'Uspěšně nastaveno'));

            } catch (PDOException $e) {
                echo json_encode(array('error' => 'Nepovedlo se nastavit: ' . $e->getMessage()));
            }
            break;
            case 'update_validations':
                //add or delete another phrases for filter
                try {
                    if (!isset($_SESSION['user_id']) || !isset($_SESSION['auth']) || $_SESSION['auth'] != 0) {
                        echo json_encode(array('error' => 'Na toto nemá uživatel práva'));
                        exit();
                    }
            
                    $post_data = json_decode(file_get_contents('php://input'), true);
            
                    if (isset($post_data['phrases']) && is_array($post_data['phrases'])) {
                        $newPhrases = $post_data['phrases'];
            
                        $existingPhrases = $pdo->query("SELECT phrase FROM validations")->fetchAll(PDO::FETCH_COLUMN);
            
                        $missingPhrases = array_diff($newPhrases, $existingPhrases);
            
                        $phrasesToDelete = array_diff($existingPhrases, $newPhrases);
                        
                        //deleting phrases
                        if (!empty($phrasesToDelete)) {
                            $placeholders = implode(',', array_fill(0, count($phrasesToDelete), '?'));
                            $stmtDelete = $pdo->prepare("DELETE FROM validations WHERE phrase IN ($placeholders)");
            
                            $stmtDelete->execute(array_values($phrasesToDelete));

                            
                            echo json_encode(['message' => 'Slova úspěšně odebrána']);
                        }
                        //adding phrases
                        if (!empty($missingPhrases)) {
                            $stmtAdd = $pdo->prepare("INSERT INTO validations (phrase) VALUES (:phrase)");
            
                            foreach ($missingPhrases as $phrase) {
                                $stmtAdd->bindValue(':phrase', $phrase, PDO::PARAM_STR);
                                $stmtAdd->execute();
                            }
            
                            echo json_encode(['message' => 'Slova úspěšně přidána']);
                        }
                        
                        
                        if(empty($phrasesToDelete) && empty($missingPhrases)) {
                            echo json_encode(['message' => 'Nenalezena žádná nová slova']);
                        } 
                    } else {
                        echo json_encode(['error' => 'Invalid data']);
                    }
                } catch (PDOException $e) {
                    echo json_encode(['error' => 'Nepodařilo se aktualizovat slova: ' . $e->getMessage()]);
                }
                break;
            
        
        default:
            echo json_encode(array('error' => 'Invalid action'));
            break;
    }
}
?>
