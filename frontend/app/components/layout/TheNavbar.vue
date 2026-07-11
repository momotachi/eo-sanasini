<script setup lang="ts">
const navLinks = [
  { href: '/#about', label: 'Tentang' },
  { href: '/#services', label: 'Layanan' },
  { href: '/events', label: 'Event' },
  { href: '/#portfolio', label: 'Portofolio' },
  { href: '/#contact', label: 'Kontak' },
];
</script>

<template>
  <header class="sticky top-0 z-50 w-full border-b border-border/60 bg-background/85 backdrop-blur-md">
    <div class="container flex h-16 items-center justify-between">
      <NuxtLink to="/" class="flex items-center gap-2">
        <div class="flex h-9 w-9 items-center justify-center rounded-md bg-primary font-serif text-lg font-bold text-primary-foreground">
          S
        </div>
        <div class="leading-none">
          <span class="font-serif text-lg font-semibold tracking-tight">Sanasini</span>
          <span class="block text-[10px] uppercase tracking-[0.2em] text-muted-foreground">Event Organizer</span>
        </div>
      </NuxtLink>

      <nav class="hidden items-center gap-8 md:flex">
        <a
          v-for="link in navLinks"
          :key="link.href"
          :href="link.href"
          class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
        >
          {{ link.label }}
        </a>
      </nav>

      <div class="flex items-center gap-2">
        <a
          href="/events"
          class="hidden md:inline-flex items-center justify-center gap-2 h-9 px-4 rounded-md bg-primary text-primary-foreground text-sm font-medium hover:bg-primary/90 transition-colors"
        >
          Lihat Event
        </a>
      </div>
    </div>
  </header>
</template>
