<template>
    <div>
        <Breadcrumb class="mb-4" :model="items" position="bottom">
            <template #icon="{ item }">
                <a :href="item.url">{{item.label}}</a>
            </template>
        </Breadcrumb>
    </div>
</template>

<script>
import Breadcrumb from 'primevue/breadcrumb';

export default {
    components: { Breadcrumb },
    data() {
        return {
            items: [
                {
                    label: 'Articles',
                    icon: 'pi pi-fw pi-home',
                    command: () => {
                        window.location = '/admin';
                    }
                },
                {
                    label: 'Sites',
                    icon: 'pi pi-fw pi-link',
                    command: () => {
                        window.location = '/sites';
                    }
                },
            ]
        }
    },
}
</script>
