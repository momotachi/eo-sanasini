<script setup lang="ts">
const portfolio = [
  {
    title: 'Pekan Raya Indonesia',
    desc: 'Festival nasional multi-aspek dengan ribuan pengunjung dan ratusan tenant.',
    metric: '10K+ pengunjung',
    tag: 'Festival',
  },
  {
    title: 'Festival Olahraga (Feskul)',
    desc: 'Penyelenggaraan festival olahraga dengan beragam cabang lomba antar-daerah.',
    metric: 'Multi-cabang',
    tag: 'Olahraga',
  },
  {
    title: 'Sanasini Taekwondo Championship',
    desc: 'Kejuaraan taekwondo nasional dengan sistem pendaftaran online dan klasemen live.',
    metric: 'Multi-kontingen',
    tag: 'Kejuaraan',
  },
];
</script>

<template>
  <section id="portfolio" class="py-20 md:py-28">
    <div class="container">
      <div class="mx-auto max-w-2xl text-center">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-primary">Portofolio</p>
        <h2 class="mt-3 text-3xl font-semibold tracking-tight md:text-4xl">Event yang telah kami selenggarakan.</h2>
      </div>

      <div class="mt-14 grid gap-6 md:grid-cols-3">
        <div v-for="p in portfolio" :key="p.title" class="group overflow-hidden rounded-lg border bg-card transition-all hover:-translate-y-1 hover:shadow-lg">
          <div class="relative aspect-[4/3] bg-gradient-to-br from-primary/80 to-primary/50">
            <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 1px 1px, currentColor 1px, transparent 0); background-size: 20px 20px;" />
            <div class="absolute inset-0 flex items-center justify-center p-6 text-center text-primary-foreground">
              <span class="text-sm font-medium uppercase tracking-wider opacity-90">{{ p.metric }}</span>
            </div>
          </div>
          <div class="p-5">
            <span class="text-xs font-medium uppercase tracking-wider text-primary">{{ p.tag }}</span>
            <h3 class="mt-1.5 text-lg font-semibold leading-tight">{{ p.title }}</h3>
            <p class="mt-2 text-sm text-muted-foreground">{{ p.desc }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
