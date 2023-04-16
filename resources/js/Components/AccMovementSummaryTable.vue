<script>
import { ref } from 'vue';
export default {
    props: {
        subDailySummary: Array,
        subUserAccount: Object,
    },
    data() {
        return {
            dailySummary: [],
            dateValue: ref([]),
            formatter: ref({
                date: 'YYYY-MM-DD',
            })
        };
    },
    methods: {
        submit() {
            if (this.dateValue[0] === undefined || this.dateValue[1] === undefined) return;
            // TODO: bind above to dataType and disable the submit button if true.

            this.$inertia.get(`/accounts/${this.subUserAccount.id}/daily-summary`, {
                start_date: this.dateValue[0],
                end_date: this.dateValue[1],
            });
        }
    },
};
</script>

<template>
    <section class="container px-4 mx-auto">
        <div class="flex pt-6">
            <div class="flex-initial w-1/2">
                <vue-tailwind-datepicker
                    v-model="dateValue"
                    :formatter="formatter"
                    placeholder="Select a date range to change the default 30 day summary"
                    @select:month="submit()"/>
            </div>
            <div class="flex-initial w-20 items-center px-4 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-xs
                           text-white tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 ml-2"
                    @click="submit">
                Submit
            </div>
        </div>

        <div class="flex flex-col mt-6">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                    <div class="overflow-hidden border border-gray-200 dark:border-gray-700 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th scope="col"
                                        class="py-3.5 px-4 text-sm font-normal text-left rtl:text-right text-gray-500
                                        dark:text-gray-400">
                                        <button class="flex items-center gap-x-3 focus:outline-none">
                                            <span>Day</span>
                                        </button>
                                    </th>
                                    <th scope="col"
                                        class="px-4 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500
                                        dark:text-gray-400">
                                        Opening Balance
                                    </th>
                                    <th scope="col"
                                        class="px-12 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500
                                        dark:text-gray-400">
                                        Total Credit
                                    </th>
                                    <th scope="col"
                                        class="px-4 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500
                                        dark:text-gray-400">
                                        Total Debit
                                    </th>
                                    <th scope="col"
                                        class="px-4 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500
                                        dark:text-gray-400">
                                        Closing Balance
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:divide-gray-700 dark:bg-gray-900">
                                <tr v-for="transactionSummary in subDailySummary" class="hover:bg-gray-700">
                                    <td class="px-4 py-4 text-sm font-medium whitespace-nowrap">
                                        <div>
                                            <h2 class="font-medium text-gray-800 dark:text-white ">{{ transactionSummary.day }}</h2>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm font-medium whitespace-nowrap">
                                        <div>
                                            <h2 class="font-medium text-gray-800 dark:text-white ">R {{ transactionSummary.opening_balance }}</h2>
                                        </div>
                                    </td>
                                    <td class="px-12 py-4 text-sm font-medium whitespace-nowrap">
                                        <div>
                                            <h2 class="inline px-3 py-1 text-sm font-normal rounded-full text-emerald-500
                                            gap-x-2 bg-emerald-100/60 dark:bg-gray-800">
                                                R {{ transactionSummary.total_credits }}
                                            </h2>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm font-medium whitespace-nowrap">
                                        <div>
                                            <h2 class="font-medium text-gray-800 dark:text-white ">R {{ transactionSummary.total_debits }}</h2>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm font-medium whitespace-nowrap">
                                        <div>
                                            <h2 class="inline px-3 py-1 text-sm font-normal rounded-full text-emerald-500
                                            gap-x-2 bg-emerald-100/60 dark:bg-gray-800">R {{ transactionSummary.closing_balance }}</h2>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div><br>
    </section>
</template>
