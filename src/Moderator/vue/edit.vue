<template>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <nav aria-label="breadcrumb">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page"><a href="/home">Back</a></li>
                  </ol>
                </nav>

                <div v-if="post" class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Název:</label>
                            <input type="text" class="form-control" v-model="post.title">
                        </div>
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug:</label>
                            <input type="text" class="form-control" v-model="post.slug">
                        </div>
                        <div class="mb-3">
                            <label for="content" class="form-label">Text:</label>
                            <textarea rows="15" class="form-control" v-model="post.content" />
                        </div>
                        <div class="mb-3 flex w-full justify-between">
                            <button type="button" class="btn btn-danger" @click="remove">Delete</button>
                            <button type="button" class="btn btn-primary" @click="update">Update</button>
                            <button v-if="post.status !== 'publish'" type="button" class="btn btn-success" @click="publish">Publish</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        id: {
            type: Number,
            required: true,
        }
    },
    data() {
        return {
            post: null,
        }
    },
    methods: {
        loadData() {
            axios.get(`/api/posts/${this.id}/get`)
                .then(({data}) => {
                    this.post = data;
                });
        },
        update() {
            axios.post(`/api/posts/${this.id}/update`, {
                    title: this.post.title,
                    slug: this.post.slug,
                    content: this.post.content,
                })
                .then(({data}) => {
                    this.loadData();
                });
        },
        remove() {
            axios.post(`/api/posts/${this.id}/remove`)
                .then(({data}) => {
                    window.location = '/home';
                });
        },
        publish() {
            axios.post(`/api/posts/${this.id}/publish`, {
                    title: this.post.title,
                    slug: this.post.slug,
                    content: this.post.content,
                })
                .then(({data}) => {
                    window.location = '/home';
                });
        },
    },
    mounted() {
        this.loadData();
    }
}
</script>
