<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index as postsIndex } from '@/routes/posts';
import { WeatherData, type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import MapView from '@/components/MapView.vue';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { ref } from 'vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const props = defineProps<{
    weather: WeatherData;
    city?: string;
    markers: {
        id: number
        name: string
        lat: number
        lng: number
        description: string | null
        added: string | null
        edited: string | null
    }[]
}>();

const searchCity = ref(props.city ?? '');
const search = () => {
    router.get(dashboard().url, { city: searchCity.value }, { preserveState: true });
};
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 overflow-x-auto p-4">
            <div class="grid auto-rows-min gap-4 md:grid-cols-3">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <div class="flex h-full flex-col gap-1 p-2">
                        <div class="flex gap-1">
                            <Input v-model="searchCity" placeholder="Linn" class="h-8 text-sm" @keyup.enter="search" />
                            <Button size="sm" class="h-8" @click="search">Otsi</Button>
                        </div>
                        <template v-if="weather?.weather?.[0] && weather?.main">
                            <div class="flex flex-1 flex-col items-center justify-center gap-1">
                                <img :src="`https://openweathermap.org/img/wn/${weather.weather[0].icon}@2x.png`" alt="" class="h-10 w-10" />
                                <span class="text-sm">{{ weather.name }}</span>
                                <span class="text-2xl font-medium">{{ Number(weather.main.temp).toFixed(1) }}°C</span>
                            </div>
                        </template>
                        <template v-else>
                            <div class="flex flex-1 items-center justify-center text-sm text-muted-foreground">
                                –
                            </div>
                        </template>
                    </div>
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <div class="flex h-full items-center justify-center p-4">
                        <Link
                            :href="postsIndex().url"
                            class="rounded border border-neutral-300 px-4 py-2 text-sm hover:bg-neutral-50 dark:border-neutral-700 dark:hover:bg-neutral-900"
                        >
                            Blogi
                        </Link>
                    </div>
                </div>
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                </div>
            </div>
            <div class="relative min-h-[400px] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <MapView :markers="markers" />
            </div>
        </div>
    </AppLayout>
</template>