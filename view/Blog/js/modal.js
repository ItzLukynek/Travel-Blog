function showModal(htmlContent) {
    const modalOverlay = document.querySelector('.HTML_Modal_Overlay');
    const modalContent = document.getElementById('show_HTML_Modal');

    modalContent.innerHTML = htmlContent;
    modalOverlay.style.display = 'flex';

    

}



function closeModal() {
    const modalOverlay = document.querySelector('.HTML_Modal_Overlay');

    modalOverlay.style.display = 'none';
    document.getElementById('show_HTML_Modal').innerHTML = '';
}


document.addEventListener("DOMContentLoaded", function() {
    const modalContent = document.getElementById('show_HTML_Modal');
    const modalOverlay = document.querySelector('.HTML_Modal_Overlay');


    if(modalContent.innerHTML.trim() !== ""){
        modalOverlay.style.display = 'flex';
    }


    modalOverlay.addEventListener('click', function (e) {
        if (e.target === modalOverlay) {
            closeModal();
        }
    });
});
