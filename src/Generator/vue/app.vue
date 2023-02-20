<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">

                        <div class="mb-3">
                            <label for="title" class="form-label">{{ __('Enter question or task') }}:</label>
                            <div class="input-group">
                              <input ref="title" id="title" type="text" class="form-control" v-model="title" @keydown.enter="submit">
                              <button type="button" class="btn btn-secondary" :title="__('Send')" @click="submit"><arrow /></button>
                            </div>
                        </div>

                        <div v-show="content" class="mb-3">
                          <label for="content" class="form-label">{{ __('Answer') }}:</label>
                          <textarea ref="content" id="content" :style="style" class="form-control" v-model="content" @keydown="resize"></textarea>
                        </div>

                        <rate v-if="id" v-model="id" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import parseJsonStream from '../../UI/vue/stream.js';
import Arrow from '../../UI/vue/Icons/arrow';
import Rate from './rate';

export default {
    components: { Arrow, Rate },
    data() {
        return {
            id: null,
            title: '',
            content: '',
            height: 24,
            model: '',
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
        submit() {
            const q=this.title || this.content;
            if (q) this.process(q);
        },
        clean() {
            this.id = null;
            this.content = '';
        },
        query(q) {
            return this.__('V češtine :query', { query: q });
        },
        process(q) {
            this.clean();
            fetch(`${window._config.api_url}?q=${encodeURIComponent(this.query(q))}&userid=${window._config.api_user}`)
                .then(async (response) => {
                    // response.body is a ReadableStream
                    for await (const chunk of this.parseJsonStream(response.body)) {
                        this.model = chunk.model || '';
                        if (!Array.isArray(chunk) && !chunk.choices[0].finish_reason) {
                            this.append(chunk.choices[0].text || '');
                        } else {
                            this.store();
                        }
                    }
                });
        },
        append(text) {
            this.content = `${this.content}${text}`.trimStart();
            this.resize();
        },
        store() {
            axios.post('/store', {
                title: this.title,
                content: this.content,
                model: this.model,
            }).then(({data}) => {
                this.id = data.id;
            });
        }
    },
    mounted() {
        this.$refs.title.focus();
        this.$nextTick(() => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('q')) {
                this.title = urlParams.get('q');
                this.process(this.title);
            }
        });
    }
}
</script>
