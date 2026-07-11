<script setup lang="ts">
const services = [
  {
    title: 'Kejuaraan Olahraga',
    desc: 'Penyelenggaraan kejuaraan dengan sistem pendaftaran online, bracket live, klasemen otomatis, dan manajemen kontingen.',
    points: ['Sistem kompetisi (knockout, liga, grup)', 'Klasemen individu & kontingen', 'Jadwal & venue real-time'],
  },
  {
    title: 'MICE',
    desc: 'Meeting, Incentive, Convention, Exhibition — dari seminar hingga konferensi skala besar dengan manajemen profesional.',
    points: ['Manajemen peserta & registrasi', 'Layout venue & teknis', 'Dokumentasi & pelaporan'],
  },
  {
    title: 'Festival & Pameran',
    desc: 'Festival publik, pameran produk, hingga pekan raya dengan pengelolaan tenant, panggung, dan hiburan.',
    points: ['Konsep tematik', 'Pengelolaan tenant & vendor', 'Program panggung'],
  },
  {
    title: 'Travel Agency',
    desc: 'Penyelenggaraan perjalanan dan trip terkurasi — untuk kontingen, korporat, maupun kelompok komunitas.',
    points: ['Logistik & transportasi', 'Akomodasi & itinerary', 'Koordinasi lintas daerah'],
  },
];
</script>

<template>
  <section id="services" class="border-y bg-secondary/30 py-20 md:py-28">
    <div class="container">
      <div class="mx-auto max-w-2xl text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-primary">Layanan Kami</p>
        <h2 class="mt-3 text-3xl font-semibold tracking-tight md:text-4xl">Empat pilar, satu standar: profesional.</h2>
      </div>

      <div class="mt-14 grid gap-6 md:grid-cols-2">
        <div v-for="s in services" :key="s.title" class="flex gap-5 rounded-lg border bg-card p-6 md:p-8">
          <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-md bg-primary text-primary-foreground">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-6 w-6"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          </div>
          <div>
            <h3 class="text-xl font-semibold">{{ s.title }}</h3>
            <p class="mt-2 text-sm text-muted-foreground">{{ s.desc }}</p>
            <ul class="mt-3 space-y-1.5">
              <li v-for="point in s.points" :key="point" class="flex items-center gap-2 text-sm">
                <span class="h-1 w-1 rounded-full bg-primary" />
                {{ point }}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
