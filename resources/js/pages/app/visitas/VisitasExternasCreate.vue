<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import DatePicker from 'primevue/datepicker';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Select from 'primevue/select';
import IftaLabel from 'primevue/iftalabel';
import Card from 'primevue/card';
import { ref, computed } from 'vue';

interface Sede {
    id: number;
    name: string;
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Visitas Externas', href: '/visitas' },
    { title: 'Nueva Solicitud', href: '/visitas/externas/create' }
];

const page = usePage();
const sedes = ref<Sede[]>(page.props.sedes as Sede[]);

const form = useForm({
    time_start: null as Date | null,
    time_end: null as Date | null,
    dates: [] as Date[] | null,
    sede_id: null as number | null,
    visitor_name: '',
    visitor_email: '',
    visitor_company: '',
    visitor_reason: '',
    participants: [] as { name: string; document: string }[],
});

const datesValue = computed(() => {
    if (!form.dates) return [];
    const arr = Array.isArray(form.dates) ? form.dates : [form.dates];
    return arr
        .filter((d): d is Date => d instanceof Date && !isNaN(d.getTime()))
        .map((d) => d.toISOString());
});

function formatTime(date: Date | null): string | null {
    if (!date) return null;
    const h = date.getHours().toString().padStart(2, '0');
    const m = date.getMinutes().toString().padStart(2, '0');
    return `${h}:${m}`;
}

const addParticipant = () => {
    form.participants.push({ name: '', document: '' });
};

const removeParticipant = (index: number) => {
    form.participants.splice(index, 1);
};

const submit = () => {
    const payload = {
        ...form,
        time_start: form.time_start ? formatTime(form.time_start) : null,
        time_end: form.time_end ? formatTime(form.time_end) : null,
        dates: datesValue.value,
    };

    form.transform(() => payload).post('/visitas/externas/store', {
        onSuccess: () => {
            // Opcional: resetear formulario o redireccionar o mostrar notificación
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">

        <Head title="Registrar Visita Externa" />
        <div class="h-full rounded-xl p-4 relative">
            <Card>
                <template #title>
                    Registrar Visita Externa
                </template>
                <template #content>
                    <div class="grid grid-cols-12 gap-4 mb-6 w-full">
                        <!-- Horas -->
                        <div class="col-span-6">
                            <IftaLabel>
                                <DatePicker v-model="form.time_start" hourFormat="12" timeOnly showIcon class="w-full"
                                    iconDisplay="input" />
                                <label>Hora Inicio</label>
                            </IftaLabel>
                        </div>
                        <div class="col-span-6">
                            <IftaLabel>
                                <DatePicker v-model="form.time_end" hourFormat="12" timeOnly showIcon class="w-full"
                                    iconDisplay="input" />
                                <label>Hora Fin</label>
                            </IftaLabel>
                        </div>

                        <!-- Días -->
                        <div class="col-span-12">
                            <IftaLabel>
                                <DatePicker v-model="form.dates" selectionMode="range" showIcon class="w-full"
                                    iconDisplay="input" />
                                <label>Días que asistirá</label>
                            </IftaLabel>
                        </div>

                        <!-- Sede -->
                        <div class="col-span-12 md:col-span-6">
                            <IftaLabel>
                                <Select v-model="form.sede_id" :options="sedes" optionLabel="name" optionValue="id"
                                    showClear filter class="w-full" />
                                <label>Sede</label>
                            </IftaLabel>
                        </div>
                    </div>

                    <!-- Datos del visitante principal -->
                    <fieldset class="mb-6 p-4 border rounded col-span-12">
                        <legend class="text-lg font-semibold mb-2">Datos del Visitante Principal</legend>

                        <IftaLabel class="mb-3 block">
                            <InputText v-model="form.visitor_name" class="w-full" />
                            <label>Nombre Completo</label>
                        </IftaLabel>

                        <IftaLabel class="mb-3 block">
                            <InputText v-model="form.visitor_email" type="email" class="w-full" />
                            <label>Email</label>
                        </IftaLabel>

                        <IftaLabel class="mb-3 block">
                            <InputText v-model="form.visitor_company" class="w-full" />
                            <label>Empresa</label>
                        </IftaLabel>

                        <IftaLabel class="mb-3 block">
                            <InputText v-model="form.visitor_reason" class="w-full" />
                            <label>Motivo de la visita</label>
                        </IftaLabel>
                    </fieldset>

                    <!-- Participantes adicionales -->
                    <fieldset class="mb-6 p-4 border rounded col-span-12">
                        <legend class="text-lg font-semibold mb-2 flex justify-between items-center">
                            Participantes Adicionales
                            <Button label="Agregar" icon="pi pi-plus" size="small" @click="addParticipant"
                                severity="secondary" />
                        </legend>

                        <div v-if="form.participants.length === 0" class="text-gray-500 text-sm mb-3">
                            No hay participantes adicionales agregados.
                        </div>

                        <div v-for="(participant, index) in form.participants" :key="index"
                            class="grid grid-cols-12 gap-3 items-center mb-3">
                            <div class="col-span-5">
                                <IftaLabel>
                                    <InputText v-model="participant.name" class="w-full" />
                                    <label>Nombre</label>
                                </IftaLabel>
                            </div>
                            <div class="col-span-5">
                                <IftaLabel>
                                    <InputText v-model="participant.document" class="w-full" />
                                    <label>Documento</label>
                                </IftaLabel>
                            </div>
                            <div class="col-span-2 flex justify-end">
                                <Button icon="pi pi-times" severity="danger" text rounded size="small"
                                    @click="removeParticipant(index)" />
                            </div>
                        </div>
                    </fieldset>

                    <div class="flex justify-end gap-2">
                        <Button label="Crear Solicitud" icon="pi pi-check" @click="submit" severity="secondary"
                            size="small" />
                        <Link href="/visitas">
                        <Button label="Cancelar" icon="pi pi-times" severity="secondary" />
                        </Link>
                    </div>
                </template>
            </Card>
        </div>
    </AppLayout>
</template>
