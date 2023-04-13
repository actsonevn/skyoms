import axios from "axios";

export const axiosClient = axios.create({
    baseURL: 'http://localhost:8000/api',
});

axiosClient.interceptors.response.use((response) => {
    // Do something with response data
    return response.data;
}, (error) => {
    // Do something with response error
    return Promise.reject(error);
});