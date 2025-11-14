<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Users Management</h1>
        <p class="text-gray-600 mt-1">Kelola pengguna sistem</p>
      </div>
      <button 
        v-permission="'create users'"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center"
      >
        <i class="fas fa-plus mr-2"></i>
        Add User
      </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <!-- Filter & Search -->
      <div class="mb-6 flex gap-4">
        <div class="flex-1">
          <input 
            type="text" 
            placeholder="Search users..." 
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option value="">All Roles</option>
          <option value="admin">Admin</option>
          <option value="staff">Staff</option>
          <option value="user">User</option>
        </select>
      </div>

      <!-- Table -->
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                    <span class="text-blue-600 font-semibold">JD</span>
                  </div>
                  <div>
                    <div class="font-medium text-gray-900">John Doe</div>
                    <div class="text-sm text-gray-500">ID: 001</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">john@example.com</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">Admin</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <button 
                  v-permission="'edit users'"
                  class="text-blue-600 hover:text-blue-800 mr-3"
                >
                  <i class="fas fa-edit"></i>
                </button>
                <button 
                  v-permission="'delete users'"
                  class="text-red-600 hover:text-red-800"
                >
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
            <!-- Add more sample rows as needed -->
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="mt-6 flex items-center justify-between">
        <div class="text-sm text-gray-700">
          Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium">50</span> results
        </div>
        <div class="flex gap-2">
          <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">Previous</button>
          <button class="px-3 py-1 bg-blue-600 text-white rounded">1</button>
          <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">2</button>
          <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">3</button>
          <button class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">Next</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
// Component logic here
</script>