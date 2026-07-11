<script setup lang="ts">
const pillars = [
  { title: 'Kurasi Premium', desc: 'Setiap event dirancang dengan detail — dari konsep, eksekusi, hingga dokumentasi.' },
  { title: 'Tim Berpengalaman', desc: 'Tim yang sudah menangani event berskala nasional selama lebih dari satu dekade.' },
  { title: 'Eksekusi Terpercaya', desc: 'Standar operasional yang teruji, dengan mitra logistik dan teknologi yang solid.' },
  { title: 'Jangkauan Nasional', desc: 'Pengalaman menangani kontingen dan peserta dari berbagai daerah di Indonesia.' },
];
</script>

<template>
  <section id="about" class="py-20 md:py-28">
    <div class="container">
      <div class="mx-auto max-w-2xl text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-primary">Tentang Kami</p>
        <h2 class="mt-3 text-3xl font-semibold tracking-tight md:text-4xl">Lebih dari sekadar penyelenggara event.</h2>
        <p class="mt-4 text-muted-foreground">
          EO Sanasini lahir dari kecintaan pada detail dan keinginan untuk menghadirkan
          pengalaman yang tidak terlupakan. Dengan portofolio yang mencakup Pekan Raya
          Indonesia, Festival Olahraga, hingga kejuaraan nasional, kami memahami apa yang
          dibutuhkan sebuah event untuk sukses.
        </p>
      </div>

      <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div v-for="p in pillars" :key="p.title" class="group rounded-lg border bg-card p-6 transition-shadow hover:shadow-md">
          <div class="flex h-11 w-11 items-center justify-center rounded-md bg-accent text-accent-foreground transition-colors group-hover:bg-primary group-hover:text-primary-foreground">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-5 w-5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
          </div>
          <h3 class="mt-4 text-lg font-semibold">{{ p.title }}</h3>
          <p class="mt-2 text-sm text-muted-foreground">{{ p.desc }}</p>
        </div>
      </div>
    </div>
  </section>
</template>
