"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import { Button } from "@/components/ui/button";
import { registerParticipant } from "@/app/events/[slug]/register/actions";
import { CheckCircle2, Loader2, AlertCircle } from "lucide-react";

interface RegisterFormProps {
  eventSlug: string;
  divisions: { id: string; label: string }[];
  contingents: { id: string; name: string }[];
}

export function RegisterForm({ eventSlug, divisions, contingents }: RegisterFormProps) {
  const router = useRouter();
  const [submitting, setSubmitting] = useState(false);
  const [success, setSuccess] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [fieldErrors, setFieldErrors] = useState<Record<string, string>>({});

  async function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setSubmitting(true);
    setError(null);
    setFieldErrors({});

    const formData = new FormData(e.currentTarget);
    const result = await registerParticipant(eventSlug, formData);

    setSubmitting(false);

    if (result.success) {
      setSuccess(true);
    } else {
      setError(result.error);
      setFieldErrors(result.fieldErrors || {});
    }
  }

  // sukses state
  if (success) {
    return (
      <div className="rounded-lg border border-emerald-200 bg-emerald-50 p-8 text-center">
        <CheckCircle2 className="mx-auto h-12 w-12 text-emerald-600" />
        <h2 className="mt-4 font-serif text-2xl font-semibold text-emerald-900">
          Pendaftaran Berhasil!
        </h2>
        <p className="mt-2 text-sm text-emerald-800">
          Data Anda telah tersimpan dengan status <strong>menunggu verifikasi</strong>.
          Panitia akan menghubungi Anda untuk konfirmasi.
        </p>
        <div className="mt-6 flex flex-col gap-2 sm:flex-row sm:justify-center">
          <Button
            variant="outline"
            onClick={() => {
              setSuccess(false);
              router.refresh();
            }}
          >
            Daftar peserta lain
          </Button>
          <Button asChild>
            <a href={`/events/${eventSlug}`}>Lihat detail event</a>
          </Button>
        </div>
      </div>
    );
  }

  const inputClass = "mt-1 w-full rounded-md border bg-background px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-ring focus:ring-offset-1";
  const labelClass = "text-sm font-medium";
  const errorClass = "mt-1 text-xs text-destructive";

  return (
    <form onSubmit={handleSubmit} className="space-y-6">
      {error && (
        <div className="flex items-center gap-2 rounded-md border border-destructive/30 bg-destructive/5 px-4 py-3 text-sm text-destructive">
          <AlertCircle className="h-4 w-4 shrink-0" />
          {error}
        </div>
      )}

      {/* Data Atlet */}
      <fieldset className="space-y-4">
        <legend className="font-serif text-lg font-semibold">Data Atlet</legend>

        <div>
          <label htmlFor="name" className={labelClass}>
            Nama Lengkap <span className="text-destructive">*</span>
          </label>
          <input
            id="name"
            name="name"
            type="text"
            required
            placeholder="cth. Budi Santoso"
            className={inputClass}
          />
          {fieldErrors.name && <p className={errorClass}>{fieldErrors.name}</p>}
        </div>

        <div className="grid gap-4 sm:grid-cols-2">
          <div>
            <label htmlFor="gender" className={labelClass}>
              Jenis Kelamin <span className="text-destructive">*</span>
            </label>
            <select id="gender" name="gender" required className={inputClass}>
              <option value="PUTRA">Putra</option>
              <option value="PUTRI">Putri</option>
              <option value="MIXED">Campuran</option>
            </select>
          </div>
          <div>
            <label htmlFor="birthDate" className={labelClass}>
              Tanggal Lahir <span className="text-destructive">*</span>
            </label>
            <input
              id="birthDate"
              name="birthDate"
              type="date"
              required
              className={inputClass}
            />
            {fieldErrors.birthDate && <p className={errorClass}>{fieldErrors.birthDate}</p>}
          </div>
        </div>

        <div className="grid gap-4 sm:grid-cols-2">
          <div>
            <label htmlFor="email" className={labelClass}>Email</label>
            <input
              id="email"
              name="email"
              type="email"
              placeholder="opsional"
              className={inputClass}
            />
            {fieldErrors.email && <p className={errorClass}>{fieldErrors.email}</p>}
          </div>
          <div>
            <label htmlFor="phone" className={labelClass}>No. Telepon</label>
            <input
              id="phone"
              name="phone"
              type="tel"
              placeholder="08xxxxxxxxxx"
              className={inputClass}
            />
            {fieldErrors.phone && <p className={errorClass}>{fieldErrors.phone}</p>}
          </div>
        </div>
      </fieldset>

      {/* Kontingen */}
      <fieldset className="space-y-4">
        <legend className="font-serif text-lg font-semibold">Kontingen</legend>
        <div>
          <label htmlFor="contingentId" className={labelClass}>
            Mewakili Kontingen <span className="text-destructive">*</span>
          </label>
          <select id="contingentId" name="contingentId" required defaultValue="" className={inputClass}>
            <option value="" disabled>Pilih kontingen / perguruan / provinsi</option>
            {contingents.map((c) => (
              <option key={c.id} value={c.id}>{c.name}</option>
            ))}
          </select>
          {fieldErrors.contingentId && <p className={errorClass}>{fieldErrors.contingentId}</p>}
        </div>
      </fieldset>

      {/* Kelas Pertandingan */}
      <fieldset className="space-y-4">
        <legend className="font-serif text-lg font-semibold">Kelas Pertandingan</legend>
        <div>
          <label htmlFor="divisionId" className={labelClass}>
            Pilih Kelas <span className="text-destructive">*</span>
          </label>
          <select id="divisionId" name="divisionId" required defaultValue="" className={inputClass}>
            <option value="" disabled>Pilih kelas pertandingan</option>
            {divisions.map((d) => (
              <option key={d.id} value={d.id}>{d.label}</option>
            ))}
          </select>
          {fieldErrors.divisionId && <p className={errorClass}>{fieldErrors.divisionId}</p>}
        </div>
      </fieldset>

      <div className="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
        <Button asChild type="button" variant="ghost">
          <a href={`/events/${eventSlug}`}>Batal</a>
        </Button>
        <Button type="submit" disabled={submitting}>
          {submitting ? (
            <>
              <Loader2 className="h-4 w-4 animate-spin" />
              Mengirim...
            </>
          ) : (
            "Daftar Sekarang"
          )}
        </Button>
      </div>
    </form>
  );
}
