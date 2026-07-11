import Link from "next/link";
import { prisma } from "@/lib/prisma";
import { EventCard } from "@/components/event/event-card";
import { ArrowRight } from "lucide-react";
import { Button } from "@/components/ui/button";

export async function EventsPreview() {
  // ambil 3 event public, prioritaskan upcoming/ongoing
  const events = await prisma.event.findMany({
    where: { isPublic: true },
    orderBy: [{ status: "asc" }, { startDate: "desc" }],
    take: 3,
  });

  return (
    <section id="events" className="py-20 md:py-28">
      <div className="container">
        <div className="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-end">
          <div className="max-w-xl">
            <p className="text-sm font-semibold uppercase tracking-[0.2em] text-primary">
              Event Mendatang
            </p>
            <h2 className="mt-3 font-serif text-3xl font-semibold tracking-tight md:text-4xl">
              Event yang sedang &amp; akan datang.
            </h2>
          </div>
          <Button asChild variant="ghost">
            <Link href="/events">
              Lihat semua
              <ArrowRight className="h-4 w-4" />
            </Link>
          </Button>
        </div>

        {events.length === 0 ? (
          <div className="mt-12 rounded-lg border border-dashed p-12 text-center">
            <p className="font-serif text-xl font-semibold">Segera Hadir</p>
            <p className="mt-2 text-sm text-muted-foreground">
              Event pertama kami sedang dalam persiapan. Pantau terus halaman
              ini.
            </p>
          </div>
        ) : (
          <div className="mt-12 grid gap-6 md:grid-cols-3">
            {events.map((event) => (
              <EventCard key={event.id} event={event} />
            ))}
          </div>
        )}
      </div>
    </section>
  );
}
