<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { ref } from 'vue';

interface Sede {
    id: number;
    name: string;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Sedes',
        href: '/sedes',
    }
];

const page = usePage();
const sedes = ref<Sede[]>(page.props.sedes as Sede[]);
</script>

<template>

    <Head title="Sedes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="h-full rounded-xl p-4 relative">
            <Card>
                <template #title>Sedes</template>
                <template #content>
                    <p class="m-0">
                        En esta sección puedes visualizar tus sedes.
                    </p>

                    <DataTable :value="sedes" paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]"
                        tableStyle="min-width: 30rem">
                        <template #header>
                            <div class="text-end pb-4">
                                <Link href="/sedes/create">
                                <Button label="Nueva sede" icon="pi pi-building" size="small" severity="secondary" />
                                </Link>
                            </div>
                        </template>

                        <Column field="name" header="Nombre" style="width: 100%"></Column>
                    </DataTable>
                </template>
            </Card>
        </div>
    </AppLayout>
</template>
