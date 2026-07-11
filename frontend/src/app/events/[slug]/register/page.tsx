import { notFound } from "next/navigation";
import { prisma } from "@/lib/prisma";
import { statusConfig, formatDateRange } from "@/lib/event";
import { RegisterForm } from "@/components/event/register-form";

export const metadata = { title: "Pendaftaran Peserta" };

export default async function RegisterPage({
  params,
}: {
  params: { slug: string };
}) {
  const event = await prisma.event.findUnique({
    where: { slug: params.slug, isPublic: true },
    include: {
      config: true,
      divisions: {
        orderBy: [
          { discipline: "asc" },
          { ageCategory: "asc" },
          { gender: "asc" },
        ],
      },
      contingents: { orderBy: { name: "asc" } },
    },
  });

  if (!event) notFound();

  // cek apakah pendaftaran dibuka
  const registrationOpen =
    event.status === "REGISTRATION_OPEN" || event.status === "DRAFT";

  const status = statusConfig[event.status];

  return (
    <div className="min-h-screen">
      {/* Header */}
      <div className="border-b bg-secondary/30">
        <div className="container py-10 md:py-12">
          <a
            href={`/events/${event.slug}`}
            className="text-sm text-muted-foreground hover:text-primary"
          >
            ← Kembali ke {event.name}
          </a>
          <h1 className="mt-3 font-serif text-3xl font-semibold tracking-tight md:text-4xl">
            Pendaftaran Peserta
          </h1>
          <p className="mt-2 text-muted-foreground">
            {event.name} · {formatDateRange(event.startDate, event.endDate)}
          </p>
        </div>
      </div>

      <div className="container max-w-2xl py-12">
        {!registrationOpen ? (
          <div className="rounded-lg border border-destructive/30 bg-destructive/5 p-8 text-center">
            <h2 className="font-serif text-xl font-semibold">Pendaftaran Ditutup</h2>
            <p className="mt-2 text-sm text-muted-foreground">
              Status event saat ini: <strong>{status.label}</strong>.
              Pendaftaran hanya dibuka saat event berstatus "Pendaftaran Dibuka".
            </p>
          </div>
        ) : (
          <>
            {/* Info */}
            {event.config && (
              <div className="mb-6 rounded-lg border bg-card p-4 text-sm">
                <p className="font-medium">Tipe Pendaftaran: Hybrid</p>
                <p className="mt-1 text-muted-foreground">
                  Atlet mendaftar sebagai individu, namun wajib memilih
                  kontingen (perguruan/provinsi). Hasil akan masuk ke klasemen
                  individu dan klasemen kontingen.
                </p>
              </div>
            )}

            <RegisterForm
              eventSlug={event.slug}
              divisions={event.divisions.map((d) => ({
                id: d.id,
                label: `${d.discipline} — ${d.ageCategory} ${d.gender} ${d.className}`,
              }))}
              contingents={event.contingents.map((c) => ({
                id: c.id,
                name: c.name,
              }))}
            />
          </>
        )}
      </div>
    </div>
  );
}
