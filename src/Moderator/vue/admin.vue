<template>
    <div>
        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-md-12">

                    <admin-menu />

                    <div class="flex justify-between items-center space-x-4">
                        <div class="btn-group" role="group" aria-label="Status">
                          <input type="radio" class="btn-check" name="status" id="status_all" autocomplete="off" :checked="filters.status === ''">
                          <label class="btn btn-outline-primary" for="status_all" @click="updateStatus('')">{{ __('All') }}</label>

                          <input type="radio" class="btn-check" name="status" id="status_draft" autocomplete="off" :checked="filters.status === 'draft'">
                          <label class="btn btn-outline-primary" for="status_draft" @click="updateStatus('draft')">{{ __('Draft') }}</label>

                          <input type="radio" class="btn-check" name="status" id="status_publish" autocomplete="off" :checked="filters.status === 'published'">
                          <label class="btn btn-outline-primary" for="status_publish" @click="updateStatus('published')">{{ __('Published') }}</label>
                        </div>
                        <div class="input-group">
                          <input type="text" class="form-control bg-white" :placeholder="__('Search')+'...'" aria-describedby="search" v-model="filters.search" @keydown.enter="search">
                          <button type="button" class="btn btn-secondary" :title="__('Search')" @click="search"><search-icon /></button>
                        </div>
                    </div>

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
                                <tr v-for="item in posts" class="">
                                    <td class="table-data flex space-x-2">
                                        <a :href="editLink(item)">{{item.title}}</a>
                                        <a v-if="item.status === 'published'" :href="showLink(item)" target="_blank"><i class="pi pi-external-link text-gray-500 text-sm"></i></a>
                                    </td>
                                    <td class="table-data">
                                        {{item.category}}
                                    </td>
                                    <td class="table-data">
                                        {{__(item.status)}}
                                    </td>
                                    <td class="table-data">
                                        {{item.likes - item.dislikes}}
                                    </td>
                                    <td class="table-data">
                                        {{formatDate(item.created_at)}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <div><button v-if="filters.page > 1" type="button" class="btn btn-secondary" @click="updatePage(-1)">
                            <div class="flex items-center space-x-2">
                                <prev-icon />
                                <span>Prev</span>
                            </div>
                        </button></div>
                        <div><button v-if="filters.page < lastPage" type="button" class="btn btn-secondary" @click="updatePage(1)">
                            <div class="flex items-center space-x-2">
                                <span>Next</span>
                                <next-icon />
                            </div>
                        </button></div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import "moment/locale/cs";
import SearchIcon from '../../UI/vue/Icons/search';
import PrevIcon from '../../UI/vue/Icons/prev';
import NextIcon from '../../UI/vue/Icons/next';
import AdminMenu from './menu';

export default {
    components: { SearchIcon, PrevIcon, NextIcon, AdminMenu },
    data() {
        return {
            headers: [
                this.__('Title'),
                this.__('Category'),
                this.__('Status'),
                this.__('Likes'),
                this.__('Created'),
            ],
            posts: [],
            filters: {
                status: '',
                search: '',
                page: 1,
            },
            lastPage: 1,
        }
    },
    methods: {
        loadData() {
            axios.get('/api/posts/list', { params: this.filters })
            .then(({data}) => {
                this.posts = data.data;
                this.filters.page = data.current_page;
                this.lastPage = data.last_page;
            });
        },
        editLink(post) {
            return `/posts/${post.id}/edit`;
        },
        showLink(post) {
            return `/wiki/${post.slug}/`;
        },
        formatDate(date) {
            return moment(date).fromNow();
        },
        updateStatus(status) {
            this.filters.page = 1;
            this.filters.status = status;
            this.loadData();
        },
        updatePage(increment) {
            this.filters.page += increment;
            this.loadData();
        },
        search() {
            this.filters.page = 1;
            this.loadData();
        }
    },
    mounted() {
        this.loadData();
    }
}
</script>
