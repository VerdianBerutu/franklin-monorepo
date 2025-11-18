// src/services/user.js
import axios from 'axios'

const BASE_URL = '/users'

export const userAPI = {
  /**
   * Get all users with pagination and filters
   * @param {Object} params - Query parameters (search, role, per_page, page)
   * @returns {Promise}
   */
  getUsers(params = {}) {
    return axios.get(BASE_URL, { params })
  },

  /**
   * Get single user by ID
   * @param {number} id - User ID
   * @returns {Promise}
   */
  getUser(id) {
    return axios.get(`${BASE_URL}/${id}`)
  },

  /**
   * Create new user
   * @param {Object} data - User data (name, email, password, password_confirmation, role)
   * @returns {Promise}
   */
  createUser(data) {
    return axios.post(BASE_URL, data)
  },

  /**
   * Update existing user
   * @param {number} id - User ID
   * @param {Object} data - User data to update
   * @returns {Promise}
   */
  updateUser(id, data) {
    return axios.put(`${BASE_URL}/${id}`, data)
  },

  /**
   * Delete user
   * @param {number} id - User ID
   * @returns {Promise}
   */
  deleteUser(id) {
    return axios.delete(`${BASE_URL}/${id}`)
  }
}