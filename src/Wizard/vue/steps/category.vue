<template>
    <div class="card">
        <div class="card-body">
            <div class="field">
                <label class="font-bold" for="search">{{ __('Select or write article category') }}:</label><br />
                <Dropdown class="w-full" v-model="post.category" :options="options" optionLabel="name" :editable="true"/>
            </div>
        </div>
    </div>
</template>

<script>
import Dropdown from 'primevue/dropdown';

export default {
    components: { Dropdown },
    props: {
        value: {
            type: Object,
            required: true,
        },
        options: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            post: this.value,
        }
    },
    methods: {
    },
}
</script>
