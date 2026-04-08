<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

type CartItem = {
    id: number;
    name: string;
    description: string;
    price: number;
    quantity: number;
};

const props = defineProps<{
    items: CartItem[];
    total: number;
    cartCount: number;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Matkapood', href: '/shop' },
    { title: 'Ostukorv', href: '/cart' },
];

const updateQuantity = (item: CartItem) => {
    router.patch(`/cart/update/${item.id}`, { quantity: item.quantity }, { preserveScroll: true });
};

const removeItem = (id: number) => {
    router.delete(`/cart/remove/${id}`, { preserveScroll: true });
};
</script>

<template>
    <Head title="Ostukorv" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-4 bg-journal-bg p-4 text-journal-on-surface sm:p-6">
            <div class="rounded-xl border border-journal-outline-variant/30 bg-journal-surface-lowest p-6">
                <h1 class="text-2xl font-semibold text-journal-primary">Ostukorv</h1>
                <p class="mt-1 text-sm text-journal-on-surface-variant">Uuenda koguseid ja liigu maksesse.</p>
            </div>

            <div
                v-if="items.length === 0"
                class="rounded-xl border border-journal-outline-variant/30 bg-journal-surface-lowest p-4 text-sm text-journal-on-surface-variant"
            >
                Ostukorv on tühi.
            </div>

            <div v-else class="space-y-3">
                <div
                    v-for="item in items"
                    :key="item.id"
                    class="rounded-xl border border-journal-outline-variant/30 bg-journal-surface-lowest p-4 shadow-sm"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="font-medium text-journal-primary">{{ item.name }}</h3>
                            <p class="text-sm text-journal-on-surface-variant">{{ item.description }}</p>
                            <p class="mt-1 text-sm font-medium">{{ Number(item.price).toFixed(2) }} EUR</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <Input v-model.number="item.quantity" type="number" min="1" class="h-9 w-20" />
                            <Button class="h-9 border-journal-outline-variant/40 text-journal-primary" variant="outline" @click="updateQuantity(item)">
                                Uuenda
                            </Button>
                            <Button class="h-9" variant="destructive" @click="removeItem(item.id)">Kustuta</Button>
                        </div>
                    </div>
                </div>

                <div
                    class="mt-4 flex items-center justify-between rounded-xl border border-journal-outline-variant/30 bg-journal-surface-lowest p-4"
                >
                    <p class="font-semibold text-journal-primary">Kokku: {{ Number(total).toFixed(2) }} EUR</p>
                    <a
                        href="/checkout"
                        class="rounded-full bg-journal-primary px-4 py-2 text-sm font-semibold text-journal-on-primary hover:bg-journal-primary/90"
                    >
                        Maksma
                    </a>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

