<?php

//call method to get pdo
function getPDO(){
  //local connection
  if($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1'){
    $servername = "localhost"; 
    $username = "root";  
    $password = ""; 
    $dbname = "hornets"; 
    try {
      $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $pdo;
    } catch(PDOException $e) {
      return null;
    }
  }else{
    //mp connection - deleted info for safety on git
    $servername = ""; 
    $username = "";  
    $password = ""; 
    $dbname = ""; 
    try {
      $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $pdo;
    } catch(PDOException $e) {
      return null;
    }
  }
  
}
?>