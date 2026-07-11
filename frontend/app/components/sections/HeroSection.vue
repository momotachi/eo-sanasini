<script setup lang="ts">
const pillars = [
  { value: '15+', label: 'Tahun Pengalaman' },
  { value: '500+', label: 'Event Ditangani' },
  { value: '50K+', label: 'Peserta Terlibat' },
  { value: '100+', label: 'Kontingen & Mitra' },
];
</script>

<template>
  <section class="relative overflow-hidden border-b">
    <div
      class="pointer-events-none absolute inset-0 opacity-[0.04]"
      style="background-image: radial-gradient(circle at 1px 1px, currentColor 1px, transparent 0); background-size: 32px 32px;"
    />
    <div class="container relative py-24 md:py-32">
      <div class="mx-auto max-w-3xl text-center">
        <div class="mb-6 inline-flex items-center gap-2 rounded-full border bg-card/60 px-4 py-1.5 text-xs font-medium text-muted-foreground backdrop-blur">
          <span class="h-1.5 w-1.5 rounded-full bg-primary" />
          Berpengalaman sejak 2009
        </div>
        <h1 class="text-4xl font-semibold leading-[1.1] tracking-tight md:text-6xl">
          Menghadirkan event yang
          <span class="block text-primary">berkesan &amp; terkurasi.</span>
        </h1>
        <p class="mx-auto mt-6 max-w-xl text-lg text-muted-foreground">
          Event Organizer, MICE &amp; Travel Agency yang menangani kejuaraan
          olahraga, festival, hingga konferensi berskala nasional — dengan
          standar eksekusi profesional.
        </p>
        <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
          <a href="/events" class="inline-flex w-full sm:w-auto h-12 items-center justify-center rounded-md bg-primary px-8 text-base font-medium text-primary-foreground hover:bg-primary/90 transition-colors">
            Jelajahi Event
          </a>
          <a href="/#contact" class="inline-flex w-full sm:w-auto h-12 items-center justify-center gap-2 rounded-md border px-8 text-base font-medium hover:bg-accent hover:text-accent-foreground transition-colors">
            Hubungi Kami →
          </a>
        </div>
      </div>
    </div>

    <!-- Stats bar -->
    <div class="border-t bg-secondary/40">
      <div class="container grid grid-cols-2 divide-x divide-border md:grid-cols-4">
        <div v-for="p in pillars" :key="p.label" class="px-4 py-10 text-center">
          <div class="text-3xl font-semibold text-primary md:text-4xl">{{ p.value }}</div>
          <div class="mt-1 text-xs uppercase tracking-wider text-muted-foreground md:text-sm">{{ p.label }}</div>
        </div>
      </div>
    </div>
  </section>
</template>
