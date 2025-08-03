<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { ref } from 'vue';

interface Activo {
    id: number;
    name: string;
    serie: string;
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Activos',
        href: '/activos',
    }
];

// Accede a los props enviados por Inertia desde Laravel
const page = usePage();
const activos = ref<Activo[]>(page.props.activos as Activo[]);
</script>

<template>

    <Head title="Activos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="h-full rounded-xl p-4 relative">
            <Card>
                <template #title>Activos</template>
                <template #content>
                    <p class="m-0">
                        En esta sección puedes visualizar tus activos.
                    </p>

                    <DataTable :value="activos" paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]"
                        tableStyle="min-width: 50rem">
                        <template #header>
                            <div class="text-end pb-4">
                                <Link href="/activos/create">
                                <Button label="Nuevo activo" icon="pi pi-box" size="small" severity="secondary" />
                                </Link>
                            </div>
                        </template>

                        <Column field="name" header="Nombre" style="width: 50%"></Column>
                        <Column field="serie" header="Serie" style="width: 50%"></Column>
                    </DataTable>
                </template>
            </Card>
        </div>
    </AppLayout>
</template>
