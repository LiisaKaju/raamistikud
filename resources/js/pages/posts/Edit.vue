<script setup lang="ts">
import { ref } from 'vue'
import { Head, useForm, router } from '@inertiajs/vue3'
import InputError from '@/components/InputError.vue'
import Button from '@/components/ui/button/Button.vue'
import Input from '@/components/ui/input/Input.vue'
import Label from '@/components/ui/label/Label.vue'
import Switch from '@/components/ui/switch/Switch.vue'
import Textarea from '@/components/ui/textarea/Textarea.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'

/**
 * TYPED POST (muuda vastavalt oma mudelile)
 */
type Post = {
  id: number | string
  title?: string
  content?: string
  author?: string
  published?: boolean
}

/**
 * Props — Inertia peab saatma `post`
 */
const props = defineProps<{ post: Post }>()

/**
 * Breadcrumbs — kui kasutad edit() helperit, võid siin asendada
 */
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Posts edit',
    href: typeof window !== 'undefined' && typeof (window as any).route === 'function'
      ? (window as any).route('posts.edit', props.post.id)
      : `/posts/${props.post.id}/edit`,
  },
]

/**
 * Fallback URL builder (kui Ziggy puudub)
 */
const buildUrl = (name: string, id?: number | string) => {
  if (typeof window !== 'undefined' && typeof (window as any).route === 'function') {
    try {
      return (window as any).route(name, id)
    } catch (e) {
      // fallthrough to fallback
    }
  }

  if (name === 'posts.update' && id !== undefined) return `/posts/${id}`
  if (name === 'posts.index') return `/posts`
  if (name === 'posts.edit' && id !== undefined) return `/posts/${id}/edit`
  return id ? `/posts/${id}` : `/posts`
}

const form = useForm({
  title: props.post?.title ?? '',
  content: props.post?.content ?? '',
  author: props.post?.author ?? '',
  published: !!props.post?.published,
})

const genericError = ref<string | null>(null)

/**
 * Submit: kasutame PUT (posts.update)
 */
const submit = async () => {
  genericError.value = null
  const url = buildUrl('posts.update', props.post.id)

  try {
    await form.put(url, {
      onSuccess: () => {
        // optional: router.visit(buildUrl('posts.index'))  — Inertia server võib redirect'ida
      },
      onError: () => {
        // form.errors täitub automaatselt
      },
    })
  } catch (err) {
    genericError.value = 'Something went wrong. Check console.'
    // eslint-disable-next-line no-console
    console.error(err)
  }
}

/**
 * Cancel
 */
const cancel = () => {
  router.visit(buildUrl('posts.index'))
}
</script>

<template>
  <Head title="Edit Post" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <div class="mx-auto h-full w-full max-w-2xl p-4">
        <h3 class="text-lg font-medium mb-4">Post Edit</h3>

        <form @submit.prevent="submit" novalidate>
          <div class="mt-6 grid gap-4">
            <div>
              <Label for="title">Title</Label>
              <Input class="mt-1" id="title" v-model="form.title" :disabled="form.processing" />
              <InputError :message="form.errors.title" />
            </div>

            <div>
              <Label for="content">Content</Label>
              <Textarea class="mt-1" id="content" v-model="form.content" :disabled="form.processing" />
              <InputError :message="form.errors.content" />
            </div>

            <div>
              <Label for="author">Author</Label>
              <Input class="mt-1" id="author" v-model="form.author" :disabled="form.processing" />
              <InputError :message="form.errors.author" />
            </div>

            <div class="mt-4 flex items-center space-x-2">
              <!-- Switch peab olema seotud form.published-ga -->
              <Switch id="published" v-model="form.published" />
              <Label for="published">Published</Label>
            </div>
          </div>

          <div class="mt-6 flex justify-end space-x-2">
            <button type="button" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg" @click="cancel" :disabled="form.processing">Cancel</button>

            <Button :disabled="form.processing">
              <template #default>
                <span v-if="form.processing">Saving…</span>
                <span v-else>Save</span>
              </template>
            </Button>
          </div>

          <p v-if="genericError" class="mt-4 text-sm text-red-600">{{ genericError }}</p>

          <!-- Debug: eemalda tootmises -->
          <pre class="mt-6 text-xs">{{ form }}</pre>
        </form>
      </div>
    </div>
  </AppLayout>
</template>
