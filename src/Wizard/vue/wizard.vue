<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 flex flex-col space-y-4">
                <titles v-model="post" :api-url="apiUrl" :api-user="apiUser" />
                <post v-if="post.title" v-model="post" :api-url="apiUrl" :api-user="apiUser" @input="(v) => post.content = v" />
                <tags v-if="post.content" v-model="post" :api-url="apiUrl" :api-user="apiUser" />
                <category v-if="post.tags.length" v-model="post" :options="categories" />
                <cover v-if="post.category" v-model="post" />
                <store v-if="post.content" v-model="post" />
            </div>
        </div>
    </div>
</template>

<script>
import Titles from './steps/titles';
import Post from './steps/post';
import Tags from './steps/tags';
import Category from './steps/category';
import Cover from './steps/cover';
import Store from './steps/store';

export default {
    components: { Titles, Post, Tags, Category, Cover, Store },
    props: {
        apiUrl: {
            type: String,
            required: true,
        },
        apiUser: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            categories: [],
            post: {
                title: '',
                content: '',
                status: 'draft',
                tags: [],
                category: '',
                image: '',
            },
        }
    },
    methods: {
        loadData() {
            axios.get(`/api/meta/dropdowns`)
                .then(({data}) => {
                    this.categories = data.dropdowns.categories;
                });
        },
    },
    mounted() {
        this.loadData();
    }
}
</script>
