<template>
    <Dialog :visible="true" :style="{width: '960px'}" :header="__('Generate google ads')" :modal="true" class="p-fluid" @update:visible="hideDialog">
        <div class="mb-3">
            <label for="title" class="form-label font-bold">{{ __('Title') }}:</label>
            <div class="p-inputgroup">
                <InputText class="w-full" id="title" type="text" v-model="data.title" />
                <Button icon="pi pi-copy" class="p-button-secondary" :title="__('Copy')" @click="copyToClipboard(data.title)" />
            </div>
        </div>
        <div class="mb-3">
            <label for="link" class="form-label font-bold">{{ __('Link') }}:</label>
            <div class="p-inputgroup">
                <InputText class="w-full" id="link" type="text" v-model="data.link" />
                <Button icon="pi pi-copy" class="p-button-secondary" :title="__('Copy')" @click="copyToClipboard(data.link)" />
            </div>
        </div>
        <div>
            <label for="sharing" class="form-label font-bold">{{ __('Texts') }}:</label>
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
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';

export default {
    components: { Dialog, InputText, Button },
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
                { name: 'AdWords', code: 'adwords' },
            ],
            content: this.site ? this.value[this.site] : '',
        }
    },
    computed: {
        style() {
            return `height: ` + Math.max(24, this.height) + `px;overflow-y:hidden;`;
        },
        plainText() {
            let tmp = document.createElement("div");
            tmp.innerHTML = this.data.content;
            return tmp.textContent || tmp.innerText || "";
        }
    },
    methods: {
        parseJsonStream,
        resize() {
            this.height = document.getElementById("sharing").scrollHeight;
        },
        submit() {
            if (this.type && !this.content && !this.running) {
                this.generate(this.__("Article content is:\n\n:content\n\n---\n\nFor the above article text write:\n- 2 engaging ad copy for Google Ads in RSA (Responsive Search Ads) format\n- each ad contains 5 headlines (each headline has a maximum of 30 characters)\n- each ad contains 4 descriptions (each description has a maximum of 90 characters)\n- headline text of each ad contains words from artitle title \":title\"", {
                    content: this.plainText,
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
                            this.content = `${this.content}${chunk.choices[0].delta.content || chunk.choices[0].text || ''}`.trimStart();
                            this.resize();
                        } else {
                            this.running = false;
                            this.addLink();
                        }
                    }
                });
        },
        addLink() {
            this.content = this.content.replace(/[\!]/, '');
            this.$nextTick(() => {
                this.resize();
            });
        },
        hideDialog() {
            this.$emit('hide');
        },
        store() {
            this.copyToClipboard(this.content);
            axios.post(`/api/articles/${this.data.id}/social/${this.type}/store`, {
                content: this.content,
            }).then(({data}) => {
                this.data[this.type] = this.content;
                this.hideDialog();
            });
        },
        async copyToClipboard(text) {
            try {
                await navigator.clipboard.writeText(text);
                this.$toast.success(this.__('Text copied'));
            } catch($e) {
                this.$toast.danger(this.__('Cannot copy text'));
            }
        }
    },
    mounted() {
        this.$nextTick(() => {
            if (this.site && !this.content) this.submit();
            this.resize();
            document.getElementById("title").readOnly = true;
            document.getElementById("link").readOnly = true;
            this.$refs.sharing.focus();
        });
    }
}
</script>
