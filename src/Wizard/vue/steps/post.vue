<template>
    <div class="card">
        <div class="card-body">
            <div class="field">
                <label class="font-bold" for="search">{{ __('Select length of content') }}:</label><br />
                <div class="btn-group" role="group">
                    <template v-for="item in sizes">
                        <input type="radio" class="btn-check" name="size" :id="`size_${item.code}`" autocomplete="off" :checked="size === item.code">
                        <label class="btn btn-outline-primary" :for="`size_${item.code}`" @click="updateSize(item.code)">{{ __(item.name) }}</label>
                    </template>
                </div>
            </div>
            <div class="mt-2">
                <textarea ref="content" id="content" :style="style" class="form-control" v-model="post.content" @keydown="resize"></textarea>
            </div>
        </div>
    </div>
</template>

<script>
import parseJsonStream from '../../../UI/vue/stream.js';

export default {
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
            height: 24,
            size: null,
            sizes: [
                { name: '50 znaků', code: 50 },
                { name: '100 znaků', code: 100 },
                { name: '500 znaků', code: 500 },
                { name: '1000 znaků', code: 1000 },
                { name: '1500 znaků', code: 1500 },
                { name: '2000 znaků', code: 2000 },
            ],
        }
    },
    computed: {
        style() {
            return `height: ${this.height}px;overflow-y:hidden;`;
        }
    },
    methods: {
        parseJsonStream,
        resize() {
            this.height = document.getElementById("content").scrollHeight;
        },
        updateSize(size) {
            this.size = size;
            this.submit();
        },
        submit() {
            if (this.size && !this.running) {
                this.generate(this.__('White an article with minimum length :size on subject ":title"', {
                    size: this.size,
                    title: this.post.title
                }));
            }
        },
        generate(q) {
            this.post.content = '';
            fetch(`${window._config.api_url}?q=${encodeURIComponent(q)}&userid=${window._config.api_user}`)
                .then(async (response) => {
                    this.running = true;
                    for await (const chunk of this.parseJsonStream(response.body)) {
                        this.model = chunk.model || '';
                        if (!Array.isArray(chunk) && !chunk.choices[0].finish_reason) {
                            this.post.content = `${this.post.content}${chunk.choices[0].text || ''}`.trimStart();
                            this.resize();
                        } else {
                            this.running = false;
                        }
                    }
                });
        },
    },
}
</script>
