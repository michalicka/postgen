<template>
    <div class="card">
        <div class="card-body">
            <div class="flex w-full justify-end">
                <Button :label="__('Store')" class="p-button-primary bg-blue-600" :loading="loading" @click="store" />
            </div>
        </div>
    </div>
</template>

<script>
import Button from 'primevue/button';

export default {
    components: { Button },
    props: {
        value: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            loading: false,
            post: this.value,
        }
    },
    methods: {
        store() {
            this.loading = true;
            axios.post(`/api/posts/store`, {
                    title: this.post.title,
                    content: this.post.content,
                    category: this.post.category,
                    tags: this.post.tags,
                    image: this.post.image,
                })
                .then(({data}) => {
                    this.$toast.success(this.__('Post stored'));
                    window.location = `/posts/${data.id}/edit`;
                })
                .finally(() => { this.loading = false });
        },
    },
}
</script>
