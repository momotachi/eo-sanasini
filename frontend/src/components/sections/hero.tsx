import Link from "next/link";
import { Button } from "@/components/ui/button";
import { ArrowRight, CalendarDays } from "lucide-react";

export function Hero() {
  return (
    <section className="relative overflow-hidden border-b">
      {/* background pattern */}
      <div
        className="pointer-events-none absolute inset-0 opacity-[0.04]"
        style={{
          backgroundImage:
            "radial-gradient(circle at 1px 1px, hsl(var(--foreground)) 1px, transparent 0)",
          backgroundSize: "32px 32px",
        }}
      />
      <div className="container relative py-24 md:py-32">
        <div className="mx-auto max-w-3xl text-center">
          <div className="mb-6 inline-flex items-center gap-2 rounded-full border bg-card/60 px-4 py-1.5 text-xs font-medium text-muted-foreground backdrop-blur">
            <span className="h-1.5 w-1.5 rounded-full bg-primary" />
            Berpengalaman sejak 2009
          </div>

          <h1 className="font-serif text-4xl font-semibold leading-[1.1] tracking-tight md:text-6xl">
            Menghadirkan event yang
            <span className="block text-primary">berkesan &amp; terkurasi.</span>
          </h1>

          <p className="mx-auto mt-6 max-w-xl text-lg text-muted-foreground">
            Event Organizer, MICE &amp; Travel Agency yang menangani kejuaraan
            olahraga, festival, hingga konferensi berskala nasional — dengan
            standar eksekusi profesional.
          </p>

          <div className="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
            <Button asChild size="lg" className="w-full sm:w-auto">
              <Link href="/events">
                <CalendarDays className="h-4 w-4" />
                Jelajahi Event
              </Link>
            </Button>
            <Button asChild size="lg" variant="outline" className="w-full sm:w-auto">
              <Link href="/#contact">
                Hubungi Kami
                <ArrowRight className="h-4 w-4" />
              </Link>
            </Button>
          </div>
        </div>
      </div>
    </section>
  );
}
