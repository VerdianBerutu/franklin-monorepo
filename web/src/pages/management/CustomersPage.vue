<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Customers Management</h1>
        <p class="text-gray-600 mt-1">Kelola data pelanggan</p>
      </div>
      <button @click="openAddModal" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
        <i class="fas fa-plus"></i> Add Customer
      </button>
    </div>

    <AddCustomerModal :isOpen="showAddModal" @close="closeAddModal" @saved="handleSaved" />
    <EditCustomerModal :isOpen="showEditModal" :customer="editingCustomer" @close="closeEditModal" @saved="handleSaved" />

    <CustomersFilters v-model:search="filters.search" @filter="loadCustomers" />

    <CustomersTable
      :customers="customers"
      :loading="loading"
      :pagination="pagination"
      @edit="openEditModal"
      @delete="confirmDelete"
      @page-change="page => { filters.page = page; loadCustomers() }"
    />

    <!-- Delete Confirmation -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" @click.self="showDeleteModal = false">
      <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-8 text-center">
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
          <i class="fas fa-trash-alt text-red-600 text-3xl"></i>
        </div>
        <h3 class="text-2xl font-bold mb-3">Hapus Customer?</h3>
        <p class="text-gray-600 mb-6">
          Yakin ingin menghapus <strong>{{ deletingCustomer?.name }}</strong>?<br>
          Customer yang sudah punya transaksi tidak akan terhapus permanen.
        </p>
        <div class="flex gap-4">
          <button @click="showDeleteModal = false" class="flex-1 px-6 py-3 border rounded-lg hover:bg-gray-50">Batal</button>
          <button @click="handleDelete" :disabled="deleting" class="flex-1 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-70">
            {{ deleting ? 'Menghapus...' : 'Hapus' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { customerService } from '@/services/customer'
import AddCustomerModal from '@/components/customers/AddCustomerModal.vue'
import EditCustomerModal from '@/components/customers/EditCustomerModal.vue'
import CustomersTable from '@/components/customers/CustomersTable.vue'
import CustomersFilters from '@/components/customers/CustomersFilters.vue'

const customers = ref([])
const loading = ref(false)
const showAddModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const editingCustomer = ref(null)
const deletingCustomer = ref(null)
const deleting = ref(false)

const filters = reactive({
  search: '',
  page: 1,
  per_page: 10
})

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0
})

const loadCustomers = async () => {
  loading.value = true
  try {
    const res = await customerService.getAll(filters)
    if (res.data.success) {
      const d = res.data.data
      customers.value = d.data || d
      Object.assign(pagination, {
        current_page: d.current_page || 1,
        last_page: d.last_page || 1,
        total: d.total || customers.value.length
      })
    }
  } catch (err) {
    console.error(err)
    alert('Gagal memuat data customer')
  } finally {
    loading.value = false
  }
}

const openAddModal = () => showAddModal.value = true
const closeAddModal = () => showAddModal.value = false
const openEditModal = (cust) => {
  editingCustomer.value = cust
  showEditModal.value = true
}
const closeEditModal = () => {
  editingCustomer.value = null
  showEditModal.value = false
}
const handleSaved = () => {
  closeAddModal()
  closeEditModal()
  loadCustomers()
}
const confirmDelete = (cust) => {
  deletingCustomer.value = cust
  showDeleteModal.value = true
}
const handleDelete = async () => {
  deleting.value = true
  try {
    await customerService.delete(deletingCustomer.value.id)
    alert('Customer berhasil dihapus!')
    loadCustomers()
  } catch (err) {
    alert('Gagal menghapus: ' + (err.response?.data?.message || err.message))
  } finally {
    deleting.value = false
    showDeleteModal.value = false
    deletingCustomer.value = null
  }
}

onMounted(loadCustomers)
</script>