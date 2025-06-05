function isFieldEmpty(field){
    if (field.value.trim() === "") {
        return true;
    }
    return false;
}

function validateFieldEmpty(fieldID, errorID){
    let field = document.getElementById(fieldID);
    if (field && isFieldEmpty(field)){
        let emptyError = document.getElementById(errorID);
        if (emptyError){
            emptyError.classList.remove("hidden");
        }
    }
    else if (field && !isFieldEmpty(field)){
        let emptyError = document.getElementById(errorID);
        if (emptyError){
            emptyError.classList.add("hidden");
        }
    }
}

function validateFieldFormat(fieldID, pattern, errorID){
    let field = document.getElementById(fieldID);
    if (field){
        if (isFieldEmpty(field)){
            return false;
        }
        else{
            if (!field.value.match(pattern)){
                let error = document.getElementById(errorID);
                if(error){
                    error.classList.remove("hidden");
                }
                return false;
            }
            return true;
        }
    }
}


const fields = [
    {id: "email", pattern: /^[a-z0-9._]+@[a-z0-9.]+\.[a-z0-9]{2,8}$/, emptyErrorID: "email-empty", wrongFormatErrorID: "email-wrongformat"},
    {id: "password", pattern: /^(?=.*[0-9])(?=.*[!@#$%^&*-_])[a-zA-Z0-9!@#$%^&*-_]{8,32}$/, emptyErrorID: "password-empty", wrongFormatErrorID: "password-wrongformat"}];

fields.forEach(({id, pattern, emptyErrorID, wrongFormatErrorID}) => {
    let field = document.getElementById(id);
    if (field){
        validateFieldEmpty(id, emptyErrorID);
        validateFieldFormat(id, pattern, wrongFormatErrorID);
        field.addEventListener("input", () => validateFieldEmpty(id, emptyErrorID));
        field.addEventListener("blur", () => validateFieldEmpty(id, emptyErrorID));
    }
});

const form = document.getElementById("auth-form");
if (form){
    form.addEventListener("submit", (e) => {
        let isValid = true;
        document.querySelectorAll(".error").forEach((error) => {
            error.classList.add("hidden");
        });
    
        isValid = validateFieldFormat(fields[0].id, fields[0].pattern, fields[0].wrongFormatErrorID) && isValid;
        isValid = validateFieldFormat(fields[1].id, fields[1].pattern, fields[1].wrongFormatErrorID) && isValid;
    
        if (!isValid) {
            e.preventDefault();
        }
    });
}