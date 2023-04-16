<script>
export default {
    props: {
        subUserAccounts: Array,
    },
};
</script>

<template>
    <section class="container px-4 py-4 mx-auto">

        <div>
            <div class="flex items-center gap-x-3">
                <h1 class="text-lg font-medium text-gray-800">Welcome, you have a total of:</h1>
                <span
                    class="px-3 py-1 text-xs text-blue-600 bg-blue-100 rounded-full dark:bg-gray-800 dark:text-blue-400">
                    {{ subUserAccounts.length }} accounts
                </span>
            </div>
            <p class="mt-1 text-sm">
                Please click on the
                <span class="italic text-emerald-500">"View movement summary"</span> link to see a detailed breakdown for the account.
            </p>
        </div>

        <div class="overflow-hidden dark:border-gray-700 md:rounded-lg mt-4">
            <table class="min-w-full divide-gray-200 ">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th scope="col"
                            class="py-3.5 px-4 text-sm font-normal text-left rtl:text-right text-gray-500
                            dark:text-gray-400">
                            <button class="flex items-center gap-x-3 focus:outline-none">
                                <span>Account Number</span>
                            </button>
                        </th>
                        <th scope="col"
                            class="px-12 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500
                            dark:text-gray-400">
                            Account Type
                        </th>
                        <th scope="col"
                            class="px-4 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500
                            dark:text-gray-400">
                            Account Balance
                        </th>
                        <th scope="col" class="relative py-3.5 px-4">
                            <span class="sr-only">View</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200 dark:divide-gray-700 dark:bg-gray-900">
                    <tr v-for="userAccount in subUserAccounts" class="hover:bg-gray-700">
                        <td class="px-4 py-3.5 text-sm font-medium whitespace-nowrap">
                            <div>
                                <h2 class="font-medium text-gray-800 dark:text-white ">{{ userAccount.number }}</h2>
                            </div>
                        </td>
                        <td class="px-12 py-3.5 text-sm font-medium whitespace-nowrap">
                            <div>
                                <h2 class="inline px-3 py-1 text-sm font-normal rounded-full text-emerald-500
                                gap-x-2 bg-emerald-100/60 dark:bg-gray-800">
                                    {{ userAccount.type }}
                                </h2>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-sm font-medium whitespace-nowrap">
                            <div>
                                <h2 class="font-medium text-gray-800 dark:text-white ">R {{ userAccount.balance }}</h2>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-sm whitespace-nowrap">
                            <a :href="'/accounts/' + userAccount.id + '/daily-summary'"
                                class="inline px-3 py-1 text-sm font-normal rounded-full text-emerald-500
                                gap-x-2 bg-emerald-100/60 dark:bg-gray-800 hover:bg-gray-100">
                                View Movement Summary
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>
