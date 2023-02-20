<template>
    <div class="card">
        <div class="card-body">
            <div class="field">
                <label class="font-bold" for="search">{{ __('Enter what keyword should be included in title') }}:</label>
                <div class="p-inputgroup">
                    <InputText class="w-full" id="search" type="text" v-model="search" @keydown.enter="submit" />
                    <Button icon="pi pi-search" class="p-button-secondary" @click="submit" />
                </div>
            </div>
            <div v-if="result.length" class="mt-2">
                <label class="font-bold" for="selected">{{ __('Select title to continue with') }}:</label>
                <div v-for="item in result" class="flex items-center space-x-2">
                    <RadioButton name="selected" :value="item" v-model="selected" @input="select" />
                    <div>{{ item }}</div>
                </div>
            </div>
            <div v-if="post.title" class="mt-2">
                <label class="font-bold" for="selected">{{ __('Update title if needed') }}:</label>
                <input type="text" class="form-control" v-model="post.title" />
            </div>
        </div>
    </div>
</template>

<script>
import parseJsonStream from '../../../UI/vue/stream.js';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import RadioButton from 'primevue/radiobutton';

export default {
    components: { InputText, Button, RadioButton },
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
            search: '',
            content: '',
            result: [],
            selected: '',
        }
    },
    methods: {
        parseJsonStream,
        submit() {
            if (this.search && !this.running) {
                this.generate(this.__('Write 5 article titles containing ":search" in ordered list format', { search: this.search }));
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
                            this.parse();
                        } else {
                            this.running = false;
                        }
                    }
                });
        },
        parse() {
            this.result = this.content.split("\n").map(item => item.replace(/^\d+\.\s/, ""));
        },
        select(title) {
            this.post.title = title.charAt(0).toUpperCase() + title.slice(1).toLowerCase();
        }
    },
}
</script>
