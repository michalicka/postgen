<template>
    <div>
        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-md-12">

                    <admin-menu />

                    <DataTable :value="sites" editMode="row" dataKey="id" :editingRows="editingRows" @row-edit-save="onRowEditSave" class="p-datatable-sm mt-4" stripedRows responsiveLayout="scroll" :loading="loading">
                        <template #empty>
                            {{ __('No sites found.') }}
                        </template>
                        <template #loading>
                            {{ __('Loading sites data. Please wait.') }}
                        </template>
                        <Column :header="__('Web name')">
                            <template #body="{data}">
                                <div v-if="selected === data.id" class="flex flex-col sm:flex-row space-x-2 justify-between">
                                    <div class="grid grid-col-1 sm:grid-cols-3 gap-3 justify-items-stretch w-full">
                                        <div class="p-float-label mt-4">
                                            <InputText id="name" type="text" v-model="data.name" />
                                            <label for="name">{{ __('Web name') }}</label>
                                        </div>
                                        <div class="p-float-label mt-4">
                                            <InputText id="api_url" type="text" v-model="data.api_url" aria-describedby="api_url-help" :class="{'p-invalid': !validUrl}" />
                                            <label for="api_url">{{ __('API Url') }}</label>
                                            <small v-if="!validUrl" id="api_url-help" class="p-error"><br />{{ __('Please enter valid URL') }}</small>
                                        </div>
                                        <div class="p-float-label mt-4">
                                            <InputText id="api_key" type="text" v-model="data.api_key" />
                                            <label for="api_key">{{ __('API Key') }}</label>
                                        </div>
                                    </div>
                                    <div class="flex flex-row justify-end space-x-2 mt-2">
                                        <button v-if="data.name && data.api_url && validUrl" class="btn btn-primary mt-4" :title="__('Store')" @click="update">
                                            <i class="pi pi-check"></i>
                                        </button>
                                        <button class="btn btn-danger mt-4" :title="__('Delete')" @click="remove">
                                            <i class="pi pi-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div v-else class="flex justify-between space-x-2">
                                    <div class="flex space-x-2">
                                        <div class="text-sm font-bold mt-2">{{data.name}}</div>
                                        <div class="text-sm text-gray-500 mt-2">{{ data.api_url }}</div>
                                    </div>
                                    <button class="btn btn-primary pt-2" @click="selected = data.id" :title="__('Edit')">
                                        <i class="pi pi-pencil"></i>
                                    </button>
                                </div>
                            </template>
                        </Column>
                    </DataTable>

                    <div v-if="selected === null" class="flex items-center justify-end mt-2">
                        <Button type="button" :label="__('Add')" icon="pi pi-plus" class="p-button-secondary" @click="add" />
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import "moment/locale/cs";
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import AdminMenu from './menu';

export default {
    components: { Button, InputText, DataTable, Column, AdminMenu },
    data() {
        return {
            loading: false,
            editingRows: [],
            selected: null,
            sites: [],
        }
    },
    computed: {
        validUrl() {
            const value = this.selected !== null ? _.find(this.sites, { id: this.selected }).api_url : '';
            if (!value) return true;
            try {
                new URL(value);
                return true;
            } catch (err) {
            }
            return false;
        }
    },
    methods: {
        loadData() {
            this.loading = true;
            axios.get('/api/sites/list')
            .then(({data}) => {
                this.sites = data;
            })
            .finally(() => {
                this.loading = false;
            });
        },
        add() {
            this.sites.push({ id: 0, name: '', api_url: '', api_key: ''});
            this.selected = 0;
        },
        update() {
            axios.post(`/api/sites/update`, _.find(this.sites, { id: this.selected }))
                .then(({data}) => {
                    this.selected = null;
                    this.$toast.success(this.__('Site updated'));
                    this.loadData();
                })
                .catch(() => {
                    this.$toast.error(this.__('Update failed'));
                });
        },
        remove() {
            _.remove(this.sites, (item) => item.id === this.selected);
            if (this.selected) {
                axios.post(`/api/sites/${this.selected}/remove`)
                    .then(({data}) => {
                        this.selected = null;
                        this.$toast.success(this.__('Site removed'));
                        this.loadData();
                    });
            } else {
                this.selected = null;
                this.$toast.success(this.__('Site removed'));
            }
        },
        onRowEditSave(event) {
            let { newData, index } = event;

            console.log(newData, index);
        },
    },
    mounted() {
        this.loadData();
    }
}
</script>

<style>

.p-float-label input.p-inputtext {
    width: 100%;
}

</style>
