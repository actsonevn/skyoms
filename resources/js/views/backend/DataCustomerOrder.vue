<script setup>
    import { useDashboardStore } from '@/stores/dashboard'; 

    import DataTable from 'datatables.net-vue3';
    import DataTablesCore from 'datatables.net';

    DataTable.use(DataTablesCore);
    const dashboard = useDashboardStore();
    
</script>

<style>
@import 'datatables.net-dt';
</style>

<template>
    <BaseBlock title="List Customer Order" >
      <DataTable 
        :data="dashboard.orderList" 
        class="display"
        v-if="!dashboard.orderLoading"
      >
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Status</th>
                <th>Total Amount Order</th>
                <th>Create time</th>
                <th>Item Order</th>
            </tr>
        </thead>
      </DataTable>
      <div v-else="dashboard.orderLoading">
        loading data...
      </div>
    </BaseBlock>
</template>
