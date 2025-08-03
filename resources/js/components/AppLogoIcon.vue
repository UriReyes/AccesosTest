<script setup lang="ts">

import Image from 'primevue/image';
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <Image src="/assets/logos/nadro-seeklogo.png" class="max-w-30" />
</template>
