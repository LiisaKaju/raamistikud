<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { index, show } from '@/routes/posts';
import { type BreadcrumbItem } from '@/types';
import { Post } from './Index.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { add } from '@/routes/comments';
import { Textarea } from '@/components/ui/textarea';
import { Button } from '@/components/ui/button';

const props = defineProps<{ post: Post }>();
const page = usePage();
const isAdmin = Number((page.props as { auth?: { user?: { is_admin?: number } } })?.auth?.user?.is_admin ?? 0) === 1;

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Postitused', href: index().url },
    { title: props.post.title, href: show(props.post.id).url },
];

const form = useForm({
    content: '',
});

const submit = () => {
    form.post(add(props.post.id).url, {
        onSuccess: () => {
            form.reset();
        },
    });
};

const deleteComment = (id: number) => {
    if (!confirm('Kustuta kommentaar?')) return;
    router.delete(`/comments/${id}`, { preserveScroll: true });
};

const heroImage =
    'https://lh3.googleusercontent.com/aida-public/AB6AXuBFsmJhnkAKH2GVbyn98_LMWKy_HLY5LCMgXJ4Rn6Fb1Y7sg3Fra46LeGpD1eg1g1gQOToPZvNkO1cdc3WtBw8vKGN_2J2muoTPWg97q6cdM_9SiFXuvgkFfIK5GEUkiIpeh0l-i8yWBpFD0lqrKNApvHSlZZGgAPt6Hb-YYC932njnp8m_kIlx3nGSnJoN7R7JJdPyE1NxoT5MtrkinEyOxzb2xxWDWW4ZnPpf0W1T0v0ZXc2oaw4SFb2AAusIJ3Up3WyYIjxb5zKO';

const inlineImage = `https://picsum.photos/seed/post-${props.post.id}/1200/500`;
</script>

<template>
    <Head :title="`${post.title} — Matkarajad ja Mõtted`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="min-h-full bg-journal-bg font-journal-body text-journal-on-surface selection:bg-journal-primary-fixed-dim selection:text-journal-primary"
        >
            <header class="sticky top-0 z-30 w-full border-b border-journal-outline-variant/25 bg-[#fbf9f5]/95 backdrop-blur-sm">
                <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                    <Link :href="index().url" class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-journal-primary">forest</span>
                        <span class="font-journal-headline text-lg font-bold tracking-tight text-journal-primary">
                            Matkarajad ja Mõtted
                        </span>
                    </Link>
                </nav>
            </header>

            <main>
                <section class="relative flex h-[min(380px,45vh)] items-center justify-center overflow-hidden">
                    <div class="absolute inset-0">
                        <img :src="heroImage" alt="" class="h-full w-full object-cover" />
                        <div class="journal-hero-gradient absolute inset-0" />
                    </div>
                    <div class="relative z-10 max-w-4xl px-6 text-center">
                        <h1 class="font-journal-headline text-3xl font-extrabold tracking-tight text-journal-on-primary md:text-5xl">
                            {{ post.title }}
                        </h1>
                        <p class="mt-3 font-journal-headline text-lg italic text-journal-primary-fixed md:text-xl">
                            Postitus #{{ post.id }}
                            <span class="mx-2 text-journal-on-primary/70">·</span>
                            Koostatud: {{ post.created_at_formatted }}
                        </p>
                    </div>
                </section>

                <div class="relative z-20 mx-auto max-w-4xl px-6 pb-24 -mt-12">
                    <article class="mb-16 overflow-hidden rounded-xl bg-journal-surface-lowest p-8 shadow-sm md:p-12">
                        <div class="mb-6 flex flex-wrap items-center gap-4">
                            <span
                                class="rounded-full bg-journal-secondary-container px-4 py-1 text-xs font-bold tracking-wide text-journal-on-secondary-container"
                            >
                                Postitus #{{ post.id }}
                            </span>
                            <span class="text-sm text-journal-on-surface-variant">
                                Koostatud: {{ post.created_at_formatted }}
                                <span class="mx-2 text-journal-outline-variant">·</span>
                                Uuendatud: {{ post.updated_at_formatted }}
                            </span>
                        </div>

                        <div class="max-w-none text-lg leading-relaxed text-journal-on-surface-variant">
                            <div class="mb-8 rounded-xl bg-journal-surface-low p-6 md:p-8">
                                <img
                                    :src="inlineImage"
                                    alt=""
                                    class="mb-6 h-64 w-full rounded-lg object-cover"
                                />
                                <p class="text-center text-sm italic text-journal-on-surface-variant">
                                    Rada läbi metsa — illustratsioon postitusele
                                </p>
                            </div>
                            <div class="whitespace-pre-line text-journal-on-surface-variant">
                                {{ post.content }}
                            </div>
                        </div>

                        <div
                            class="mt-10 flex flex-wrap items-center justify-between gap-6 border-t border-journal-outline-variant/20 pt-10"
                        >
                            <Link
                                :href="index().url"
                                class="inline-flex items-center gap-2 rounded-full bg-journal-primary px-8 py-3 text-sm font-bold text-journal-on-primary hover:opacity-90"
                            >
                                <span class="material-symbols-outlined text-base">arrow_back</span>
                                Tagasi blogisse
                            </Link>
                        </div>
                    </article>

                    <section class="rounded-xl bg-journal-surface-low p-8 md:p-12">
                        <h3 class="mb-8 flex items-center gap-3 font-journal-headline text-2xl text-journal-primary">
                            <span class="material-symbols-outlined">comment</span>
                            Kommentaarid
                        </h3>

                        <div class="mb-12">
                            <label class="mb-3 block text-xs font-bold uppercase tracking-widest text-journal-secondary">
                                Lisa kommentaar
                            </label>
                            <form @submit.prevent="submit">
                                <Textarea
                                    v-model="form.content"
                                    placeholder="Jaga oma mõtteid või küsi raja kohta..."
                                    class="mb-4 min-h-[120px] rounded-xl border-0 bg-journal-surface-container-highest text-journal-on-surface placeholder:text-journal-on-surface-variant focus-visible:ring-2 focus-visible:ring-journal-primary-fixed-dim"
                                />
                                <div class="flex justify-end">
                                    <Button
                                        type="submit"
                                        :disabled="form.processing || !form.content.trim()"
                                        class="rounded-full border-0 bg-journal-secondary-container px-6 py-2 font-bold text-journal-on-secondary-container hover:opacity-90"
                                    >
                                        Postita
                                    </Button>
                                </div>
                            </form>
                        </div>

                        <div class="space-y-6">
                            <div
                                v-for="comment in post.comments"
                                :key="comment.id"
                                class="group relative rounded-xl bg-journal-surface-lowest p-6"
                            >
                                <div class="mb-2 flex justify-between gap-4">
                                    <span class="font-bold text-journal-primary">{{ comment.user?.name }}</span>
                                    <span class="shrink-0 text-xs text-journal-on-surface-variant">{{
                                        comment.created_at_formatted
                                    }}</span>
                                </div>
                                <p class="mb-4 text-sm leading-relaxed text-journal-on-surface-variant">
                                    {{ comment.content }}
                                </p>
                                <button
                                    v-if="isAdmin"
                                    type="button"
                                    class="flex items-center gap-1 text-xs font-bold uppercase tracking-tighter text-journal-error opacity-0 transition-opacity group-hover:opacity-100"
                                    @click="deleteComment(comment.id)"
                                >
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                    Kustuta
                                </button>
                            </div>
                            <p v-if="!post.comments?.length" class="py-6 text-center text-journal-on-surface-variant">
                                Kommentaare veel ei ole.
                            </p>
                        </div>
                    </section>
                </div>
            </main>

            <footer class="mt-12 w-full bg-journal-bg pb-12 pt-10">
                <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-6 px-8 md:flex-row">
                    <span class="font-journal-headline text-xl font-bold text-journal-primary">Matkarajad ja Mõtted</span>
                    <p class="text-xs uppercase tracking-wide text-journal-secondary">
                        © {{ new Date().getFullYear() }} Estonian Field Journal — õpilasprojekt
                    </p>
                </div>
            </footer>
        </div>
    </AppLayout>
</template>
