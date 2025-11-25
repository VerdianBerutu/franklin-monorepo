<template>
  <div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Sales Management</h1>
        <p class="text-gray-600 mt-1">Kelola transaksi penjualan</p>
      </div>
      <button 
        @click="openAddModal"
        v-permission="'create sales'"
        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2"
      >
        <i class="fas fa-plus"></i> Add Sale
      </button>
    </div>

    <!-- ADD SALE MODAL -->
    <AddSaleModal 
      v-if="showAddModal"
      :isOpen="showAddModal"
      :customers="customers"
      :products="products"
      @close="closeAddModal"
      @saved="handleSaleSaved"
    />

    <!-- Loading saat master data belum siap -->
    <div v-if="showAddModal && loadingMasterData" class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center">
      <div class="bg-white rounded-xl p-8 shadow-2xl text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-4 border-blue-600 mx-auto mb-4"></div>
        <p class="text-lg font-medium">Memuat data customer & produk...</p>
      </div>
    </div>

    <!-- Stats -->
    <SalesStats :stats="statistics" :loading="loadingStats" />

    <!-- Filters -->
    <SalesFilters 
      v-model:search="filters.search"
      v-model:dateRange="filters.dateRange"
      v-model:status="filters.status"
      @filter="handleFilter"
      @export="handleExport"
    />

    <!-- Table -->
    <SalesTable
      :sales="sales"
      :loading="loading"
      :pagination="pagination"
      @edit="() => alert('Edit coming soon!')"
      @delete="confirmDelete"
      @view="sale => window.open(`/sales/invoice/${sale.id}`, '_blank')"
      @print="sale => window.open(`/api/sales/${sale.id}/print`, '_blank')"
      @page-change="page => { filters.page = page; loadSales() }"
    />

    <!-- Delete Confirmation -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" @click.self="showDeleteModal = false">
      <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-8">
        <div class="text-center">
          <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-trash-alt text-red-600 text-3xl"></i>
          </div>
          <h3 class="text-2xl font-bold mb-3">Hapus Penjualan?</h3>
          <p class="text-gray-600 mb-6">
            Yakin ingin menghapus <strong>{{ deletingSale?.invoice_number }}</strong>?<br>
            Tindakan ini tidak dapat dibatalkan.
          </p>
          <div class="flex gap-4">
            <button @click="showDeleteModal = false" class="flex-1 px-6 py-3 border rounded-lg hover:bg-gray-50">
              Batal
            </button>
            <button @click="handleDelete" :disabled="deleting" class="flex-1 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-70">
              {{ deleting ? 'Menghapus...' : 'Hapus Permanen' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { saleService } from '@/services/sale'
import { customersAPI, productsAPI } from '@/services/api'
import AddSaleModal from '@/components/sales/AddSaleModal.vue'
import SalesStats from '@/components/sales/SalesStats.vue'
import SalesFilters from '@/components/sales/SalesFilters.vue'
import SalesTable from '@/components/sales/SalesTable.vue'

// === STATE ===
const sales = ref([])
const statistics = ref({
  total_revenue: 0,
  total_transactions: 0,
  products_sold: 0,
  this_month_revenue: 0
})
const customers = ref([])
const products = ref([])

const loading = ref(false)
const loadingStats = ref(false)
const loadingMasterData = ref(false)

const showAddModal = ref(false)
const showDeleteModal = ref(false)
const deletingSale = ref(null)
const deleting = ref(false)

const filters = reactive({
  search: '',
  dateRange: '',
  status: '',
  page: 1,
  per_page: 10
})

const pagination = reactive({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0
})

// === API CALLS ===
const loadSales = async () => {
  loading.value = true
  try {
    const res = await saleService.getAll(filters)
    if (res.data.success) {
      const data = res.data.data
      sales.value = Array.isArray(data) ? data : data.data || []

      Object.assign(pagination, {
        current_page: data.current_page || 1,
        last_page: data.last_page || 1,
        total: data.total || sales.value.length,
        from: data.from || 1,
        to: data.to || sales.value.length
      })
    }
  } catch (err) {
    console.error('Load sales error:', err)
  } finally {
    loading.value = false
  }
}

const loadStatistics = async () => {
  loadingStats.value = true
  try {
    const res = await saleService.getStatistics()
    if (res.data.success) statistics.value = res.data.data
  } catch (err) {
    console.error('Stats error:', err)
  } finally {
    loadingStats.value = false
  }
}

// FUNGSI INI SUDAH 100% BERSIH — TIDAK ADA DUMMY LAGI!
const loadMasterData = async () => {
  loadingMasterData.value = true
  try {
    console.log('Loading master data (customers & products)...')

    const [custRes, prodRes] = await Promise.all([
      customersAPI.getAll(),
      productsAPI.getAll()
    ])

    // Laravel paginate → data biasanya di .data.data.data
    customers.value = custRes.data?.data?.data || custRes.data?.data || custRes.data || []
    products.value  = prodRes.data?.data?.data || prodRes.data?.data || prodRes.data  || []

    console.log('Master data loaded!', {
      customers: customers.value.length,
      products: products.value.length
    })
  } catch (err) {
    console.error('GAGAL load master data:', err.response || err)
    customers.value = []
    products.value = []
    alert('Gagal memuat data Customer/Produk!\nPastikan Laravel backend sudah jalan.')
  } finally {
    loadingMasterData.value = false
  }
}

// === ACTIONS ===
const openAddModal = () => {
  console.log('Opening add sale modal...', { customers: customers.value.length, products: products.value.length })
  showAddModal.value = true
}

const closeAddModal = () => {
  showAddModal.value = false
}

const handleSaleSaved = async () => {
  showAddModal.value = false
  await Promise.all([loadSales(), loadStatistics()])
}

const handleFilter = () => {
  filters.page = 1
  loadSales()
}

const handleExport = async () => {
  try {
    await saleService.export(filters)
  } catch (err) {
    alert('Export gagal: ' + (err.response?.data?.message || err.message))
  }
}

const confirmDelete = (sale) => {
  deletingSale.value = sale
  showDeleteModal.value = true
}

const handleDelete = async () => {
  deleting.value = true
  try {
    await saleService.delete(deletingSale.value.id)
    alert('Penjualan berhasil dihapus!')
    await Promise.all([loadSales(), loadStatistics()])
  } catch (err) {
    alert('Gagal menghapus: ' + (err.response?.data?.message || err.message))
  } finally {
    deleting.value = false
    showDeleteModal.value = false
    deletingSale.value = null
  }
}

// === LIFECYCLE ===
onMounted(async () => {
  console.log('SalesPage mounted - loading data...')
  await loadMasterData()
  await Promise.all([loadSales(), loadStatistics()])
  console.log('Initial data loaded')
})
</script>