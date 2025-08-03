<template>
    <div>
        <!-- QR Pequeño -->
        <div class="relative cursor-pointer" @click="visible = true">
            <qrcode-vue :value="value" :size="safeSize" :level="level || 'H'" render-as="canvas" class="rounded-md" />
        </div>

        <!-- Modal -->
        <Dialog v-model:visible="visible" modal :closable="true" header="Código QR" class="w-full max-w-xl">
            <div class="flex justify-center p-4">
                <qrcode-vue :value="value" :size="safeSize * 10" :level="level || 'H'" render-as="canvas"
                    class="rounded-lg" />
            </div>
        </Dialog>
    </div>
</template>

<script setup lang="ts">
import QrcodeVue from 'qrcode.vue';
import Dialog from 'primevue/dialog';
import { ref, computed } from 'vue';

const visible = ref(false);

const props = defineProps<{
    value: string;
    size?: number;
    level?: 'L' | 'M' | 'Q' | 'H';
}>();

const safeSize = computed(() => props.size ?? 160);
</script>
