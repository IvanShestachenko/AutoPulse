function validateSelect(fieldId, errorId){
    const field = document.getElementById(fieldId);
    if (field){
        if (field.value == "default"){
            const error = document.getElementById(errorId);
            if (error){
                error.classList.remove("hidden");
            }
            return false;
        }
        else {
            const error = document.getElementById(errorId);
            if (error){
                error.classList.add("hidden");
            }
            return true; 
        }
    }
    return false;
}

function validateEmptyNumberPhoto(fieldId, errorId){
    const field = document.getElementById(fieldId);
    if (field){
        if (field.value === ""){
            const error = document.getElementById(errorId);
            if (error){
                error.classList.remove("hidden");
            }
            return false;
        }
        else {
            const error = document.getElementById(errorId);
            if (error){
                error.classList.add("hidden");
            }
            return true; 
        }
    }
    return false;
}

function validateText(fieldId, max_length, errorId){
    const field = document.getElementById(fieldId);
    if (field){
        if (field.value.trim().length > max_length){
        const error = document.getElementById(errorId);
            if (error){
                error.classList.remove("hidden");
            }
            return false;
        }
        else {
            const error = document.getElementById(errorId);
            if (error){
                error.classList.add("hidden");
            }
            return true; 
        }
    }
    return false;
}

function validateNumberFormat(fieldId, min_value, max_value, errorId){
    const field = document.getElementById(fieldId);
    if (field){
        if (field.value === ""){
            return false;
        }
        const numericValue = parseInt(field.value);
        if (!isNaN(numericValue) && numericValue >= min_value && numericValue <= max_value){
            const error = document.getElementById(errorId);
            if (error){
                error.classList.add("hidden");
            }
            return true; 
        }
        else {
            const error = document.getElementById(errorId);
            if (error){
                error.classList.remove("hidden");
            }
            return false;
        }
    }
    return false;
}


text_fields = [
    {id: "short_description", max_length: 50, wrongFormatErrorId: "short_description-wrongformat"},
    {id: "description", max_length: 600, wrongFormatErrorId: "description-wrongformat"}
];

number_fields = [
    {id: "price", min_value: 10000, max_value: 20000000, emptyErrorId: "price-empty", wrongFormatErrorId: "price-wrongformat"},
    {id: "year", min_value: 1980, max_value: 2026, emptyErrorId: "year-empty", wrongFormatErrorId: "year-wrongformat"},
    {id: "mileage", min_value: 0, max_value: 3000000, emptyErrorId: "mileage-empty", wrongFormatErrorId: "mileage-wrongformat"},
    {id: "power", min_value: 30, max_value: 5000, emptyErrorId: "power-empty", wrongFormatErrorId: "power-wrongformat"},
    {id: "engine_capacity", min_value: 1000, max_value: 8000, emptyErrorId: "engine_capacity-empty", wrongFormatErrorId: "engine_capacity-wrongformat"}
];

select_fields = [
    {id: "make", emptyErrorId: "make-empty"},
    {id: "model", emptyErrorId: "model-empty"},
    {id: "fuel", emptyErrorId: "fuel-empty"}
];

photo_fields = [
    {id: "photo1", emptyErrorId: "photo1-empty"},
    {id: "photo2", emptyErrorId: "photo2-empty"},
    {id: "photo3", emptyErrorId: "photo3-empty"},
    {id: "photo4", emptyErrorId: "photo4-empty"},
    {id: "photo5", emptyErrorId: "photo5-empty"}
];

select_fields.forEach(({id, emptyErrorId}) => {
    const field = document.getElementById(id);
    if (field){
        validateSelect(id, emptyErrorId);
        field.addEventListener('change', () => validateSelect(id, emptyErrorId));
        field.addEventListener('blur', () => validateSelect(id, emptyErrorId));
    }
});

number_fields.forEach(({id, min_value, max_value, emptyErrorId, wrongFormatErrorId}) => {
    const field = document.getElementById(id);
    if (field){
        validateEmptyNumberPhoto(id, emptyErrorId);
        field.addEventListener("input", () => validateEmptyNumberPhoto(id, emptyErrorId));
        field.addEventListener("blur", () => validateEmptyNumberPhoto(id, emptyErrorId));
    }
});

photo_fields.forEach(({id, emptyErrorId}) => {
    const field = document.getElementById(id);
    if (field){
        validateEmptyNumberPhoto(id, emptyErrorId);
        field.addEventListener("change", () => validateEmptyNumberPhoto(id, emptyErrorId));
        field.addEventListener("blur", () => validateEmptyNumberPhoto(id, emptyErrorId));    
    }
});

const form = document.getElementById("insertion_form");
if (form){
    form.addEventListener("submit", (e) => {
        let isValid = true;
        document.querySelectorAll(".error").forEach((error) => {
            error.classList.add("hidden");
        });

        select_fields.forEach(({id, emptyErrorId}) => {
            const field = document.getElementById(id);
            if (field){
                isValid = validateSelect(id, emptyErrorId) && isValid;
            }
        });

        number_fields.forEach(({id, min_value, max_value, emptyErrorId, wrongFormatErrorId}) => {
            const field = document.getElementById(id);
            if (field){
                isValid = validateNumberFormat(id, min_value, max_value, wrongFormatErrorId) && isValid;
            }
        });

        text_fields.forEach(({id, max_length, wrongFormatErrorId}) => {
            const field = document.getElementById(id);
            if (field){
                isValid = validateText(id, max_length, wrongFormatErrorId) && isValid;
            }
        });

        photo_fields.forEach(({id, emptyErrorId}) => {
            const field = document.getElementById(id);
            if (field){
                isValid = validateEmptyNumberPhoto(id, emptyErrorId) && isValid;
            }
        });

        if (!isValid){
            e.preventDefault();
        }
    });
}