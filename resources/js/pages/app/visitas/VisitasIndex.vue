<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import { ref } from 'vue';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Popover from 'primevue/popover';
import HumanDates from '@/components/HumanDates.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { getInitials } from '@/composables/useInitials';
import { computed } from 'vue';
import StatusBadge from '@/components/StatusBadge.vue';
import QrWithLogo from '@/components/QrWithLogo.vue';

interface Sede {
    id: number;
    name: string;
}

interface Usuario {
    id: number;
    name: string;
}

interface Visita {
    id: number;
    time_start: string;   // ISO datetime string
    time_end: string;     // ISO datetime string
    dates: string[];      // array of dates (strings)
    sede: Sede;
    user: Usuario;
    status: string;
}

const props = defineProps<{
    visitas: Visita[];
}>();
const page = usePage();
const auth = computed(() => page.props.auth);
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Visitas',
        href: '/visitas',
    }
];

const visitas = ref<Visita[]>(props.visitas);

const op = ref();

const toggle = (event: any) => {
    op.value.toggle(event);
};

function format12Hrs(time: string): string {
    if (!time) return '';
    const [hora, minuto] = time.split(':').map(Number);
    const periodo = hora >= 12 ? 'PM' : 'AM';
    const hora12 = hora % 12 === 0 ? 12 : hora % 12;
    return `${hora12}:${minuto.toString().padStart(2, '0')} ${periodo}`;
}
</script>

<template>

    <Head title="Visitas" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="h-full rounded-xl p-4 relative">
            <Card>
                <template #title>Visitas</template>
                <template #content>
                    <p class="m-0">
                        En esta sección puedes visualizar tu historial.
                    </p>
                    <DataTable :value="visitas" paginator :rows="5" :rowsPerPageOptions="[5, 10, 20, 50]"
                        tableStyle="min-width: 50rem" stripedRows sortMode="multiple">
                        <Column field="dates" header="Fecha" style="width: 20%">
                            <template #body="{ data }">
                                <div class="capitalize">
                                    <HumanDates :dates="data.dates" />
                                </div>
                            </template>
                        </Column>
                        <Column field="time_start" header="Hora Inicio" sortable style="width: 20%">
                            <template #body="{ data }">
                                {{ format12Hrs(data.time_start) }}
                            </template>
                        </Column>
                        <Column field="time_end" header="Hora Fin" sortable style="width: 20%">
                            <template #body="{ data }">
                                {{ format12Hrs(data.time_end) }}
                            </template>
                        </Column>
                        <Column field="sede.name" header="Sede" sortable style="width: 15%">
                            <template #body="{ data }">
                                <span>
                                    <i class="pi pi-building"></i>
                                    {{ data.sede.name }}
                                </span>
                            </template>
                        </Column>
                        <Column field="user.name" header="Interno" style="width: 15%">
                            <template #body="{ data }">
                                <Avatar class="size-8 overflow-hidden rounded-full">
                                    <AvatarImage v-if="auth.user.avatar" :src="auth.user.avatar"
                                        :alt="auth.user.name" />
                                    <AvatarFallback
                                        class="rounded-sm bg-neutral-200 font-semibold text-black dark:bg-neutral-700 dark:text-white">
                                        {{ getInitials(auth.user?.name) }}
                                    </AvatarFallback>
                                </Avatar>
                            </template>
                        </Column>
                        <Column header="QR">
                            <template #body="{ data }">
                                <div v-if="data.status === 'authorized' || data.status === 'approved'"
                                    class="flex items-center justify-center">
                                    <QrWithLogo :value="data.qr_token" :size="70" />
                                </div>
                                <div v-else>
                                    No se ha autorizado la visita
                                </div>
                            </template>
                        </Column>
                        <Column header="QR Token">
                            <template #body="{ data }">
                                <div class="flex items-center justify-center"
                                    v-if="data.status === 'authorized' || data.status === 'approved'">
                                    {{ data.qr_token }}
                                </div>
                                <div v-else>
                                    No se ha autorizado la visita
                                </div>
                            </template>
                        </Column>
                        <Column field="status" header="Estado" style="width: 15%">
                            <template #body="{ data }">
                                <StatusBadge :status="data.status" lang="en" />
                            </template>
                        </Column>
                    </DataTable>
                </template>
            </Card>
            <div class="absolute bottom-5 right-5">
                <Button rounded type="button" icon="pi pi-plus" @click="toggle" severity="secondary" size="large" />
                <Popover ref="op">
                    <div class="flex flex-col gap-4 w-[25rem]">
                        <Link href="/visitas/create">
                        <Button label=" Nueva Solicitud" icon="pi pi-user" size="small" severity="secondary"
                            class="w-full" />
                        </Link>
                        <Link href="/visitas/externas/create">
                        <Button label="Gestionar Solicitud Externa" icon="pi pi-users" size="small" class="w-full"
                            severity="secondary" />
                        </Link>
                    </div>
                </Popover>
            </div>
        </div>
    </AppLayout>
</template>
