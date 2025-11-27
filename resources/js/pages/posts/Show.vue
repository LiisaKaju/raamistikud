<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import type { Post } from './Index.vue';

const props = defineProps<{ post: Post }>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Posts', href: '/posts' },
  { title: props.post.title ?? `Post #${props.post.id}`, href: `/posts/${props.post.id}` },
];
</script>

<template>
  <Head :title="props.post.title ?? `Post #${props.post.id}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="max-w-3xl mx-auto p-6 flex flex-col gap-6">

      <!-- Title + meta -->
      <header>
        <h1 class="text-3xl font-semibold mb-2">{{ props.post.title }}</h1>

        <div class="text-sm text-gray-500 flex flex-wrap gap-2 items-center">
          <span v-if="props.post.author?.full_name">
            By <strong>{{ props.post.author.full_name }}</strong>
          </span>

          <span v-if="props.post.created_at_formatted">
            • Created: {{ props.post.created_at_formatted }}
          </span>

          <span v-if="props.post.updated_at_formatted">
            • Updated: {{ props.post.updated_at_formatted }}
          </span>

          <span
            class="ml-2 inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
            :class="props.post.published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
          >
            {{ props.post.published ? 'Published' : 'Draft' }}
          </span>
        </div>
      </header>

      <!-- Content -->
      <article
        class="bg-white rounded-xl shadow-sm border p-6 prose max-w-none whitespace-pre-line"
      >
        {{ props.post.content }}
      </article>

    </div>
  </AppLayout>
</template>
