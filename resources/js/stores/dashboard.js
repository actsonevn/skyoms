import { defineStore } from "pinia";
import dashboardApi from "../apis/dashboard.api";

export const useDashboardStore = defineStore({ 
    id: 'dashboard',
    state: () => ({
        overviewLoading: false,
        overview: {
            totalOrder: 0,
            orderCompleted: 0,
            orderItemSale: 0,
            avgTotalPrice: 0
        },
        orderLoading: false,
        orderList: [],
    }),
    actions: {
        async fetchOverviewData() {
            this.overviewLoading = true;
            
            const result = await dashboardApi.getOverview();
            const { data } = result;
            
            this.overview = {
                ...this.overview,
                totalOrder: data.total_order,
                orderCompleted: data.order_completed,
                orderItemSale: data.order_item_sale,
                avgTotalPrice: data.avg_total_price
            }
            this.overviewLoading = false;
        },
        async fetchCustomerOrderList() {
            this.orderLoading = true;
            
            const result = await dashboardApi.getCustomerOrderList();
            const { data } = result;
            
            let cleanData = data.map(dt => {
                let arrObj = [];
                Object.keys(dt).forEach(function(key) {
                    arrObj.push(dt[key]);
                });

                return arrObj;
            })
            
            this.orderList = cleanData;
            this.orderLoading = false;
        },
    }
})