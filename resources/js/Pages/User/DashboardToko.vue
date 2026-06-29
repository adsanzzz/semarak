<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'
import { ref, computed } from 'vue'

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({
      totalProduk: 0,
      totalPesanan: 0,
      totalPenjualan: 0,
      totalPendapatan: 0,
      averageOrderValue: 0,
      growth: {
        hasComparison: false,
        revenue: 0,
        sales: 0,
        orders: 0
      }
    })
  },
  topProducts: {
    type: Array,
    default: () => []
  },
  recentOrders: {
    type: Array,
    default: () => []
  },
  availableMonths: {
    type: Array,
    default: () => []
  },
  selectedMonth: {
    type: String,
    default: ''
  },
  chartData: {
    type: Array,
    default: () => []
  }
})

const activePoint = ref(null)

// Format currency
const formatIDR = (value) => {
  return 'Rp' + Number(value).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
}

// Convert format 'YYYY-MM' to 'Juni 2026'
const formatMonthLabel = (ymString) => {
  if (!ymString || ymString === 'semua') return 'Semua Waktu';
  const parts = ymString.split('-');
  if (parts.length !== 2) return ymString;
  const [year, month] = parts;
  const monthNames = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
  ];
  const mIndex = parseInt(month, 10) - 1;
  return `${monthNames[mIndex]} ${year}`;
}

const handleMonthChange = (event) => {
  const month = event.target.value
  router.get('/dashboard', { month }, {
    preserveState: true,
    preserveScroll: true
  })
}

// SVG Chart Calculations
const maxRevenue = computed(() => {
  if (!props.chartData || props.chartData.length === 0) return 100000;
  const max = Math.max(...props.chartData.map(d => d.revenue));
  return max > 0 ? max : 100000;
})

const chartPoints = computed(() => {
  const data = props.chartData || [];
  if (data.length === 0) return [];
  
  const width = 760;
  const height = 240;
  const padding = { top: 20, right: 20, bottom: 35, left: 70 };
  const chartW = width - padding.left - padding.right;
  const chartH = height - padding.top - padding.bottom;
  const maxRev = maxRevenue.value;
  
  return data.map((d, i) => {
    const x = padding.left + (data.length > 1 ? (i / (data.length - 1)) * chartW : chartW / 2);
    const y = padding.top + chartH - (d.revenue / maxRev) * chartH;
    return {
      x,
      y,
      label: d.label,
      revenue: d.revenue,
      qty: d.qty
    };
  })
})

const areaPath = computed(() => {
  const pts = chartPoints.value;
  if (pts.length === 0) return '';
  const padding = { top: 20, bottom: 35, left: 70 };
  const chartH = 240 - padding.top - padding.bottom;
  const baseY = padding.top + chartH;
  
  let d = `M ${pts[0].x} ${baseY}`;
  pts.forEach(p => {
    d += ` L ${p.x} ${p.y}`;
  });
  d += ` L ${pts[pts.length - 1].x} ${baseY} Z`;
  return d;
})

const linePath = computed(() => {
  const pts = chartPoints.value;
  if (pts.length === 0) return '';
  let d = `M ${pts[0].x} ${pts[0].y}`;
  for (let i = 1; i < pts.length; i++) {
    d += ` L ${pts[i].x} ${pts[i].y}`;
  }
  return d;
})

const yTicks = computed(() => {
  const max = maxRevenue.value;
  return [0, max * 0.25, max * 0.5, max * 0.75, max];
})

const getY = (val) => {
  const padding = { top: 20, bottom: 35, left: 70 };
  const chartH = 240 - padding.top - padding.bottom;
  return padding.top + chartH - (val / maxRevenue.value) * chartH;
}
</script>

<template>
  <Head title="Dashboard Toko - Analisis Lengkap" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h2 class="text-2xl font-extrabold text-[#0A3551] tracking-tight">
            Dashboard Toko
          </h2>
          <p class="text-sm text-gray-500 mt-1">
            Analisis performa penjualan toko Anda secara real-time.
          </p>
        </div>
        
        <!-- Dropdown Month Selector -->
        <div class="flex items-center gap-3">
          <label for="month-select" class="text-sm font-semibold text-gray-600 flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
            </svg>
            Periode Analisis:
          </label>
          <div class="relative">
            <select
              id="month-select"
              :value="selectedMonth"
              @change="handleMonthChange"
              class="appearance-none bg-white border border-gray-200 text-gray-700 font-medium py-2 pl-4 pr-10 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition duration-150 ease-in-out text-sm"
            >
              <option value="semua">Semua Waktu</option>
              <option v-for="month in availableMonths" :key="month" :value="month">
                {{ formatMonthLabel(month) }}
              </option>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
              <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="flex">
      <!-- Sidebar -->
      <Sidebar class="h-screen fixed left-0 top-0 bg-white shadow-md z-10" />

      <!-- Main Content Area -->
      <div class="flex-1 bg-gray-50 min-h-screen p-8 transition-all duration-300">
        <div class="max-w-7xl mx-auto space-y-8">

          <!-- 📊 Statistik Cards -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Card: Pendapatan -->
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between relative overflow-hidden group">
              <div class="absolute right-0 top-0 translate-x-2 -translate-y-2 opacity-5 group-hover:scale-110 transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-32 h-32 text-indigo-600">
                  <path d="M12 7.5a2.25 2.25 0 1 0 0 4.5 2.25 2.25 0 0 0 0-4.5Z" />
                  <path fill-rule="evenodd" d="M1.5 4.875C1.5 3.839 2.34 3 3.375 3h17.25c1.035 0 1.875.84 1.875 1.875v9.75c0 1.036-.84 1.875-1.875 1.875H3.375A1.875 1.875 0 0 1 1.5 14.625v-9.75ZM8.25 9.75a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0ZM18.75 9a.75.75 0 0 0-.75.75v.008c0 .414.336.75.75.75h.008a.75.75 0 0 0 .75-.75V9.75a.75.75 0 0 0-.75-.75h-.008ZM4.5 9.75A.75.75 0 0 1 5.25 9h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75H5.25a.75.75 0 0 1-.75-.75V9.75Z" clip-rule="evenodd" />
                  <path d="M2.25 18a.75.75 0 0 0 0 1.5c5.4 0 10.63.211 15.796.623a.75.75 0 0 0 .848-.62c.11-.75-.015-1.485-.373-2.124h-1.902a28.9 28.9 0 0 0-14.37 0.62Z" />
                </svg>
              </div>
              <div class="flex items-center gap-4">
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h16.5m-18 0a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 3.75 19.5h16.5a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 20.25 4.5m-16.5 0V3.75m0 7.5h18" />
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Total Pendapatan</p>
                  <h3 class="text-2xl font-black text-gray-800 mt-1">{{ formatIDR(stats.totalPendapatan) }}</h3>
                </div>
              </div>
              <div class="mt-4 flex items-center justify-between border-t border-gray-50 pt-3">
                <span class="text-xs text-gray-400 font-medium">Bulan Terpilih</span>
                <span v-if="stats.growth.hasComparison" 
                      :class="['text-xs font-bold px-2.5 py-0.5 rounded-full flex items-center gap-0.5', 
                               stats.growth.revenue >= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700']">
                  <span class="text-sm">{{ stats.growth.revenue >= 0 ? '↑' : '↓' }}</span>
                  {{ Math.abs(stats.growth.revenue) }}%
                </span>
                <span v-else class="text-xs text-gray-400 font-medium">-</span>
              </div>
            </div>

            <!-- Card: Produk Terjual -->
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between relative overflow-hidden group">
              <div class="absolute right-0 top-0 translate-x-2 -translate-y-2 opacity-5 group-hover:scale-110 transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-32 h-32 text-blue-600">
                  <path fill-rule="evenodd" d="M7.5 6v.75H5.513c-.96 0-1.764.724-1.865 1.679l-1.263 12A1.875 1.875 0 0 0 4.25 22.5h15.5a1.875 1.875 0 0 0 1.865-2.071l-1.263-12a1.875 1.875 0 0 0-1.865-1.679H16.5V6a4.5 4.5 0 1 0-9 0ZM12 3a3 3 0 0 0-3 3v.75h6V6a3 3 0 0 0-3-3Zm-3 8.25a.75.75 0 1 0 0 1.5.75.75 0 0 0 0-1.5Zm6 0a.75.75 0 1 0 0 1.5.75.75 0 0 0 0-1.5Z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Item Terjual</p>
                  <h3 class="text-2xl font-black text-gray-800 mt-1">{{ stats.totalPenjualan }} <span class="text-sm font-normal text-gray-400">Unit</span></h3>
                </div>
              </div>
              <div class="mt-4 flex items-center justify-between border-t border-gray-50 pt-3">
                <span class="text-xs text-gray-400 font-medium">Bulan Terpilih</span>
                <span v-if="stats.growth.hasComparison" 
                      :class="['text-xs font-bold px-2.5 py-0.5 rounded-full flex items-center gap-0.5', 
                               stats.growth.sales >= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700']">
                  <span class="text-sm">{{ stats.growth.sales >= 0 ? '↑' : '↓' }}</span>
                  {{ Math.abs(stats.growth.sales) }}%
                </span>
                <span v-else class="text-xs text-gray-400 font-medium">-</span>
              </div>
            </div>

            <!-- Card: Total Pesanan -->
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between relative overflow-hidden group">
              <div class="absolute right-0 top-0 translate-x-2 -translate-y-2 opacity-5 group-hover:scale-110 transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-32 h-32 text-emerald-600">
                  <path fill-rule="evenodd" d="M5.625 1.5H9a3.75 3.75 0 0 1 3.75 3.75v1.875c0 1.036.84 1.875 1.875 1.875H16.5a3.75 3.75 0 0 1 3.75 3.75v7.875c0 1.035-.84 1.875-1.875 1.875H5.625a1.875 1.875 0 0 1-1.875-1.875V3.375c0-1.036.84-1.875 1.875-1.875ZM12.75 12a.75.75 0 0 0-1.5 0v2.25H9a.75.75 0 0 0 0 1.5h2.25V18a.75.75 0 0 0 1.5 0v-2.25H15a.75.75 0 0 0 0-1.5h-2.25V12Z" clip-rule="evenodd" />
                  <path d="M14.25 5.25a1.5 1.5 0 0 0-1.5-1.5H9v3h3.75a1.5 1.5 0 0 0 1.5-1.5Z" />
                </svg>
              </div>
              <div class="flex items-center gap-4">
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Total Pesanan</p>
                  <h3 class="text-2xl font-black text-gray-800 mt-1">{{ stats.totalPesanan }} <span class="text-sm font-normal text-gray-400">Order</span></h3>
                </div>
              </div>
              <div class="mt-4 flex items-center justify-between border-t border-gray-50 pt-3">
                <span class="text-xs text-gray-400 font-medium">Bulan Terpilih</span>
                <span v-if="stats.growth.hasComparison" 
                      :class="['text-xs font-bold px-2.5 py-0.5 rounded-full flex items-center gap-0.5', 
                               stats.growth.orders >= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700']">
                  <span class="text-sm">{{ stats.growth.orders >= 0 ? '↑' : '↓' }}</span>
                  {{ Math.abs(stats.growth.orders) }}%
                </span>
                <span v-else class="text-xs text-gray-400 font-medium">-</span>
              </div>
            </div>

            <!-- Card: Produk Terdaftar -->
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between relative overflow-hidden group">
              <div class="absolute right-0 top-0 translate-x-2 -translate-y-2 opacity-5 group-hover:scale-110 transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-32 h-32 text-amber-600">
                  <path d="M11.53 3.007a.75.75 0 0 1 .94 0l8.25 6a.75.75 0 0 1 .28.58v8.75a.75.75 0 0 1-.75.75h-3.25a.75.75 0 0 1-.75-.75V15h-3.5v3.328a.75.75 0 0 1-.75.75H8.75a.75.75 0 0 1-.75-.75V15h-3.5v3.328a.75.75 0 0 1-.75.75H.75a.75.75 0 0 1-.75-.75V9.587a.75.75 0 0 1 .28-.58l8.25-6Z" />
                </svg>
              </div>
              <div class="flex items-center gap-4">
                <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Produk Aktif</p>
                  <h3 class="text-2xl font-black text-gray-800 mt-1">{{ stats.totalProduk }} <span class="text-sm font-normal text-gray-400">Item</span></h3>
                </div>
              </div>
              <div class="mt-4 flex items-center justify-between border-t border-gray-50 pt-3">
                <span class="text-xs text-gray-400 font-medium">Nilai Transaksi Rata-rata</span>
                <span class="text-xs font-bold text-gray-700">{{ formatIDR(stats.averageOrderValue) }}</span>
              </div>
            </div>

          </div>

          <!-- 📈 Grafik Tren Penjualan (Custom Interactive SVG Chart) -->
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-2">
              <div>
                <h3 class="text-lg font-bold text-gray-800">Tren Pendapatan Toko</h3>
                <p class="text-xs text-gray-500 mt-0.5">
                  Visualisasi grafik pendapatan harian/bulanan berdasarkan filter periode yang aktif.
                </p>
              </div>
              <div class="flex items-center gap-4 text-xs font-medium">
                <div class="flex items-center gap-1.5">
                  <span class="w-3.5 h-3.5 rounded bg-indigo-600"></span>
                  <span class="text-gray-600">Pendapatan (Rupiah)</span>
                </div>
              </div>
            </div>

            <!-- SVG Line/Area Graph Container -->
            <div class="relative w-full overflow-x-auto select-none">
              <div class="min-w-[760px] h-[260px] relative">
                
                <!-- Tooltip Overlay -->
                <div v-if="activePoint" 
                     class="absolute bg-gray-900/95 text-white p-3 rounded-lg shadow-xl text-xs z-20 pointer-events-none border border-gray-700"
                     :style="{ left: `${activePoint.x - 70}px`, top: `${activePoint.y - 85}px` }">
                  <p class="font-bold border-b border-gray-700 pb-1 mb-1 text-center text-yellow-400">
                    {{ selectedMonth === 'semua' ? activePoint.label : `Tanggal ${activePoint.label}` }}
                  </p>
                  <p class="flex justify-between gap-4"><span>Pendapatan:</span> <span class="font-bold text-right">{{ formatIDR(activePoint.revenue) }}</span></p>
                  <p class="flex justify-between gap-4"><span>Qty Terjual:</span> <span class="font-bold text-right">{{ activePoint.qty }} Unit</span></p>
                </div>

                <svg class="w-full h-full overflow-visible" viewBox="0 0 760 240">
                  <defs>
                    <!-- Background Area Gradient -->
                    <linearGradient id="revenueGradient" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="0%" stop-color="#4f46e5" stop-opacity="0.25" />
                      <stop offset="100%" stop-color="#4f46e5" stop-opacity="0.0" />
                    </linearGradient>
                  </defs>

                  <!-- Y-Axis Gridlines & Labels -->
                  <g class="text-[10px] fill-gray-400">
                    <g v-for="(tick, idx) in yTicks" :key="idx">
                      <line x1="70" :y1="getY(tick)" x2="740" :y2="getY(tick)" stroke="#f1f5f9" stroke-width="1.5" />
                      <text x="60" :y="getY(tick) + 3" text-anchor="end">{{ formatIDR(tick) }}</text>
                    </g>
                  </g>

                  <!-- Area Path under Curve -->
                  <path v-if="chartPoints.length > 0" :d="areaPath" fill="url(#revenueGradient)" />

                  <!-- Main Curve Line -->
                  <path v-if="chartPoints.length > 0" :d="linePath" fill="none" stroke="#4f46e5" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />

                  <!-- Interactive Data Point Circles & Trigger Rects -->
                  <g v-for="(pt, idx) in chartPoints" :key="idx">
                    <!-- Invisible wider trigger bars for better hover experience -->
                    <rect 
                      :x="pt.x - 12" 
                      y="15" 
                      width="24" 
                      height="190" 
                      fill="transparent" 
                      class="cursor-pointer"
                      @mouseenter="activePoint = pt"
                      @mouseleave="activePoint = null"
                    />

                    <!-- Data circle marker -->
                    <circle 
                      v-if="pt.revenue > 0"
                      :cx="pt.x" 
                      :cy="pt.y" 
                      r="4" 
                      fill="white" 
                      stroke="#4f46e5" 
                      stroke-width="2.5" 
                      class="transition-all duration-150 pointer-events-none"
                      :class="activePoint && activePoint.label === pt.label ? 'r-6 stroke-yellow-500 scale-125' : ''"
                    />
                  </g>

                  <!-- X-Axis Labels -->
                  <g class="text-[9px] fill-gray-400 font-medium" text-anchor="middle">
                    <!-- Display fewer labels if data points are high to prevent overlap (e.g. 31 days) -->
                    <text 
                      v-for="(pt, idx) in chartPoints" 
                      :key="idx"
                      v-show="chartPoints.length <= 15 || idx % 2 === 0"
                      :x="pt.x" 
                      y="230"
                    >
                      {{ pt.label }}
                    </text>
                  </g>
                </svg>
              </div>
            </div>
            <p class="text-[10px] text-gray-400 text-center mt-3">
              * Arahkan kursor / hover pada titik grafik untuk detail rekapan pendapatan dan kuantitas unit terjual.
            </p>
          </div>

          <!-- Bottom Columns: Top Selling & Recent Activity -->
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- 🏆 Column 1: Produk Terlaris (7/12 wide) -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:col-span-7 flex flex-col justify-between">
              <div>
                <div class="flex items-center justify-between border-b border-gray-50 pb-4 mb-4">
                  <div>
                    <h3 class="text-lg font-bold text-gray-800">Daftar Produk Terlaris</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Rekapitulasi 10 produk dengan volume penjualan tertinggi.</p>
                  </div>
                  <span class="px-2.5 py-1 text-[11px] font-bold bg-yellow-100 text-yellow-800 rounded-full">Top Product</span>
                </div>

                <div class="overflow-x-auto">
                  <table class="min-w-full text-sm">
                    <thead>
                      <tr class="text-left text-gray-400 font-semibold border-b border-gray-100">
                        <th class="pb-3 px-2 w-10 text-center">No</th>
                        <th class="pb-3 px-2">Info Produk</th>
                        <th class="pb-3 px-2 text-center w-24">Terjual</th>
                        <th class="pb-3 px-2 text-right w-32">Total Revenue</th>
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                      <tr v-for="product in topProducts" :key="product.product_id" class="group hover:bg-gray-50/50 transition">
                        <td class="py-4 px-2 text-center font-bold text-gray-500">
                          <!-- Top Rank Badges -->
                          <span v-if="product.rank === 1" class="flex items-center justify-center w-6 h-6 rounded-full bg-yellow-500 text-white text-xs">1</span>
                          <span v-else-if="product.rank === 2" class="flex items-center justify-center w-6 h-6 rounded-full bg-slate-300 text-slate-800 text-xs">2</span>
                          <span v-else-if="product.rank === 3" class="flex items-center justify-center w-6 h-6 rounded-full bg-amber-600 text-white text-xs">3</span>
                          <span v-else>{{ product.rank }}</span>
                        </td>
                        <td class="py-4 px-2">
                          <div class="flex items-center gap-3">
                            <img 
                              :src="product.image || 'https://via.placeholder.com/150'" 
                              class="w-12 h-12 object-cover rounded-lg border border-gray-100 shadow-sm"
                              alt="Product Image"
                            />
                            <div class="max-w-[200px]">
                              <p class="font-semibold text-gray-800 truncate group-hover:text-indigo-600 transition">{{ product.nama }}</p>
                              <p class="text-xs text-gray-400 mt-0.5">{{ product.category }} • {{ formatIDR(product.harga) }}</p>
                            </div>
                          </div>
                        </td>
                        <td class="py-4 px-2 text-center">
                          <span class="font-bold text-gray-800">{{ product.total_terjual }}</span>
                          <p class="text-[10px] text-gray-400 mt-0.5">kontribusi {{ product.percentage }}%</p>
                          <div class="w-full bg-gray-100 rounded-full h-1 mt-1 max-w-[80px] mx-auto overflow-hidden">
                            <div class="bg-indigo-600 h-1 rounded-full" :style="{ width: `${product.percentage}%` }"></div>
                          </div>
                        </td>
                        <td class="py-4 px-2 text-right font-black text-indigo-600">
                          {{ formatIDR(product.total_pendapatan) }}
                        </td>
                      </tr>
                      <tr v-if="topProducts.length === 0">
                        <td colspan="4" class="py-12 text-center text-gray-400">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 mx-auto text-gray-300 mb-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.008 1.24l.885 1.77a2.25 2.25 0 0 0 2.007 1.24h1.98a2.25 2.25 0 0 0 2.007-1.24l.885-1.77a2.25 2.25 0 0 1 2.007-1.24h3.86m-18 0h18" />
                          </svg>
                          Belum ada data penjualan produk terlaris di periode ini.
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <!-- 📑 Column 2: Transaksi Terbaru (5/12 wide) -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:col-span-5 flex flex-col justify-between">
              <div>
                <div class="flex items-center justify-between border-b border-gray-50 pb-4 mb-4">
                  <div>
                    <h3 class="text-lg font-bold text-gray-800">Transaksi Terbaru</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Daftar pesanan terbaru pembeli di toko Anda.</p>
                  </div>
                </div>

                <div class="space-y-4 max-h-[480px] overflow-y-auto pr-1">
                  <div v-for="order in recentOrders" :key="order.id" class="p-3.5 hover:bg-gray-50 border border-gray-100 hover:border-gray-200 rounded-xl transition flex justify-between items-start gap-4">
                    <div class="flex items-start gap-3">
                      <img 
                        :src="order.product_image || 'https://via.placeholder.com/150'" 
                        class="w-10 h-10 object-cover rounded-lg border border-gray-100 shadow-xs mt-0.5" 
                        alt="Product Image"
                      />
                      <div>
                        <h4 class="font-semibold text-gray-800 text-sm line-clamp-1">{{ order.product_name }}</h4>
                        <p class="text-xs text-gray-400 mt-0.5">Oleh: <span class="text-gray-600 font-medium">{{ order.buyer_name }}</span></p>
                        <p class="text-[10px] text-gray-400 mt-1">{{ order.created_at }} • Qty: {{ order.qty }}</p>
                      </div>
                    </div>
                    <div class="text-right flex flex-col items-end gap-1.5">
                      <span class="font-bold text-gray-800 text-sm">{{ formatIDR(order.total_harga) }}</span>
                      
                      <!-- Modern Status Badges -->
                      <span v-if="order.status === 'selesai'" class="px-2 py-0.5 text-[9px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">
                        Selesai
                      </span>
                      <span v-else-if="order.status === 'diproses' || order.status === 'dikirim'" class="px-2 py-0.5 text-[9px] font-bold rounded-full bg-blue-50 text-blue-700 border border-blue-100">
                        Proses
                      </span>
                      <span v-else-if="order.status === 'dibatalkan'" class="px-2 py-0.5 text-[9px] font-bold rounded-full bg-red-50 text-red-700 border border-red-100">
                        Batal
                      </span>
                      <span v-else class="px-2 py-0.5 text-[9px] font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-100">
                        Menunggu
                      </span>
                    </div>
                  </div>

                  <div v-if="recentOrders.length === 0" class="py-12 text-center text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 mx-auto text-gray-300 mb-2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Belum ada transaksi di periode ini.
                  </div>
                </div>
              </div>
            </div>

          </div>

        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

