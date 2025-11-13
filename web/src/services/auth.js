import { api } from './api';

export const authAPI = {
  login: (email, password) => api.post('/login', { email, password }),
  register: (name, email, password, password_confirmation) =>
    api.post('/register', { name, email, password, password_confirmation }),
  logout: () => api.post('/logout'),
  getCurrentUser: () => api.get('/me'),
};

// Tambahkan helper functions untuk local storage
export const authService = {
  /**
   * Login and handle response
   */
  async login(email, password) {
    try {
      const response = await authAPI.login(email, password);
      
      if (response.data.success) {
        const token = response.data.data.token;
        const user = response.data.data.user;
        
        // Save to localStorage
        localStorage.setItem('auth_token', token);
        localStorage.setItem('user', JSON.stringify(user));
        
        return response.data;
      }
    } catch (error) {
      console.error('Login error:', error);
      throw error.response?.data || { message: 'Login failed' };
    }
  },

  /**
   * Logout user
   */
  async logout() {
    try {
      await authAPI.logout();
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user');
    }
  },

  /**
   * Get current user from localStorage
   */
  getCurrentUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
  },

  /**
   * Check if user is authenticated
   */
  isAuthenticated() {
    return !!localStorage.getItem('auth_token');
  },

  /**
   * Get auth token
   */
  getToken() {
    return localStorage.getItem('auth_token');
  }
};