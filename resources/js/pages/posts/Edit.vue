<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import InputError from '@/components/InputError.vue';
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
        <div class="space-y-6 bg-journal-bg p-4 text-journal-on-surface sm:p-6">
            <section class="rounded-xl border border-journal-outline-variant/30 bg-journal-surface-lowest p-6">
                <h1 class="text-2xl font-semibold text-journal-primary">Muuda postitust</h1>
                <p class="mt-2 text-sm text-journal-on-surface-variant">
                    Uuenda pealkirja ja sisu ning salvesta muudatused.
                </p>
            </section>

            <section class="rounded-xl border border-journal-outline-variant/30 bg-journal-surface-lowest p-6">
                <form class="grid gap-5" @submit.prevent="submit">
                    <div>
                        <Label for="title">Pealkiri</Label>
                        <Input id="title" v-model="form.title" class="mt-1" />
                        <InputError :message="form.errors.title" class="mt-1" />
                    </div>

                    <div>
                        <Label for="content">Sisu</Label>
                        <Textarea id="content" v-model="form.content" rows="8" class="mt-1" />
                        <InputError :message="form.errors.content" class="mt-1" />
                    </div>

                    <div
                        class="rounded-lg border border-journal-outline-variant/30 bg-journal-secondary-container/20 p-3 text-sm text-journal-on-surface-variant"
                    >
                        <p>ID: {{ post.id }}</p>
                        <p>Koostatud: {{ post.created_at_formatted }}</p>
                        <p>Muudetud: {{ post.updated_at_formatted }}</p>
                    </div>

                    <div class="flex flex-wrap gap-3 pt-2">
                        <Button type="submit" :disabled="form.processing" class="bg-journal-primary text-journal-on-primary hover:bg-journal-primary/90">
                            Salvesta muudatused
                        </Button>
                        <Button type="button" variant="outline" @click="router.visit(index().url)">Tühista</Button>
                    </div>
                </form>
            </section>
        </div>
    </AppLayout>
</template>
