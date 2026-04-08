<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { ref } from 'vue';

type Product = {
    id: number;
    sku: string;
    name: string;
    description: string;
    price: number | string;
};

const props = defineProps<{
    products: Product[];
    cartCount: number;
}>();

const qty: Record<number, number> = {};
props.products.forEach((p) => {
    qty[p.id] = 1;
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Matkapood', href: '/shop' },
];

const addToCart = (id: number) => {
    router.post(`/cart/add/${id}`, { quantity: qty[id] || 1 }, { preserveScroll: true });
};

const productImagesBySku: Record<string, string> = {
    'MTK-BOO-001': 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=900&q=80',
    'MTK-BAG-002': 'https://images.unsplash.com/photo-1622560480654-d96214fdc887?auto=format&fit=crop&w=900&q=80',
    'MTK-POL-003': 'https://images.unsplash.com/photo-1522163182402-834f871fd851?auto=format&fit=crop&w=900&q=80',
    'MTK-THM-004': 'https://images.unsplash.com/photo-1523362628745-0c100150b504?auto=format&fit=crop&w=900&q=80',
    'MTK-LMP-005': 'https://images.unsplash.com/photo-1516117172878-fd2c41f4a759?auto=format&fit=crop&w=900&q=80',
    'MTK-MAT-006': 'https://images.unsplash.com/photo-1504280390368-3971d27453c7?auto=format&fit=crop&w=900&q=80',
    'MTK-RAI-007': 'https://images.unsplash.com/photo-1519904981063-b0cf448d479e?auto=format&fit=crop&w=900&q=80',
    'MTK-FLT-008': 'https://images.unsplash.com/photo-1521208916900-7f5f2a87f9d8?auto=format&fit=crop&w=900&q=80',
    'MTK-FST-009': 'https://images.unsplash.com/photo-1584515933487-779824d29309?auto=format&fit=crop&w=900&q=80',
};

const fallbackImages = Object.values(productImagesBySku);
const failedSkus = ref<Record<string, boolean>>({});

const fallbackImageFor = (product: Product) =>
    `https://picsum.photos/seed/${encodeURIComponent(product.sku)}/900/600`;

const imageForProduct = (product: Product) => {
    if (failedSkus.value[product.sku]) {
        return fallbackImageFor(product);
    }

    return productImagesBySku[product.sku] ?? fallbackImages[product.id % fallbackImages.length];
};

const onImageError = (product: Product) => {
    failedSkus.value[product.sku] = true;
};
</script>

<template>
    <Head title="Matkapood" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 bg-journal-bg p-4 text-journal-on-surface sm:p-6">
            <div class="rounded-xl border border-journal-outline-variant/30 bg-journal-surface-lowest p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-journal-primary">Matkapood</h1>
                        <p class="mt-1 text-sm text-journal-on-surface-variant">Vali tooted ja lisa need ostukorvi.</p>
                    </div>
                    <a
                        class="rounded-full bg-journal-primary px-4 py-2 text-sm font-semibold text-journal-on-primary transition hover:bg-journal-primary/90"
                        href="/cart"
                    >
                        Ostukorv ({{ cartCount }})
                    </a>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="product in products"
                    :key="product.id"
                    class="rounded-xl border border-journal-outline-variant/30 bg-journal-surface-lowest p-3 shadow-sm"
                >
                    <img
                        :src="imageForProduct(product)"
                        :alt="product.name"
                        class="mb-3 h-40 w-full rounded object-cover"
                        loading="lazy"
                        @error="onImageError(product)"
                    />
                    <h3 class="font-medium text-journal-primary">{{ product.name }}</h3>
                    <p class="mt-1 text-sm text-journal-on-surface-variant">{{ product.description }}</p>
                    <p class="mt-2 font-semibold text-journal-primary">{{ Number(product.price).toFixed(2) }} EUR</p>

                    <div class="mt-3 flex items-center gap-2">
                        <Input v-model.number="qty[product.id]" type="number" min="1" class="h-9 w-20" />
                        <Button
                            class="h-9 bg-journal-primary text-journal-on-primary hover:bg-journal-primary/90"
                            @click="addToCart(product.id)"
                        >
                            Lisa korvi
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

