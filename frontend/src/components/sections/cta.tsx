import Link from "next/link";
import { Button } from "@/components/ui/button";
import { ArrowRight } from "lucide-react";

export function CTA() {
  return (
    <section id="contact" className="py-20 md:py-28">
      <div className="container">
        <div className="relative overflow-hidden rounded-2xl bg-primary px-8 py-16 text-center text-primary-foreground md:px-16 md:py-20">
          {/* decorative */}
          <div
            className="pointer-events-none absolute inset-0 opacity-10"
            style={{
              backgroundImage:
                "radial-gradient(circle at 1px 1px, currentColor 1px, transparent 0)",
              backgroundSize: "24px 24px",
            }}
          />
          <div className="relative mx-auto max-w-2xl">
            <h2 className="font-serif text-3xl font-semibold tracking-tight md:text-4xl">
              Punya event yang ingin diwujudkan?
            </h2>
            <p className="mt-4 text-primary-foreground/80">
              Konsultasikan kebutuhan event Anda — dari kejuaraan olahraga,
              festival, hingga konferensi. Kami bantu dari konsep hingga eksekusi.
            </p>
            <div className="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
              <Button asChild size="lg" variant="secondary" className="w-full sm:w-auto">
                <Link href="/events">
                  Lihat Event Berjalan
                  <ArrowRight className="h-4 w-4" />
                </Link>
              </Button>
              <a
                href="https://www.instagram.com/eosanasini/"
                target="_blank"
                rel="noreferrer"
                className="inline-flex h-12 w-full items-center justify-center gap-2 rounded-md border border-primary-foreground/30 px-8 text-base font-medium transition-colors hover:bg-primary-foreground/10 sm:w-auto"
              >
                Hubungi via Instagram
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
