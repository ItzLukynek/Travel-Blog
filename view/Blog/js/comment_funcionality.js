document.addEventListener("DOMContentLoaded", function () {
whenload();
delete_comments();
likes();
});




function returnHTML(text){
    let html = '<div class="message_box">\
                        <div class="d-flex flex-row justify-content-between">\
                                <i onclick="closeModal()" id="modalClose" class="modal-close fa-solid fa-xmark"></i>\
                                <div class="ml-2 error-title d-flex flex-row">\
                                <h2>Oznámení<i class="ml-2 warning_icon fa-solid fa-circle-info"></i></h2>\
                                </div>\
                                </div>\
                                <div class="container">\
                                <h4 id="modal_message" class="">'+text+'</h4></div></div>';
    return html;
}

async function delete_comments(){
    document.querySelectorAll('.delete-comment').forEach(function (deleteIcon) {
        deleteIcon.addEventListener('click', function () {
            var commentId = this.getAttribute('data-comment-id');


            let request_html=`
                <div class="message_box">
                <div class="d-flex flex-row justify-content-between">
                        <i onclick="closeModal()" id="modalClose" class="modal-close fa-solid fa-xmark"></i>
                        <div class="ml-2 error-title d-flex flex-row">
                        <h2>Potvrzení<i class="ml-2 warning_icon fa-solid fa-circle-info"></i></h2>
                        </div>
                        </div>
                        <div class="container">
                        <h4 id="modal_message" class="text-center">
                        Opravdu chcete odstranit tento komentář?
                        </h4>
                </div>
                <div class="d-flex justify-content-center mt-3 ">
                <button type="button" id="delete_affirmation" class="mr-3 w-25 btn btn-danger bg-danger">Odstranit</button>
                        
                </div>
                </div>
                `;
                showModal(request_html);
                document.getElementById('delete_affirmation').addEventListener('click', function () {

                        fetch('../../controllers/adminControlers/process_comments.php?action=remove_comment', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                comment_id: commentId,
                            }),
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.message) {
                                    console.log('Comment deleted successfully' + data.message);
                                } else if(data.error){
                                    console.error('Error deleting comment:', data.error);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });

                            closeModal();
                            location.reload();

                    })
        });
    });
}

async function whenload(){
    try {
        
    const addCommentButton = document.querySelector('.add-comment-button');
    addCommentButton.addEventListener('click', async function () {
        const commentText = document.getElementById('comment').value;

        if (commentText.trim() !== "") {
            try {

                const URLobject = new URL(window.location.href)

                let blog_id = URLobject.searchParams.get("blog_id")
                if(blog_id){
                    
                    const response = await addComment(commentText,  blog_id);

                    console.log(response);
                    if (response.error) {
                        
                        showModal(returnHTML(response.error));
                    } else {
                        location.reload();
                    }
                }else{
                    console.log('Blog ID is not set');
                    showModal(returnHTML("Nepodařilo se najít blog id"));
                }
            } catch (error) {
                showModal(returnHTML("Nepodařilo se přidat komentář" + error));
            }
        } else {
            showModal(returnHTML("Vyplňte prosím text komentáře"));
        }
    });

    
    const cancelCommentButton = document.querySelector('.cancel-comment-button');
    cancelCommentButton.addEventListener('click', function () {
        document.getElementById('comment').value = ''; 
    });
    } catch (error) {
        console.log("Komentáře jsou zakázané");
    }
}

async function addComment(text,  blogsId) {
    let response;
    try {
        response = await fetch('../../controllers/adminControlers/process_comments.php?action=add_comment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                text: text,
                Blogs_id: blogsId,
            }),
        });
    } catch (error) {
        console.log("Fetching error comment")
    }

    return await response.text();
}


async function likes() {
    try {
        const likeButton = document.getElementById('likeButton');
        if (likeButton) {
            likeButton.addEventListener('click', async function () {
                const URLobject = new URL(window.location.href)

                let blog_id = URLobject.searchParams.get("blog_id")
                if (blog_id) {
                    try {
                        const response = await addLike(blog_id);
                        console.log(response);
                        if (response.error) {
                            showModal(returnHTML(response.error));
                        } else {
                            location.reload();
                        }
                    } catch (error) {
                        showModal(returnHTML("Nepodařilo se přidat like " + error));
                    }
                } else {
                    showModal(returnHTML("Chybí data pro přidání like"));
                }
            });
        }
    } catch (error) {
        console.log("Likes are disabled");
    }
}

async function addLike(blogId) {
    let response;
    try {
        response = await fetch('../../controllers/adminControlers/process_comments.php?action=add_like', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                Blogs_id: blogId,
            }),
        });
    } catch (error) {
        console.log("Fetching error like")
    }

    return await response.json();
}
