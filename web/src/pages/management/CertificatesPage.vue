<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Certificates Management</h1>
        <p class="text-gray-600 mt-1">Kelola sertifikat sistem</p>
      </div>
      <button 
        @click="openAddModal"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center"
      >
        <i class="fas fa-plus mr-2"></i>
        Add Certificate
      </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
        <p class="text-gray-600">Loading certificates...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="text-center py-12">
        <i class="fas fa-exclamation-circle text-red-500 text-4xl mb-4"></i>
        <p class="text-red-600 font-medium mb-4">{{ error }}</p>
        <button 
          @click="loadCertificates"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
          Try Again
        </button>
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Certificate
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                No WS
              </th>
              
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Issue Date
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Expiry Date
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Download
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="certificate in certificates" :key="certificate.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-blue-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                      <path fill-rule="evenodd" d="M9 2.221V7H4.221a2 2 0 0 1 .365-.5L8.5 2.586A2 2 0 0 1 9 2.22ZM11 2v5a2 2 0 0 1-2 2H4v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2h-7ZM8 16a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H9a1 1 0 0 1-1-1Zm1-5a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2H9Z" clip-rule="evenodd"/>
                    </svg>
                  </div>
                  <div>
                    <div class="text-sm font-medium text-gray-900">
                      {{ certificate.name }}
                    </div>
                    <div class="text-sm text-gray-500">
                      {{ certificate.issuing_authority }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ certificate.certificate_number }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatDate(certificate.issue_date) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatDate(certificate.expiry_date) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  :class="[
                    'px-2 py-1 text-xs font-semibold rounded-full',
                    getStatusClass(certificate.status)
                  ]"
                >
                  {{ certificate.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <button 
                  @click="openEditModal(certificate)" 
                  class="text-blue-600 hover:text-blue-800 mr-3"
                  title="Edit"
                >
                  <i class="fas fa-edit"></i>
                </button>
                <button 
                  @click="confirmDelete(certificate)" 
                  class="text-red-600 hover:text-red-800"
                  title="Delete"
                >
                  <i class="fas fa-trash"></i>
                </button>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <button 
                  v-if="certificate.file_path"
                  @click="downloadCertificate(certificate)" 
                  :disabled="downloading[certificate.id]"
                  class="inline-flex items-center justify-center w-8 h-8 text-white bg-green-600 hover:bg-green-700 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                  title="Download"
                >
                  <i v-if="downloading[certificate.id]" class="fas fa-spinner fa-spin"></i>
                  <svg v-else class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 13V4M7 14H5a1 1 0 0 0-1 1v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1h-2m-1-5-4 5-4-5m9 8h0"/>
                  </svg>
                </button>
                <span v-else class="text-gray-400">-</span>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Empty State -->
        <div v-if="certificates.length === 0" class="text-center py-12">
          <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
            <path fill-rule="evenodd" d="M9 2.221V7H4.221a2 2 0 0 1 .365-.5L8.5 2.586A2 2 0 0 1 9 2.22ZM11 2v5a2 2 0 0 1-2 2H4v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2h-7ZM8 16a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H9a1 1 0 0 1-1-1Zm1-5a1 1 0 1 0 0 2h6a1 1 0 1 0 0-2H9Z" clip-rule="evenodd"/>
          </svg>
          <p class="text-gray-600">No certificates found</p>
          <button 
            @click="openAddModal"
            class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            Add Your First Certificate
          </button>
        </div>
      </div>
    </div>

    <!-- Certificate Modal -->
    <CertificateModal
      v-model="showModal"
      :certificate="selectedCertificate"
      @saved="onCertificateSaved"
      @closed="onModalClosed"
    />

    <!-- Delete Confirmation Modal -->
    <div 
      v-if="showDeleteModal" 
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="resetDeleting"
    >
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="p-6 text-center">
          <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
          </div>
          
          <h3 class="text-xl font-bold text-gray-900 mb-2">Delete Certificate</h3>
          
          <p class="text-gray-600 mb-4">
            Are you sure you want to delete <strong>{{ deletingCertificate?.name }}</strong>?
          </p>
          <p class="text-sm text-gray-500 mb-6">
            This action cannot be undone.
          </p>

          <div class="flex gap-3">
            <button
              @click="resetDeleting"
              :disabled="deleting"
              class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50"
            >
              Cancel
            </button>
            <button
              @click="handleDelete"
              :disabled="deleting"
              class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50"
            >
              <i v-if="deleting" class="fas fa-spinner fa-spin mr-2"></i>
              <i v-else class="fas fa-trash mr-2"></i>
              {{ deleting ? 'Deleting...' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { certificateService } from '@/services/certificate'
import CertificateModal from '@/components/modals/CertificateModal.vue'

// State
const certificates = ref([])
const loading = ref(false)
const error = ref(null)
const showModal = ref(false)
const showDeleteModal = ref(false)
const selectedCertificate = ref(null)
const deletingCertificate = ref(null)
const deleting = ref(false)
const downloading = reactive({})

// Methods
const loadCertificates = async () => {
  loading.value = true
  error.value = null
  
  try {
    const response = await certificateService.getAll()
    if (response.data.success) {
      certificates.value = response.data.data.data || []
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load certificates'
    console.error('Error loading certificates:', err)
  } finally {
    loading.value = false
  }
}

const downloadCertificate = async (certificate) => {
  if (!certificate.file_path) {
    alert('No file available for download')
    return
  }

  downloading[certificate.id] = true
  
  try {
    await certificateService.download(certificate)
    // Success notification could be added here
  } catch (error) {
    console.error('Download error:', error)
    alert(error.response?.data?.message || 'Failed to download certificate')
  } finally {
    downloading[certificate.id] = false
  }
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const getStatusClass = (status) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    expiring_soon: 'bg-yellow-100 text-yellow-800',
    expired: 'bg-red-100 text-red-800',
    revoked: 'bg-gray-100 text-gray-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const openAddModal = () => {
  selectedCertificate.value = null
  showModal.value = true
}

const openEditModal = (certificate) => {
  selectedCertificate.value = { ...certificate }
  showModal.value = true
}

const onCertificateSaved = () => {
  showModal.value = false
  loadCertificates()
}

const onModalClosed = () => {
  selectedCertificate.value = null
}

const confirmDelete = (certificate) => {
  deletingCertificate.value = certificate
  showDeleteModal.value = true
}

const handleDelete = async () => {
  if (!deletingCertificate.value) return

  deleting.value = true

  try {
    const response = await certificateService.delete(deletingCertificate.value.id)
    
    if (response.data.success) {
      // Remove from local array
      const index = certificates.value.findIndex(c => c.id === deletingCertificate.value.id)
      if (index > -1) {
        certificates.value.splice(index, 1)
      }
      
      alert('Certificate deleted successfully!')
      resetDeleting()
    }
  } catch (err) {
    console.error('Delete error:', err)
    alert(err.response?.data?.message || 'Failed to delete certificate')
  } finally {
    deleting.value = false
  }
}

const resetDeleting = () => {
  if (!deleting.value) {
    deletingCertificate.value = null
    showDeleteModal.value = false
  }
}

// Lifecycle
onMounted(() => {
  loadCertificates()
})
</script>