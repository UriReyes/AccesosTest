<script setup lang="ts">
import { parseISO, format } from 'date-fns';
import { es } from 'date-fns/locale';
import { computed } from 'vue';

interface Props {
    dates: string[];
    size?: 'sm' | 'md' | 'lg';
}

const props = withDefaults(defineProps<Props>(), {
    size: 'md'
});

const dateCount = computed(() => props.dates.length);
const isEmpty = computed(() => props.dates.length === 0);

function formatDate(date: string, isCompact = false): string {
    const parsed = parseISO(date);
    return isCompact
        ? format(parsed, "d MMM", { locale: es })
        : format(parsed, "EEEE d 'de' MMMM", { locale: es });
}

function getDateDisplay(): { primary: string; secondary?: string; icon: string } {
    if (isEmpty.value) {
        return {
            primary: 'Sin fechas',
            secondary: 'Selecciona un rango',
            icon: 'empty'
        };
    }

    if (dateCount.value === 1) {
        return {
            primary: formatDate(props.dates[0]),
            secondary: format(parseISO(props.dates[0]), 'yyyy', { locale: es }),
            icon: 'single'
        };
    }

    const startYear = format(parseISO(props.dates[0]), 'yyyy', { locale: es });
    const endYear = format(parseISO(props.dates[props.dates.length - 1]), 'yyyy', { locale: es });
    const yearDisplay = startYear === endYear ? startYear : `${startYear} - ${endYear}`;

    return {
        primary: `${formatDate(props.dates[0], true)} - ${formatDate(props.dates[props.dates.length - 1], true)}`,
        secondary: `${dateCount.value} días seleccionados • ${yearDisplay}`,
        icon: 'range'
    };
}

const display = computed(() => getDateDisplay());

const containerClasses = computed(() => {
    const sizeClasses = {
        sm: 'px-3 py-2 text-sm',
        md: 'px-4 py-3 text-base',
        lg: 'px-6 py-4 text-lg'
    };

    return `flex items-center gap-3 select-none ${sizeClasses[props.size]}`;
});

const iconSize = {
    sm: 'w-5 h-5',
    md: 'w-6 h-6',
    lg: 'w-7 h-7'
};

</script>

<template>
    <div :class="containerClasses" role="status" :aria-label="`Fechas seleccionadas: ${display.primary}`">
        <!-- Iconos -->
        <div class="flex-shrink-0 relative">
            <!-- Icono -->
            <svg v-if="display.icon === 'empty'" :class="`${iconSize[props.size]} text-gray-400 dark:text-gray-500`"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12h.01" opacity="0.5" />
            </svg>
            <svg v-else-if="display.icon === 'single'"
                :class="`${iconSize[props.size]} text-blue-600 dark:text-blue-400`" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                <circle cx="12" cy="15" r="1.5" fill="currentColor" />
            </svg>
            <svg v-else :class="`${iconSize[props.size]} text-blue-600 dark:text-blue-400`" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6"
                    opacity="0.7" />
            </svg>

            <!-- Badge -->
            <div v-if="!isEmpty && dateCount > 1"
                class="absolute -top-1 -right-1 w-4 h-4 rounded-full text-[10px] font-bold flex items-center justify-center bg-blue-600 text-white dark:bg-blue-500">
                {{ dateCount > 99 ? '99+' : dateCount }}
            </div>
        </div>

        <!-- Texto -->
        <div class="flex-1 min-w-0">
            <div class="font-semibold leading-tight text-gray-800 dark:text-white">
                {{ display.primary }}
            </div>
            <div v-if="display.secondary" class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                {{ display.secondary }}
            </div>
        </div>

    </div>
</template>
