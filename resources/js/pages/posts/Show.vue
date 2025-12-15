<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { index, show } from '@/routes/posts';
import { type BreadcrumbItem } from '@/types';
import { Post } from './Index.vue';
import { useForm } from '@inertiajs/vue3';
import { add } from '@/routes/comments';
import { Textarea } from '@/components/ui/textarea';
const props = defineProps<{ post: Post }>();
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Posts',
    href: index().url,
  },
  {
    title: props.post.title,
    href: show(props.post.id).url,
  },
];
const form = useForm({
  content: '',
});
const submit = () => {
  form.post(add(props.post.id).url), {
  onSuccess: () => {
    form.reset();
  },
};
};

</script>
<template>
  <AppLayout :breadcrumbs="breadcrumbs">
      <div class="flex flex-col gap-6 p-6 h-full">
        <!-- Post Header -->
        <header class="rounded-xl bg-muted/40 p-6 shadow-sm">
          <h1 class="text-3xl font-semibold tracking-tight">{{ props.post.title }}</h1>
          <p class="mt-2 text-sm text-muted-foreground">
            By {{ props.post.author.first_name }} {{ props.post.author.last_name }} |
            Created at: {{ props.post.created_at_formatted }} |
            Updated at: {{ props.post.updated_at_formatted }}
          </p>
        </header>
        <!-- Post Content -->
        <section class="rounded-xl border border-border/60 bg-background p-6 shadow-sm">
          <h2 class="mb-4 text-lg font-semibold text-foreground">Content</h2>
          <div class="prose max-w-none text-sm leading-relaxed text-foreground/90 dark:prose-invert">
            <p class="whitespace-pre-line">{{ props.post.content }}</p>
          </div>
        </section>
        <section class="bg-gray-50 p-6 rounded-xl shadow-sm">
          <h2 class="text-xl font-semibold mb-4 text-gray-800">Comments</h2>
          <div class="mb-6">
            <form @submit.prevent="submit" >
              <Textarea v-model="form.content" class="backgroud-white mb-4 rounded-lg p-4 border border-border/60 width-full"></Textarea>
              <div class="mt-2 flex justify-end">
                <Button class="background-blue-600 text-black p-4 rounded-lg hover:bg-blue-400"
                  :disabled="form.processing || !form.content.trim()"
                  @click="submit"
                >
                  Submit
                </Button>
              </div>
            </form>
          </div>
          <div class="flex flex-col gap-4 overflow-x-auto">
            <ul>
              <li
                v-for="comment in post.comments"
                :key="comment.id"
                class="bg-white p-4 rounded-lg shadow hover:shadow-md transition-shadow duration-200"
              >
                <div class="flex items-center justify-between mb-2">
                  <div class="flex items-center gap-2">
                    <span class="font-medium text-gray-800">{{ comment.user.name }}</span>
                    <span class="text-sm text-gray-400">#{{ comment.id }}</span>
                  </div>
                  <span class="text-xs text-gray-400">{{ comment.created_at_formatted }}</span>
                </div>
                <p class="text-gray-700">{{ comment.content }}</p>
              </li>
            </ul>
            <div v-if="!post.comments || post.comments.length === 0" class="text-gray-500 text-center py-4">
              No comments yet. Be the first to comment!
            </div>
          </div>
        </section>
      </div>
  </AppLayout>
</template>