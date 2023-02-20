<template>
    <Dialog :visible="true" :style="{width: '640px'}" header="Generate social post" :modal="true" class="p-fluid" @update:visible="hideDialog">
        <div class="field flex justify-between">
            <div class="btn-group" role="group">
                <template v-for="item in types">
                    <input type="radio" class="btn-check" name="type" :id="`type_${item.code}`" autocomplete="off" :checked="type === item.code">
                    <label class="btn btn-outline-primary" :for="`type_${item.code}`" @click="updateType(item.code)">{{ __(item.name) }}</label>
                </template>
            </div>
            <Button icon="pi pi-refresh" class="p-button-sm p-button-secondary" @click="refresh"/>
        </div>
        <div v-if="type" class="mt-2">
            <textarea ref="sharing" id="sharing" :style="style" class="form-control" v-model="content" @keyup="resize"></textarea>
        </div>
        <template #footer>
            <Button :label="__('Close')" icon="pi pi-times" class="p-button-text" @click="hideDialog"/>
            <Button :label="__('Store')" icon="pi pi-check" class="p-button-text" @click="store"/>
        </template>
    </Dialog>
</template>
<script>
import parseJsonStream from '../../../UI/vue/stream.js';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';

export default {
    components: { Dialog, Button },
    props: {
        value: {
            type: Object,
            required: true,
        },
        site: {
            type: String,
            default: null,
        }
    },
    data() {
        return {
            running: false,
            data: this.value,
            height: 24,
            type: this.site,
            types: [
                { name: 'Twitter', code: 'twitter', size: 280 },
                { name: 'Facebook', code: 'facebook', size: 560 },
            ],
            content: this.site ? this.value[this.site] : '',
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
            this.height = document.getElementById("sharing").scrollHeight;
        },
        updateSize(size) {
            this.size = size;
            this.submit();
        },
        updateType(type) {
            this.type = type;
            this.content = this.data[this.type];
            this.submit();
        },
        refresh() {
            this.content = '';
            this.submit();
        },
        submit() {
            if (this.type && !this.content && !this.running) {
                this.generate(this.__("Write a :site post with a goal to read article with title \":title\". Maximum lenght of post should be :size characters. At the end add 3 tags starting with #.", {
                    site: _.find(this.types, { code: this.type }).name,
                    size: _.find(this.types, { code: this.type }).size,
                    title: this.data.title,
                }));
            }
        },
        generate(q) {
            this.content = '';
            fetch(`${window._config.api_url}?q=${encodeURIComponent(q)}&userid=${window._config.api_user}`)
                .then(async (response) => {
                    this.running = true;
                    for await (const chunk of this.parseJsonStream(response.body)) {
                        this.model = chunk.model || '';
                        if (!Array.isArray(chunk) && !chunk.choices[0].finish_reason) {
                            this.content = `${this.content}${chunk.choices[0].text || ''}`.trimStart();
                            this.resize();
                        } else {
                            this.running = false;
                            this.addLink();
                        }
                    }
                });
        },
        addLink() {
            const regexp = /https?:\/\/[\n\S]+/g;
            this.content = this.content.replace(regexp, "👉 " + this.data.link);
            if (!regexp.test(this.content)) this.content = this.content.replace("#", "👉 " + this.data.link + " #");
            this.$nextTick(() => {
                this.resize();
            });
        },
        hideDialog() {
            this.$emit('hide');
        },
        store() {
            axios.post(`/api/articles/${this.data.id}/social/${this.type}/store`, {
                content: this.content,
            }).then(({data}) => {
                this.data[this.type] = this.content;
                this.hideDialog();
            });
        }
    },
    mounted() {
        this.$nextTick(() => {
            if (this.site && !this.content) this.submit();
            this.resize();
            this.$refs.sharing.focus();
        });
    }
}
</script>
