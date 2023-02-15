<template>
    <div>
        <div class="container mt-4">
            <div class="row justify-content-center">
                <div class="col-md-12">

                    <admin-menu />

                    <div class="border-b border-gray-200 dark:border-dark-body mt-4">
                        <table class="rounded-table w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-neutral-700">
                                <tr>
                                    <th class="px-6 py-3 text-sm text-gray-500 dark:text-gray-200 text-left w-full">
                                        {{ __('Web name') }}
                                    </th>
                                    <th class="px-6 py-3 text-sm text-gray-500 dark:text-gray-200 text-right">
                                        {{ __('Action') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-dark-header divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="item in sites" :key="item.id" class="">
                                    <td class="table-data">
                                        <div v-if="selected === item.id" class="grid grid-col-1 sm:grid-cols-3 gap-3 justify-items-stretch">
                                            <div class="p-float-label mt-2">
                                                <InputText id="name" type="text" v-model="item.name" />
                                                <label for="name">{{ __('Web name') }}</label>
                                            </div>
                                            <div class="p-float-label mt-2">
                                                <InputText id="api_url" type="text" v-model="item.api_url" aria-describedby="api_url-help" :class="{'p-invalid': !validUrl}" />
                                                <label for="api_url">{{ __('API Url') }}</label>
                                                <small v-if="!validUrl" id="api_url-help" class="p-error"><br />{{ __('Please enter valid URL') }}</small>
                                            </div>
                                            <div class="p-float-label mt-2">
                                                <InputText id="api_key" type="text" v-model="item.api_key" />
                                                <label for="api_key">{{ __('API Key') }}</label>
                                            </div>
                                        </div>
                                        <div v-else class="flex space-x-2">
                                            <div class="text-black font-bold">{{item.name}}</div>
                                            <div>{{ item.api_url }}</div>
                                        </div>
                                    </td>
                                    <td class="table-data">
                                        <div v-if="selected === item.id" class="flex flex-col justify-end sm:flex-row space-x-0 sm:space-x-2 space-y-2 sm:space-y-0 mt-2">
                                            <button v-if="item.name && item.api_url && validUrl" class="btn btn-primary pt-2" :title="__('Store')" @click="update">
                                                <i class="pi pi-check"></i>
                                            </button>
                                            <button class="btn btn-danger pt-2" :title="__('Delete')" @click="remove">
                                                <i class="pi pi-times"></i>
                                            </button>
                                        </div>
                                        <div v-else class="flex justify-end">
                                            <button class="btn btn-primary pt-2" @click="selected = item.id" :title="__('Edit')">
                                                <i class="pi pi-pencil"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
import AdminMenu from './menu';

export default {
    components: { Button, InputText, AdminMenu },
    data() {
        return {
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
            axios.get('/api/sites/list')
            .then(({data}) => {
                this.sites = data;
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
        }
    },
    mounted() {
        this.loadData();
    }
}
</script>
