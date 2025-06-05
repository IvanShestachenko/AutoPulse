async function loadMakes() {
    const response = await fetch('get_makes.php');
    const makes = await response.json();
    const makeSelect = document.getElementById('make');
    if (makeSelect){
        makes.forEach(make => {
            const option = document.createElement('option');
            option.value = make;
            option.textContent = make;
            makeSelect.appendChild(option);
        });
    }
    const url = new URL(window.location.href);
    let params = url.searchParams;

    if (params.has('make')) {
        let makeValue = params.get('make');
        if (makeSelect) {
            for (let option of makeSelect.options) {
                if (option.value === makeValue) {
                    option.selected = true;
                    await loadModels();
                    break;
                }
            }
        }
    }
}

async function loadModels() {
    const make = document.getElementById('make');
    const modelSelect = document.getElementById('model');
    if (make && modelSelect) {
        const response = await fetch(`get_models.php?make=${make.value}`);
        const models = await response.json();
        models.forEach(model => {
            const option = document.createElement('option');
            option.value = model;
            option.textContent = model;
            modelSelect.appendChild(option);
        });
        modelSelect.disabled = false;
        const searchButton = document.getElementById('search');
        if (searchButton){
            searchButton.disabled = true;
        }
    }
    const url = new URL(window.location.href);
    let params = url.searchParams;

    if (params.has('model')) {
        let modelValue = params.get('model');
        if (modelSelect) {
            for (let option of modelSelect.options) {
                if (option.value === modelValue) {
                    option.selected = true;
                    break;
                }
            }
        }
    }
}

function removeModels(){
    const modelSelect = document.getElementById('model');
    if (modelSelect){
        const options = document.querySelectorAll("#model option");
        options.forEach(option => {
            if(option.value != "default"){
                option.remove();
            }
        });
    }
}

window.onload = loadMakes;
const makeSelect = document.getElementById('make');
const modelSelect = document.getElementById('model');
const searchButton = document.getElementById('search');
if(makeSelect && modelSelect){
    makeSelect.addEventListener('change', () => {
        removeModels();
        modelSelect.dispatchEvent(new Event("change"));
        if (makeSelect.value != "default"){
            loadModels();
        }
        else{
            modelSelect.disabled = true;
            if (searchButton){
                searchButton.disabled = true;
            }
        }
    });
}
if (modelSelect && searchButton){
    modelSelect.addEventListener('change', () => {
        if (modelSelect.value != "default"){
            searchButton.disabled = false;
           
        }
        else {
            searchButton.disabled = true;
        }
    })
}

// const url = new URL(window.location.href);
// let params = url.searchParams;

// if (params.has('make')) {
//     alert(1);
//     let makeValue = params.get('make');
//     if (makeSelect) {
//         alert(2);
//         for (let option of makeSelect.options) {
//             if (option.value === makeValue) {
//                 alert(3);
//                 option.selected = true;
//                 break;
//             }
//         }
//     }
// }

