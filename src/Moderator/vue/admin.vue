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

                    <DataTable :value="posts" class="p-datatable-sm mt-4" stripedRows responsiveLayout="scroll" :loading="loading">
                        <template #empty>
                            {{ __('No posts found.') }}
                        </template>
                        <template #loading>
                            {{ __('Loading posts data. Please wait.') }}
                        </template>
                        <Column :header="__('Title')">
                            <template #body="{data}">
                                <div class="text-sm flex space-x-2">
                                    <a :href="editLink(data)">{{data.title}}</a>
                                    <a v-if="data.status === 'published'" :href="showLink(data)" target="_blank"><i class="pi pi-external-link text-gray-500 text-sm"></i></a>
                                </div>
                            </template>
                        </Column>
                        <Column :header="__('Category')">
                            <template #body="{data}">
                                <div class="text-sm text-gray-500">{{__(data.category)}}</div>
                            </template>
                        </Column>
                        <Column :header="__('Status')">
                            <template #body="{data}">
                                <Badge v-if="data.status === 'published'" :value="__(data.status)" severity="success"></Badge>
                                <Badge v-else :value="__(data.status)" severity="warning"></Badge>
                            </template>
                        </Column>
                        <Column :header="__('Likes')">
                            <template #body="{data}">
                                <like-icon v-if="data.likes + data.dislikes > 0" class="w-4 h-4 text-green-700" />
                                <dislike-icon v-if="data.likes + data.dislikes < 0" class="w-4 h-4 text-red-700" />
                            </template>
                        </Column>
                        <Column :header="__('Created')">
                            <template #body="{data}">
                                <div class="text-sm text-gray-500">{{ formatDate(data.created_at) }}</div>
                            </template>
                        </Column>
                    </DataTable>

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
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Badge from 'primevue/badge';
import SearchIcon from '../../UI/vue/Icons/search';
import PrevIcon from '../../UI/vue/Icons/prev';
import NextIcon from '../../UI/vue/Icons/next';
import LikeIcon from '../../UI/vue/Icons/like';
import DislikeIcon from '../../UI/vue/Icons/dislike';
import AdminMenu from './menu';

export default {
    components: { DataTable, Column, Badge, SearchIcon, PrevIcon, NextIcon, LikeIcon, DislikeIcon, AdminMenu },
    data() {
        return {
            loading: false,
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
            this.loading = true;
            axios.get('/api/posts/list', { params: this.filters })
            .then(({data}) => {
                this.posts = data.data;
                this.filters.page = data.current_page;
                this.lastPage = data.last_page;
            })
            .finally(() => {
                this.loading = false;
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
