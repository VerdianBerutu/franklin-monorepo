<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-screen overflow-y-auto p-8">
      <h2 class="text-2xl font-bold mb-6">Tambah Customer Baru</h2>
      <form @submit.prevent="submit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
            <input v-model="form.name" required type="text" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Perusahaan</label>
            <input v-model="form.company" type="text" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input v-model="form.email" type="email" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
            <input v-model="form.phone" type="text" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
            <select v-model="form.type" class="w-full px-4 py-2 border rounded-lg">
              <option value="individual">Individual</option>
              <option value="company">Company</option>
            </select>
          </div>
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
            <textarea v-model="form.address" rows="3" class="w-full px-4 py-2 border rounded-lg"></textarea>
          </div>
        </div>
        <div class="flex gap-4 mt-8">
          <button type="button" @click="$emit('close')" class="flex-1 px-6 py-3 border rounded-lg hover:bg-gray-50">Batal</button>
          <button type="submit" :disabled="saving" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-70">
            {{ saving ? 'Menyimpan...' : 'Tambah Customer' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { customerService } from '@/services/customer'

const props = defineProps(['isOpen'])
const emit = defineEmits(['close', 'saved'])

const form = ref({
  name: '', company: '', email: '', phone: '', address: '', type: 'individual'
})
const saving = ref(false)

const submit = async () => {
  saving.value = true
  try {
    await customerService.create(form.value)
    form.value = { name: '', company: '', email: '', phone: '', address: '', type: 'individual' }
    emit('saved')
  } catch (err) {
    alert('Gagal menyimpan: ' + (err.response?.data?.message || err.message))
  } finally {
    saving.value = false
  }
}
</script>