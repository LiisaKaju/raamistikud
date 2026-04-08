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
        <div class="flex h-full flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="mx-auto h-full w-full max-w-2xl bg-muted p-4">
                <h3 class="text-lg font-medium">Uus postitus</h3>
                <form @submit.prevent="submit">
                    <div class="mt-6 grid gap-4">
                        <div>
                            <Label for="title">Pealkiri</Label>
                            <Input id="title" v-model="form.title" class="mt-1" name="title" />
                            <InputError :message="form.errors.title" />
                        </div>
                        <div>
                            <Label for="content">Sisu</Label>
                            <Textarea id="content" v-model="form.content" class="mt-1" />
                            <InputError :message="form.errors.content" />
                        </div>
                        <div class="mt-6 flex justify-end">
                            <Button type="submit">Salvesta</Button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
