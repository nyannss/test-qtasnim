import api from "./base";

export const getAllSalesReport = ({ start_date = null, end_date = null }) =>
    api.get("/sales-report", {
        params: {
            start_date,
            end_date,
        },
    });
