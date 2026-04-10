<script setup lang="ts">
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, store } from '@/routes/posts';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import Button from '@/components/ui/button/Button.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import InputError from '@/components/InputError.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Uus postitus',
        href: create().url,
    },
];

const form = useForm({
    title: '',
    content: '',
});

const submit = () => {
    form.post(store().url);
};
</script>

<template>
    <Head title="Uus postitus" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6 bg-journal-bg p-4 text-journal-on-surface sm:p-6">
            <section class="rounded-xl border border-journal-outline-variant/30 bg-journal-surface-lowest p-6">
                <h1 class="text-2xl font-semibold text-journal-primary">Uus postitus</h1>
                <p class="mt-2 text-sm text-journal-on-surface-variant">
                    Kirjuta uus postitus ja salvesta see blogisse.
                </p>
            </section>

            <section class="rounded-xl border border-journal-outline-variant/30 bg-journal-surface-lowest p-6">
                <form class="grid gap-5" @submit.prevent="submit">
                    <div>
                        <Label for="title">Pealkiri</Label>
                        <Input id="title" v-model="form.title" class="mt-1" name="title" />
                        <InputError :message="form.errors.title" class="mt-1" />
                    </div>
                    <div>
                        <Label for="content">Sisu</Label>
                        <Textarea id="content" v-model="form.content" class="mt-1" rows="8" />
                        <InputError :message="form.errors.content" class="mt-1" />
                    </div>
                    <div class="flex justify-end pt-2">
                        <Button type="submit" class="bg-journal-primary text-journal-on-primary hover:bg-journal-primary/90">Salvesta postitus</Button>
                    </div>
                </form>
            </section>
        </div>
    </AppLayout>
</template>
