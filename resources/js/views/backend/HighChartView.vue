<script>
    import { ref, computed } from 'vue';
    import VueHighcharts from 'vue3-highcharts';

    export default {
        name: 'test',
        components: {
            VueHighcharts,
        },
        setup() {
            const hmCharts = ref([
            {
                id: 'one',
                options: computed(() => {
                return {
                    chart: {
                        type: 'line',
                    },
                    title: {
                        text: 'Number of project stars',
                    },
                    xAxis: {
                        categories: ['Jun', 'Jul', 'Aug', 'Sept'],
                    },
                    yAxis: {
                        title: {
                            text: 'Number of stars',
                        },
                    },
                    series: [{
                        name: 'New project stars',
                        data: [25, 39, 30, 15],
                    }],
                    }
                }),
            }])

            const chartData = computed(() => {
                return {
                    chart: {
                        type: 'line',
                    },
                    title: {
                        text: 'Number of project stars',
                    },
                    xAxis: {
                        categories: ['Jun', 'Jul', 'Aug', 'Sept'],
                    },
                    yAxis: {
                        title: {
                            text: 'Number of stars',
                        },
                    },
                    series: [{
                        name: 'New project stars',
                        data: [25, 39, 30, 15],
                    }],
                }
            });
        
            const onRender = () => {
                console.log('Chart rendered');
            };
        
            const onUpdate = () => {
                console.log('Chart updated');
            };
        
            const onDestroy = () => {
                console.log('Chart destroyed');
            };
        
            return {
                hmCharts,
                chartData,
                onRender,
                onUpdate,
                onDestroy,
            };
        }
    }
</script>

<style>
    .vue-highcharts {
        width: 100%;
    }
</style>

<template>
    {{ chartData }}
    <!-- <vue-highcharts
        :options="chartData"
        :redrawOnUpdate="true"
        :oneToOneUpdate="false"
        :animateOnUpdate="true"
        @rendered="onRender"
        @update="onUpdate"
        @destroy="onDestroy"
    /> -->
</template>
