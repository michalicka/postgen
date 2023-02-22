<template>
    <Dialog :visible="true" :style="{width: '640px'}" :header="__('Insert link')" :modal="true" class="p-fluid" @update:visible="hideDialog">
        <div class="h-80">
            <Dropdown ref="input" v-model="link" :options="links" optionLabel="code" :editable="true" :filter="true" :showClear="true">
                <template #option="slotProps">
                    <div class="text-sm flex flex-col">
                        <div class="font-bold">{{display(slotProps.option.name)}}</div>
                        <div>{{slotProps.option.code}}</div>
                    </div>
                </template>
            </Dropdown>
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
    methods: {
        display(name) {
            return name.slice(0, 3).join(', ');
        },
        hideDialog() {
            this.$emit('hide');
        },
        submit(item) {
            this.$emit('input', this.link.code || this.link);
        }
    },
}
</script>
