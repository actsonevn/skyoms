import { axiosClient } from "./axios.config";


const dashboardApi = {
    getOverview: async () => {
        return await axiosClient.get('/overview');
    },
    getCustomerOrderList: async () => {
        return await axiosClient.get('/customer/orders');
    }
};

export default dashboardApi;