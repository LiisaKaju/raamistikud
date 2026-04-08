<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';
import { computed } from 'vue';

type CartItem = {
    id: number;
    name: string;
    price: number;
    quantity: number;
};

const props = defineProps<{
    items: CartItem[];
    total: number;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Matkapood', href: '/shop' },
    { title: 'Ostukorv', href: '/cart' },
    { title: 'Makse', href: '/checkout' },
];

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    payment_method: 'stripe',
});

const submit = () => {
    form.post('/checkout/start');
};

const formattedTotal = computed(() => Number(props.total).toFixed(2));
</script>

<template>
    <Head title="Makse" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto max-w-5xl space-y-6 bg-journal-bg p-1 text-journal-on-surface">
            <div class="rounded-xl border border-journal-outline-variant/30 bg-journal-surface-lowest p-6">
                <h1 class="text-2xl font-semibold text-journal-primary sm:text-3xl">Lõpeta tellimus</h1>
                <p class="mt-2 text-sm text-journal-on-surface-variant">
                    Sisesta andmed ja vajuta „Maksa“ - suunatakse Stripe turvalisele makselehele.
                </p>
            </div>

            <div class="grid gap-6 lg:grid-cols-12">
                <div class="lg:col-span-7">
                    <Card class="border border-journal-outline-variant/30 bg-journal-surface-lowest shadow-sm">
                        <CardHeader>
                            <CardTitle class="text-lg">Kontaktandmed</CardTitle>
                            <p class="text-sm text-muted-foreground">
                                Need lähevad arvele ja tellimuse kinnituseks.
                            </p>
                        </CardHeader>
                        <CardContent>
                            <form class="space-y-4" @submit.prevent="submit">
                                <InputError :message="form.errors.payment" />
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium">Eesnimi</label>
                                        <Input
                                            v-model="form.first_name"
                                            class="h-11"
                                            autocomplete="given-name"
                                        />
                                        <InputError :message="form.errors.first_name" />
                                    </div>
                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium">Perenimi</label>
                                        <Input
                                            v-model="form.last_name"
                                            class="h-11"
                                            autocomplete="family-name"
                                        />
                                        <InputError :message="form.errors.last_name" />
                                    </div>
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium">E-post</label>
                                    <Input
                                        v-model="form.email"
                                        type="email"
                                        class="h-11"
                                        autocomplete="email"
                                    />
                                    <InputError :message="form.errors.email" />
                                </div>
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium">Telefon</label>
                                    <Input
                                        v-model="form.phone"
                                        type="tel"
                                        class="h-11"
                                        autocomplete="tel"
                                    />
                                    <InputError :message="form.errors.phone" />
                                </div>

                                <div>
                                    <p class="mb-2 text-sm font-medium">Makseviis</p>
                                    <label
                                        class="flex items-center gap-3 rounded-lg border border-journal-outline-variant/30 bg-journal-secondary-container/30 p-3"
                                    >
                                        <input
                                            v-model="form.payment_method"
                                            type="radio"
                                            value="stripe"
                                        />
                                        <div>
                                            <p class="font-medium text-journal-primary">Stripe - kaardimakse</p>
                                            <p class="text-sm text-journal-on-surface-variant">Visa, Mastercard, Apple Pay</p>
                                        </div>
                                    </label>
                                </div>

                                <div
                                    class="flex flex-col gap-3 border-t border-journal-outline-variant/30 pt-5 sm:flex-row sm:items-center sm:justify-between"
                                >
                                    <p class="text-xs text-journal-on-surface-variant">
                                        Makse toodeldakse Stripe turvalises keskkonnas.
                                    </p>
                                    <Button
                                        type="submit"
                                        size="lg"
                                        :disabled="form.processing"
                                        class="h-11 min-w-[170px] bg-journal-primary text-journal-on-primary hover:bg-journal-primary/90"
                                    >
                                        {{ form.processing ? 'Suunan...' : `Maksa ${formattedTotal} €` }}
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>
                </div>

                <div class="lg:col-span-5">
                    <Card class="border border-journal-outline-variant/30 bg-journal-surface-lowest shadow-sm">
                        <CardHeader class="pb-2">
                            <CardTitle class="text-base">Tellimuse kokkuvote</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3 pt-0">
                            <div
                                v-for="item in items"
                                :key="item.id"
                                class="flex justify-between gap-4 border-b border-journal-outline-variant/25 pb-3 text-sm last:border-0 last:pb-0"
                            >
                                <span class="text-journal-on-surface">
                                    {{ item.name }}
                                    <span class="text-journal-on-surface-variant">x {{ item.quantity }}</span>
                                </span>
                                <span class="shrink-0 tabular-nums font-medium text-journal-primary">
                                    {{ (Number(item.price) * item.quantity).toFixed(2) }} €
                                </span>
                            </div>
                            <div
                                class="flex items-center justify-between border-t border-journal-outline-variant/30 pt-4 text-base font-semibold text-journal-primary"
                            >
                                <span>Kokku</span>
                                <span class="tabular-nums">{{ formattedTotal }} €</span>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
