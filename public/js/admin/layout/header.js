// header.js

function initHeaderDropdown(){

    const profileBtn = document.getElementById("profileBtn");
    const profileMenu = document.getElementById("profileMenu");

    if(!profileBtn || !profileMenu) return;

    profileBtn.addEventListener("click", () => {
        profileMenu.classList.toggle("active");
    });

    window.addEventListener("click", (e) => {

        if(
            !profileBtn.contains(e.target) &&
            !profileMenu.contains(e.target)
        ){
            profileMenu.classList.remove("active");
        }

    });

}

