<script>
import {defineComponent} from "vue";
import AppLayout from '@/Layouts/AppLayout.vue';
import AccMovementSummaryTable from "@/Components/AccMovementSummaryTable.vue";

export default defineComponent({
    components: {
        AccMovementSummaryTable,
        AppLayout
    },
    props: {
        user_account: Object,
        daily_summary: Array,
    },
});
</script>

<template>
    <AppLayout title="Movement Summary">
        <div class="py-12">
            <div class="max-w-7xl mx-auto">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <AccMovementSummaryTable :sub-userAccount="user_account" :sub-dailySummary="daily_summary"/>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
