import api from "./base";

export const getAllProducts = ({
    searchByName = "",
    per_page = 15,
    page = 1,
}) =>
    api.get("/product", {
        params: {
            searchByName,
            per_page,
            page,
        },
    });
