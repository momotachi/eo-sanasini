<script setup lang="ts">
const route = useRoute();
const config = useRuntimeConfig();
const slug = computed(() => route.params.slug as string);

const { data: formData, error } = await useFetch<any>(`${config.public.apiBase}/events/${slug.value}/register-form`);
if (error.value || !formData.value) {
  throw createError({ statusCode: 404, statusMessage: 'Event tidak ditemukan' });
}

const divisions = computed(() => formData.value.divisions || []);
const contingents = computed(() => formData.value.contingents || []);
const registrationOpen = computed(() => formData.value.registration_open);

const submitting = ref(false);
const success = ref(false);
const errorMsg = ref<string | null>(null);
const fieldErrors = ref<Record<string, string>>({});

const genderLabel: Record<string, string> = { PUTRA: 'Putra', PUTRI: 'Putri', MIXED: 'Campuran' };

async function handleSubmit(e: Event) {
  e.preventDefault();
  submitting.value = true;
  errorMsg.value = null;
  fieldErrors.value = {};

  const form = e.target as HTMLFormElement;
  const fd = new FormData(form);
  const payload = {
    name: fd.get('name'),
    gender: fd.get('gender'),
    birth_date: fd.get('birth_date'),
    email: fd.get('email'),
    phone: fd.get('phone'),
    contingent_id: fd.get('contingent_id'),
    division_id: fd.get('division_id'),
  };

  try {
    await $fetch(`${config.public.apiBase}/events/${slug.value}/register`, {
      method: 'POST',
      body: payload,
    });
    success.value = true;
  } catch (err: any) {
    const data = err?.data;
    errorMsg.value = data?.message || 'Terjadi kesalahan. Coba lagi.';
    if (data?.errors) {
      fieldErrors.value = Object.fromEntries(
        Object.entries(data.errors).map(([k, v]: [string, any]) => [k, Array.isArray(v) ? v[0] : v])
      );
    }
  } finally {
    submitting.value = false;
  }
}

useHead({ title: `Pendaftaran — ${formData.value.event.name}` });
</script>

<template>
  <div class="min-h-screen">
    <div class="border-b bg-secondary/30">
      <div class="container-x py-10 md:py-12">
        <NuxtLink :to="`/events/${slug}`" class="text-sm text-muted-foreground hover:text-primary">
          ← Kembali ke {{ formData.event.name }}
        </NuxtLink>
        <h1 class="mt-3 text-3xl font-semibold tracking-tight md:text-4xl">Pendaftaran Peserta</h1>
      </div>
    </div>

    <div class="container-x max-w-2xl py-12">
      <!-- Sukses -->
      <div v-if="success" class="rounded-lg border border-emerald-200 bg-emerald-50 p-8 text-center">
        <div class="text-5xl">✓</div>
        <h2 class="mt-4 text-2xl font-semibold text-emerald-900">Pendaftaran Berhasil!</h2>
        <p class="mt-2 text-sm text-emerald-800">
          Data Anda tersimpan dengan status <strong>menunggu verifikasi</strong>. Panitia akan menghubungi Anda.
        </p>
        <div class="mt-6 flex flex-col gap-2 sm:flex-row sm:justify-center">
          <button @click="success = false" class="h-10 rounded-md border px-4 text-sm font-medium hover:bg-secondary">
            Daftar peserta lain
          </button>
          <NuxtLink :to="`/events/${slug}`" class="h-10 inline-flex items-center justify-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground">
            Lihat detail event
          </NuxtLink>
        </div>
      </div>

      <!-- Ditutup -->
      <div v-else-if="!registrationOpen" class="rounded-lg border border-destructive/30 bg-destructive/5 p-8 text-center">
        <h2 class="text-xl font-semibold">Pendaftaran Ditutup</h2>
        <p class="mt-2 text-sm text-muted-foreground">
          Status event saat ini tidak menerima pendaftaran baru.
        </p>
      </div>

      <!-- Form -->
      <form v-else @submit="handleSubmit" class="space-y-8">
        <div v-if="errorMsg" class="rounded-md border border-destructive/30 bg-destructive/5 px-4 py-3 text-sm text-destructive">
          ⚠ {{ errorMsg }}
        </div>

        <!-- Data Atlet -->
        <fieldset class="space-y-4">
          <legend class="text-lg font-semibold">Data Atlet</legend>

          <div>
            <label class="text-sm font-medium">Nama Lengkap <span class="text-destructive">*</span></label>
            <input name="name" type="text" required placeholder="cth. Budi Santoso"
              class="mt-1 w-full rounded-md border bg-background px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary" />
            <p v-if="fieldErrors.name" class="mt-1 text-xs text-destructive">{{ fieldErrors.name }}</p>
          </div>

          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="text-sm font-medium">Jenis Kelamin <span class="text-destructive">*</span></label>
              <select name="gender" required class="mt-1 w-full rounded-md border bg-background px-3 py-2 text-sm">
                <option value="PUTRA">Putra</option>
                <option value="PUTRI">Putri</option>
                <option value="MIXED">Campuran</option>
              </select>
            </div>
            <div>
              <label class="text-sm font-medium">Tanggal Lahir <span class="text-destructive">*</span></label>
              <input name="birth_date" type="date" required
                class="mt-1 w-full rounded-md border bg-background px-3 py-2 text-sm" />
            </div>
          </div>

          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="text-sm font-medium">Email</label>
              <input name="email" type="email" placeholder="opsional"
                class="mt-1 w-full rounded-md border bg-background px-3 py-2 text-sm" />
            </div>
            <div>
              <label class="text-sm font-medium">No. Telepon</label>
              <input name="phone" type="tel" placeholder="08xxxxxxxxxx"
                class="mt-1 w-full rounded-md border bg-background px-3 py-2 text-sm" />
            </div>
          </div>
        </fieldset>

        <!-- Kontingen -->
        <fieldset class="space-y-4">
          <legend class="text-lg font-semibold">Kontingen</legend>
          <div>
            <label class="text-sm font-medium">Mewakili Kontingen <span class="text-destructive">*</span></label>
            <select name="contingent_id" required
              class="mt-1 w-full rounded-md border bg-background px-3 py-2 text-sm">
              <option value="" disabled selected>Pilih kontingen</option>
              <option v-for="c in contingents" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>
        </fieldset>

        <!-- Kelas Pertandingan -->
        <fieldset class="space-y-4">
          <legend class="text-lg font-semibold">Kelas Pertandingan</legend>
          <div>
            <label class="text-sm font-medium">Pilih Kelas <span class="text-destructive">*</span></label>
            <select name="division_id" required
              class="mt-1 w-full rounded-md border bg-background px-3 py-2 text-sm">
              <option value="" disabled selected>Pilih kelas</option>
              <option v-for="d in divisions" :key="d.id" :value="d.id">
                {{ d.discipline }} — {{ d.age_category }} {{ genderLabel[d.gender] }} {{ d.class_name }}
              </option>
            </select>
          </div>
        </fieldset>

        <div class="flex justify-end gap-3">
          <NuxtLink :to="`/events/${slug}`" class="h-10 inline-flex items-center px-4 text-sm font-medium text-muted-foreground hover:text-foreground">
            Batal
          </NuxtLink>
          <button type="submit" :disabled="submitting"
            class="h-10 inline-flex items-center justify-center gap-2 rounded-md bg-primary px-6 text-sm font-medium text-primary-foreground hover:bg-primary/90 disabled:opacity-50">
            <span v-if="submitting">Mengirim...</span>
            <span v-else>Daftar Sekarang</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
