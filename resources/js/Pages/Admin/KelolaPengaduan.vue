<script setup>
const props = defineProps({
  complaints: Array
})
</script>

<template>
  <div>
    <h1>Kelola Pengaduan Komplain</h1>

    <table border="1" cellpadding="10">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Toko</th>
          <th>Keluhan</th>
          <th>Masukan</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="(item, index) in complaints" :key="item.id">
          <td>{{ index + 1 }}</td>
          <td>{{ item.nama_toko }}</td>
          <td>{{ item.uraian_keluhan }}</td>
          <td>{{ item.masukan }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>