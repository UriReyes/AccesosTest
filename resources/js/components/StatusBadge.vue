<template>
    <span :class="[
        'inline-flex items-center rounded-full px-3 py-1 text-sm font-medium',
        statusClasses
    ]">
        <i v-if="icon" :class="['mr-2', icon]"></i>
        {{ label }}
    </span>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    status: string; // puede venir en inglés o español
    lang?: 'es' | 'en';
}>();

// Mapeo para normalizar el status entrante a una "clave" interna estándar (en español)
const normalizeStatusMap: Record<string, string> = {
    autorizado: 'autorizado',
    authorized: 'autorizado',

    aprobado: 'aprobado',
    approved: 'aprobado',

    pendiente: 'pendiente',
    pending: 'pendiente',

    rechazado: 'rechazado',
    rejected: 'rechazado',

    cancelado: 'cancelado',
    cancelled: 'cancelado',
    canceled: 'cancelado',
};

const iconMap: Record<string, string> = {
    aprobado: 'pi pi-check-circle',
    autorizado: 'pi pi-check-circle',
    pendiente: 'pi pi-clock',
    rechazado: 'pi pi-times-circle',
    cancelado: 'pi pi-ban',
};

const labelMapEs: Record<string, string> = {
    aprobado: 'Aprobado',
    autorizado: 'Autorizado',
    pendiente: 'Pendiente',
    rechazado: 'Rechazado',
    cancelado: 'Cancelado',
};

const labelMapEn: Record<string, string> = {
    aprobado: 'Approved',
    autorizado: 'Authorized',
    pendiente: 'Pending',
    rechazado: 'Rejected',
    cancelado: 'Cancelled',
};

const normalizedStatus = computed(() => {
    const s = props.status.toLowerCase();
    return normalizeStatusMap[s] || 'pendiente'; // fallback
});

const icon = computed(() => iconMap[normalizedStatus.value]);
const label = computed(() => {
    return props.lang === 'en'
        ? labelMapEn[normalizedStatus.value]
        : labelMapEs[normalizedStatus.value];
});
const statusClasses = computed(() => {
    switch (normalizedStatus.value) {
        case 'aprobado':
            return 'bg-green-100 text-green-800';
        case 'autorizado':
            return 'bg-green-100 text-green-800';
        case 'pendiente':
            return 'bg-yellow-100 text-yellow-800';
        case 'rechazado':
            return 'bg-red-100 text-red-800';
        case 'cancelado':
            return 'bg-gray-100 text-gray-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
});
</script>
