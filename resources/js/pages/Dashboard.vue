<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index as postsIndex } from '@/routes/posts';
import { WeatherData } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import MapView from '@/components/MapView.vue';
import { ref } from 'vue';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';

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

const heroImage = '/images/dashboard-hero.png';
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <div class="flex flex-1 flex-col gap-6 bg-journal-bg p-4 sm:p-6">
            <section class="relative overflow-hidden rounded-xl border border-journal-outline-variant/30 bg-journal-surface-lowest shadow-sm">
                <img :src="heroImage" alt="Matkarada mägede vahel" class="h-[280px] w-full object-cover md:h-[360px]" />
                <div class="absolute inset-0 bg-gradient-to-t from-black/35 via-black/10 to-transparent" />
                <div class="absolute bottom-0 left-0 p-6 text-white">
                    <p class="text-xs font-semibold uppercase tracking-wider text-white/80">Matkarajad ja Mõtted</p>
                    <h2 class="mt-1 font-journal-headline text-2xl font-bold md:text-3xl">Valmis uueks rajaseikluseks?</h2>
                </div>
            </section>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border border-journal-outline-variant/30 bg-journal-surface-lowest p-4 shadow-sm">
                    <div class="mb-3 flex gap-2">
                        <Input v-model="searchCity" placeholder="Linn" class="h-10" @keyup.enter="search" />
                        <Button size="sm" class="h-10 bg-journal-primary text-journal-on-primary hover:bg-journal-primary/90" @click="search">
                            Otsi
                        </Button>
                    </div>
                    <template v-if="weather?.weather?.[0] && weather?.main">
                        <div class="flex items-center justify-between rounded-lg bg-journal-secondary-container/30 p-3">
                            <div>
                                <p class="text-sm text-journal-on-surface-variant">{{ weather.name }}</p>
                                <p class="text-3xl font-semibold text-journal-primary">{{ Number(weather.main.temp).toFixed(1) }}°C</p>
                            </div>
                            <img :src="`https://openweathermap.org/img/wn/${weather.weather[0].icon}@2x.png`" alt="" class="h-14 w-14" />
                        </div>
                    </template>
                    <template v-else>
                        <div class="flex h-[88px] items-center justify-center rounded-lg bg-journal-secondary-container/20 text-sm text-journal-on-surface-variant">
                            Ilmaandmed puuduvad
                        </div>
                    </template>
                </div>

                <div class="rounded-xl border border-journal-outline-variant/30 bg-journal-surface-lowest p-4 shadow-sm">
                    <div class="mb-3">
                        <h2 class="text-lg font-semibold text-journal-primary">Blogi</h2>
                        <p class="text-sm text-journal-on-surface-variant">Loe viimaseid postitusi ja mõtteid matkaradadelt.</p>
                    </div>
                    <div class="flex items-end justify-start">
                        <Link
                            :href="postsIndex().url"
                            class="rounded-full bg-journal-primary px-4 py-2 text-sm font-semibold text-journal-on-primary hover:bg-journal-primary/90"
                        >
                            Blogi
                        </Link>
                    </div>
                </div>

                <div class="rounded-xl border border-journal-outline-variant/30 bg-journal-surface-lowest p-4 shadow-sm">
                    <div class="mb-3">
                        <h2 class="text-lg font-semibold text-journal-primary">Matkapood</h2>
                        <p class="text-sm text-journal-on-surface-variant">Vali varustus, lisa ostukorvi ja mine maksma.</p>
                    </div>
                    <div class="flex items-end justify-start">
                        <Link
                            href="/shop"
                            class="rounded-full bg-journal-primary px-4 py-2 text-sm font-semibold text-journal-on-primary hover:bg-journal-primary/90"
                        >
                            Ava matkapood
                        </Link>
                    </div>
                </div>

                <div class="rounded-xl border border-journal-outline-variant/30 bg-journal-surface-lowest p-4 shadow-sm">
                    <div class="mb-3">
                        <h2 class="text-lg font-semibold text-journal-primary">Matka API</h2>
                        <p class="text-sm text-journal-on-surface-variant">Lisa matkaradu ja filtreeri andmeid JSON API kaudu.</p>
                    </div>
                    <div class="flex items-end justify-start">
                        <Link
                            href="/hiking"
                            class="rounded-full bg-journal-primary px-4 py-2 text-sm font-semibold text-journal-on-primary hover:bg-journal-primary/90"
                        >
                            Ava Matka API
                        </Link>
                    </div>
                </div>
            </div>

            <div class="relative min-h-[420px] flex-1 overflow-hidden rounded-xl border border-journal-outline-variant/30 bg-journal-surface-lowest shadow-sm">
                <MapView :markers="markers" />
            </div>
        </div>
    </AppLayout>
</template>