<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{
    status: 'success' | 'failed' | 'pending';
    message: string;
}>();
</script>

<template>
    <Head title="Makse tulemus" />
    <AppLayout>
        <div class="mx-auto max-w-2xl bg-journal-bg p-6 text-journal-on-surface">
            <div class="rounded-xl border border-journal-outline-variant/30 bg-journal-surface-lowest p-6 shadow-sm">
                <h1 class="text-2xl font-semibold text-journal-primary">Makse tulemus</h1>
                <p
                    class="mt-2 text-sm"
                    :class="{
                        'text-emerald-700': status === 'success',
                        'text-amber-700': status === 'pending',
                        'text-red-700': status === 'failed',
                    }"
                >
                    {{ message }}
                </p>
                <div class="mt-5 flex gap-3">
                    <a
                        href="/shop"
                        class="rounded-full bg-journal-primary px-4 py-2 text-sm font-semibold text-journal-on-primary hover:bg-journal-primary/90"
                    >
                        Tagasi matkapoodi
                    </a>
                    <a
                        href="/cart"
                        class="rounded-full border border-journal-outline-variant/40 px-4 py-2 text-sm font-semibold text-journal-primary hover:bg-journal-secondary-container/40"
                    >
                        Ava ostukorv
                    </a>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

