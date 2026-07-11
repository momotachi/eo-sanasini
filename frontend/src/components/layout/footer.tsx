import Link from "next/link";
import { Mail, Phone, MapPin } from "lucide-react";
import { Instagram } from "@/components/icons/instagram";

export function Footer() {
  return (
    <footer className="border-t bg-secondary/40">
      <div className="container grid gap-10 py-14 md:grid-cols-4">
        <div className="md:col-span-2">
          <div className="flex items-center gap-2">
            <div className="flex h-9 w-9 items-center justify-center rounded-md bg-primary font-serif text-lg font-bold text-primary-foreground">
              S
            </div>
            <span className="font-serif text-lg font-semibold">
              EO Sanasini
            </span>
          </div>
          <p className="mt-4 max-w-sm text-sm text-muted-foreground">
            Event Organizer, MICE &amp; Travel Agency yang berpengalaman sejak
            2009. Kami menghadirkan pengalaman event yang terkurasi, profesional,
            dan berkesan.
          </p>
          <a
            href="https://www.instagram.com/eosanasini/"
            target="_blank"
            rel="noreferrer"
            className="mt-4 inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline"
          >
            <Instagram className="h-4 w-4" />
            @eosanasini
          </a>
        </div>

        <div>
          <h4 className="font-serif text-sm font-semibold uppercase tracking-wider">
            Navigasi
          </h4>
          <ul className="mt-4 space-y-2 text-sm text-muted-foreground">
            <li><Link href="/#about" className="hover:text-foreground">Tentang</Link></li>
            <li><Link href="/#services" className="hover:text-foreground">Layanan</Link></li>
            <li><Link href="/events" className="hover:text-foreground">Event</Link></li>
            <li><Link href="/#contact" className="hover:text-foreground">Kontak</Link></li>
          </ul>
        </div>

        <div>
          <h4 className="font-serif text-sm font-semibold uppercase tracking-wider">
            Kontak
          </h4>
          <ul className="mt-4 space-y-3 text-sm text-muted-foreground">
            <li className="flex items-center gap-2">
              <Mail className="h-4 w-4 shrink-0" /> hello@sanasini.id
            </li>
            <li className="flex items-center gap-2">
              <Phone className="h-4 w-4 shrink-0" /> +62 21 0000 0000
            </li>
            <li className="flex items-start gap-2">
              <MapPin className="mt-0.5 h-4 w-4 shrink-0" /> Jakarta, Indonesia
            </li>
          </ul>
        </div>
      </div>
      <div className="border-t py-6">
        <div className="container flex flex-col items-center justify-between gap-2 text-xs text-muted-foreground sm:flex-row">
          <p>© {new Date().getFullYear()} EO Sanasini. Berpengalaman sejak 2009.</p>
          <p>Dirancang &amp; dikembangkan dengan perhatian terhadap detail.</p>
        </div>
      </div>
    </footer>
  );
}
