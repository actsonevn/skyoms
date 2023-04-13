import { axiosClient } from "./axios.config";


const dashboardApi = {
    getOverview: async () => {
        return await axiosClient.get('/overview');
    }
};

export default dashboardApi;