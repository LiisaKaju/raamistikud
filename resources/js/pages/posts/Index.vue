<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Pagination from '@/components/ui/pagination/Pagination.vue';
import PaginationContent from '@/components/ui/pagination/PaginationContent.vue';
import PaginationEllipsis from '@/components/ui/pagination/PaginationEllipsis.vue';
import PaginationFirst from '@/components/ui/pagination/PaginationFirst.vue';
import PaginationItem from '@/components/ui/pagination/PaginationItem.vue';
import PaginationLast from '@/components/ui/pagination/PaginationLast.vue';
import PaginationNext from '@/components/ui/pagination/PaginationNext.vue';
import PaginationPrevious from '@/components/ui/pagination/PaginationPrevious.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { create, destroy, edit, show, index } from '@/routes/posts';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Postitused',
        href: index().url,
    },
];

interface PaginationLink {
    url: string | null;
    label: string;
    page?: number | null;
    active: boolean;
}

interface PaginatedResponse {
    current_page: number;
    data: Post[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: PaginationLink[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

export type Post = {
    id: number;
    title: string;
    content: string;
    created_at: string;
    updated_at: string;
    created_at_formatted: string;
    updated_at_formatted: string;
    comments_count?: number;
    comments?: [
        {
            id: number;
            post_id: number;
            user_id: number;
            content: string;
        },
    ];
};

export type User = {
    id: number;
    name: string;
    email: string;
    created_at?: string;
    updated_at?: string;
};

export type Comment = {
    id: number;
    post_id: number;
    user_id: number;
    content: string;
    created_at_formatted?: string;
    updated_at_formatted?: string;
    user?: User;
};

defineProps<{
    posts: PaginatedResponse;
}>();

const heroImage =
    'https://lh3.googleusercontent.com/aida-public/AB6AXuBFsmJhnkAKH2GVbyn98_LMWKy_HLY5LCMgXJ4Rn6Fb1Y7sg3Fra46LeGpD1eg1g1gQOToPZvNkO1cdc3WtBw8vKGN_2J2muoTPWg97q6cdM_9SiFXuvgkFfIK5GEUkiIpeh0l-i8yWBpFD0lqrKNApvHSlZZGgAPt6Hb-YYC932njnp8m_kIlx3nGSnJoN7R7JJdPyE1NxoT5MtrkinEyOxzb2xxWDWW4ZnPpf0W1T0v0ZXc2oaw4SFb2AAusIJ3Up3WyYIjxb5zKO';

const excerpt = (text: string, max = 220) => {
    const t = text.trim();
    if (t.length <= max) return t;
    return `${t.slice(0, max).trim()}…`;
};

const deletePost = (postId: number) => {
    if (!confirm('Aga miks sa kustutad?')) return;
    router.delete(destroy.url(postId), {
        preserveScroll: true,
        onSuccess: () => {
            console.log('Postitus sai kustutatud.');
        },
        onError: (err) => {
            console.error(err);
            alert('Ups, sa ei saanud eluga hakkama.');
        },
    });
};
</script>

<template>
    <Head title="Matkarajad ja Mõtted — blogi" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="min-h-full bg-journal-bg font-journal-body text-journal-on-surface selection:bg-journal-primary-fixed-dim selection:text-journal-primary"
        >
            <header class="sticky top-0 z-30 w-full border-b border-journal-outline-variant/25 bg-[#fbf9f5]/95 backdrop-blur-sm">
                <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-journal-primary">forest</span>
                        <span class="font-journal-headline text-lg font-bold tracking-tight text-journal-primary">
                            Matkarajad ja Mõtted
                        </span>
                    </div>
                </nav>
            </header>

            <main>
                <section class="relative flex h-[min(420px,52vh)] items-center justify-center overflow-hidden">
                    <div class="absolute inset-0">
                        <img
                            :src="heroImage"
                            alt="Eesti metsa maastik"
                            class="h-full w-full object-cover"
                        />
                        <div class="journal-hero-gradient absolute inset-0" />
                    </div>
                    <div class="relative z-10 px-6 text-center">
                        <h1
                            class="mb-4 font-journal-headline text-4xl font-extrabold tracking-tight text-journal-on-primary md:text-6xl"
                        >
                            Matkarajad ja Mõtted
                        </h1>
                        <p
                            class="mx-auto max-w-2xl font-journal-headline text-xl italic text-journal-primary-fixed md:text-2xl"
                        >
                            Ühe tudengi rännakud ja tähelepanekud Eesti looduses
                        </p>
                    </div>
                </section>

                <div class="relative z-20 mx-auto max-w-4xl px-6 pb-24 pt-6">
                    <div class="mb-10 flex flex-wrap items-center justify-end gap-3">
                        <Button
                            as-child
                            class="rounded-full border-0 bg-journal-primary px-8 py-3 font-bold text-journal-on-primary shadow-sm hover:bg-journal-primary/90"
                        >
                            <Link :href="create().url">Uus postitus</Link>
                        </Button>
                    </div>

                    <article
                        v-for="post in posts.data"
                        :key="post.id"
                        class="mb-14 overflow-hidden rounded-xl bg-journal-surface-lowest p-8 shadow-sm md:p-12"
                    >
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

                        <h2 class="mb-6 font-journal-headline text-3xl leading-tight text-journal-primary md:text-4xl">
                            <Link :href="show.url(post.id)" class="hover:underline">{{ post.title }}</Link>
                        </h2>

                        <div class="mb-8 space-y-4 text-lg leading-relaxed text-journal-on-surface-variant">
                            <p class="text-xl font-medium italic text-journal-on-surface">
                                {{ excerpt(post.content, 280) }}
                            </p>
                        </div>

                        <div
                            class="flex flex-wrap items-center justify-between gap-6 border-t border-journal-outline-variant/30 pt-8"
                        >
                            <div class="flex flex-wrap items-center gap-4">
                                <Link
                                    :href="show.url(post.id)"
                                    class="inline-flex items-center gap-2 rounded-full bg-journal-primary px-8 py-3 text-sm font-bold text-journal-on-primary transition-all hover:opacity-90"
                                >
                                    Loe edasi
                                    <span class="material-symbols-outlined text-base">arrow_forward</span>
                                </Link>
                                <Link
                                    :href="edit.url(post.id)"
                                    class="text-sm font-semibold uppercase tracking-wide text-journal-secondary hover:underline"
                                >
                                    Muuda
                                </Link>
                                <button
                                    type="button"
                                    class="text-sm font-semibold uppercase tracking-wide text-journal-error hover:underline"
                                    @click="deletePost(post.id)"
                                >
                                    Kustuta
                                </button>
                            </div>
                            <div class="flex items-center gap-6 text-journal-on-surface-variant">
                                <div class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[20px]">chat_bubble</span>
                                    <span class="text-sm font-medium">{{ post.comments_count ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </article>

                    <div
                        v-if="posts.last_page > 1"
                        class="rounded-xl bg-journal-surface-container p-4"
                    >
                        <Pagination
                            class="w-full"
                            :page="posts.current_page"
                            v-slot="{ page }"
                            :total="posts.total"
                            :items-per-page="posts.per_page"
                            @update:page="(p) => router.get(index().url, { page: p })"
                        >
                            <PaginationContent v-slot="{ items }" class="flex flex-wrap items-center justify-center gap-1">
                                <PaginationFirst />
                                <PaginationPrevious />

                                <template v-for="(item, idx) in items" :key="idx">
                                    <PaginationItem v-if="item.type === 'page'" :value="item.value" as-child>
                                        <Button
                                            class="h-10 w-10 border-journal-outline-variant/40 p-0 text-journal-primary hover:bg-journal-secondary-container"
                                            :variant="item.value === page ? 'default' : 'outline'"
                                        >
                                            {{ item.value }}
                                        </Button>
                                    </PaginationItem>

                                    <PaginationEllipsis v-else :index="idx" />
                                </template>

                                <PaginationNext />
                                <PaginationLast />
                            </PaginationContent>
                        </Pagination>
                    </div>
                </div>
            </main>

            <footer class="mt-12 w-full border-t border-journal-outline-variant/20 bg-journal-bg pb-12 pt-10">
                <div
                    class="mx-auto flex w-full max-w-7xl flex-col items-center justify-between gap-6 px-8 md:flex-row"
                >
                    <span class="font-journal-headline text-xl font-bold text-journal-primary">Matkarajad ja Mõtted</span>
                    <p class="text-xs uppercase tracking-wide text-journal-secondary">
                        © {{ new Date().getFullYear() }} Estonian Field Journal — õpilasprojekt
                    </p>
                </div>
            </footer>
        </div>
    </AppLayout>
</template>
