function showPopup() {

    const overlay = document.createElement('div');
    overlay.className = 'popupOverlay-header';

    const popup = document.createElement('div');
    popup.className = 'popupWindow-header';

    const closeContainer = document.createElement('div');
    closeContainer.className = "close-container-header";
    popup.appendChild(closeContainer);

    const closeButton = document.createElement('span');
    closeButton.textContent = '\u2716';
    closeButton.className = "closeButton-header";
    closeButton.onclick = function () {
        document.body.removeChild(overlay);
    };
    closeContainer.appendChild(closeButton);

    const message = document.createElement('p');
    message.textContent = 'Pro pokračování je nutné se přihlásit.';
    popup.appendChild(message);

    const registerButton = document.createElement('a');
    registerButton.href = 'login.php'; 
    registerButton.textContent = 'Přihlásit se';
    registerButton.className = "registerButton-header";
    popup.appendChild(registerButton);

    overlay.appendChild(popup);
    document.body.appendChild(overlay);
}

window.addEventListener('DOMContentLoaded', () => {
    const publishInsertionLink = document.getElementById('publishInsertionLink');
    const myProfileLink = document.getElementById('myProfileLink');
    if (publishInsertionLink && myProfileLink) {
        publishInsertionLink.addEventListener('click', (event) => {
            event.preventDefault();
            showPopup();
        });
        myProfileLink.addEventListener('click', (event) => {
            event.preventDefault();
            showPopup();
        });
    }
});
