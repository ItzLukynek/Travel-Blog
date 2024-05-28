document.addEventListener("DOMContentLoaded", async function () {
    get_blogs_html("new"); //load the table first time
    search_bar()
    filter_blogs()
});

//filter blogs based on filter
function filter_blogs(){
    let select = document.getElementById("filterSelect")

    select.addEventListener("input",function(){
        get_blogs_html(select.value);
    })
}
//search blogs
function search_bar(){
    const blogSearchInput = document.getElementById("blogSearch");
    const blogListContainer = document.getElementById("blogList");
    
    blogSearchInput.addEventListener("input", function () {
        const searchValue = blogSearchInput.value.toLowerCase();
        const blogs = document.querySelectorAll(".manage_blog");
        let hasResults = false;

        blogs.forEach((blog) => {
            const blogTitle = blog.querySelector("p").textContent.toLowerCase();
            
            if (blogTitle.includes(searchValue)) {
                blog.style.display = "flex";
                hasResults = true;
            } else {
                blog.style.display = "none";
            }
        });

        const messageElement = document.createElement("div");
        messageElement.classList.add("text-center", "w-100","no_message","mt-3");
        messageElement.innerHTML = hasResults ? "" : "<h1>Nenalezeny žádné výsledky</h1>";

        const existingMessage = blogListContainer.querySelector(".no_message");
        if (existingMessage) {
            blogListContainer.removeChild(existingMessage);
        }

        blogListContainer.appendChild(messageElement);
    });
    
}
function br2n(inputString) {
    return inputString.replace(/<br\s*\/?>/gm, '\n');
}
//get blogs html
async function get_blogs_html(filter){
    try {
        const data = {
            filter: filter,
            request: true,
        };

        const response = await fetch("../../controllers/adminControlers/process_blogs.php?action=get_blogs", {
            method: "POST",
            body: JSON.stringify(data),
            headers: {
                "Content-Type": "application/json",
            },
        });


        let blogs = [0];
        let blogs_HTML = "";
        if (response.ok) {
         blogs = await response.json();
            
            blogs.forEach((blog) => {
                let disabled = "no-visibility";
                if(blog.active){
                    disabled = "";
                }
                blogs_HTML += `
                    <div class="manage_blog row">
                        <div class="col col-lg-6 col-md-6 col-12"><p>${blog.desc}</p></div>
                        <div class="col col-lg-1 col-md-1 col-6 text-center justify-content-end justify-content-md-center d-flex flex-row align-items-center"><i class="mr-1 fa-solid fa-thumbs-up"></i>${blog.like_count}</div>
                        <div class="col col-lg-1 col-md-1 col-6 text-center d-flex flex-row align-items-center"><i class="mr-1 fa-regular fa-comment-dots"></i>${blog.comment_count}</div>
                        <div class="col col-lg-4 col-md-4 col-12 mt-md-0 mt-2 text-center">
                            <div class="table-icons d-flex flex-row justify-content-center ">
                                <button class="edit-icon mr-1"><i class="fas fa-edit"></i></button>
                                <button class="photo-icon mr-1"><i class="fas fa-images"></i></button>
                                <button class="delete-icon mr-1"><i class="fas fa-trash"></i></button>
                                <button  class="visibility-icon mr-1 ${disabled}"><i class="fas fa-eye"></i></button>
                                <button class="info-icon"><i class="fas fa-info"></i></button>
                               
                            </div>
                        </div>
                    </div>
                `;
            });

            const manageBlogs = document.querySelector(".manage_blogs");
            manageBlogs.innerHTML = blogs_HTML;

            
            const deletes = document.querySelectorAll(".info-icon");

            deletes.forEach((button,index) =>{
                button.addEventListener("click",()=>{
                    const id = blogs[index].id; 
                    show_blog_info(id); 
                })
            })

            const infos = document.querySelectorAll(".delete-icon");

            infos.forEach((button,index) =>{
                button.addEventListener("click",()=>{
                    const id = blogs[index].id; 
                    remove_blog(id); 
                })
            })

            const visibilityes = document.querySelectorAll(".visibility-icon");

            visibilityes.forEach((button,index) =>{
                button.addEventListener("click",()=>{
                    const id = blogs[index].id; 
                    blog_visibility(id); 
                })
            })

            const edits = document.querySelectorAll(".edit-icon");

            edits.forEach((button,index) =>{
                button.addEventListener("click",()=>{
                    const id = blogs[index].id; 
                    edit_blog(id); 
                })
            })
            const photosg = document.querySelectorAll(".photo-icon");

            photosg.forEach((button,index) =>{
                button.addEventListener("click",()=>{
                    const id = blogs[index].id; 
                    edit_gallery(id); 
                })
            })
        } else {
            blogs_HTML = `<div class="text-center w-100"><h1>Data nenalezena</h1></div>`
        }
        
        
    } catch (error) {
        console.log("Error: " + error);
    }
}
//remove blog
    async function remove_blog(id) {
    try {
            
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
                Opravdu chcete odstranit blog?
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
        remove_id:id
        };
        console.log(id)
        const response = await fetch("../../controllers/adminControlers/process_blogs.php?action=remove_blog", {
        method: "POST",
        body: JSON.stringify(data),
        headers: {
        "Content-Type": "application/json",
        },
        });
        location.reload()
    })
    } catch (error) {
        console.log(error);
    }
    
}
//edit blog
async function edit_blog(id) {
    const data = {
        request:true,
        filter:"id",
        blog_id:id
    };

    const response = await fetch("../../controllers/adminControlers/process_blogs.php?action=get_blogs", {
        method: "POST",
        body: JSON.stringify(data),
        headers: {
            "Content-Type": "application/json",
        },
    });
    
    if (response.ok) {
        let blog_info = await response.json();

        let infocard = `
        <div class="ecard"  id="write_blog">
            <div class="ecard-body" style="height:auto;">
                <div class="row flex-column flex-lg-row d-flex" >
                        <div class="col-12 col-lg-9 d-flex flex-column mb-3 mb-lg-0">
                            <label for="blogTitle">Nadpis:</label>
                            <input  value="${blog_info.desc}" type="text" id="edit_title" name="blogTitle" class="" required>
                        </div>
                        <div class="col-12 col-lg-3 d-flex flex-column">
                            <label for="selectCountry">Vybrat zemi:</label>
                            <select id="edit_country" name="selectCountry" class="">
                                <option value="${blog_info.Destination_id}">${blog_info.DestinationName}</option>
                                ${document.querySelector('#selectCountry').innerHTML}
                            </select>
                        </div>
                    </div>
                    <div class="blog_images row mt-3">
                        <div class="form-group col-md-6 col-12 col">
                            <label for="titlePhotoInput">Nahrajte novou ůvodní fotku</label>
                            <input type="file" class="form-control-file" id="edit_titlePhotoInput" accept="image/*" name="titlePhoto" >
                        </div>
                        <div class="form-group col-md-6 col-12 col">
                            <label for="galleryPhotosInput">Přidejte fotky do galerie</label>
                            <input type="file" class="form-control-file " id="edit_galleryPhotosInput" accept="image/*" name="galleryPhotos[]" multiple >
                        </div>
                    </div>
                    <div style="margin:0px 15px;">
                    <label for="blogText">Text:</label>
                    <textarea id="edit_text" name="blogText" rows="5" class="form-control" style="resize: none; height: 100%;" required>${br2n(blog_info.text)}</textarea> 
                    <div class="d-flex justify-content-end">
                    <div class="d-flex flex-row w-50">
                    <button  id="close_it" class="w-50 mt-3 mr-2 btn btn-success">Zavřít</button>
                    <button id="save_blog" class="w-50 mt-3 btn btn-success">Uložit</button>
                    </div>
                    </div>
                    
                    </div>

            </div>
        </div>
        `

        showModal(infocard);

        document.querySelector("#close_it").addEventListener("click",()=>{
            closeModal()
        })

        document.querySelector("#save_blog").addEventListener("click", async () => {
            try {
                const blog_Title = document.querySelector("#edit_title").value;
                const blog_Country = document.querySelector("#edit_country").value;
                const blog_Text = document.querySelector("#edit_text").value;
                const id = blog_info.id;
        
                const formData = new FormData();
                formData.append("id", id);
                formData.append("blogTitle", blog_Title); 
                formData.append("selectCountry", blog_Country); 
                formData.append("blogText", blog_Text); 
                formData.append("active", blog_info.active); 
                const titlePhotoInput = document.querySelector("#edit_titlePhotoInput");
                if (titlePhotoInput.files.length > 0) {
                    formData.append("titlePhoto", titlePhotoInput.files[0]);
                }
        
                const galleryPhotosInput = document.querySelector("#edit_galleryPhotosInput");
                if (galleryPhotosInput.files.length > 0) {
                    for (let file of galleryPhotosInput.files) {
                        formData.append("galleryPhotos[]", file);
                    }
                }
        
                const response = await fetch("../../controllers/adminControlers/process_blogs.php?action=update_blog", {
                    method: "POST",
                    body: formData,
                });
                location.reload()
            } catch (error) {
                console.log(error);
            }
        });
               
    } else {
        location.reload()

    }
}

async function show_blog_info(id) {
        const data = {
            request:true,
            filter:"id",
            blog_id:id
        };

        const response = await fetch("../../controllers/adminControlers/process_blogs.php?action=get_blogs", {
            method: "POST",
            body: JSON.stringify(data),
            headers: {
                "Content-Type": "application/json",
            },
        });
        
        if (response.ok) {
            let blog_info = await response.json();
            
            const formattedDate = blog_info.created_at;
            const truncatedText = limitText(blog_info.text, 300);
    
            const infocard = `
                <div class="blog-info ecard">
                    <div class="ecard-body">
                        <h2>Informace o blogu</h2>
                        <p>Autor: ${blog_info.FirstName + " " + blog_info.LastName}</p>
                        <p>Vytvořeno: ${formattedDate}</p>
                        <p>Název: ${blog_info.desc}</p>
                        <p>Počet lajků: ${blog_info.like_count}</p>
                        <p>Počet komentářů: ${blog_info.comment_count}</p>
                        <p>ID: ${blog_info.id}</p>
                    </div>
                </div>
            `;
    
            showModal(infocard);
        } else {
            console.log("Nejde zobrazit data")

        }
}

async function blog_visibility(id){
    const data = {
        blog_id:id
    };

    const response = await fetch("../../controllers/adminControlers/process_blogs.php?action=set_visibility_blog", {
        method: "POST",
        body: JSON.stringify(data),
        headers: {
            "Content-Type": "application/json",
        },
    });
    location.reload()
}

async function edit_gallery(id){

    
    const blog_id = {
        blog_id:id
    };

    const images = await fetch("../../controllers/adminControlers/process_blogs.php?action=get_img_urls", {
    method: "POST",
    body: JSON.stringify(blog_id),
    headers: {
        "Content-Type": "application/json",
    },
    });

    let blog_images = await images.json()
    let image_html = "";

    if(blog_images[0] !== null){
        image_html += `<div class="ecard"><div class="ecard-body"><div class="image-container d-flex justify-content-center">`
        image_html += `
                <div class="image_card ">
                <label for="image">Titulní fotka</label>
                <div class="image ">
                <img data-enlargable src="${blog_images[0]}" alt="Image">
                </div></div>`
        if(blog_images.length > 1){
            for (let index = 1; index < blog_images.length; index++) {
                image_html += `
                <div class="image_card ">
                <label for="image">Fotka č.${index}</label>
                <div class="image ">
                <img data-enlargable src="${blog_images[index]}" alt="Image">
                <button class="img-delete-button"><i class="fas fa-trash"></i></button>
                
                </div>
                </div>`
            }
        }
        image_html += "</div></div></div>"
    }
    

    showModal(image_html)
    add_fullscreen()

    const deletes = document.querySelectorAll(".img-delete-button");

    deletes.forEach((button,index) =>{
        button.addEventListener("click",()=>{
            const path = blog_images[index + 1]; 
            delete_photo(path); 
        })
    })

}


async function delete_photo(path){
    const data = {
        path:path
    };

    const images = await fetch("../../controllers/adminControlers/process_blogs.php?action=remove_image", {
    method: "POST",
    body: JSON.stringify(data),
    headers: {
        "Content-Type": "application/json",
    },
    });
    location.reload()
}

function formatDateToCzech(dateString) {
    const options = {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "numeric",
        minute: "numeric",
        second: "numeric",
    };
    return new Date(dateString).toLocaleDateString("cs-CZ", options);
}

function limitText(text, maxLength) {
    if (text.length > maxLength) {
        return text.slice(0, maxLength) + '...';
    }
    return text;
}



function add_fullscreen(){
    $('img[data-enlargable]').addClass('img-enlargable').click(function () {
        var src = $(this).attr('src');
        var fullscreenDiv = $('<div>').css({
        background:'RGBA(0,0,0,.5)',
          width: '100%',
          height: '100%',
          position: 'fixed',
          zIndex: '10000',
          top: '0',
          left: '0',
          cursor: 'zoom-out',
          display: 'flex',
          alignItems: 'flex-start', 
          justifyContent: 'center', 
          paddingBottom: '50px',
          paddingTop: '50px' 
        }).addClass('fullscreen-wrapper');
    
        var imageDiv = $('<div>').css({
          background: ' url(' + src + ') no-repeat center',
          backgroundSize: 'contain',
          width: '100%',
          height: '100%',
          borderRadius:'0px'
        });
    
        fullscreenDiv.click(function () {
          $(this).remove();
          $('body').removeClass('fullscreen-enabled');
        });
    
        fullscreenDiv.append(imageDiv);
        fullscreenDiv.appendTo('body');
    
        $('body').addClass('fullscreen-enabled');
      });
}