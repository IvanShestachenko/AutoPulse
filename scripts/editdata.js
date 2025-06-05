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

function validateConfirmPassword() {
    const password = document.getElementById("new-password");
    const confirmPassword = document.getElementById("confirm-password");
    if (password && confirmPassword){
        if(!isFieldEmpty(confirmPassword)){
            if (password.value !== confirmPassword.value) {
                let error = document.getElementById("confirmpassword-wrong");
                if (error){
                    error.classList.remove("hidden");
                }
                return false;
            }
            return true;
        }
        else{
            validateFieldEmpty("confirm-password", "confirmpassword-empty");
            return false;
        }
    }
    return false;
}

const fields = [
    {id: "first-name", pattern: /^[a-zA-ZčČřŘžŽáÁíÍéÉěĚýÝůŮúÚóÓďĎťŤňŇ.\-]{2,20}$/, emptyErrorID: "firstname-empty", wrongFormatErrorID: "firstname-wrongformat"},
    {id: "last-name", pattern: /^[a-zA-ZčČřŘžŽáÁíÍéÉěĚýÝůŮúÚóÓďĎťŤňŇ.\-]{2,20}$/, emptyErrorID: "lastname-empty", wrongFormatErrorID: "lastname-wrongformat"},
    {id: "company-name", pattern: /^[a-zA-ZčČřŘžŽáÁíÍéÉěĚýÝůŮúÚóÓďĎťŤňŇ.\- ]{2,20}$/, emptyErrorID: "companyname-empty", wrongFormatErrorID: "companyname-wrongformat"},
    {id: "current-password", pattern: /^(?=.*[0-9])(?=.*[!@#$%^&*-_])[a-zA-Z0-9!@#$%^&*-_]{8,32}$/, emptyErrorID: "current-password-empty", wrongFormatErrorID: "current-password-wrongformat"},
    {id: "new-password", pattern: /^(?=.*[0-9])(?=.*[!@#$%^&*-_])[a-zA-Z0-9!@#$%^&*-_]{8,32}$/, emptyErrorID: "new-password-empty", wrongFormatErrorID: "new-password-wrongformat"}
];


fields.forEach(({id, pattern, emptyErrorID, wrongFormatErrorID}) => {
    let field = document.getElementById(id);
    if (field){
        validateFieldEmpty(id, emptyErrorID);
        validateFieldFormat(id, pattern, wrongFormatErrorID);
        field.addEventListener("input", () => validateFieldEmpty(id, emptyErrorID));
        field.addEventListener("blur", () => validateFieldEmpty(id, emptyErrorID));
    }
});

validateFieldEmpty("confirm-password", "confirmpassword-empty");
const confirmPasswordField = document.getElementById("confirm-password");
if (confirmPasswordField){
    confirmPasswordField.addEventListener("input", () => validateFieldEmpty("confirm-password", "confirmpassword-empty"));
    confirmPasswordField.addEventListener("blur", () => validateFieldEmpty("confirm-password", "confirmpassword-empty"));
}

const form = document.getElementById("auth-form");
if (form){
    form.addEventListener("submit", (e) => {
        let isValid = true;
        document.querySelectorAll(".error").forEach((error) => {
            error.classList.add("hidden");
        });
        const userTypeSelect = document.getElementById("user-type");
        if (userTypeSelect && userTypeSelect.value === "private") {
            isValid = validateFieldFormat(fields[0].id, fields[0].pattern, fields[0].wrongFormatErrorID) && isValid;
            isValid = validateFieldFormat(fields[1].id, fields[1].pattern, fields[1].wrongFormatErrorID) && isValid;
        }
            
        if (userTypeSelect && userTypeSelect.value === "company") {
            isValid = validateFieldFormat(fields[2].id, fields[2].pattern, fields[2].wrongFormatErrorID) && isValid;
        }

        isValid = validateFieldFormat(fields[3].id, fields[3].pattern, fields[3].wrongFormatErrorID) && isValid;

        isValid = validateFieldFormat(fields[4].id, fields[4].pattern, fields[4].wrongFormatErrorID) && isValid;

        isValid = validateConfirmPassword() && isValid;


    
        if (!isValid) {
            e.preventDefault();
        }
    });
}

