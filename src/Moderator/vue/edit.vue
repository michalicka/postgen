<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div v-if="post" class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">{{ __('Title') }}:</label>
                            <input type="text" class="form-control" v-model="post.title">
                        </div>
                        <div class="mb-3">
                            <label for="slug" class="form-label">{{ __('Slug') }}:</label>
                            <input type="text" class="form-control" v-model="post.slug">
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">{{ __('Text') }}:</label>
                            <textarea rows="15" class="form-control" v-model="post.content" />
                        </div>
                        <div class="flex w-full justify-between">
                            <button type="button" class="btn btn-danger" @click="remove">{{ __('Delete') }}</button>
                            <button type="button" class="btn btn-primary" @click="update">{{ __('Update') }}</button>
                            <button v-if="post.status !== 'published'" type="button" class="btn btn-success" @click="publish">{{ __('Publish') }}</button>
                        </div>
                    </div>
                </div>

                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb mt-4">
                    <li class="breadcrumb-item active" aria-current="page"><a href="/admin">{{ __('Back') }}</a></li>
                  </ol>
                </nav>

            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">{{ __('Published date') }}:</label>
                            <input type="datetime-local" class="form-control" v-model="post.published_at">
                        </div>
                        <div class="mb-3">
                            <label for="title" class="form-label">{{ __('Category') }}:</label><br />
                            <Dropdown class="w-full" v-model="post.category" :options="categories" optionLabel="name" :editable="true"/>
                        </div>
                        <div class="mb-3">
                            <label for="title" class="form-label">{{ __('Tags') }}:</label>
                            <input type="text" class="form-control" v-model="post.tags">
                        </div>
                        <div class="mb-3">
                            <label for="title" class="form-label">{{ __('Image') }}:</label>
                            <input type="text" class="form-control" v-model="post.image">
                            <img v-if="post.image" :src="post.image" class="w-full rounded mt-2" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment'
import Dropdown from 'primevue/dropdown';

export default {
    components: { Dropdown },
    props: {
        id: {
            type: Number,
            required: true,
        }
    },
    data() {
        return {
            post: {},
            categories: [],
        }
    },
    methods: {
        loadData() {
            axios.get(`/api/posts/${this.id}/get`)
                .then(({data}) => {
                    this.post = data.post;
                    this.categories = data.dropdowns.categories;
                    this.post.published_at = this.post.published_at ? moment(this.post.published_at).format('YYYY-MM-DD hh:mm:ss') : '';
                });
        },
        update() {
            axios.post(`/api/posts/${this.id}/update`, {
                    category: this.post.category,
                    title: this.post.title,
                    slug: this.post.slug,
                    content: this.post.content,
                    published_at: this.post.published_at,
                    tags: this.post.tags,
                    image: this.post.image,
                })
                .then(({data}) => {
                    this.$toast.success(this.__('Post updated'));
                    this.loadData();
                });
        },
        remove() {
            axios.post(`/api/posts/${this.id}/remove`)
                .then(({data}) => {
                    this.$toast.success(this.__('Post removed'));
                    window.location = '/admin';
                });
        },
        publish() {
            axios.post(`/api/posts/${this.id}/publish`, {
                    category: this.post.category,
                    title: this.post.title,
                    slug: this.post.slug,
                    content: this.post.content,
                    published_at: this.post.published_at,
                    tags: this.post.tags,
                    image: this.post.image,
                })
                .then(({data}) => {
                    this.$toast.success(this.__('Post published'));
                    window.location = '/admin';
                });
        },
        formatDate(date) {
            return moment(date);
        },
    },
    mounted() {
        this.loadData();
    }
}
</script>
