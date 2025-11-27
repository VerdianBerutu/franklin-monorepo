import api from './api'

export const productService = {
  /**
   * Get all products
   */
  getAll(params = {}) {
    return api.get('/products', { params })
  },

  /**
   * Get single product
   */
  getById(id) {
    return api.get(`/products/${id}`)
  },

  /**
   * Create new product
   */
  create(data) {
    return api.post('/products', data)
  },

  /**
   * Update product
   */
  update(id, data) {
    return api.put(`/products/${id}`, data)
  },

  /**
   * Delete product
   */
  delete(id) {
    return api.delete(`/products/${id}`)
  }
}

export default productService