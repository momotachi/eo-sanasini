<script setup lang="ts">
const route = useRoute();
const config = useRuntimeConfig();
const slug = computed(() => route.params.slug as string);

const { data: resp, error } = await useFetch<any>(`${config.public.apiBase}/events/${slug.value}`);
if (error.value || !resp.value?.event) {
  throw createError({ statusCode: 404, statusMessage: 'Event tidak ditemukan', fatal: true });
}

const event = computed(() => resp.value.event);
const stats = computed(() => resp.value.stats);
const standings = computed<any[]>(() => resp.value.standings || []);

const statusLabel: Record<string, string> = {
  DRAFT: 'Draft', REGISTRATION_OPEN: 'Pendaftaran Dibuka', UPCOMING: 'Segera Hadir',
  ONGOING: 'Sedang Berlangsung', COMPLETED: 'Selesai', CANCELLED: 'Dibatalkan',
};
const statusVariant: Record<string, string> = {
  REGISTRATION_OPEN: 'bg-amber-100 text-amber-800',
  UPCOMING: 'bg-primary text-primary-foreground',
  ONGOING: 'bg-emerald-100 text-emerald-800',
};
const typeLabel: Record<string, string> = {
  CHAMPIONSHIP: 'Kejuaraan', LEAGUE: 'Liga', FESTIVAL: 'Festival', MICE: 'Konferensi', OTHER: 'Event',
};
const genderLabel: Record<string, string> = { PUTRA: 'Putra', PUTRI: 'Putri', MIXED: 'Campuran' };
const formatLabel: Record<string, string> = {
  FULL_KNOCKOUT: 'Knockout',
  GROUP_KNOCKOUT: 'Grup → Knockout',
  ROUND_ROBIN: 'Liga',
  SCORING: 'Penilaian',
  NON_COMPETITIVE: 'Non-kompetitif',
};
const contingentType: Record<string, string> = { CLUB: 'Perguruan', PROVINCE: 'Provinsi', COUNTRY: 'Negara', OTHER: 'Lainnya' };

function fmtRange(start: string, end: string) {
  const s = new Date(start), e = new Date(end);
  const fmt = (d: Date) => d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
  return s.toDateString() === e.toDateString() ? fmt(s) : `${fmt(s)} — ${fmt(e)}`;
}
function fmtDate(d: string) {
  return new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
}
function fmtTime(d: string) {
  return new Date(d).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
}

// Kelompokkan divisions per disiplin
const groupedDivisions = computed(() => {
  const map: Record<string, any[]> = {};
  for (const d of event.value.divisions || []) {
    (map[d.discipline] = map[d.discipline] || []).push(d);
  }
  return map;
});

// Kelompokkan schedule per hari
const groupedSchedule = computed(() => {
  const map: Record<string, any[]> = {};
  for (const s of event.value.schedule || []) {
    const day = fmtDate(s.time);
    (map[day] = map[day] || []).push(s);
  }
  return map;
});

useHead({ title: `${event.value.name} — EO Sanasini` });
</script>

<template>
  <div class="min-h-screen">
    <!-- HERO -->
    <div class="border-b bg-secondary/30">
      <div class="container-x py-12 md:py-16">
        <div class="flex flex-wrap items-center gap-2">
          <span class="rounded-full border bg-card px-2.5 py-0.5 text-xs font-medium">{{ typeLabel[event.type] || event.type }}</span>
          <span :class="['rounded-full px-2.5 py-0.5 text-xs font-medium', statusVariant[event.status] || 'border border-border']">
            {{ statusLabel[event.status] || event.status }}
          </span>
        </div>
        <h1 class="mt-4 max-w-3xl text-3xl font-semibold leading-tight tracking-tight md:text-5xl">{{ event.name }}</h1>
        <p v-if="event.description" class="mt-4 max-w-2xl text-muted-foreground md:text-lg">{{ event.description }}</p>

        <!-- CTA -->
        <div v-if="event.status === 'REGISTRATION_OPEN'" class="mt-6">
          <a :href="`/events/${event.slug}/register`" class="inline-flex h-12 items-center justify-center rounded-md bg-primary px-8 text-base font-medium text-primary-foreground hover:bg-primary/90">
            Daftar Sekarang
          </a>
        </div>

        <!-- Meta -->
        <div class="mt-6 grid gap-3 sm:grid-cols-3">
          <div class="flex items-center gap-3 rounded-md border bg-card px-4 py-3 text-sm">
            <span class="text-primary">📅</span>
            <div>
              <div class="font-medium">Tanggal</div>
              <div class="text-muted-foreground">{{ fmtRange(event.start_date, event.end_date) }}</div>
            </div>
          </div>
          <div v-if="event.venue" class="flex items-center gap-3 rounded-md border bg-card px-4 py-3 text-sm">
            <span class="text-primary">📍</span>
            <div>
              <div class="font-medium">Lokasi</div>
              <div class="text-muted-foreground">{{ event.venue }}</div>
            </div>
          </div>
          <div v-if="event.config" class="flex items-center gap-3 rounded-md border bg-card px-4 py-3 text-sm">
            <span class="text-primary">👥</span>
            <div>
              <div class="font-medium">Registrasi</div>
              <div class="text-muted-foreground">
                {{ event.config.registration_type === 'HYBRID' ? 'Individu + Kontingen' : event.config.registration_type === 'TEAM' ? 'Per Kontingen' : 'Individu' }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- STATS -->
    <div class="border-b">
      <div class="container-x grid grid-cols-3 divide-x divide-border">
        <div class="px-4 py-8 text-center">
          <div class="text-2xl font-semibold text-primary md:text-3xl">{{ stats.participants }}</div>
          <div class="mt-1 text-xs uppercase tracking-wider text-muted-foreground">Peserta</div>
        </div>
        <div class="px-4 py-8 text-center">
          <div class="text-2xl font-semibold text-primary md:text-3xl">{{ stats.contingents }}</div>
          <div class="mt-1 text-xs uppercase tracking-wider text-muted-foreground">Kontingen</div>
        </div>
        <div class="px-4 py-8 text-center">
          <div class="text-2xl font-semibold text-primary md:text-3xl">{{ stats.matches }}</div>
          <div class="mt-1 text-xs uppercase tracking-wider text-muted-foreground">Pertandingan</div>
        </div>
      </div>
    </div>

    <!-- CONTENT -->
    <div class="container-x space-y-16 py-14 md:py-20">
      <!-- DIVISIONS (sport only) -->
      <section v-if="event.category === 'SPORT' && (event.divisions?.length || 0) > 0" id="divisions">
        <div class="mb-6">
          <h2 class="text-2xl font-semibold tracking-tight md:text-3xl">Kelas Pertandingan</h2>
          <p class="mt-1.5 text-sm text-muted-foreground">Daftar kelas dan kategori yang dipertandingkan</p>
        </div>
        <div class="space-y-8">
          <div v-for="(divs, discipline) in groupedDivisions" :key="discipline">
            <h3 class="mb-3 text-lg font-semibold">{{ discipline }}</h3>
            <div class="grid gap-2 sm:grid-cols-2">
              <div v-for="d in divs" :key="d.id" class="flex items-center justify-between rounded-md border bg-card px-4 py-3">
                <div>
                  <div class="font-medium">{{ d.age_category }} — {{ genderLabel[d.gender] }}</div>
                  <div class="text-sm text-muted-foreground">{{ d.class_name }}</div>
                </div>
                <span class="shrink-0 rounded-full border px-2 py-0.5 text-xs">{{ formatLabel[d.format] || d.format }}</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- CONTINGENTS -->
      <section v-if="event.contingents?.length" id="contingents">
        <div class="mb-6">
          <h2 class="text-2xl font-semibold tracking-tight md:text-3xl">Kontingen Peserta</h2>
          <p class="mt-1.5 text-sm text-muted-foreground">{{ event.contingents.length }} kontingen terdaftar</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
          <div v-for="c in event.contingents" :key="c.id" class="flex items-center gap-3 rounded-md border bg-card px-4 py-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-md bg-accent font-serif font-semibold">
              {{ c.name.charAt(0) }}
            </div>
            <div class="min-w-0">
              <div class="truncate font-medium">{{ c.name }}</div>
              <div class="text-xs text-muted-foreground">{{ contingentType[c.type] || c.type }}</div>
            </div>
          </div>
        </div>
      </section>

      <!-- SCHEDULE -->
      <section v-if="event.schedule?.length" id="schedule">
        <div class="mb-6">
          <h2 class="text-2xl font-semibold tracking-tight md:text-3xl">Jadwal</h2>
          <p class="mt-1.5 text-sm text-muted-foreground">Timeline pertandingan per hari</p>
        </div>
        <div class="space-y-8">
          <div v-for="(items, day) in groupedSchedule" :key="day">
            <h3 class="mb-4 text-lg font-semibold text-primary">{{ day }}</h3>
            <div class="relative space-y-4 border-l-2 border-border pl-6">
              <div v-for="item in items" :key="item.id" class="relative">
                <span class="absolute -left-[27px] top-1.5 h-3 w-3 rounded-full border-2 border-background bg-primary" />
                <div class="flex flex-col sm:flex-row sm:items-baseline sm:gap-3">
                  <span class="shrink-0 font-mono text-sm font-medium text-primary">{{ fmtTime(item.time) }}</span>
                  <div>
                    <div class="font-medium">{{ item.title }}</div>
                    <div v-if="item.notes" class="text-sm text-muted-foreground">{{ item.notes }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- BRACKET (sport only) -->
      <section v-if="event.category === 'SPORT' && (event.divisions?.length || 0) > 0" id="bracket">
        <div class="mb-6">
          <h2 class="text-2xl font-semibold tracking-tight md:text-3xl">Bracket &amp; Hasil Pertandingan</h2>
          <p class="mt-1.5 text-sm text-muted-foreground">Pilih kelas untuk melihat bracket</p>
        </div>
        <EventsBracketExplorer :divisions="event.divisions" />
      </section>

      <!-- STANDINGS -->
      <section v-if="event.category === 'SPORT'" id="standings">
        <div class="mb-6">
          <h2 class="text-2xl font-semibold tracking-tight md:text-3xl">Klasemen Kontingen</h2>
          <p class="mt-1.5 text-sm text-muted-foreground">Perolehan medali akumulasi — Overall Champion</p>
        </div>
        <div v-if="standings.length === 0" class="rounded-lg border border-dashed p-8 text-center text-sm text-muted-foreground">
          Belum ada medali yang dibagikan.
        </div>
        <div v-else class="overflow-hidden rounded-lg border">
          <table class="w-full text-sm">
            <thead class="bg-secondary/50">
              <tr class="text-left">
                <th class="px-4 py-3 font-semibold">#</th>
                <th class="px-4 py-3 font-semibold">Kontingen</th>
                <th class="px-3 py-3 text-center font-semibold">🥇</th>
                <th class="px-3 py-3 text-center font-semibold">🥈</th>
                <th class="px-3 py-3 text-center font-semibold">🥉</th>
                <th class="px-4 py-3 text-center font-semibold">Total</th>
              </tr>
            </thead>
            <tbody class="divide-y">
              <tr v-for="s in standings" :key="s.contingent?.id || s.rank" :class="s.rank === 1 ? 'bg-accent/40' : ''">
                <td class="px-4 py-3 font-medium">{{ s.rank === 1 ? '🏆' : '' }} {{ s.rank }}</td>
                <td class="px-4 py-3 font-medium">{{ s.contingent?.name }}</td>
                <td class="px-3 py-3 text-center font-semibold text-primary">{{ s.gold }}</td>
                <td class="px-3 py-3 text-center text-muted-foreground">{{ s.silver }}</td>
                <td class="px-3 py-3 text-center text-muted-foreground">{{ s.bronze }}</td>
                <td class="px-4 py-3 text-center font-bold">{{ s.total }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </div>
</template>
