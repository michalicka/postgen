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
                            <div class="p-inputgroup">
                                <Button :label="`${hostname}/wiki/`" class="p-button-secondary p-button-text" />
                                <InputText class="w-full" id="slug" type="text" v-model="post.slug" />
                                <Button class="p-button-secondary">
                                    <a class="no-underline text-white" :href="`/wiki/${post.slug}/`" target="_blank" @click.prevent="preview">
                                        <i class="pi pi-external-link"></i>
                                    </a>
                                </Button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">{{ __('Text') }}:</label>
                            <Editor ref="content" id="content" v-model="post.content">
                                <template #toolbar>
                                    <span class="ql-formats">
                                        <button class="ql-header" value="1" type="button"></button>
                                        <button class="ql-header" value="2" type="button"></button>
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-bold" type="button"></button>
                                        <button class="ql-italic" type="button"></button>
                                        <button class="ql-underline" type="button"></button>
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-list" value="ordered" type="button"></button>
                                        <button class="ql-list" value="bullet" type="button"></button>
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-link" type="button"></button>
                                        <button class="ql-blockquote" type="button"></button>
                                        <button class="ql-code-block" type="button"></button>
                                    </span>
                                    <span class="ql-formats">
                                        <button class="ql-clean" type="button"></button>
                                    </span>
                                </template>
                            </Editor>
                        </div>
                        <div class="mb-3">
                            <label for="title" class="form-label">{{ __('Tags') }}:</label>
                            <Chips class="w-full" v-model="post.tags">
                                <template #chip="slotProps">
                                    <div>
                                        <span class="text-sm">{{slotProps.value}}</span>
                                    </div>
                                </template>
                            </Chips>
                        </div>
                        <div class="flex w-full justify-between">
                            <a class="no-underline" href="/admin">
                                <Button :label="__('Back')" icon="pi pi-angle-double-left" iconPos="left" class="p-button-secondary" />
                            </a>
                            <button type="button" class="btn btn-primary" @click="update">{{ __('Update') }}</button>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">{{ __('Published date') }}:</label>
                            <input type="datetime-local" class="form-control" v-model="post.published_at">
                        </div>
                        <div class="flex justify-between w-full">
                            <button type="button" class="btn btn-danger" @click="remove">{{ __('Delete') }}</button>
                            <button v-if="post.status !== 'published'" type="button" class="btn btn-success" @click="publish">{{ __('Publish') }}</button>
                        </div>
                    </div>
                </div>
                <div class="card mt-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">{{ __('Category') }}:</label><br />
                            <Dropdown class="w-full" v-model="post.category" :options="categories" optionLabel="name" :editable="true"/>
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
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Dropdown from 'primevue/dropdown';
import Editor from 'primevue/editor';
import Chips from 'primevue/chips';

export default {
    components: { InputText, Button, Dropdown, Editor, Chips },
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
    computed: {
        hostname() {
            return window.location.origin;
        }
    },
    methods: {
        loadData() {
            axios.get(`/api/posts/${this.id}/get`)
                .then(({data}) => {
                    this.post = data.post;
                    this.categories = data.dropdowns.categories;
                    this.post.published_at = this.post.published_at ? moment(this.post.published_at).format('YYYY-MM-DD hh:mm:ss') : '';
                    this.resize();
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
        preview() {
            window.open(`/posts/${this.post.id}/preview`);
        },
        resize() {
            this.$nextTick(() => {
                const editor = document.querySelector('.ql-editor');
                editor.style.height = 'auto';
                editor.style.height = (editor.scrollHeight + 10) + 'px';
            });
        },
    },
    mounted() {
        this.loadData();
        this.$nextTick(() => {
            this.resize();
            document.querySelector('.ql-editor').addEventListener('keyup', () => {
                this.resize();
            });
            document.querySelector('.p-chips .p-inputtext').style.width = "100%";
        });
    }
}
</script>
