<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import DropdownMenu from '@/components/ui/dropdown-menu/DropdownMenu.vue';
import DropdownMenuContent from '@/components/ui/dropdown-menu/DropdownMenuContent.vue';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import DropdownMenuSeparator from '@/components/ui/dropdown-menu/DropdownMenuSeparator.vue';
import DropdownMenuTrigger from '@/components/ui/dropdown-menu/DropdownMenuTrigger.vue';
import Pagination from '@/components/ui/pagination/Pagination.vue';
import PaginationContent from '@/components/ui/pagination/PaginationContent.vue';
import PaginationEllipsis from '@/components/ui/pagination/PaginationEllipsis.vue';
import PaginationItem from '@/components/ui/pagination/PaginationItem.vue';
import PaginationNext from '@/components/ui/pagination/PaginationNext.vue';
import PaginationPrevious from '@/components/ui/pagination/PaginationPrevious.vue';
import Table from '@/components/ui/table/Table.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCaption from '@/components/ui/table/TableCaption.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
import TableHead from '@/components/ui/table/TableHead.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/routes/posts';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { MoreVertical } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Posts',
        href: index().url,
    },
];
export interface PaginationLink {
    url: string | null;
    label: string;
    page?: number | null;
    active: boolean;
}

export interface PaginatedResponse {
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
type Post = {
    id: number;
    title: string;
    content: string;
    author: string;
    published: boolean;
    created_at: string;
    updated_at: string;
    created_at_formated: string;
    updated_at_formated: string;
};

defineProps<{
    posts: PaginatedResponse;
}>();
</script>

<template>
    <Head title="Posts" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"></div>

        <Table>
            <TableCaption>A list of your recent blog posts.</TableCaption>
            <TableHeader>
                <TableRow>
                    <TableHead class="w-[100px]">ID</TableHead>
                    <TableHead>Title</TableHead>
                    <TableHead>Author</TableHead>
                    <TableHead class="text-right">Create at</TableHead>
                    <TableHead class="text-right">Updated at</TableHead>
                    <TableHead class="text-right">Published</TableHead>
                    <TableHead>
                        <span class="sr-only">Actions</span>
                    </TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="(post, index) in posts.data" :key="index">
                    <TableCell class="font-medium">{{ post.id }}</TableCell>
                    <TableCell>{{ post.title }}</TableCell>
                    <TableCell>{{ post.author }}</TableCell>
                    <TableCell class="text-right">{{ post.created_at_formated }}</TableCell>
                    <TableCell class="text-right">{{ post.updated_at_formated }}</TableCell>
                    <TableCell class="text-right">{{ post.published }}</TableCell>
                    <TableCell>
                        <div class="flex justify-end">
                            <DropdownMenu>
                                <DropdownMenuTrigger as-child>
                                    <Button size="icon" variant="ghost">
                                        <MoreVertical />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent>
                                    <DropdownMenuItem @click="router.get(route('posts.edit', post.id))"> Edit </DropdownMenuItem>
                                    <DropdownMenuSeparator />
                                    <DropdownMenuItem class="text-destructive" @click="router.delete(route('posts.destroy', post.id))">
                                        Delete
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </div>
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
        <Pagination
            class="w-full"
            :page="posts.current_page"
            v-slot="{ page }"
            :items-per-page="posts.per_page"
            :total="posts.total"
            @update:page="(page) => router.get(index().url, { page })"
            :default-page="2"
        >
            <PaginationContent v-slot="{ items }">
                <PaginationPrevious />
                <template v-for="(item, index) in items" :key="index">
                    <PaginationItem v-if="item.type === 'page'" :value="item.value" :is-active="item.value === page">
                        {{ item.value }}
                    </PaginationItem>
                </template>
                <PaginationEllipsis :index="6" />
                <PaginationNext />
            </PaginationContent>
        </Pagination>
    </AppLayout>
</template>
