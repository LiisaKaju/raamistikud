<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { create } from '@/routes/posts';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Posts create',
        href: create().url,
    },
];

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
           
        </div>
    </AppLayout>
</template>
