import api from "./base";

export const getAllTransactions = ({
    searchByName = "",
    per_page = 15,
    page = 1,
    sort_by = "id",
    sort_order = "desc",
}) =>
    api.get("/transaction", {
        params: {
            searchByName,
            per_page,
            page,
            sort_by,
            sort_order,
        },
    });

export const insertNewTransaction = ({ productID, qty }) =>
    api.post("/transaction", {
        product_id: productID,
        qty,
    });

export const deleteTransaction = (transactionID) =>
    api.delete(`/transaction/${transactionID}`);
