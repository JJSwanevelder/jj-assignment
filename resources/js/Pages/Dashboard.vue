<script setup>
import {defineComponent} from "vue";
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    userAccounts: Array,
});
</script>

<template>
    <AppLayout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <UserAccountsTable :sub-userAccounts="userAccounts"/>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import { defineComponent } from 'vue'
import UserAccountsTable from "@/Components/UserAccountsTable.vue";

export default defineComponent({
    components: {
        UserAccountsTable,
    },
    props: {
        userAccount: Array,
    },
})
</script>
