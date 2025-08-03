<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import IftaLabel from 'primevue/iftalabel';
import InputText from 'primevue/inputtext';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Activos',
        href: '/activos',
    },
    {
        title: 'Formulario',
        href: '/activos/create',
    }
];

interface Activo {
    name: string;
    serie: string;
    [key: string]: string;
}

const form = useForm<Activo>({
    name: '',
    serie: '',
});

function storeActivo() {
    form.post('/activos', {
        onSuccess: () => {
            alert('Activo creado exitosamente');
            form.reset();
        },
        onError: () => {
            alert('Error al crear activo. Revisa los datos.');
        },
    });
}
</script>

<template>

    <Head title="Formulario Activo" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="h-full rounded-xl p-4 relative">
            <Card>
                <template #title>Registrar Activo</template>
                <template #content>
                    <div class="flex gap-4 flex-col items-center justify-center">
                        <div class="flex flex-col gap-6 w-full">
                            <p class="m-0">
                                En esta sección puedes registrar un nuevo activo.
                            </p>
                            <div class="grid grid-cols-12 gap-4 w-full">
                                <div class="col-span-6">
                                    <IftaLabel>
                                        <InputText id="name" v-model="form.name" class="w-full" />
                                        <label for="name">Nombre del activo</label>
                                    </IftaLabel>
                                </div>
                                <div class="col-span-6">
                                    <IftaLabel>
                                        <InputText id="serie" v-model="form.serie" class="w-full" />
                                        <label for="serie">Serie del activo</label>
                                    </IftaLabel>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end items-center w-full">
                            <div class="flex gap-4">
                                <Button label="Crear Activo" icon="pi pi-box" severity="secondary" @click="storeActivo"
                                    :loading="form.processing" />
                                <Link href="/activos">
                                <Button label="Cancelar" icon="pi pi-times" severity="secondary" />
                                </Link>
                            </div>
                        </div>
                    </div>
                </template>
            </Card>
        </div>
    </AppLayout>
</template>
