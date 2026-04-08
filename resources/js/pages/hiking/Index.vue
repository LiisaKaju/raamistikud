<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

type HikingItem = {
    id: number;
    title: string;
    image: string | null;
    description: string;
    location: string;
    difficulty: 'easy' | 'medium' | 'hard' | string;
    distance_km: number | string;
    user?: { id: number; name: string };
};

const props = defineProps<{
    items: {
        data: HikingItem[];
    };
    filters: {
        q?: string;
        difficulty?: string;
        sort?: string;
    };
    auth: {
        user: { id: number };
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Matka API', href: '/hiking' },
];

const form = useForm({
    title: '',
    image: '',
    description: '',
    location: '',
    difficulty: 'easy',
    distance_km: '',
});

const filterForm = useForm({
    q: props.filters.q ?? '',
    difficulty: props.filters.difficulty ?? '',
    sort: props.filters.sort ?? 'latest',
});

const submit = () => {
    form.post('/hiking', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const applyFilters = () => {
    router.get('/hiking', filterForm.data(), { preserveState: true, preserveScroll: true });
};

const myItems = computed(() => props.items.data.filter((item) => item.user?.id === props.auth.user.id));
const otherItems = computed(() => props.items.data.filter((item) => item.user?.id !== props.auth.user.id));

type GeoapifyFeature = {
    properties?: {
        name?: string;
        formatted?: string;
        categories?: string[];
    };
};

const placesFilters = useForm({
    category: 'natural',
    q: '',
    lat: '59.437',
    lon: '24.7536',
    radius: '10000',
    limit: '8',
});

const placesLoading = ref(false);
const placesError = ref('');
const placesResults = ref<GeoapifyFeature[]>([]);

const loadPlaces = async () => {
    placesLoading.value = true;
    placesError.value = '';

    try {
        const params = new URLSearchParams({
            category: placesFilters.category || 'natural',
            q: placesFilters.q || '',
            lat: placesFilters.lat || '',
            lon: placesFilters.lon || '',
            radius: placesFilters.radius || '10000',
            limit: placesFilters.limit || '8',
        });

        const response = await fetch(`/api/places?${params.toString()}`);
        const json = await response.json();

        if (!response.ok) {
            throw new Error(json?.error || 'Päring ebaõnnestus');
        }

        placesResults.value = Array.isArray(json?.data) ? json.data : [];
    } catch (error) {
        placesError.value = error instanceof Error ? error.message : 'Tundmatu viga';
        placesResults.value = [];
    } finally {
        placesLoading.value = false;
    }
};
</script>

<template>
    <Head title="Matka API" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 bg-journal-bg p-4 text-journal-on-surface sm:p-6">
            <section class="rounded-xl border border-journal-outline-variant/30 bg-journal-surface-lowest p-6">
                <h1 class="text-2xl font-semibold text-journal-primary">Matka API andmed</h1>
                <p class="mt-2 text-sm text-journal-on-surface-variant">
                    Lisa uusi matkaradu ja sirvi teiste lisatud kirjeid.
                </p>
            </section>

            <section class="rounded-xl border border-journal-outline-variant/30 bg-journal-surface-lowest p-6">
                <h2 class="mb-4 text-lg font-semibold text-journal-primary">Lisa uus kirje</h2>
                <form class="grid gap-4 md:grid-cols-2" @submit.prevent="submit">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Pealkiri</label>
                        <Input v-model="form.title" />
                        <InputError :message="form.errors.title" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Pildi URL</label>
                        <Input v-model="form.image" placeholder="https://..." />
                        <InputError :message="form.errors.image" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="mb-1 block text-sm font-medium">Kirjeldus</label>
                        <textarea
                            v-model="form.description"
                            class="min-h-24 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs"
                        />
                        <InputError :message="form.errors.description" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Asukoht</label>
                        <Input v-model="form.location" />
                        <InputError :message="form.errors.location" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Raskusaste</label>
                        <select v-model="form.difficulty" class="h-10 w-full rounded-md border border-input bg-transparent px-3 text-sm">
                            <option value="easy">easy</option>
                            <option value="medium">medium</option>
                            <option value="hard">hard</option>
                        </select>
                        <InputError :message="form.errors.difficulty" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Pikkus (km)</label>
                        <Input v-model="form.distance_km" type="number" step="0.1" min="0.5" />
                        <InputError :message="form.errors.distance_km" />
                    </div>
                    <div class="md:col-span-2">
                        <Button type="submit" :disabled="form.processing" class="bg-journal-primary text-journal-on-primary hover:bg-journal-primary/90">
                            Salvesta
                        </Button>
                    </div>
                </form>
            </section>

            <section class="rounded-xl border border-journal-outline-variant/30 bg-journal-surface-lowest p-6">
                <div class="mb-4 grid gap-3 md:grid-cols-4">
                    <Input v-model="filterForm.q" placeholder="Otsi pealkirja/asukoha järgi..." />
                    <select v-model="filterForm.difficulty" class="h-10 rounded-md border border-input bg-transparent px-3 text-sm">
                        <option value="">Kõik raskused</option>
                        <option value="easy">easy</option>
                        <option value="medium">medium</option>
                        <option value="hard">hard</option>
                    </select>
                    <select v-model="filterForm.sort" class="h-10 rounded-md border border-input bg-transparent px-3 text-sm">
                        <option value="latest">Uuemad ees</option>
                        <option value="distance_asc">Lühemad ees</option>
                        <option value="distance_desc">Pikemad ees</option>
                        <option value="title_asc">Nimi A-Z</option>
                    </select>
                    <Button variant="outline" @click="applyFilters">Filtreeri</Button>
                </div>

                <h3 class="mb-3 text-base font-semibold text-journal-primary">Minu kirjed</h3>
                <div class="mb-6 grid gap-3 md:grid-cols-2">
                    <article v-for="item in myItems" :key="item.id" class="rounded-lg border border-journal-outline-variant/30 p-3">
                        <img v-if="item.image" :src="item.image" :alt="item.title" class="mb-2 h-32 w-full rounded object-cover" />
                        <h4 class="font-semibold">{{ item.title }}</h4>
                        <p class="text-sm text-journal-on-surface-variant">{{ item.location }} • {{ item.distance_km }} km • {{ item.difficulty }}</p>
                        <p class="mt-1 text-sm">{{ item.description }}</p>
                    </article>
                    <p v-if="myItems.length === 0" class="text-sm text-journal-on-surface-variant">Sul pole veel kirjeid.</p>
                </div>

                <h3 class="mb-3 text-base font-semibold text-journal-primary">Teiste kirjed</h3>
                <div class="grid gap-3 md:grid-cols-2">
                    <article v-for="item in otherItems" :key="item.id" class="rounded-lg border border-journal-outline-variant/30 p-3">
                        <img v-if="item.image" :src="item.image" :alt="item.title" class="mb-2 h-32 w-full rounded object-cover" />
                        <h4 class="font-semibold">{{ item.title }}</h4>
                        <p class="text-sm text-journal-on-surface-variant">
                            {{ item.location }} • {{ item.distance_km }} km • {{ item.difficulty }} • {{ item.user?.name }}
                        </p>
                        <p class="mt-1 text-sm">{{ item.description }}</p>
                    </article>
                    <p v-if="otherItems.length === 0" class="text-sm text-journal-on-surface-variant">Teiste kirjeid ei leitud.</p>
                </div>
            </section>

            <section class="rounded-xl border border-journal-outline-variant/30 bg-journal-surface-lowest p-6">
                <h3 class="mb-4 text-base font-semibold text-journal-primary">Live kohad Geoapify API-st</h3>
                <div class="mb-4 grid gap-3 md:grid-cols-3">
                    <Input v-model="placesFilters.category" placeholder="category (nt natural)" />
                    <Input v-model="placesFilters.q" placeholder="otsing (q), nt raba" />
                    <Input v-model="placesFilters.limit" type="number" min="1" max="50" placeholder="limit" />
                    <Input v-model="placesFilters.lat" placeholder="lat (nt 59.437)" />
                    <Input v-model="placesFilters.lon" placeholder="lon (nt 24.7536)" />
                    <Input v-model="placesFilters.radius" type="number" min="100" max="50000" placeholder="radius meetrites" />
                </div>
                <div class="mb-3">
                    <Button
                        :disabled="placesLoading"
                        class="bg-journal-primary text-journal-on-primary hover:bg-journal-primary/90"
                        @click="loadPlaces"
                    >
                        {{ placesLoading ? 'Laen...' : 'Lae kohad API-st' }}
                    </Button>
                </div>
                <p v-if="placesError" class="mb-3 text-sm text-red-600">{{ placesError }}</p>
                <div class="grid gap-3 md:grid-cols-2">
                    <article
                        v-for="(place, idx) in placesResults"
                        :key="`${place.properties?.name ?? 'place'}-${idx}`"
                        class="rounded-lg border border-journal-outline-variant/30 p-3"
                    >
                        <h4 class="font-semibold">{{ place.properties?.name || 'Nimetu koht' }}</h4>
                        <p class="text-sm text-journal-on-surface-variant">
                            {{ place.properties?.formatted || 'Aadress puudub' }}
                        </p>
                        <p class="mt-1 text-xs text-journal-on-surface-variant">
                            {{ place.properties?.categories?.slice(0, 3).join(', ') }}
                        </p>
                    </article>
                    <p v-if="!placesLoading && placesResults.length === 0" class="text-sm text-journal-on-surface-variant">
                        Tulemused puuduvad. Vajuta "Lae kohad API-st".
                    </p>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
