<template>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="border-b border-gray-200 dark:border-dark-body">
                    <table class="rounded-table w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-neutral-700">
                            <tr>
                                <th class="px-6 py-3 text-sm text-gray-500 dark:text-gray-200 text-left" v-for="header in headers">
                                    {{header}}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-dark-header divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="item in posts" class="">
                                <td class="table-data">
                                    <a :href="link(item.id)">{{item.title}}</a>
                                </td>
                                <td class="table-data">
                                    {{item.status}}
                                </td>
                                <td class="table-data">
                                    {{item.likes}}
                                </td>
                                <td class="table-data">
                                    {{formatDate(item.created_at)}}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment'

export default {
    data() {
        return {
            posts: [],
        }
    },
    methods: {
        loadData() {
            axios.get('/api/posts/list')
                .then(({data}) => {
                    this.posts = data.posts;
                });
        },
        link(id) {
            return `/posts/${id}/edit`;
        },
        formatDate(date) {
            return moment(date).fromNow();
        },
    },
    mounted() {
        this.loadData();
    }
}
</script>

<script setup>
const headers = [
    'Title',
    'Status',
    'Likes',
    'Created',
];
</script>
