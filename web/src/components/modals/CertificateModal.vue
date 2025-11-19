<template>
  <div 
    v-if="showModal" 
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
    @click.self="closeModal"
  >
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
      <!-- Header -->
      <div class="flex justify-between items-center p-6 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-900">
          {{ modalTitle }}
        </h2>
        <button
          @click="closeModal"
          class="text-gray-400 hover:text-gray-600 transition"
        >
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>

      <!-- Body -->
      <form @submit.prevent="submitForm" class="p-6">
        <div class="space-y-4">
          <!-- Certificate Name -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Certificate Name <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.name"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="e.g., ISO 9001:2015"
            />
          </div>

          <!-- Certificate Number -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Certificate Number <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.certificate_number"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="e.g., CERT-001-2024"
            />
          </div>

          <!-- Issuing Authority -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Issuing Authority <span class="text-red-500">*</span>
            </label>
            <input
              v-model="form.issuing_authority"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="e.g., ISO Certification Body"
            />
          </div>

          <!-- Date Fields -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Issue Date <span class="text-red-500">*</span>
              </label>
              <input
                v-model="form.issue_date"
                type="date"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Expiry Date
              </label>
              <input
                v-model="form.expiry_date"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
          </div>

          <!-- Status (Edit mode only) -->
          <div v-if="isEditing">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Status
            </label>
            <select
              v-model="form.status"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="active">Active</option>
              <option value="expired">Expired</option>
              <option value="revoked">Revoked</option>
            </select>
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Description
            </label>
            <textarea
              v-model="form.description"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="Additional information about the certificate..."
            ></textarea>
          </div>

          <!-- File Upload -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Certificate File 
              <span v-if="!isEditing" class="text-red-500">*</span>
              <span v-else class="text-gray-500 text-xs">(Leave empty to keep current file)</span>
            </label>
            
            <input
              ref="fileInput"
              type="file"
              @change="handleFileUpload"
              accept=".pdf,.jpg,.jpeg,.png"
              class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
            />
            <p class="mt-1 text-xs text-gray-500">PDF, JPG, or PNG (Max: 2MB)</p>
            
            <!-- Current File Info -->
            <div v-if="isEditing && certificate?.file_name && !form.file" class="mt-2 p-3 bg-blue-50 rounded-lg">
              <div class="flex items-center text-sm text-blue-800">
                <i class="fas fa-file mr-2"></i>
                <span>Current file: {{ certificate.file_name }}</span>
              </div>
            </div>

            <!-- New File Selected -->
            <div v-if="form.file" class="mt-2 p-3 bg-green-50 rounded-lg">
              <div class="flex items-center justify-between">
                <div class="flex items-center text-sm text-green-800">
                  <i class="fas fa-file mr-2"></i>
                  <span>{{ form.file.name }}</span>
                </div>
                <button
                  type="button"
                  @click="removeFile"
                  class="text-red-600 hover:text-red-800"
                >
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="mt-6 flex justify-end space-x-3">
          <button
            type="button"
            @click="closeModal"
            :disabled="loading"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <i v-if="loading" class="fas fa-spinner fa-spin mr-2"></i>
            {{ loading ? 'Saving...' : (isEditing ? 'Update' : 'Create') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
<script setup>
import { ref, watch, computed } from 'vue'
import { certificateService } from '@/services/certificate'

const props = defineProps({
  modelValue: Boolean,
  certificate: Object
})

const emit = defineEmits(['update:modelValue', 'saved', 'closed'])

const showModal = ref(false)
const loading = ref(false)
const fileInput = ref(null)

const form = ref({
  name: '',
  certificate_number: '',
  description: '',
  issue_date: '',
  expiry_date: '',
  issuing_authority: '',
  status: 'active',
  file: null
})

const isEditing = computed(() => !!props.certificate)
const modalTitle = computed(() => isEditing.value ? 'Edit Certificate' : 'Add New Certificate')

// Watch for modal open/close
watch(() => props.modelValue, (value) => {
  showModal.value = value
  if (value) {
    if (props.certificate) {
      // Populate form for editing
      form.value = {
        name: props.certificate.name || '',
        certificate_number: props.certificate.certificate_number || '',
        description: props.certificate.description || '',
        issue_date: formatDateForInput(props.certificate.issue_date) || '',
        expiry_date: formatDateForInput(props.certificate.expiry_date) || '',
        issuing_authority: props.certificate.issuing_authority || '',
        status: props.certificate.status || 'active',
        file: null
      }
    } else {
      // Reset form for adding
      resetForm()
    }
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

// Format date for input (YYYY-MM-DD)
const formatDateForInput = (date) => {
  if (!date) return ''
  const d = new Date(date)
  const year = d.getFullYear()
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const day = String(d.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

// Handle file upload
const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    // Validate file size (2MB max)
    if (file.size > 2 * 1024 * 1024) {
      alert('File size must be less than 2MB')
      event.target.value = ''
      return
    }

    // Validate file type
    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png']
    if (!allowedTypes.includes(file.type)) {
      alert('File type must be PDF, JPG, or PNG')
      event.target.value = ''
      return
    }

    form.value.file = file
  }
}

// Remove file
const removeFile = () => {
  form.value.file = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

// Submit form
const submitForm = async () => {
  if (loading.value) return

  // Validation
  if (!form.value.name || !form.value.certificate_number || !form.value.issue_date || !form.value.issuing_authority) {
    alert('Please fill in all required fields')
    return
  }

  // Validate file for new certificate
  if (!isEditing.value && !form.value.file) {
    alert('Please upload a certificate file')
    return
  }

  // Validate expiry date
  if (form.value.expiry_date && form.value.issue_date) {
    if (new Date(form.value.expiry_date) <= new Date(form.value.issue_date)) {
      alert('Expiry date must be after issue date')
      return
    }
  }

  loading.value = true

  try {
    const formData = new FormData()
    
    // Append all form fields
    formData.append('name', form.value.name)
    formData.append('certificate_number', form.value.certificate_number)
    formData.append('issuing_authority', form.value.issuing_authority)
    formData.append('issue_date', form.value.issue_date)
    
    if (form.value.expiry_date) {
      formData.append('expiry_date', form.value.expiry_date)
    }
    
    if (form.value.description) {
      formData.append('description', form.value.description)
    }

    if (isEditing.value) {
      formData.append('status', form.value.status)
    }

    // Append file if selected
    if (form.value.file) {
      formData.append('file', form.value.file)
    }

    let response
    if (isEditing.value) {
      response = await certificateService.update(props.certificate.id, formData)
    } else {
      response = await certificateService.create(formData)
    }

    if (response.data.success) {
      alert(`Certificate ${isEditing.value ? 'updated' : 'created'} successfully!`)
      emit('saved', response.data.data)
      closeModal()
    } else {
      throw new Error(response.data.message || 'Failed to save certificate')
    }
  } catch (error) {
    console.error('Certificate error:', error)
    const errorMessage = error.response?.data?.message || error.message || `Failed to ${isEditing.value ? 'update' : 'create'} certificate`
    alert(errorMessage)
  } finally {
    loading.value = false
  }
}

// Close modal
const closeModal = () => {
  if (!loading.value) {
    showModal.value = false
  }
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
    status: 'active',
    file: null
  }
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}
</script>