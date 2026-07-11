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
const route = useRoute();
const selectedType = computed(() => (route.query.type as string) || 'ALL');

const { data: resp } = await useFetch<any>(`${config.public.apiBase}/events`, {
  query: computed(() => (selectedType.value !== 'ALL' ? { category: selectedType.value } : {})),
});
const events = computed<EventItem[]>(() => resp.value?.data || []);

const filters = [
  { value: 'ALL', label: 'Semua' },
  { value: 'SPORT', label: 'Kejuaraan' },
  { value: 'FESTIVAL', label: 'Festival' },
  { value: 'MICE', label: 'Konferensi' },
  { value: 'OTHER', label: 'Lainnya' },
];

const statusVariant: Record<string, string> = {
  REGISTRATION_OPEN: 'bg-amber-100 text-amber-800',
  UPCOMING: 'bg-primary text-primary-foreground',
  ONGOING: 'bg-emerald-100 text-emerald-800',
  COMPLETED: 'border border-border text-foreground',
};
const statusLabel: Record<string, string> = {
  DRAFT: 'Draft', REGISTRATION_OPEN: 'Pendaftaran Dibuka', UPCOMING: 'Segera Hadir',
  ONGOING: 'Sedang Berlangsung', COMPLETED: 'Selesai', CANCELLED: 'Dibatalkan',
};
const typeLabel: Record<string, string> = {
  CHAMPIONSHIP: 'Kejuaraan', LEAGUE: 'Liga', FESTIVAL: 'Festival', MICE: 'Konferensi', OTHER: 'Event',
};

function fmtRange(start: string, end: string) {
  const s = new Date(start), e = new Date(end);
  const fmt = (d: Date) => d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
  return s.toDateString() === e.toDateString() ? fmt(s) : `${fmt(s)} — ${fmt(e)}`;
}
</script>

<template>
  <div class="min-h-screen">
    <div class="border-b bg-secondary/30">
      <div class="container-x py-16 text-center md:py-20">
        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-primary">Katalog Event</p>
        <h1 class="mt-3 text-4xl font-semibold tracking-tight md:text-5xl">Semua Event Sanasini</h1>
        <p class="mx-auto mt-4 max-w-xl text-muted-foreground">
          Dari kejuaraan olahraga, festival, hingga konferensi.
        </p>
      </div>
    </div>

    <div class="container-x py-12">
      <div class="mb-10 flex flex-wrap items-center justify-center gap-2">
        <NuxtLink
          v-for="f in filters"
          :key="f.value"
          :to="f.value === 'ALL' ? '/events' : `/events?type=${f.value}`"
          :class="[
            'inline-flex items-center rounded-full border px-4 py-1.5 text-sm font-medium transition-colors',
            selectedType === f.value
              ? 'bg-primary text-primary-foreground border-primary'
              : 'bg-card text-muted-foreground hover:border-primary hover:text-foreground'
          ]"
        >
          {{ f.label }}
        </NuxtLink>
      </div>

      <div v-if="events.length === 0" class="rounded-lg border border-dashed p-16 text-center">
        <p class="text-2xl font-semibold">Belum ada event</p>
        <p class="mt-2 text-sm text-muted-foreground">Belum ada event untuk kategori ini.</p>
      </div>

      <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <NuxtLink v-for="event in events" :key="event.id" :to="`/events/${event.slug}`" class="group block">
          <div class="h-full overflow-hidden rounded-lg border bg-card transition-all hover:-translate-y-1 hover:shadow-lg">
            <div class="relative aspect-[16/9] overflow-hidden bg-gradient-to-br from-primary/90 to-primary/70">
              <div class="absolute inset-0 flex items-center justify-center p-6 text-center">
                <span class="font-serif text-xl font-semibold text-primary-foreground">{{ event.name }}</span>
              </div>
              <div class="absolute left-3 top-3">
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
  </div>
</template>
