let filterForm = document.getElementById("filter-form");
const filterSelect = document.getElementById("filter-select");
let filteringProperty = null;
let filteringOrder = null;

if(filterForm && filterSelect){
    filterSelect.addEventListener('change', () => {
        let sortingSelected = filterSelect.value;

        if (sortingSelected === "price asc"){
            filteringProperty = "price";
            filteringOrder = "asc";
        }

        else if (sortingSelected === "price desc"){
            filteringProperty = "price";
            filteringOrder = "desc";
        }

        else {
            filteringProperty = "id";
            filteringOrder = "desc";
        }

        const url = new URL(window.location.href);
        let params = url.searchParams;
        params.delete("page");
        params.set("filter", filteringProperty);
        params.set("order", filteringOrder);
        let paramsString = params.toString();
        const pureURL = window.location.origin + window.location.pathname;
        window.location.href = `${pureURL}?${paramsString}`;
    });
}