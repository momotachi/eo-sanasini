<script setup lang="ts">
import { ref } from 'vue';

const navLinks = [
  { href: '/#about', label: 'Tentang' },
  { href: '/#services', label: 'Layanan' },
  { href: '/events', label: 'Event' },
  { href: '/#portfolio', label: 'Portofolio' },
  { href: '/#contact', label: 'Kontak' },
];

const mobileOpen = ref(false);

function toggleMobile() {
  mobileOpen.value = !mobileOpen.value;
}
function closeMobile() {
  mobileOpen.value = false;
}
</script>

<template>
  <header class="sticky top-0 z-50 w-full border-b border-border/60 bg-background/85 backdrop-blur-md">
    <div class="container-x flex h-16 items-center justify-between">
      <NuxtLink to="/" class="flex items-center gap-2" @click="closeMobile">
        <div class="flex h-9 w-9 items-center justify-center rounded-md bg-primary font-serif text-lg font-bold text-primary-foreground">
          S
        </div>
        <div class="leading-none">
          <span class="font-serif text-lg font-semibold tracking-tight">Sanasini</span>
          <span class="block text-[10px] uppercase tracking-[0.2em] text-muted-foreground">Event Organizer</span>
        </div>
      </NuxtLink>

      <!-- Desktop nav -->
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

      <!-- Desktop CTA -->
      <div class="hidden md:flex items-center gap-2">
        <a
          href="/events"
          class="inline-flex items-center justify-center h-9 px-4 rounded-md bg-primary text-primary-foreground text-sm font-medium hover:bg-primary/90 transition-colors"
        >
          Lihat Event
        </a>
      </div>

      <!-- Mobile hamburger button -->
      <button
        type="button"
        @click="toggleMobile"
        class="md:hidden inline-flex h-10 w-10 items-center justify-center rounded-md text-foreground hover:bg-secondary transition-colors"
        :aria-expanded="mobileOpen"
        aria-label="Toggle menu"
      >
        <svg v-if="!mobileOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-6 w-6">
          <line x1="4" y1="6" x2="20" y2="6"/>
          <line x1="4" y1="12" x2="20" y2="12"/>
          <line x1="4" y1="18" x2="20" y2="18"/>
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-6 w-6">
          <line x1="6" y1="6" x2="18" y2="18"/>
          <line x1="6" y1="18" x2="18" y2="6"/>
        </svg>
      </button>
    </div>

    <!-- Mobile menu dropdown -->
    <Transition
      enter-active-class="transition duration-150 ease-out"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-100 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div v-if="mobileOpen" class="md:hidden border-t bg-background">
        <nav class="container-x py-3 flex flex-col gap-1">
          <a
            v-for="link in navLinks"
            :key="link.href"
            :href="link.href"
            @click="closeMobile"
            class="block px-3 py-3 text-base font-medium text-foreground rounded-md hover:bg-secondary transition-colors"
          >
            {{ link.label }}
          </a>
          <a
            href="/events"
            @click="closeMobile"
            class="mt-2 inline-flex items-center justify-center h-11 px-4 rounded-md bg-primary text-primary-foreground text-base font-medium hover:bg-primary/90 transition-colors"
          >
            Lihat Event
          </a>
        </nav>
      </div>
    </Transition>
  </header>
</template>
