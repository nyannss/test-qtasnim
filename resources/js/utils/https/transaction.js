import api from './base';

export const getAllTransactions = ({
    searchByName,
    per_page = 15,
    page = 1,
    sortBy = "id",
    sortOrder = "asc",
}) => {
    return api.get("/transaction", {
        params: {
            searchByName,
            per_page,
            page,
            sortBy,
            sortOrder,
        },
    });
};
