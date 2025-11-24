<template>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Total Revenue -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <div class="flex items-center justify-between">
        <div class="flex-1">
          <p class="text-sm text-gray-600 mb-1">Total Revenue</p>
          <p class="text-2xl font-bold text-gray-900">
            {{ loading ? '...' : formatCurrency(stats.total_revenue) }}
          </p>
          <p class="text-sm text-green-600 mt-1">
            <i class="fas fa-arrow-up"></i>
            {{ stats.revenue_growth || '+0%' }} vs last month
          </p>
        </div>
        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
          <i class="fas fa-dollar-sign text-blue-600 text-xl"></i>
        </div>
      </div>
    </div>

    <!-- Total Transactions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <div class="flex items-center justify-between">
        <div class="flex-1">
          <p class="text-sm text-gray-600 mb-1">Total Transactions</p>
          <p class="text-2xl font-bold text-gray-900">
            {{ loading ? '...' : stats.total_transactions }}
          </p>
          <p class="text-sm text-green-600 mt-1">
            <i class="fas fa-arrow-up"></i>
            {{ stats.transaction_growth || '+0' }} sales this month
          </p>
        </div>
        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
          <i class="fas fa-chart-line text-green-600 text-xl"></i>
        </div>
      </div>
    </div>

    <!-- Products Sold -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <div class="flex items-center justify-between">
        <div class="flex-1">
          <p class="text-sm text-gray-600 mb-1">Products Sold</p>
          <p class="text-2xl font-bold text-gray-900">
            {{ loading ? '...' : stats.products_sold }} <span class="text-sm font-normal">units</span>
          </p>
          <p class="text-sm text-green-600 mt-1">
            <i class="fas fa-arrow-up"></i>
            {{ stats.products_growth || '+0%' }} vs last month
          </p>
        </div>
        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
          <i class="fas fa-box text-purple-600 text-xl"></i>
        </div>
      </div>
    </div>

    <!-- This Month Revenue -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <div class="flex items-center justify-between">
        <div class="flex-1">
          <p class="text-sm text-gray-600 mb-1">This Month</p>
          <p class="text-2xl font-bold text-gray-900">
            {{ loading ? '...' : formatCurrency(stats.this_month_revenue) }}
          </p>
          <p class="text-sm text-green-600 mt-1">
            <i class="fas fa-arrow-up"></i>
            {{ stats.month_growth || '+0%' }} vs last month
          </p>
        </div>
        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
          <i class="fas fa-calendar text-yellow-600 text-xl"></i>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { saleService } from '@/services/sale'

defineProps({
  stats: {
    type: Object,
    default: () => ({
      total_revenue: 0,
      total_transactions: 0,
      products_sold: 0,
      this_month_revenue: 0,
      revenue_growth: '+0%',
      transaction_growth: '+0',
      products_growth: '+0%',
      month_growth: '+0%'
    })
  },
  loading: {
    type: Boolean,
    default: false
  }
})

const formatCurrency = (value) => {
  return saleService.formatCurrency(value)
}
</script>