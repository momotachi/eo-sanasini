<script setup lang="ts">
const year = new Date().getFullYear();
</script>

<template>
  <footer class="border-t bg-secondary/40">
    <div class="container grid gap-10 py-14 md:grid-cols-4">
      <div class="md:col-span-2">
        <div class="flex items-center gap-2">
          <div class="flex h-9 w-9 items-center justify-center rounded-md bg-primary font-serif text-lg font-bold text-primary-foreground">S</div>
          <span class="font-serif text-lg font-semibold">EO Sanasini</span>
        </div>
        <p class="mt-4 max-w-sm text-sm text-muted-foreground">
          Event Organizer, MICE &amp; Travel Agency yang berpengalaman sejak 2009.
          Kami menghadirkan pengalaman event yang terkurasi, profesional, dan berkesan.
        </p>
        <a
          href="https://www.instagram.com/eosanasini/"
          target="_blank"
          rel="noreferrer"
          class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline"
        >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
            <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
          </svg>
          @eosanasini
        </a>
      </div>

      <div>
        <h4 class="font-serif text-sm font-semibold uppercase tracking-wider">Navigasi</h4>
        <ul class="mt-4 space-y-2 text-sm text-muted-foreground">
          <li><a href="/#about" class="hover:text-foreground">Tentang</a></li>
          <li><a href="/#services" class="hover:text-foreground">Layanan</a></li>
          <li><a href="/events" class="hover:text-foreground">Event</a></li>
          <li><a href="/#contact" class="hover:text-foreground">Kontak</a></li>
        </ul>
      </div>

      <div>
        <h4 class="font-serif text-sm font-semibold uppercase tracking-wider">Kontak</h4>
        <ul class="mt-4 space-y-3 text-sm text-muted-foreground">
          <li>hello@sanasini.id</li>
          <li>+62 21 0000 0000</li>
          <li>Jakarta, Indonesia</li>
        </ul>
      </div>
    </div>
    <div class="border-t py-6">
      <div class="container flex flex-col items-center justify-between gap-2 text-xs text-muted-foreground sm:flex-row">
        <p>© {{ year }} EO Sanasini. Berpengalaman sejak 2009.</p>
        <p>Dirancang &amp; dikembangkan dengan perhatian terhadap detail.</p>
      </div>
    </div>
  </footer>
</template>
