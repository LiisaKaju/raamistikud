<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';
import { Link } from '@inertiajs/vue3';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>
        <nav class="ml-auto flex items-center gap-2">
            <Link href="/dashboard" class="rounded-full px-3 py-1.5 text-sm font-medium text-journal-primary hover:bg-journal-secondary-container/40">
                Avaleht
            </Link>
            <Link href="/shop" class="rounded-full px-3 py-1.5 text-sm font-medium text-journal-primary hover:bg-journal-secondary-container/40">
                Matkapood
            </Link>
            <Link href="/posts" class="rounded-full px-3 py-1.5 text-sm font-medium text-journal-primary hover:bg-journal-secondary-container/40">
                Blogi
            </Link>
        </nav>
    </header>
</template>
