<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Products Management</h1>
        <p class="text-gray-600 mt-1">Kelola produk dan inventory</p>
      </div>
      <button 
        v-permission="'create products'"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center"
      >
        <i class="fas fa-plus mr-2"></i>
        Add Product
      </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <!-- Filter & Search -->
      <div class="mb-6 flex gap-4">
        <div class="flex-1">
          <input 
            type="text" 
            placeholder="Search products..." 
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="">All Categories</option>
          <option value="electronics">Electronics</option>
          <option value="furniture">Furniture</option>
          <option value="clothing">Clothing</option>
        </select>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center mr-3">
                    <i class="fas fa-box text-gray-500"></i>
                  </div>
                  <div>
                    <div class="font-medium text-gray-900">Product Name</div>
                    <div class="text-sm text-gray-500">Sample description</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">PRD-001</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Electronics</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">150</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Rp 500,000</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <button 
                  v-permission="'edit products'"
                  class="text-blue-600 hover:text-blue-800 mr-3"
                >
                  <i class="fas fa-edit"></i>
                </button>
                <button 
                  v-permission="'delete products'"
                  class="text-red-600 hover:text-red-800"
                >
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="mt-6 flex items-center justify-between">
        <div class="text-sm text-gray-700">
          Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium">100</span> results
        </div>
        <div class="flex gap-2">
          <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">Previous</button>
          <button class="px-3 py-1 bg-blue-600 text-white rounded">1</button>
          <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">2</button>
          <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">Next</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Component logic here
</script>