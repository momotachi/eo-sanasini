<script setup lang="ts">
const props = defineProps<{
  divisions: Array<{ id: number; discipline: string; age_category: string; gender: string; class_name: string; format: string }>;
}>();

const config = useRuntimeConfig();
const genderLabel: Record<string, string> = { PUTRA: 'Putra', PUTRI: 'Putri', MIXED: 'Campuran' };
const formatLabel: Record<string, string> = {
  FULL_KNOCKOUT: 'Knockout',
  GROUP_KNOCKOUT: 'Grup → Knockout',
  ROUND_ROBIN: 'Liga',
  SCORING: 'Penilaian',
  NON_COMPETITIVE: 'Non-kompetitif',
};

const competitive = computed(() => props.divisions.filter((d) => d.format !== 'NON_COMPETITIVE'));
const selectedId = ref<number | null>(competitive.value[0]?.id || null);

const selectedDivision = computed(() => competitive.value.find((d) => d.id === selectedId.value));

// Fetch bracket data when selection changes
const { data: bracketData, pending } = await useLazyFetch<any>(
  () => `${config.public.apiBase}/divisions/${selectedId.value}/bracket`,
  { watch: [selectedId] }
);

const stages = computed<Record<string, any[]>>(() => bracketData.value?.stages || {});
</script>

<template>
  <div class="space-y-4">
    <!-- Selector -->
    <div v-if="competitive.length > 0" class="flex flex-wrap gap-2">
      <button
        v-for="d in competitive"
        :key="d.id"
        @click="selectedId = d.id"
        :class="[
          'rounded-full border px-3 py-1 text-xs font-medium transition-colors',
          selectedId === d.id ? 'border-primary bg-primary text-primary-foreground' : 'bg-card text-muted-foreground hover:border-primary'
        ]"
      >
        {{ d.discipline }} · {{ d.age_category }} {{ genderLabel[d.gender] }} {{ d.class_name }}
      </button>
    </div>

    <div v-if="selectedDivision" class="text-xs text-muted-foreground">
      Format: <span class="font-medium text-foreground">{{ formatLabel[selectedDivision.format] }}</span>
    </div>

    <div v-if="pending" class="py-8 text-center text-sm text-muted-foreground">Memuat bracket...</div>

    <div v-else-if="Object.keys(stages).length === 0" class="rounded-lg border border-dashed p-8 text-center text-sm text-muted-foreground">
      Bracket belum dibuat. Panitia akan generate setelah pendaftaran ditutup.
    </div>

    <!-- Stages -->
    <div v-else class="space-y-6">
      <div v-for="(matches, stage) in stages" :key="stage">
        <h4 class="mb-3 text-sm font-semibold uppercase tracking-wider text-muted-foreground">{{ stage }}</h4>
        <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
          <div v-for="m in matches" :key="m.id" class="overflow-hidden rounded-md border bg-card text-sm">
            <div class="flex items-center justify-between bg-secondary/40 px-3 py-1 text-[10px] text-muted-foreground">
              <span>{{ m.round.replace(/_/g, ' ') }}</span>
              <span v-if="m.status === 'COMPLETED'" class="text-emerald-600">Selesai</span>
              <span v-else-if="m.status === 'BYE'">BYE</span>
            </div>
            <div class="divide-y">
              <div :class="['flex items-center justify-between px-3 py-2', m.winner?.id === m.participant_a?.id ? 'bg-emerald-50' : '']">
                <div class="min-w-0">
                  <div :class="['truncate', m.winner?.id === m.participant_a?.id ? 'font-semibold text-emerald-900' : !m.participant_a ? 'text-muted-foreground' : '']">
                    {{ m.participant_a?.name || '—' }}
                  </div>
                  <div v-if="m.participant_a?.contingent" class="truncate text-[10px] text-muted-foreground">
                    {{ m.participant_a.contingent.name }}
                  </div>
                </div>
                <span v-if="m.winner?.id === m.participant_a?.id" class="ml-1 shrink-0 text-emerald-600">✓</span>
              </div>
              <div :class="['flex items-center justify-between px-3 py-2', m.winner?.id === m.participant_b?.id ? 'bg-emerald-50' : '']">
                <div class="min-w-0">
                  <div :class="['truncate', m.winner?.id === m.participant_b?.id ? 'font-semibold text-emerald-900' : !m.participant_b ? 'text-muted-foreground' : '']">
                    {{ m.participant_b?.name || '—' }}
                  </div>
                  <div v-if="m.participant_b?.contingent" class="truncate text-[10px] text-muted-foreground">
                    {{ m.participant_b.contingent.name }}
                  </div>
                </div>
                <span v-if="m.winner?.id === m.participant_b?.id" class="ml-1 shrink-0 text-emerald-600">✓</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
