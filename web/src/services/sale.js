import api from './api'

export const saleService = {
  /**
   * Get all sales with filters
   */
  getAll(params = {}) {
    const cleanParams = {}
    Object.keys(params).forEach(key => {
      if (params[key] !== '' && params[key] !== null && params[key] !== undefined) {
        cleanParams[key] = params[key]
      }
    })
    
    console.log('Sales API Params:', cleanParams)
    return api.get('/sales', { params: cleanParams }) // ← GUNAKAN /sales SAJA
  },

  /**
   * Get single sale
   */
  getById(id) {
    return api.get(`/sales/${id}`) // ← GUNAKAN /sales SAJA
  },

  /**
   * Create new sale - INI YANG PERLU DIFIX
   */
  create(data) {
    return api.post('/sales', data) // ← GUNAKAN /sales SAJA
  },

  /**
   * Update sale
   */
  update(id, data) {
    return api.put(`/sales/${id}`, data) // ← GUNAKAN /sales SAJA
  },

  /**
   * Delete sale
   */
  delete(id) {
    return api.delete(`/sales/${id}`) // ← GUNAKAN /sales SAJA
  },

  /**
   * Get sales statistics
   */
  getStatistics() {
    return api.get('/sales/statistics') // ← GUNAKAN /sales SAJA
  },

  /**
   * Export sales to Excel
   */
  async export(params = {}) {
    try {
      const cleanParams = {}
      Object.keys(params).forEach(key => {
        if (params[key] !== '' && params[key] !== null && params[key] !== undefined) {
          cleanParams[key] = params[key]
        }
      })

      const response = await api.get('/sales/export', { // ← GUNAKAN /sales SAJA
        params: cleanParams,
        responseType: 'blob'
      })

      // Create download link
      const url = window.URL.createObjectURL(new Blob([response.data]))
      const link = document.createElement('a')
      link.href = url
      
      let filename = 'sales_export.xlsx'
      const contentDisposition = response.headers['content-disposition']
      if (contentDisposition) {
        const filenameMatch = contentDisposition.match(/filename="?(.+)"?/i)
        if (filenameMatch && filenameMatch.length === 2) {
          filename = filenameMatch[1]
        }
      }
      
      link.setAttribute('download', filename)
      document.body.appendChild(link)
      link.click()
      link.remove()
      window.URL.revokeObjectURL(url)

      return { success: true, message: 'Export successful' }
    } catch (error) {
      console.error('Export error:', error)
      throw error
    }
  },

  // ... methods lainnya tetap sama
  formatCurrency(value) {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0
    }).format(value || 0)
  },

  getStatusClass(status) {
    const classes = {
      paid: 'bg-green-100 text-green-800',
      pending: 'bg-yellow-100 text-yellow-800',
      cancelled: 'bg-red-100 text-red-800',
      partial: 'bg-blue-100 text-blue-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
  },

  formatStatus(status) {
    const labels = {
      paid: 'Paid',
      pending: 'Pending',
      cancelled: 'Cancelled',
      partial: 'Partial'
    }
    return labels[status] || status
  }
}

export default saleService