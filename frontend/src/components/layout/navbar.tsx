import Link from "next/link";
import { Button } from "@/components/ui/button";
import { Menu } from "lucide-react";

const navLinks = [
  { href: "/#about", label: "Tentang" },
  { href: "/#services", label: "Layanan" },
  { href: "/events", label: "Event" },
  { href: "/#portfolio", label: "Portofolio" },
  { href: "/#contact", label: "Kontak" },
];

export function Navbar() {
  return (
    <header className="sticky top-0 z-50 w-full border-b border-border/60 bg-background/85 backdrop-blur-md">
      <div className="container flex h-16 items-center justify-between">
        <Link href="/" className="flex items-center gap-2">
          <div className="flex h-9 w-9 items-center justify-center rounded-md bg-primary font-serif text-lg font-bold text-primary-foreground">
            S
          </div>
          <div className="leading-none">
            <span className="font-serif text-lg font-semibold tracking-tight">
              Sanasini
            </span>
            <span className="block text-[10px] uppercase tracking-[0.2em] text-muted-foreground">
              Event Organizer
            </span>
          </div>
        </Link>

        <nav className="hidden items-center gap-8 md:flex">
          {navLinks.map((link) => (
            <Link
              key={link.href}
              href={link.href}
              className="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
            >
              {link.label}
            </Link>
          ))}
        </nav>

        <div className="flex items-center gap-2">
          <Button asChild size="sm" className="hidden md:inline-flex">
            <Link href="/events">Lihat Event</Link>
          </Button>
          <Button variant="ghost" size="icon" className="md:hidden">
            <Menu className="h-5 w-5" />
          </Button>
        </div>
      </div>
    </header>
  );
}
