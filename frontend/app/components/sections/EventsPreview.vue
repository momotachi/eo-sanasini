<script setup lang="ts">
interface EventItem {
  id: number;
  name: string;
  slug: string;
  type: string;
  category: string;
  status: string;
  description: string | null;
  start_date: string;
  end_date: string;
  venue: string | null;
  poster_url: string | null;
}

const config = useRuntimeConfig();
const { data } = await useFetch<{ data: EventItem[] }>(`${config.public.apiBase}/events`, {
  transform: (r) => r,
});

const events = computed(() => (data.value as any)?.data?.slice(0, 3) || []);

const statusVariant: Record<string, string> = {
  REGISTRATION_OPEN: 'bg-amber-100 text-amber-800',
  UPCOMING: 'bg-primary text-primary-foreground',
  ONGOING: 'bg-emerald-100 text-emerald-800',
  COMPLETED: 'border border-border text-foreground',
};
const statusLabel: Record<string, string> = {
  DRAFT: 'Draft',
  REGISTRATION_OPEN: 'Pendaftaran Dibuka',
  UPCOMING: 'Segera Hadir',
  ONGOING: 'Sedang Berlangsung',
  COMPLETED: 'Selesai',
  CANCELLED: 'Dibatalkan',
};
const typeLabel: Record<string, string> = {
  CHAMPIONSHIP: 'Kejuaraan',
  LEAGUE: 'Liga',
  FESTIVAL: 'Festival',
  MICE: 'Konferensi',
  OTHER: 'Event',
};

function fmtRange(start: string, end: string) {
  const s = new Date(start), e = new Date(end);
  const fmt = (d: Date) => d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
  return s.toDateString() === e.toDateString() ? fmt(s) : `${fmt(s)} — ${fmt(e)}`;
}
</script>

<template>
  <section id="events" class="border-y bg-secondary/30 py-20 md:py-28">
    <div class="container">
      <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
        <div class="max-w-xl">
          <p class="text-sm font-semibold uppercase tracking-[0.2em] text-primary">Event Mendatang</p>
          <h2 class="mt-3 text-3xl font-semibold tracking-tight md:text-4xl">Event yang sedang &amp; akan datang.</h2>
        </div>
      </div>

      <div v-if="events.length === 0" class="mt-12 rounded-lg border border-dashed p-12 text-center">
        <p class="text-xl font-semibold">Segera Hadir</p>
        <p class="mt-2 text-sm text-muted-foreground">Event pertama kami sedang dalam persiapan.</p>
      </div>

      <div v-else class="mt-12 grid gap-6 md:grid-cols-3">
        <NuxtLink v-for="event in events" :key="event.id" :to="`/events/${event.slug}`" class="group block">
          <div class="h-full overflow-hidden rounded-lg border bg-card transition-all hover:-translate-y-1 hover:shadow-lg">
            <div class="relative aspect-[16/9] overflow-hidden bg-gradient-to-br from-primary/90 to-primary/70">
              <div class="absolute inset-0 flex items-center justify-center p-6 text-center">
                <span class="font-serif text-xl font-semibold text-primary-foreground">{{ event.name }}</span>
              </div>
              <div class="absolute left-3 top-3 flex gap-2">
                <span class="rounded-full bg-background/90 px-2.5 py-0.5 text-xs font-medium backdrop-blur">
                  {{ typeLabel[event.type] || event.type }}
                </span>
              </div>
              <div class="absolute right-3 top-3">
                <span :class="['rounded-full px-2.5 py-0.5 text-xs font-medium backdrop-blur bg-background/90', statusVariant[event.status]]">
                  {{ statusLabel[event.status] || event.status }}
                </span>
              </div>
            </div>
            <div class="p-5">
              <h3 class="text-lg font-semibold leading-tight group-hover:text-primary">{{ event.name }}</h3>
              <p v-if="event.description" class="mt-2 line-clamp-2 text-sm text-muted-foreground">{{ event.description }}</p>
              <div class="mt-4 text-xs text-muted-foreground">
                📅 {{ fmtRange(event.start_date, event.end_date) }}
                <span v-if="event.venue"> · 📍 {{ event.venue }}</span>
              </div>
            </div>
          </div>
        </NuxtLink>
      </div>
    </div>
  </section>
</template>
