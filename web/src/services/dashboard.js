import axios from 'axios';

export const dashboardService = {
  /**
   * Get dashboard statistics
   */
  async getStats() {
    try {
      const response = await axios.get('/api/dashboard/stats');
      return response.data;
    } catch (error) {
      console.error('Error fetching dashboard stats:', error);
      throw error;
    }
  },

  /**
   * Get products data
   */
  async getProducts() {
    try {
      const response = await axios.get('/api/products');
      return response.data;
    } catch (error) {
      console.error('Error fetching products:', error);
      throw error;
    }
  },

  /**
   * Get users data
   */
  async getUsers() {
    try {
      const response = await axios.get('/api/users');
      return response.data;
    } catch (error) {
      console.error('Error fetching users:', error);
      throw error;
    }
  },

  /**
   * Get customers data
   */
  async getCustomers() {
    try {
      const response = await axios.get('/api/customers');
      return response.data;
    } catch (error) {
      console.error('Error fetching customers:', error);
      throw error;
    }
  },

  /**
   * Get sales data
   */
  async getSales() {
    try {
      const response = await axios.get('/api/sales');
      return response.data;
    } catch (error) {
      console.error('Error fetching sales:', error);
      throw error;
    }
  }
};