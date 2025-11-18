<template>
  <Modal v-model="showModal" :title="modalTitle" @close="closeModal">
    <form @submit.prevent="submitForm">
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Certificate Name *</label>
          <input
            v-model="form.name"
            type="text"
            required
            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            placeholder="Enter certificate name"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Certificate Number *</label>
          <input
            v-model="form.certificate_number"
            type="text"
            required
            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            placeholder="Enter certificate number"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Description</label>
          <textarea
            v-model="form.description"
            rows="3"
            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            placeholder="Enter certificate description"
          />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Issue Date *</label>
            <input
              v-model="form.issue_date"
              type="date"
              required
              class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Expiry Date</label>
            <input
              v-model="form.expiry_date"
              type="date"
              class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Issuing Authority *</label>
          <input
            v-model="form.issuing_authority"
            type="text"
            required
            class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            placeholder="Enter issuing authority"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Certificate File</label>
          <input
            type="file"
            @change="handleFileUpload"
            accept=".pdf,.jpg,.png"
            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
          />
          <p class="mt-1 text-xs text-gray-500">PDF, JPG, or PNG (Max: 2MB)</p>
        </div>
      </div>

      <div class="mt-6 flex justify-end space-x-3">
        <button
          type="button"
          @click="closeModal"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          Cancel
        </button>
        <button
          type="submit"
          :disabled="loading"
          class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {{ loading ? 'Saving...' : (isEditing ? 'Update' : 'Create') }}
        </button>
      </div>
    </form>
  </Modal>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import Modal from './Modal.vue'
import { certificateService } from '@/services/certificate'
import { useToast } from '@/composables/useToast'

const props = defineProps({
  modelValue: Boolean,
  certificate: Object
})

const emit = defineEmits(['update:modelValue', 'saved', 'closed'])

const { showSuccess, showError } = useToast()

const showModal = ref(false)
const loading = ref(false)
const form = ref({
  name: '',
  certificate_number: '',
  description: '',
  issue_date: '',
  expiry_date: '',
  issuing_authority: '',
  file: null
})

const isEditing = computed(() => !!props.certificate)
const modalTitle = computed(() => isEditing.value ? 'Edit Certificate' : 'Add New Certificate')

// Watch for modal open/close
watch(() => props.modelValue, (value) => {
  showModal.value = value
  if (value && props.certificate) {
    // Populate form for editing
    Object.keys(form.value).forEach(key => {
      if (props.certificate[key] !== undefined && props.certificate[key] !== null) {
        form.value[key] = props.certificate[key]
      }
    })
  }
})

// Watch internal modal state
watch(showModal, (value) => {
  emit('update:modelValue', value)
  if (!value) {
    resetForm()
    emit('closed')
  }
})

// Handle file upload
const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    // Validate file size (2MB max)
    if (file.size > 2 * 1024 * 1024) {
      showError('File size must be less than 2MB')
      event.target.value = ''
      return
    }
    form.value.file = file
  }
}

// Submit form
const submitForm = async () => {
  if (loading.value) return

  // Basic validation
  if (!form.value.name || !form.value.certificate_number || !form.value.issue_date || !form.value.issuing_authority) {
    showError('Please fill in all required fields')
    return
  }

  loading.value = true

  try {
    const formData = new FormData()
    
    // Append all form fields
    Object.keys(form.value).forEach(key => {
      if (form.value[key] !== null && form.value[key] !== '') {
        formData.append(key, form.value[key])
      }
    })

    if (isEditing.value) {
      await certificateService.update(props.certificate.id, formData)
      showSuccess('Certificate updated successfully')
    } else {
      await certificateService.create(formData)
      showSuccess('Certificate created successfully')
    }

    emit('saved')
    closeModal()
  } catch (error) {
    console.error('Certificate error:', error)
    showError(`Failed to ${isEditing.value ? 'update' : 'create'} certificate: ${error.message}`)
  } finally {
    loading.value = false
  }
}

// Close modal
const closeModal = () => {
  showModal.value = false
}

// Reset form
const resetForm = () => {
  form.value = {
    name: '',
    certificate_number: '',
    description: '',
    issue_date: '',
    expiry_date: '',
    issuing_authority: '',
    file: null
  }
}
</script>