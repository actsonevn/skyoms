import { defineStore } from "pinia";

export const useDashboardStore = defineStore({ 
    id: 'dashboard',
    state: () => ({
        overiewLoading: false,
        overview: {
            totalOrder: null,
            orderCompleted: null,
            totalItemSale: null,
            avgTotalPrice: null,
        }
    }),
    actions: {
        setOverviewLoading(payload) {
            this.overiewLoading = payload;
        },
        setOverview(payload) {
            this.overview = {
                ...this.overview,
                totalOrder: payload.total_order,
                orderCompleted: payload.order_completed,
                totalItemSale: payload.order_item_sale,
                avgTotalPrice: payload.avg_total_price,
            };
        },
    }
})