<script setup>
  
    import { useDashboardStore } from '@/stores/dashboard'; 

    const dashboard = useDashboardStore();

    const props = defineProps({
        title: String,
        value: Number,
    })
</script>

<template>
    <div class="col-sm-6 col-xxl-3">
        <BaseBlock class="d-flex flex-column h-100 mb-0">
            <template #content>
            <div
                class="block-content block-content-full flex-grow-1 d-flex justify-content-between align-items-center"
            >
                <dl class="mb-0" v-if="!dashboard.overviewLoading">
                <dt class="fs-3 fw-bold"> {{ props.value }}</dt>
                <dd class="fs-sm fw-medium fs-sm fw-medium text-muted mb-0">
                    {{ props.title }}
                </dd>
                </dl>
                <dl class="mb-0" v-else="dashboard.overviewLoading">
                loading...
                </dl>
                <div class="item item-rounded-lg bg-body-light">
                <i class="far fa-gem fs-3 text-primary"></i>
                </div>
            </div>
            <!-- <div class="bg-body-light rounded-bottom">
                <a
                class="block-content block-content-full block-content-sm fs-sm fw-medium d-flex align-items-center justify-content-between"
                href="javascript:void(0)"
                >
                <span>View all orders</span>
                <i
                    class="fa fa-arrow-alt-circle-right ms-1 opacity-25 fs-base"
                ></i>
                </a>
            </div> -->
            </template>
        </BaseBlock>
    </div>
</template>