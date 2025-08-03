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
        title: 'Sedes',
        href: '/sedes',
    },
    {
        title: 'Formulario',
        href: '/sedes/create',
    }
];

interface Sede {
    name: string;
    [key: string]: string; // Para que useForm acepte el tipo
}

const form = useForm<Sede>({
    name: '',
});

function storeSede() {
    form.post('/sedes', {
        onSuccess: () => {
            alert('Sede creada exitosamente');
            form.reset();
        },
        onError: () => {
            alert('Error al crear sede, revisa los datos');
        },
    });
}
</script>

<template>

    <Head title="Formulario Sede" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="h-full rounded-xl p-4 relative">
            <Card>
                <template #title>Registrar Sede</template>
                <template #content>
                    <div class="flex gap-4 flex-col items-center justify-center">
                        <div class="flex flex-col gap-6 w-full">
                            <p class="m-0">
                                En esta sección puedes registrar una nueva sede.
                            </p>
                            <div class="grid grid-cols-12 gap-4 w-full">
                                <div class="col-span-6">
                                    <IftaLabel>
                                        <InputText id="name" v-model="form.name" class="w-full" />
                                        <label for="name">Nombre de la sede</label>
                                    </IftaLabel>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end items-center w-full">
                            <div class="flex gap-4">
                                <Button label="Crear sede" icon="pi pi-building" severity="secondary" @click="storeSede"
                                    :loading="form.processing" />
                                <Link href="/sedes">
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
