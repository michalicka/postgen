<template>
    <div class="card">
        <div class="card-body">
            <div class="field">
                <div class="flex justify-between w-full mb-1">
                    <label class="font-bold" for="search">{{ __('Describe cover image') }}:</label>
                    <pixabay-logo class="h-6" />
                </div>
                <div class="p-inputgroup">
                    <InputText class="w-full" id="search" type="text" v-model="search" @keydown.enter="submit" />
                    <Button icon="pi pi-search" class="p-button-secondary" @click="submit" />
                </div>
            </div>
            <div v-if="list.length" class="flex overflow-x-scroll h-24 mt-2">
                <template v-for="image in list">
                    <img class="cursor-pointer" :src="image.previewURL" @click="select(image.largeImageURL)" />
                </template>
            </div>
            <div v-if="post.image" class="mt-2">
                <img class="rounded w-full" :src="post.image" />
            </div>
        </div>
    </div>
</template>

<script>
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import PixabayLogo from '../../../UI/vue/Logos/pixabay';

export default {
    components: { InputText, Button, PixabayLogo },
    props: {
        value: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            running: false,
            post: this.value,
            search: _.first(this.value.tags),
            list: [],
        }
    },
    methods: {
        submit() {
            if (this.search && !this.running) {
                this.preview();
            }
        },
        preview() {
            this.running = true;
            axios.get(`/api/image/preview`, { params: {
                q: this.search,
            }}).then(({data}) => {
                this.list = data.hits;
                if (!this.list.length) {
                    this.generate();
                }
            })
            .finally(() => {
                this.running = false;
            });
        },
        select(url) {
            this.post.image = url;
        },
        generate() {
            this.running = true;
            axios.post(`/api/image/generate`, {
                q: this.search,
            })
            .then(({data}) => {
                this.post.image = data.data[0].url;
            })
            .finally(() => {
                this.running = false;
            });
        }
    },
}
</script>
