<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Switch from '@/components/ui/switch/Switch.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, store } from '@/routes/posts';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Posts create',
        href: create().url,
    },
];

const form = useForm({
    title: '',
    content: '',
    author: '',
    published: false,
});

const submit = () => {
    form.post(store().url);
};
</script>

<template>
    <Head title="Posts" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="mx-auto h-full w-full max-w-2xl p-4">
                <h3 class="text-lg font-medium">Post Create</h3>
                <form @submit.prevent="submit">
                    <div class="mt-6 grid gap-4">
                        <div>
                            <Label for="title">Title</Label>
                            <Input class="mt-1" id="title" v-model="form.title" />
                            <InputError :message="form.errors.title"/>
                        </div>
                        <div>
                            <Label for="content">Content</Label>
                            <Textarea class="mt-1" id="content" v-model="form.content" />
                            <InputError :message="form.errors.content"/>
                        </div>
                        <div>
                            <Label for="title">Author</Label>
                            <Input class="mt-1" id="author" v-model="form.author" />
                            <InputError :message="form.errors.author"/>
                        </div>
                        <div class="mt-4 flex items-center space-x-2">
                            <Switch id="published" />
                            <Label for="published">Published</Label>

                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <Button>Save</Button>
                    </div>
                </form>
                <pre>{{ form }}</pre>
            </div>
        </div>
    </AppLayout>
</template>
