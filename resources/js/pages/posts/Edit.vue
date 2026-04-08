<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { index, update } from '@/routes/posts';
import type { BreadcrumbItem } from '@/types';

const props = defineProps<{
    post: {
        id: number;
        title: string;
        content: string;
        created_at_formatted?: string;
        updated_at_formatted?: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Postitused', href: index().url },
    { title: `Muuda postitust #${props.post.id}`, href: `/posts/${props.post.id}/edit` },
];

const form = useForm({
    title: props.post.title,
    content: props.post.content,
});

const submit = () => {
    form.put(update(props.post.id).url, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Muuda postitust #${post.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="mx-auto flex max-w-2xl flex-col gap-6 p-6">
            <h1 class="text-2xl font-semibold">Muuda postitust</h1>

            <form class="flex flex-col gap-4" @submit.prevent="submit">
                <div>
                    <Label for="title">Pealkiri</Label>
                    <Input id="title" v-model="form.title" />
                    <p v-if="form.errors.title" class="text-sm text-red-600">
                        {{ form.errors.title }}
                    </p>
                </div>

                <div>
                    <Label for="content">Sisu</Label>
                    <Textarea id="content" v-model="form.content" rows="6" />
                    <p v-if="form.errors.content" class="text-sm text-red-600">
                        {{ form.errors.content }}
                    </p>
                </div>

                <div class="mt-2 text-sm text-gray-500">
                    <p>ID: {{ post.id }}</p>
                    <p>Koostatud: {{ post.created_at_formatted }}</p>
                    <p>Muudetud: {{ post.updated_at_formatted }}</p>
                </div>

                <div class="mt-4 flex gap-3">
                    <Button type="submit" :disabled="form.processing"> Salvesta </Button>
                    <Button type="button" variant="outline" @click="router.visit(index().url)"> Tühista </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
