
let visibleBlogsCount = 4;
const loadMoreBtn = document.getElementById("loadMoreBtn");

document.addEventListener("DOMContentLoaded", async function () {
   
    get_destination();
    await set_blogs()
    
     toggleVisibility(visibleBlogsCount);
    document.getElementsByClassName("search-button")[0].addEventListener("click",async()=>{
        await search_blogs();
    })

    loadMoreBtn.addEventListener("click", async function () {
        visibleBlogsCount += 3;
    
         toggleVisibility(visibleBlogsCount);
    });
});





function toggleVisibility(count) {
    let blogs = document.querySelectorAll(".blog_card");
    for (let index = 0; index < blogs.length; index++) {
        const blog = blogs[index];
        if (index < count) {
            blog.classList.remove('d-none');
            blog.classList.add('d-flex');
        } else {
            blog.classList.remove('d-flex');
            blog.classList.add('d-none');
        }
    }
    if (count >= blogs.length) {
        loadMoreBtn.classList.add('d-none');
    } else {
        loadMoreBtn.classList.remove('d-none');
    }
}

async function get_destination(){
    let menulist = document.querySelector('#destinations')

    const data = {
        request: true,
    };

    const response = await fetch("../../controllers/adminControlers/process_blogs.php?action=get_active_destination", {
        method: "POST",
        body: JSON.stringify(data),
        headers: {
            "Content-Type": "application/json",
        },
    });

    if(response.ok){
        let res_data = await response.json();
        
        let destinations = "";
        res_data.forEach(destination => {
            let li = '<a href="blog.php?destination=' + destination.id+'">'+destination.CzechName+'</a>'
            destinations += li;
        });
        menulist.innerHTML = destinations;
    }else{
        menulist.innerHTML = "<p>Erorr</p>";
    }
}

async function search_blogs(){
    let search_phrase = document.querySelector(".search-input").value
    let blogs_search = document.querySelectorAll('.blogs')[0]

    
    const data = {
        filter: "active",
        request: true,
    };

    const response = await fetch("../../controllers/adminControlers/process_blogs.php?action=get_blogs", {
        method: "POST",
        body: JSON.stringify(data),
        headers: {
            "Content-Type": "application/json",
        },
    });

    if (response.ok) {
        let blogs = await response.json();
        
        let blogs_html = "";

        const users_res = await fetch("../../controllers/adminControlers/process_users.php?action=get_users", {
            method: "POST",
            body: JSON.stringify(data),
            headers: {
                "Content-Type": "application/json",
            },
        });

            const URLobject = new URL(window.location.href)

        let destination = URLobject.searchParams.get("destination")
    
        

            if(users_res.ok){
                let users = await users_res.json()

                        blogs.forEach((blog) => {
                            if (blog.desc.toLowerCase().includes(search_phrase) || blog.text.toLowerCase().includes(search_phrase)) {        
                                if(destination){
                                    if(destination === blog.Destination_id){
                                                        
                                                let first_name = "";
                                            let last_name= "";

                                            let b_text = blog.text.slice(0, 200) + "...";

                                            let b_createdat = blog.created_at.slice(0,8);
                                            b_createdat = b_createdat.replace(/-/g,"/")

                                            users.forEach(user => {
                                                if(user.id === blog.User_id){
                                                    first_name = user.FirstName;
                                                    last_name= user.LastName;
                                                }
                                            });
                                            let blog_html= `<div class="blog_card mb-5 d-flex flex-column flex-lg-row">
                                            <div class="blog-image" style="background-image:url('../../public/images/db_images/blogs/${blog.id}/img-title.jpg')">
                                            
                                            </div>
                                            <div class="blog-content">
                                            <div class="d-flex flex-column justify-content-between">
                                                <div class="">
                                                    <div class="d-flex justify-content-between flex-row align-items-start">
                                                    <h2>${blog.desc}</h2>
                                                    <div class="meta d-flex flex-column justify-content-center align-items-center">
                                                    <h3 class="author text-center">${first_name + " " + last_name}</h3>
                                                    <h3 class="date text-center">${b_createdat}</h3>
                                                    </div>
                                                    
                                                </div>
                                                <p class="mt-2">
                                                ${b_text}
                                                </p>
                                                </div>
                                            </div>
                                            <div>
                                            <div class="d-flex flex-row justify-content-center align-items-end h-100">
                                            <a href="../read_blog.php?blog_id=${blog.id}"><button class="styled_button mt-5">Číst více</button></a>
                                            </div>
                                            </div>
                                            </div>
                                            </div>
                                        </div>`
                                        blogs_html += blog_html;
                                }
                }else{
                                  
                    let first_name = "";
                    let last_name= "";

                    let b_text = blog.text.slice(0, 200) + "...";

                    let b_createdat = blog.created_at.slice(0,8);
                    b_createdat = b_createdat.replace(/-/g,"/")

                    users.forEach(user => {
                        if(user.id === blog.User_id){
                            first_name = user.FirstName;
                            last_name= user.LastName;
                        }
                    });
                    let blog_html= `<div class="blog_card mb-5 d-flex flex-column flex-lg-row">
                    <div class="blog-image" style="background-image:url('../../public/images/db_images/blogs/${blog.id}/img-title.jpg')">
                    
                    </div>
                    <div class="blog-content">
                    <div class="d-flex flex-column justify-content-between">
                        <div class="">
                            <div class="d-flex justify-content-between flex-row align-items-start">
                            <h2>${blog.desc}</h2>
                            <div class="meta d-flex flex-column justify-content-center align-items-center">
                            <h3 class="author text-center">${first_name + " " + last_name}</h3>
                            <h3 class="date text-center">${b_createdat}</h3>
                            </div>
                            
                        </div>
                        <p class="mt-2">
                        ${b_text}
                        </p>
                        </div>
                    </div>
                    <div>
                    <div class="d-flex flex-row justify-content-center align-items-end h-100">
                    <button class="styled_button mt-5">Číst více</button>
                    </div>
                    </div>
                    </div>
                    </div>
                </div>`
                blogs_html += blog_html;
                }
                }
            });
            if(blogs_html != ""){
                blogs_search.innerHTML = blogs_html;
            }else{
                blogs_search.innerHTML = "<h3 class='text-center' style='color:red'>Nenalezeny žádné příspěvky</h3>"
            }

            toggleVisibility(4)
            let blog_cards = document.querySelectorAll(".blog_card");
            if (blog_cards.length < 3) {
                loadMoreBtn.classList.add('d-none');
            } else {
                loadMoreBtn.classList.remove('d-none');
            }
        }else{
            console.error("Error");
        }

       
    } else {
        console.log("Error" + response.error);
    }

}

async function set_blogs(){
    const data = {
        filter: "active",
        request: true,
    };

    const response = await fetch("../../controllers/adminControlers/process_blogs.php?action=get_blogs", {
        method: "POST",
        body: JSON.stringify(data),
        headers: {
            "Content-Type": "application/json",
        },
    });
    let blogs = document.querySelectorAll('.blogs')[0]

    const URLobject = new URL(window.location.href)

    let destination = URLobject.searchParams.get("destination")

    if(response.ok){
        let res_data = await response.json();
        console.log(res_data);

            const users_res = await fetch("../../controllers/adminControlers/process_users.php?action=get_users", {
            method: "POST",
            body: JSON.stringify(data),
            headers: {
                "Content-Type": "application/json",
            },
            });
            
        if(users_res.ok){
                let users = await users_res.json()

            let blogs_html = "";
            
            res_data.forEach(blog => {
                if(destination){
                    if(destination === blog.Destination_id){
                           
                        let first_name = "";
                        let last_name= "";

                        let b_text = blog.text.slice(0, 200) + "...";

                        let b_createdat = blog.created_at.slice(0,8);
                        b_createdat = b_createdat.replace(/-/g,"/")

                        users.forEach(user => {
                            if(user.id === blog.User_id){
                                first_name = user.FirstName;
                                last_name= user.LastName;
                            }
                        });
                        let blog_html= `<div class="blog_card mb-5 d-flex flex-column flex-lg-row">
                        <div class="blog-image" style="background-image:url('../../public/images/db_images/blogs/${blog.id}/img-title.jpg')">
                        
                        </div>
                        <div class="blog-content">
                        <div class="d-flex flex-column justify-content-between">
                            <div class="">
                                <div class="d-flex justify-content-between flex-row align-items-start">
                                <h2>${blog.desc}</h2>
                                <div class="meta d-flex flex-column justify-content-center align-items-center">
                                <h3 class="author text-center">${first_name + " " + last_name}</h3>
                                <h3 class="date text-center">${b_createdat}</h3>
                                </div>
                                
                            </div>
                            <p class="mt-2">
                            ${b_text}
                            </p>
                            </div>
                        </div>
                        <div>
                        <div class="d-flex flex-row justify-content-center align-items-end h-100">
                        <a href="../Blog/read_blog.php?blog_id=${blog.id}"><button class="styled_button mt-5">Číst více</button></a>
                        </div>
                        </div>
                        </div>
                        </div>
                    </div>`
                    blogs_html += blog_html;
                    }
                }else{
                       
                    let first_name = "";
                    let last_name= "";

                    let b_text = blog.text.slice(0, 200) + "...";

                    let b_createdat = blog.created_at.slice(0,8);
                    b_createdat = b_createdat.replace(/-/g,"/")

                    users.forEach(user => {
                        if(user.id === blog.User_id){
                            first_name = user.FirstName;
                            last_name= user.LastName;
                        }
                    });
                    let blog_html= `<div class="blog_card mb-5 d-flex flex-column flex-lg-row">
                    <div class="blog-image" style="background-image:url('../../public/images/db_images/blogs/${blog.id}/img-title.jpg')">
                    
                    </div>
                    <div class="blog-content">
                    <div class="d-flex flex-column justify-content-between">
                        <div class="">
                            <div class="d-flex justify-content-between flex-row align-items-start">
                            <h2>${blog.desc}</h2>
                            <div class="meta d-flex flex-column justify-content-center align-items-center">
                            <h3 class="author text-center">${first_name + " " + last_name}</h3>
                            <h3 class="date text-center">${b_createdat}</h3>
                            </div>
                            
                        </div>
                        <p class="mt-2">
                        ${b_text}
                        </p>
                        </div>
                    </div>
                    <div>
                    <div class="d-flex flex-row justify-content-center align-items-end h-100">
                    <a href="../Blog/read_blog.php?blog_id=${blog.id}"><button class="styled_button mt-5">Číst více</button></a>
                    </div>
                    </div>
                    </div>
                    </div>
                </div>`
                blogs_html += blog_html;
                }
            });
            blogs.innerHTML = blogs_html
        }else{
            console.log("Error");
            blogs.innerHTML = '<h1 class="text-center>Autoři nenalezeni</h1>"'  
        }
    }else{
        console.log("Error");
        blogs.innerHTML = '<h1 class="text-center>Blogy nenalezeny</h1>"'
    }
}