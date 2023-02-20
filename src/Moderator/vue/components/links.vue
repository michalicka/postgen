<template>
    <div v-if="value.length" class="mt-4">
        <DataTable :value="value" class="p-datatable-sm" responsiveLayout="scroll">
            <Column field="title" :header="__('Title')"></Column>
            <Column field="link" :header="__('Link')">
                <template #body="{data}">
                    <a :href="data.link" class="text-gray-500 no-underline" target="_blank">{{ data.link }}</a>
                </template>
            </Column>
            <Column field="published_at" :header="__('Published')">
                <template #body="{data}">
                    {{ formatDate(data.published_at) }}
                </template>
            </Column>
            <Column headerStyle="width: 4rem; text-align: center" bodyStyle="text-align: center; overflow: visible">
                <template #body="{data}">
                    <div class="flex space-x-2 flex-nowrap">
                        <Button type="button" icon="pi pi-twitter" @click="editSocial(data, 'twitter')"></Button>
                        <Button type="button" icon="pi pi-facebook" @click="editSocial(data, 'facebook')"></Button>
                    </div>
                </template>
            </Column>
        </DataTable>

        <social v-if="socialDialog" v-model="socialDialog" :site="socialSite" @hide="socialDialog = false" />
    </div>
</template>
<script>
import moment from 'moment'
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import Social from './social';

export default {
    components: { DataTable, Column, Button, Social },
    props: {
        value: {
            type: Array,
            required: true,
        }
    },
    data() {
        return {
            socialDialog: false,
            socialSite: null,
        }
    },
    methods: {
        editSocial(data, site) {
            this.socialDialog = data;
            this.socialSite = site;
        },
        formatDate(date) {
            return moment(date).fromNow();
        },
    }
}
</script>

<style>
table.p-datatable-table button.p-button {
    background-color: #007bff;
}
</style>
