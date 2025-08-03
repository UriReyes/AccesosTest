<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
</script>

<template>

    <Head title="Bienvenido a Nadro" />

    <main class="min-h-screen flex items-center justify-center bg-white dark:bg-neutral-900 px-4">
        <div class="max-w-xl w-full text-center">
            <h1 class="text-4xl font-bold text-gray-800 dark:text-gray-100 mb-4">
                Bienvenido a <span class="text-primary-600 dark:text-primary-400">Nadro</span>
            </h1>

            <p class="text-gray-600 dark:text-gray-300 text-lg mb-8">
                Este es el panel de gestión. Selecciona una opción en el menú para comenzar.
            </p>

            <a href="/visitas"
                class="inline-block bg-neutral-600 hover:bg-neutral-700 dark:bg-neutral-500 dark:hover:bg-neutral-600 text-white font-semibold py-2 px-4 rounded transition">
                Ir al Panel de Visitas
            </a>
        </div>
    </main>
</template>
