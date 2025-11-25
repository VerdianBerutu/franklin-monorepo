// web/src/services/customer.js
import api from './api'

export const customerService = {
  getAll(params = {}) {
    return api.get('/customers', { params })
  },
  create(data) {
    return api.post('/customers', data)
  },
  update(id, data) {
    return api.put(`/customers/${id}`, data)
  },
  delete(id) {
    return api.delete(`/customers/${id}`)
  }
}