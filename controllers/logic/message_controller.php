<?php
if(isset($_SESSION['message'])){
    $modal_HTML = "";
    $modal_Message = $_SESSION['message'];
    $modal_Title = "";
    //based on type icon
    if(isset($_SESSION['type'])){
        $message_type =$_SESSION['type'];

        if($message_type === "info"){
            $modal_Title = '<h2>Info<i class="ml-2 warning_icon fa-solid fa-circle-info"></i></h2>';  
        }else if($message_type === "message"){
            $modal_Title = '<h2>Zpráva<i class="ml-2 warning_icon fa-solid fa-circle-info"></i></h2>';
        }
        
    }else{
        $modal_Title = '<h2>Vyskytli se potíže<i class="ml-2 warning_icon fa-solid fa-circle-exclamation"></i></h2>';            

    }
    //the html for the message box
    $modal_HTML = '<div class="message_box">
    <div class="d-flex flex-row justify-content-between">
            <i onclick="closeModal()" id="modalClose" class="modal-close fa-solid fa-xmark"></i>
            <div class="ml-2 error-title d-flex flex-row">
            '. $modal_Title .'
            </div>
            </div>
            <div class="container">
            <h4 id="modal_message" class="">
            '. $modal_Message .'
            </h4>
            </div>
    </div>';
    unset($_SESSION['message']);

}

?>