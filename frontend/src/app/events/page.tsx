import type { Metadata } from "next";
import { prisma } from "@/lib/prisma";
import { EventCard } from "@/components/event/event-card";
import { typeConfig } from "@/lib/event";
import { Trophy, Building2, PartyPopper, Plane } from "lucide-react";
import type { EventType } from "@prisma/client";

export const metadata: Metadata = {
  title: "Event",
  description: "Jelajahi semua event yang diselenggarakan EO Sanasini.",
};

// daftar tipe untuk filter
const filters: { value: EventType | "ALL"; label: string; icon: typeof Trophy }[] = [
  { value: "ALL", label: "Semua", icon: Trophy },
  { value: "CHAMPIONSHIP", label: "Kejuaraan", icon: Trophy },
  { value: "FESTIVAL", label: "Festival", icon: PartyPopper },
  { value: "MICE", label: "Konferensi", icon: Building2 },
  { value: "OTHER", label: "Lainnya", icon: Plane },
];

export default async function EventsPage({
  searchParams,
}: {
  searchParams: { type?: string };
}) {
  const selectedType = (searchParams.type as EventType) || "ALL";

  const where =
    selectedType !== "ALL"
      ? { isPublic: true, type: selectedType }
      : { isPublic: true };

  const events = await prisma.event.findMany({
    where,
    orderBy: [{ status: "asc" }, { startDate: "desc" }],
  });

  return (
    <div className="min-h-screen">
      {/* Header */}
      <div className="border-b bg-secondary/30">
        <div className="container py-16 text-center md:py-20">
          <p className="text-sm font-semibold uppercase tracking-[0.2em] text-primary">
            Katalog Event
          </p>
          <h1 className="mt-3 font-serif text-4xl font-semibold tracking-tight md:text-5xl">
            Semua Event Sanasini
          </h1>
          <p className="mx-auto mt-4 max-w-xl text-muted-foreground">
            Dari kejuaraan olahraga, festival, hingga konferensi. Temukan event
            yang sedang berlangsung dan akan datang.
          </p>
        </div>
      </div>

      <div className="container py-12">
        {/* Filter chips (client-side link) */}
        <div className="mb-10 flex flex-wrap items-center justify-center gap-2">
          {filters.map((f) => {
            const active = selectedType === f.value;
            const url = f.value === "ALL" ? "/events" : `/events?type=${f.value}`;
            const cls = active
              ? "bg-primary text-primary-foreground border-primary"
              : "bg-card text-muted-foreground border-border hover:border-primary hover:text-foreground";
            return (
              <a
                key={f.value}
                href={url}
                className={`inline-flex items-center gap-1.5 rounded-full border px-4 py-1.5 text-sm font-medium transition-colors ${cls}`}
              >
                <f.icon className="h-3.5 w-3.5" />
                {f.label}
              </a>
            );
          })}
        </div>

        {events.length === 0 ? (
          <div className="rounded-lg border border-dashed p-16 text-center">
            <p className="font-serif text-2xl font-semibold">Belum ada event</p>
            <p className="mt-2 text-sm text-muted-foreground">
              Belum ada event untuk kategori ini. Pantau terus!
            </p>
          </div>
        ) : (
          <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {events.map((event) => (
              <EventCard key={event.id} event={event} />
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
