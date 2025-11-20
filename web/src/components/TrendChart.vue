<template>
  <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
      <div>
        <h3 class="text-2xl font-bold text-gray-900">Statistik Pertumbuhan</h3>
        <p class="text-gray-500 text-sm mt-1">Data real-time dari sistem</p>
      </div>

      <!-- TOMBOL HANYA 30 HARI & TAHUNAN -->
      <div class="flex bg-gray-100 rounded-xl p-1 mt-4 sm:mt-0">
        <button 
  @click="period = '30days'"
  :class="period === '30days' ? 'bg-white shadow-sm text-indigo-600' : 'text-gray-600'"
  class="px-3 py-2 sm:px-6 sm:py-2.5 text-xs sm:text-sm rounded-lg font-semibold transition-all"
>
  30 Hari
</button>
<button 
  @click="period = 'annually'"
  class="px-3 py-2 sm:px-6 sm:py-2.5 text-xs sm:text-sm rounded-lg font-medium transition-all"
>
  Tahunan
</button>
      </div>
    </div>

   <div class="h-64 sm:h-80 lg:h-96">
      <Line :data="chartData" :options="chartOptions" />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { Line } from 'vue-chartjs'
import axios from '@/plugins/axios'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Filler
} from 'chart.js'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Filler)

// State
const period = ref('30days')
const rawData = ref({ labels: [], datasets: [] })

// Load data berdasarkan period
const loadData = async () => {
  try {
    const response = await axios.get(`/dashboard/trend/${period.value}`)
    rawData.value = response.data
  } catch (error) {
    console.error('Gagal ambil data trend:', error)
    // Fallback dummy kalau API error
    rawData.value = {
      labels: period.value === 'annually'
        ? ['2021', '2022', '2023', '2024', '2025']
        : ['01 Nov', '05 Nov', '10 Nov', '15 Nov', '20 Nov'],
      datasets: [{
        label: 'Certificates',
        data: period.value === 'annually'
          ? [200, 480, 890, 1250, 1480]
          : [80, 150, 280, 420, 580]
      }]
    }
  }
}

// Watch perubahan period & load pertama kali
watch(period, loadData)
onMounted(loadData)

// Chart data (langsung pakai data dari API)
const chartData = computed(() => ({
  labels: rawData.value.labels || [],
  datasets: (rawData.value.datasets || []).map(ds => ({
    ...ds,
    borderColor: '#6366f1',
    backgroundColor: 'rgba(99, 102, 241, 0.15)',
    borderWidth: 3,
    pointBackgroundColor: '#6366f1',
    pointRadius: 6,
    pointHoverRadius: 10,
    fill: true,
    tension: 0.4
  }))
}))

// Opsi chart
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: {
      backgroundColor: 'rgba(0,0,0,0.8)',
      cornerRadius: 12,
      displayColors: false,
      padding: 12
    }
  },
  scales: {
    y: { beginAtZero: true, grid: { color: '#f3f4f6' } },
    x: { grid: { display: false } }
  },
  animation: {
    duration: 1200,
    easing: 'easeOutQuart'
  }
}
</script>