<template>
    <div class="card">
        <div class="card-body">
            <div class="field">
                <label class="font-bold" for="search">{{ __('Select number of tags to extract') }}:</label><br />
                <div class="btn-group" role="group">
                    <template v-for="item in sizes">
                        <input type="radio" class="btn-check" name="size" :id="`size_${item.code}`" autocomplete="off" :checked="size === item.code">
                        <label class="btn btn-outline-primary" :for="`size_${item.code}`" @click="updateSize(item.code)">{{ __(item.name) }}</label>
                    </template>
                </div>
            </div>
            <div v-if="size" class="mt-2">
                <input type="text" class="form-control" v-model="result" @change="update" />
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
            size: null,
            sizes: [
                { name: '2 tags', code: 2 },
                { name: '3 tags', code: 3 },
                { name: '4 tags', code: 4 },
                { name: '5 tags', code: 5 },
                { name: '6 tags', code: 6 },
            ],
            result: '',
        }
    },
    methods: {
        parseJsonStream,
        updateSize(size) {
            this.size = size;
            this.submit();
        },
        submit() {
            if (this.size && !this.running) {
                this.generate(this.__("Extract minimum of :size and separate whem by comma from article \n\n:content", {
                    size: _.find(this.sizes, { code: this.size }).name,
                    content: this.post.content
                }));
            }
        },
        generate(q) {
            this.result = '';
            fetch(`${window._config.api_url}?q=${encodeURIComponent(q)}&userid=${window._config.api_user}`)
                .then(async (response) => {
                    this.running = true;
                    for await (const chunk of this.parseJsonStream(response.body)) {
                        this.model = chunk.model || '';
                        if (!Array.isArray(chunk) && !chunk.choices[0].finish_reason) {
                            this.result = `${this.result}${chunk.choices[0].text || ''}`.trimStart();
                            this.update();
                        } else {
                            this.running = false;
                            this.result = this.post.tags.join(', ');
                        }
                    }
                });
        },
        update() {
            Vue.set(this.post, 'tags', this.result.split(',').map(
                tag => tag.trim().replace(/\.+$/, '').replace(/^Tags: /, '').replace(/^#/, '').replace('_', '').toLowerCase()
            ));
        }
    },
}
</script>
