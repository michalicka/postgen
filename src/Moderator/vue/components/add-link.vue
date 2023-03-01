<template>
    <Dialog :visible="true" :style="{width: '640px'}" :header="__('Insert link')" :modal="true" class="p-fluid" @update:visible="hideDialog">
        <div class="h-80">
            <Dropdown ref="input" v-model="link" :options="links" optionLabel="code" :editable="true" :filter="true" :showClear="true" class="mb-4">
                <template #indicator>
                    <span id="selectLinkOverlay" class="p-dropdown-trigger-icon pi pi-chevron-down"></span>
                </template>
                <template #option="slotProps">
                    <div class="text-sm flex flex-col">
                        <div class="font-bold">{{display(slotProps.option.name)}}</div>
                        <div>{{slotProps.option.code}}</div>
                    </div>
                </template>
            </Dropdown>
            <div v-for="option in top5" class="text-sm flex flex-col py-1 mx-2 px-2 cursor-pointer hover:bg-gray-200 rounded" @click="submit(option)">
                <div class="font-bold">{{display(option.name)}}</div>
                <div>{{option.code}}</div>
            </div>
        </div>
        <template #footer>
            <Button :label="__('Close')" icon="pi pi-times" class="p-button-text" @click="hideDialog"/>
            <Button :label="__('Insert')" icon="pi pi-check" class="p-button-text" @click="submit"/>
        </template>
    </Dialog>
</template>
<script>
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import Dropdown from 'primevue/dropdown';

export default {
    components: { Dialog, Button, Dropdown },
    props: {
        links: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            link: '',
        }
    },
    computed: {
        top5() {
            return _.take(this.links, 5);
        }
    },
    methods: {
        display(name) {
            return name.slice(0, 3).join(', ');
        },
        hideDialog() {
            this.$emit('hide');
        },
        submit(item) {
            this.$emit('input', item.code || this.link.code || this.link);
        },
    },
    mounted() {
        this.$nextTick(() => {
            document.querySelector('.p-dialog .p-inputtext').focus();
        });
    }
}
</script>
