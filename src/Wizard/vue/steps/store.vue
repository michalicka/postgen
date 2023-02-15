<template>
    <div class="card">
        <div class="card-body">
            <div class="flex w-full justify-end">
                <button type="button" class="btn btn-primary" @click="store">{{ __('Store') }}</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        value: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            post: this.value,
        }
    },
    methods: {
        store() {
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
                });
        },
    },
}
</script>
