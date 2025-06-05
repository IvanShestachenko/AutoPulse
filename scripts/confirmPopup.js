const logoutLink = document.getElementById("logoutLink");
if(logoutLink){
    logoutLink.addEventListener("click", (e) =>{
        e.preventDefault();
        const overlay = document.getElementById("popUpOverlay");
        if (overlay){
            overlay.classList.remove("hidden");
        }
    });
}

const deleteAccountButton = document.getElementById("deleteAccountButton");
if(deleteAccountButton){
    deleteAccountButton.addEventListener("click", (e) =>{
        e.preventDefault();
        const overlay = document.getElementById("popUpOverlayAccountDelete");
        if (overlay){
            overlay.classList.remove("hidden");
        }
    });
}

const closeButton = document.getElementById("closeButton");
if(closeButton){
    closeButton.addEventListener("click", () =>{
        const overlay = document.getElementById("popUpOverlay");
        if (overlay){
            overlay.classList.add("hidden");
        }
    });
}

const cancelButton = document.getElementById("cancelButton");
if(cancelButton){
    cancelButton.addEventListener("click", () =>{
        const overlay = document.getElementById("popUpOverlay");
        if (overlay){
            overlay.classList.add("hidden");
        }
    });
}

