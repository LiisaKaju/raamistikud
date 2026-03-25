<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import { ref, onMounted } from "vue"
import L from "leaflet"
import "leaflet/dist/leaflet.css"
import { Dialog,DialogContent,DialogHeader,DialogTitle,DialogDescription } from "./ui/dialog";
import { Form } from "@inertiajs/vue3";
import { Label } from "./ui/label";
import { Input } from "./ui/input";
import { Textarea } from "./ui/textarea";
import { Button } from "./ui/button";

const mapEl = ref(null);
const selectedLocation = ref<{ lat: number; lng: number } | null>(null);

const closeDialog = () => {
  selectedLocation.value = null
};

const handleSuccess = () => {
  selectedLocation.value = null
  editingMarker.value = null
  window.location.reload()
};

const mapClick= (e:PointerEvent)=> {
    selectedLocation.value = {
        lat: e.latlng.lat,
        lng: e.latlng.lng
    
    }
};

const deleteMarker = (id: number) => {
  router.delete(`/markers/${id}`, {
    onSuccess: () => {
      window.location.reload()
    },
    onError: (err) => {
      console.log('viga kustutamisel', err)
    }
  })
}

const props = defineProps<{
  markers: {
    id: number
    name: string
    lat: number
    lng: number
    description: string | null
    added: string | null
    edited: string | null
  }[]
}>()

const editingMarker = ref<{
  id: number
  name: string
  lat: number
  lng: number
  description: string | null
} | null>(null)

const editMarker = (marker: {
  id: number
  name: string
  lat: number
  lng: number
  description: string | null
}) => {
  editingMarker.value = marker

  selectedLocation.value = {
    lat: marker.lat,
    lng: marker.lng
  }
}

onMounted(() => {
  const map = L.map(mapEl.value, {
    zoomControl: true
  }).setView([59.4, 24.7], 8)

  L.tileLayer(
    "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
  ).addTo(map)

  map.on('click', mapClick);

  props.markers.forEach((marker) => {
  L.marker([marker.lat, marker.lng])
    .addTo(map)
    .bindPopup(`<b>${marker.name}</b><br>${marker.description ?? ""}<br>
  Lat: ${marker.lat}<br>
  Lng: ${marker.lng} <br><br>
  <button class="edit-marker-btn" data-id="${marker.id}" style="padding: 4px 8px; border: 1px solid #333; background: #f8f8f8; cursor: pointer; margin-right: 6px;">Muuda</button>
  <button class="delete-marker-btn" data-id="${marker.id}" style="border: 1px solid #ccc; padding: 5px 10px; background-color: #f0f0f0; cursor: pointer;">Kustuta</button>
  `)
  
})

map.on('popupopen', (event: L.PopupEvent) => {
  const popupElement = event.popup.getElement()
  if (!popupElement) return

  const editButton = popupElement.querySelector('.edit-marker-btn') as HTMLButtonElement | null

  if (editButton) {
    editButton.addEventListener('click', () => {
      const markerId = editButton.getAttribute('data-id')
      if (markerId) {
        const marker = props.markers.find(m => m.id === Number(markerId))
        if (marker) {
        editMarker(marker)
      }
    }
  })
}

  const deleteButton = popupElement.querySelector('.delete-marker-btn') as HTMLButtonElement | null

  if (deleteButton) {
    deleteButton.addEventListener('click', () => {
      console.log('Delete button clicked')
      const markerId = deleteButton.getAttribute('data-id')
      if (markerId) {
        deleteMarker(Number(markerId))
      }
    })
  }
})
});



</script>

<template>
  <div ref="mapEl" class="z-10 h-full"></div>
  <Dialog :open="!!selectedLocation" @update:open="closeDialog">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>{{ editingMarker ? 'Muuda markerit' : 'Salvesta uus marker' }}</DialogTitle>
        <DialogDescription>
          Lisa nimi ja kirjeldus
        </DialogDescription>
        <Form :method="editingMarker ? 'put' : 'post'"
          :action="editingMarker ? `/markers/${editingMarker.id}` : '/markers'"
          @success="handleSuccess">
           <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <Label class="mb-1.5" for="name">Name</Label>
                <Input name="name" :default-value="editingMarker?.name"/>
            </div>
            <div>
                <Label class="mb-1.5" for="lat">Lat</Label>
                <Input id="lat" name="lat" disabled :default-value="selectedLocation?.lat"/>
            </div>
            <div>
                <Label class="mb-1.5" for="lng">Lng</Label>
                <Input id="lng" name="lng" disabled :default-value="selectedLocation?.lng"/>
            </div>
            <input type="hidden" name="lat" :value="selectedLocation?.lat" />
            <input type="hidden" name="lng" :value="selectedLocation?.lng" />
            <Textarea name="description" class="col-span-2" :default-value="editingMarker?.description"></Textarea>
            <Button class="col-span-2" type="submit"> {{ editingMarker ? 'Salvesta muudatused' : 'Salvesta' }} </Button>
           </div>
        </Form>
      </DialogHeader>
    </DialogContent>
  </Dialog>
  
</template>