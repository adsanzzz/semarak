<template>
  <div>
    <h2>Pembayaran QRIS</h2>

    <canvas ref="canvas"></canvas>

    <br><br>
    <button @click="generateQR">Bayar Sekarang</button>
  </div>
</template>

<script setup>
import { ref } from "vue";
import QRCode from "qrcode";

const canvas = ref(null);

const generateQR = async () => {
  try {
    const res = await fetch("/create-qris", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ amount: 10000 }),
    });

    const data = await res.json();
    console.log(data);

    if (data.qr_string) {
      QRCode.toCanvas(canvas.value, data.qr_string);
    } else {
      alert("QR tidak ditemukan");
    }
  } catch (err) {
    console.error(err);
    alert("Terjadi error");
  }
};
</script>