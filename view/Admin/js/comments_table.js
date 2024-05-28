document.addEventListener("DOMContentLoaded", async function () {
    //methods to call at the start of the page
    get_comments_html("get_not_active_not_blocked_comments"); 
     search_bar();
     filter_items();
     document.querySelector(".add_blog").addEventListener("click",()=>{
        get_comments_html("get_not_active_not_blocked_comments");
     })
});

//for create a html for message
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

//filter comments by filter
function filter_items() {
    let select = document.getElementById("filterSelect");

    select.addEventListener("input", function () {
        get_comments_html( select.value);
    });
}

//show only comment containing something
function search_bar() {
    const commentSearchInput = document.getElementById("blogSearch");
    const commentListContainer = document.getElementById("blogList");

    commentSearchInput.addEventListener("input", function () {
        const searchValue = commentSearchInput.value.toLowerCase();
        const comments = document.querySelectorAll(".manage_blog");
        let hasResults = false;
        //get the text from the comment and compare it with the search value
        comments.forEach((comment) => {
            const commentFullName = comment.querySelector(".col-lg-3").textContent.toLowerCase();
            const commentText = comment.querySelector(".col-lg-6").textContent.toLowerCase();

            if (commentFullName.includes(searchValue) || commentText.includes(searchValue)) {
                comment.style.display = "flex";
                hasResults = true;
            } else {
                comment.style.display = "none";
            }
        });

        const messageElement = document.createElement("div");
        messageElement.classList.add("text-center", "w-100", "no_message", "mt-3");
        messageElement.innerHTML = hasResults ? "" : "<h1>Nenalezeny žádné výsledky</h1>";

        const existingMessage = commentListContainer.querySelector(".no_message");
        if (existingMessage) {
            commentListContainer.removeChild(existingMessage);
        }

        commentListContainer.appendChild(messageElement);
    });
}

let bugisek = 1
//show comment list
async function get_comments_html( filter) {
    try {
        const data = {
            request: true,
        };
        let fetchurl = "../../controllers/adminControlers/process_comments.php?action=" + filter;
        const response = await fetch(fetchurl, {
            method: "POST",
            body: JSON.stringify(data),
            headers: {
                "Content-Type": "application/json",
            },
        });

        let items = [0];
        let items_HTML = "";
        if (response.ok) {
            items = await response.json();
            
            
           
            const usersfetch = await fetch("../../controllers/adminControlers/process_users.php?action=get_users", {
                method: "POST",
                body: JSON.stringify(data),
                headers: {
                    "Content-Type": "application/json",
                },
            });
            let users = await usersfetch.json();

            items.forEach((item) => {

                let disabled = "no-visibility";
                if(item.blocked == 0){
                    disabled = "";
                }
                item.FirstName = users.find((user) => user.id == item.User_id).FirstName;
                item.LastName = users.find((user) => user.id == item.User_id).LastName;
                let adition = "";
                if(filter == "get_not_active_not_blocked_comments" && !item.active){
                    adition = '<button class="edit-icon mr-1" "><i class="fa-solid fa-check"></i></button>'
                }
                            items_HTML += `
                        <div class="manage_blog row  ">
                            <div  class="col col-lg-3 col-md-3 col-12 d-flex flex-row align-items-center ">
                                <p class="mr-2 ">${item.FirstName} </p>
                                <p>${item.LastName}</p>
                            </div>
                            <div class="col col-lg-6 col-md-6 col-12 d-flex flex-row align-items-center ">
                                <p class="mr-2 ">${item.text} </p>
                            </div>
                            
                            <div class="col col-lg-3 col-md-3 col-12 mt-md-0 mt-2 text-center">
                                <div class="table-icons d-flex flex-row justify-content-center">
                                    
                                    <button class="delete-icon mr-1""><i class="fas fa-trash"></i></button>
                                    <button class="photo-icon mr-1 ${disabled}" "><i class="fa-solid fa-ban"></i></button>
                                    ${adition}
                                </div>
                            </div>
                        </div>
                    `;
                
            });
            //add functions on the buttons
            const manageItems = document.querySelector(".manage_blogs");
            manageItems.innerHTML = items_HTML;

            const deletes = document.querySelectorAll(".delete-icon");

            deletes.forEach((button,index) =>{
                button.addEventListener("click",()=>{
                    const id = items[index].id; 
                    delete_comment(id); 
                })
            })
            
            const blocked = document.querySelectorAll(".photo-icon");

            blocked.forEach((button,index) =>{
                button.addEventListener("click",()=>{
                    const id = items[index].id; 
                    block_comment(id); 
                })
            })
            const aprove = document.querySelectorAll(".edit-icon");

            aprove.forEach((button,index) =>{
                button.addEventListener("click",()=>{
                    const id = items[index].id; 
                    approve_comment(id); 
                })
            })

            //for comment count
            
            if(bugisek){
                document.querySelector("#comment_count").innerHTML = document.querySelector("#comment_count").innerHTML + " <b>" + items.length
                bugisek = 0
            }

        } else {
            items_HTML = `<div class="text-center w-100"><h1>Data nenalezena</h1></div>`;
        }
    } catch (error) {
        console.log("Error: " + error);
        items_HTML = `<div class="text-center w-100"><h1>Data nenalezena</h1></div>`;
    }
}
//delete comment
async function delete_comment(Id) {
    try {
                //affirmation
            let request_html=`
            <div class="message_box">
            <div class="d-flex flex-row justify-content-between">
                    <i onclick="closeModal()" id="modalClose" class="modal-close fa-solid fa-xmark"></i>
                    <div class="ml-2 error-title d-flex flex-row">
                    <h2>Potvrzení<i class="ml-2 warning_icon fa-solid fa-circle-info"></i></h2>
                    </div>
                    </div>
                    <div class="container">
                    <h4 id="modal_message" class="">
                    Opravdu chcete vymazat komentář?
                    </h4>
            </div>
            <div class="d-flex justify-content-end mt-3 ">
            <button type="button" id="delete_affirmation" class="mr-3 w-25 btn btn-danger bg-danger">Odstranit</button>
                    
            </div>
            </div>
            `
            showModal(request_html)

            document.querySelector("#delete_affirmation").addEventListener("click",async()=>{
            const data = {
            comment_id:Id
            };
            const response = await fetch("../../controllers/adminControlers/process_comments.php?action=remove_comment", {
                method: "POST",
                body: JSON.stringify(data),
                headers: {
                "Content-Type": "application/json",
            },
            });
            responses = await response.json()
            if (responses.message) {
                console.log(responses.message);
                closeModal()
                location.reload()
            } else {
                console.error("Error deleting comment:", responses.error);
            }
        })
    } catch (error) {
        console.error('Error:', error);
    }
}
//block comment
async function block_comment(Id) {
    
    try {
        const response = await fetch('../../controllers/adminControlers/process_comments.php?action=block_comment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                comment_id: Id,
            }),
        });
        const data = await response.json();
        if (data.message) {
            console.log(data.message);
            location.reload()
        } else {
            console.error('Error blocking/unblocking comment:', data.error);
            let string = "Nepodařilo se změnit stav komentáře" + data.error;
            let text = returnHTML(string)
            showModal(text);
        }
        
    } catch (error) {
        console.error('Error:', error);
    }
}

async function approve_comment(Id) {
    //approve comment
    try {
        const response = await fetch('../../controllers/adminControlers/process_comments.php?action=approve_comment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                comment_id: Id,
            }),
        });
        const data = await response.json();
        if (data.message) {
            console.log(data.message);
            location.reload()
        } else {
            console.error('Error blocking/unblocking comment:', data.error);
            let string = "Nepodařilo se změnit stav komentáře" + data.error;
            let text = returnHTML(string)
            showModal(text);
        }
        
    } catch (error) {
        console.error('Error:', error);
    }
}