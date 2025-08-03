<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import DatePicker from 'primevue/datepicker';
import IftaLabel from 'primevue/iftalabel';
import InputText from 'primevue/inputtext';
import Message from 'primevue/message';
import Select from 'primevue/select';
import { computed, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Visitas', href: '/visitas' },
    { title: 'Formulario', href: '/visitas/create' }
];

const page = usePage();

interface Activo {
    id: number;
    name: string;
    serie: string;
}

interface Sede {
    id: number;
    name: string;
}

const sedes = ref<Sede[]>(page.props.sedes as Sede[]);
const activos = ref<Activo[]>(page.props.activos as Activo[]);

const selectedActivo = ref<Activo | null>(null);
const selectedActivos = ref<Activo[]>([]);

const form = useForm({
    time_start: null as Date | null,
    time_end: null as Date | null,
    dates: [] as Date[] | null,
    sede_id: null as number | null,
    activos: [] as string[], // aquí guardarás los 'serie' de activos seleccionados
});
const datesValue = computed(() => {
    if (!form.dates) return [];

    const datesArray = Array.isArray(form.dates) ? form.dates : [form.dates];

    return datesArray
        .filter((d): d is Date => d instanceof Date && !isNaN(d.getTime())) // Filtra valores nulos o inválidos
        .map(d => d.toISOString());
});
const handleSelectActivo = () => {
    if (selectedActivo.value) {
        selectedActivos.value.push(selectedActivo.value);
        activos.value = activos.value.filter(a => a.serie !== selectedActivo.value!.serie);
        selectedActivo.value = null;
    }
};

const dropActivoFromList = (activoToRemove: Activo) => {
    activos.value.push(activoToRemove);
    selectedActivos.value = selectedActivos.value.filter(a => a.serie !== activoToRemove.serie);
};

function formatTime(date: Date | null): string | null {
    if (!date) return null;
    const hours = date.getHours().toString().padStart(2, '0');
    const minutes = date.getMinutes().toString().padStart(2, '0');
    return `${hours}:${minutes}`;
}

const submit = () => {
    // Actualiza el arreglo de series para enviar
    form.activos = selectedActivos.value.map(a => a.serie);

    // Convierte fechas a ISO strings para backend
    const payload = {
        ...form,
        time_start: form.time_start ? formatTime(form.time_start as Date) : null,
        time_end: form.time_end ? formatTime(form.time_end as Date) : null,
        dates: datesValue.value,
    };

    form.transform((data) => ({
        ...payload
    })).post('/visitas', {
        onSuccess: () => {
            // Opcional: resetear formulario o redireccionar
        },
    });
};
</script>

<template>

    <Head title="Registrar Nueva Visita" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="h-full rounded-xl p-4 relative">
            <Card>
                <template #title>Registrar Nueva Visita</template>
                <template #content>
                    <div class="flex gap-4 flex-col items-center justify-center w-full">
                        <p class="m-0">En esta sección puedes registrar una nueva solicitud.</p>
                        <div class="grid grid-cols-12 items-center justify-center gap-4 w-full">
                            <div class="col-span-4">
                                <IftaLabel>
                                    <DatePicker size="small" v-model="form.time_start" hourFormat="12" timeOnly fluid
                                        showIcon iconDisplay="input" class="w-full" />
                                    <label for="time_start">Hora Inicio</label>
                                </IftaLabel>
                            </div>
                            <div class="col-span-4">
                                <IftaLabel>
                                    <DatePicker size="small" v-model="form.time_end" hourFormat="12" timeOnly fluid
                                        showIcon iconDisplay="input" class="w-full" />
                                    <label for="time_end">Hora Fin</label>
                                </IftaLabel>
                            </div>
                            <div class="col-span-4">
                                <IftaLabel>
                                    <DatePicker size="small" v-model="form.dates" selectionMode="range"
                                        :manualInput="false" showIcon iconDisplay="input" class="w-full" />
                                    <label for="dates">Días que asistirá</label>
                                </IftaLabel>
                            </div>
                        </div>
                        <div class="grid grid-cols-12 items-center justify-center gap-4 mt-4 w-full">
                            <div class="col-span-12">
                                <IftaLabel>
                                    <Select v-model="form.sede_id" :options="sedes" filter optionLabel="name"
                                        optionValue="id" class="w-full min-h-14" showClear>
                                        <template #value="{ value, placeholder }">
                                            <div v-if="value" class="flex items-center gap-2">
                                                <i class="pi pi-building"></i>
                                                <div>{{sedes.find(s => s.id === value)?.name}}</div>
                                            </div>
                                            <span v-else>{{ placeholder }}</span>
                                        </template>
                                        <template #option="{ option }">
                                            <div class="flex items-center gap-2">
                                                <i class="pi pi-building"></i>
                                                <div>{{ option.name }}</div>
                                            </div>
                                        </template>
                                    </Select>
                                    <label for="sede_id">Selecciona la sede a visitar</label>
                                </IftaLabel>
                            </div>
                        </div>
                        <div class="grid grid-cols-12 items-center justify-center gap-4 mt-4 w-full">
                            <div class="col-span-6">
                                <IftaLabel>
                                    <Select v-model="selectedActivo" :options="activos" filter optionLabel="name"
                                        class="w-full min-h-14" showClear @change="handleSelectActivo"
                                        :key="activos.length" placeholder="Selecciona un activo">
                                        <template #value="{ value, placeholder }">
                                            <div v-if="value" class="flex items-center gap-2">
                                                <i class="pi pi-box"></i>
                                                <div>{{ value.name }}</div>
                                            </div>
                                            <span v-else>{{ placeholder }}</span>
                                        </template>
                                        <template #option="{ option }">
                                            <div class="flex items-center gap-2">
                                                <i class="pi pi-box"></i>
                                                <div>{{ option.name }} [{{ option.serie }}]</div>
                                            </div>
                                        </template>
                                    </Select>
                                    <label for="activo">Selecciona los activos que llevarás</label>
                                </IftaLabel>
                            </div>

                            <div class="col-span-12 w-full">
                                <h4 class="mb-4">Activos que llevaré</h4>
                                <div v-if="selectedActivos.length > 0"
                                    class="flex gap-4 flex-col items-center w-full justify-center">
                                    <div class="grid grid-cols-12 gap-2 items-center w-full"
                                        v-for="activo in selectedActivos" :key="activo.serie">
                                        <div class="col-span-5">
                                            <IftaLabel>
                                                <InputText class="w-full" type="text" v-model="activo.name" disabled />
                                                <label>Nombre del Activo</label>
                                            </IftaLabel>
                                        </div>
                                        <div class="col-span-5">
                                            <IftaLabel>
                                                <InputText class="w-full" type="text" v-model="activo.serie" disabled />
                                                <label>Serie del Activo</label>
                                            </IftaLabel>
                                        </div>
                                        <div class="col-span-2">
                                            <Button icon="pi pi-times" severity="danger"
                                                @click="dropActivoFromList(activo)" class="cursor-pointer" />
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="w-full">
                                    <Message severity="secondary" icon="pi pi-info">
                                        No llevarás ningún activo
                                    </Message>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end items-center w-full mt-6">
                            <div class="flex gap-4">
                                <Button label="Crear Solicitud" icon="pi pi-check" severity="secondary"
                                    @click="submit" />
                                <Link href="/visitas">
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
