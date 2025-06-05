const closeButton = document.getElementById("closeButton");
if(closeButton){
    closeButton.addEventListener("click", () =>{
        const overlay = document.getElementById("popUpOverlay");
        if (overlay){
            overlay.classList.add("hidden");
        }
    });
}

const retryButton = document.getElementById("retryButton");
if(retryButton){
    retryButton.addEventListener("click", () =>{
        const overlay = document.getElementById("popUpOverlay");
        if (overlay){
            overlay.classList.add("hidden");
        }
    });
}

