<template>
    <div class="border-b border-gray-200 dark:border-dark-body mt-4">
        <table class="rounded-table w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-neutral-700">
                <tr>
                    <th class="px-6 py-3 text-sm text-gray-500 dark:text-gray-200 text-left" v-for="header in headers">
                        {{header}}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-dark-header divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="item in value" :key="item.id" class="">
                    <td class="table-data truncate">
                        <div class="text-black font-bold">{{item.title}}</div>
                    </td>
                    <td class="table-data truncate">
                        <a :href="item.link" class="text-gray-500 no-underline" target="_blank">{{ item.link }}</a>
                    </td>
                    <td class="table-data truncate">
                        {{ formatDate(item.created_at) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
import moment from 'moment'

export default {
    props: {
        value: {
            type: Array,
            required: true,
        }
    },
    data() {
        return {
            headers: [
                this.__('Web name'),
                this.__('Link'),
                this.__('Published'),
            ],
        }
    },
    methods: {
        formatDate(date) {
            return moment(date).fromNow();
        },
    }
}
</script>
