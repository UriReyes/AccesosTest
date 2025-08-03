<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { ref } from 'vue';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';

interface Visita {
    id: number;
    visitor_name: string;
    visitor_document: string | null;
    time_start: string;
    time_end: string;
    dates: string[];
    sede: { id: number; name: string };
    user: { id: number; name: string };
    status: string;
    qr_token: string;
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Visitas', href: '/visitas' },
    { title: 'Autorizar', href: '/visitas/autorizar' },
];

const page = usePage();
const visitas = ref<Visita[]>(page.props.visitas as Visita[]);

const form = useForm({
    visita_id: null as number | null,
    action: '',
});

const toast = useToast();
async function recargarVisitas() {
    const response = await fetch(route('api.visitas.pendientes'));
    if (response.ok) {
        const data = await response.json();
        visitas.value = data.visitas;
    }
}
function autorizar(visitaId: number) {
    if (!confirm('¿Confirmas autorizar esta visita?')) return;

    form.visita_id = visitaId;
    form.action = 'authorize';

    form.post(route('autorizar.visitas'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            toast.add({ severity: 'success', summary: 'Éxito', detail: 'Visita autorizada correctamente', life: 3000 });
            recargarVisitas();
        },
    });
}

function rechazar(visitaId: number) {
    if (!confirm('¿Confirmas rechazar esta visita?')) return;

    form.visita_id = visitaId;
    form.action = 'reject';

    form.post(route('autorizar.visitas'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            toast.add({ severity: 'warn', summary: 'Atención', detail: 'Visita rechazada correctamente', life: 3000 });
            recargarVisitas();
        },
    });
}
</script>

<template>

    <Head title="Autorizar Visitas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <Toast />
        <div class="h-full rounded-xl p-4 relative">
            <Card>
                <template #title>Autorizar Visitas Pendientes</template>
                <template #content>
                    <p class="m-0 mb-4">
                        Aquí puedes revisar y autorizar o rechazar las solicitudes de visita pendientes.
                    </p>

                    <DataTable :value="visitas" paginator :rows="5" :rowsPerPageOptions="[5, 10, 20]"
                        tableStyle="min-width: 40rem" emptyMessage="No hay visitas pendientes por autorizar.">
                        <Column field="visitor_name" header="Nombre Visitante" />
                        <Column header="Documento" :body="(data: Visita) => data.visitor_document ?? '-'" />
                        <Column header="Fechas" :body="(data: Visita) => data.dates.join(', ')" />
                        <Column field="sede.name" header="Sede" />
                        <Column field="user.name" header="Solicitante" />
                        <Column field="time_start" header="Hora Inicio" />
                        <Column field="time_end" header="Hora Fin" />

                        <Column header="Acciones" style="width: 180px">
                            <template #body="{ data }">
                                <Button label="Autorizar" text icon="pi pi-check" class="mr-2" severity="success"
                                    size="small"
                                    :loading="form.processing && form.visita_id === data.id && form.action === 'authorize'"
                                    @click="autorizar(data.id)" />
                                <Button label="Rechazar" text icon="pi pi-times" severity="danger" size="small"
                                    :loading="form.processing && form.visita_id === data.id && form.action === 'reject'"
                                    @click="rechazar(data.id)" />
                            </template>
                        </Column>
                    </DataTable>
                </template>
            </Card>
        </div>
    </AppLayout>
</template>
