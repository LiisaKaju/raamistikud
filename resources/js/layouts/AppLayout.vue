<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
    hideHeader?: boolean;
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    hideHeader: false,
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs" :hide-header="hideHeader">
        <slot />
    </AppLayout>
</template>
