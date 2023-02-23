<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div v-if="post" class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label font-bold">{{ __('Title') }}:</label>
                            <input type="text" class="form-control" v-model="post.title">
                        </div>
                        <div class="mb-3">
                            <label for="slug" class="form-label font-bold">{{ __('Slug') }}:</label>
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
                            <label for="content" class="form-label font-bold">{{ __('Text') }}:</label>
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
                            <label for="title" class="form-label font-bold">{{ __('Tags') }}:</label>
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
                            <Button :label="__('Update')" class="p-button-primary bg-blue-600" :loading="loading" @click="update" />
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label font-bold">{{ __('Publish to') }}:</label>
                            <div class="field-checkbox">
                                <Checkbox inputId="site0" name="site" :value="0" v-model="publish_to" />
                                <label for="site0">{{ __('PostGen') }}</label>
                            </div>
                            <div class="field-checkbox" v-for="item in dropdowns.sites">
                                <Checkbox :inputId="`site${item.code}`" name="site" :value="item.code" v-model="publish_to" />
                                <label :for="`site${item.code}`">{{ item.name }}</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label font-bold">{{ __('Published date') }}:</label>
                            <input type="datetime-local" class="form-control" v-model="post.published_at">
                        </div>
                        <div class="flex justify-between w-full">
                            <Button :label="__('Delete')" class="p-button-danger" :loading="loading" @click="remove" />
                            <Button v-if="publish_to.length" :label="__('Publish')" class="p-button-success" :loading="loading" @click="publish" />
                        </div>
                    </div>
                </div>
                <div class="card mt-4">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label font-bold">{{ __('Category') }}:</label><br />
                            <Dropdown class="w-full" v-model="post.category" :options="dropdowns.categories" optionLabel="name" :editable="true"/>
                        </div>
                        <div class="mb-3">
                            <label for="title" class="form-label font-bold">{{ __('Image') }}:</label>
                            <input type="text" class="form-control" v-model="post.image">
                            <img v-if="post.image" :src="post.image" class="w-full rounded mt-2" />
                        </div>
                    </div>
                </div>
            </div>

            <links v-model="dropdowns.articles" />
            <add-link v-if="addLinkDialog" :links="dropdowns.links" @input="addLink" @hide="addLinkDialog = false" />

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
import Checkbox from 'primevue/checkbox';
import Links from './components/links';
import AddLink from './components/add-link';

export default {
    components: { InputText, Button, Dropdown, Editor, Chips, Checkbox, Links, AddLink },
    props: {
        id: {
            type: Number,
            required: true,
        }
    },
    data() {
        return {
            loading: true,
            post: {},
            publish_to: [],
            dropdowns: {
                sites: [],
                articles: [],
                categories: [],
                links: [],
            },
            selection: null,
            addLinkDialog: false,
        }
    },
    computed: {
        hostname() {
            return window.location.origin;
        }
    },
    methods: {
        loadData() {
            this.loading = true;
            axios.get(`/api/posts/${this.id}/get`)
                .then(({data}) => {
                    this.post = data.post;
                    this.dropdowns = data.dropdowns;
                    if (this.post.published_at) this.publish_to.push(0);
                    this.publish_to.push(..._.map(this.dropdowns.articles, (item) => item.site_id));
                    this.post.published_at = this.post.published_at ? moment(this.post.published_at).format('YYYY-MM-DD hh:mm:ss') : '';
                    this.resize();
                })
                .finally(() => { this.loading = false });
        },
        update() {
            this.loading = true;
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
                })
                .finally(() => { this.loading = false });
        },
        remove() {
            this.loading = true;
            axios.post(`/api/posts/${this.id}/remove`)
                .then(({data}) => {
                    this.$toast.success(this.__('Post removed'));
                    window.location = '/admin';
                })
                .finally(() => { this.loading = false });
        },
        publish() {
            this.loading = true;
            axios.post(`/api/posts/${this.id}/publish`, {
                    category: this.post.category,
                    title: this.post.title,
                    slug: this.post.slug,
                    content: this.post.content,
                    published_at: this.post.published_at,
                    tags: this.post.tags,
                    image: this.post.image,
                    publish_to: this.publish_to,
                })
                .then(({data}) => {
                    this.$toast.success(this.__('Post published'));
                    this.loadData();
                })
                .finally(() => { this.loading = false });
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
        addLink(url) {
            const quill = this.$refs.content.quill;
            quill.theme.tooltip.edit('link', url);
            quill.theme.tooltip.save();
            this.addLinkDialog = false;
        }
    },
    mounted() {
        this.loadData();
        this.$nextTick(() => {
            this.resize();
            document.querySelector('.ql-editor').addEventListener('keyup', () => {
                this.resize();
            });
            document.querySelector('.p-chips .p-inputtext').style.width = "100%";

            document.querySelector('.ql-link').addEventListener('click', (e) => {
                e.preventDefault();
                const quill = this.$refs.content.quill;
                this.selection = quill.getSelection(true);
                if (this.selection && this.selection.length) {
                    this.addLinkDialog = true;
                }
            });
        });
    }
}
</script>
