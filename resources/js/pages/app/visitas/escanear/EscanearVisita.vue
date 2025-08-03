<script setup lang="ts">
import { onMounted, onBeforeUnmount, ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Card from 'primevue/card';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import { type BreadcrumbItem } from '@/types';
import { Html5Qrcode } from 'html5-qrcode';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Visitas', href: '/visitas' },
    { title: 'Escanear QR', href: '/visitas/autorizar/escanear' },
];

const toast = useToast();
const page = usePage();
const token = ref<string>((page.props.token as string) || '');
const form = useForm({ token: token.value });
const scanning = ref(false);
let qrScanner: Html5Qrcode | null = null;

const enviar = () => {
    if (!form.token) {
        toast.add({
            severity: 'warn',
            summary: 'Token vacío',
            detail: 'Por favor escanea o introduce un token válido.',
            life: 3000,
        });
        return;
    }

    form.post(route('escanear.visitas.qr-scan'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.add({
                severity: 'success',
                summary: 'Visita registrada',
                detail: 'El QR fue escaneado correctamente',
                life: 3000,
            });
            form.reset();
            form.clearErrors();
            reiniciarEscaner();
        },
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: form.errors.token || 'No se pudo registrar la visita.',
                life: 3000,
            });
            reiniciarEscaner();
        },
    });
};

const iniciarEscaner = async () => {
    if (scanning.value || qrScanner) return;

    scanning.value = true;
    qrScanner = new Html5Qrcode('qr-reader');

    try {
        await qrScanner.start(
            { facingMode: 'environment' },
            {
                fps: 10,
                qrbox: 400,
            },
            (decodedText) => {
                console.log('QR detectado:', decodedText);
                if (decodedText) {
                    form.token = decodedText;
                    detenerEscaner();
                    enviar();
                }
            },
            (errorMessage) => {
                console.warn('Error escaneando:', errorMessage);
            }
        );
    } catch (err) {
        console.error('Error al iniciar escáner:', err);
        toast.add({
            severity: 'error',
            summary: 'Error de cámara',
            detail: 'No se pudo acceder a la cámara',
            life: 3000,
        });
        scanning.value = false;
    }
};

const detenerEscaner = async () => {
    if (qrScanner) {
        await qrScanner.stop();
        qrScanner.clear();
        qrScanner = null;
    }
    scanning.value = false;
};

const reiniciarEscaner = async () => {
    await detenerEscaner();
    await iniciarEscaner();
};

onMounted(() => {
    iniciarEscaner();
});

onBeforeUnmount(() => {
    detenerEscaner();
});
</script>

<template>

    <Head title="Escanear Visita" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <Toast />
        <div class="h-full rounded-xl p-4 relative max-w-xl mx-auto">
            <Card>
                <template #title>Escanear QR de Visita</template>
                <template #content>
                    <p class="mb-4">
                        Escanea un código QR con tu cámara o ingresa el token manualmente.
                    </p>

                    <div class="mb-4">
                        <div id="qr-reader" class="rounded-lg overflow-hidden border"
                            style="width: 100%; height: 300px;"></div>
                        <p class="text-sm text-gray-500 mt-2" v-if="!scanning">
                            El escáner no está activo.
                        </p>
                    </div>

                    <div class="flex flex-col gap-4 mt-4">
                        <InputText v-model="form.token" placeholder="Token escaneado o manual" class="w-full" />

                        <div class="flex gap-2">
                            <Button label="Registrar ingreso" icon="pi pi-check" @click="enviar"
                                :loading="form.processing" severity="success" />
                            <Button label="Reiniciar escáner" icon="pi pi-refresh" @click="reiniciarEscaner"
                                :disabled="form.processing" severity="secondary" />
                        </div>
                    </div>

                    <div v-if="form.errors.token" class="text-red-500 text-sm mt-2">
                        {{ form.errors.token }}
                    </div>
                </template>
            </Card>
        </div>
    </AppLayout>
</template>
