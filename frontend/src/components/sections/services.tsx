import { Trophy, Building2, Plane, PartyPopper } from "lucide-react";

const services = [
  {
    icon: Trophy,
    title: "Kejuaraan Olahraga",
    desc: "Penyelenggaraan kejuaraan dengan sistem pendaftaran online, bracket live, klasemen otomatis, dan manajemen kontingen.",
    points: ["Sistem kompetisi (knockout, liga, grup)", "Klasemen individu & kontingen", "Jadwal & venue real-time"],
  },
  {
    icon: Building2,
    title: "MICE",
    desc: "Meeting, Incentive, Convention, Exhibition — dari seminar hingga konferensi skala besar dengan manajemen profesional.",
    points: ["Manajemen peserta & registrasi", "Layout venue & teknis", "Dokumentasi & pelaporan"],
  },
  {
    icon: PartyPopper,
    title: "Festival & Pameran",
    desc: "Festival publik, pameran produk, hingga pekan raya dengan pengelolaan tenant, panggung, dan hiburan.",
    points: ["Konsep tematik", "Pengelolaan tenant & vendor", "Program panggung"],
  },
  {
    icon: Plane,
    title: "Travel Agency",
    desc: "Penyelenggaraan perjalanan dan trip terkurasi — untuk kontingen, korporat, maupun kelompok komunitas.",
    points: ["Logistik & transportasi", "Akomodasi & itinerary", "Koordinasi lintas daerah"],
  },
];

export function Services() {
  return (
    <section id="services" className="border-y bg-secondary/30 py-20 md:py-28">
      <div className="container">
        <div className="mx-auto max-w-2xl text-center">
          <p className="text-sm font-semibold uppercase tracking-[0.2em] text-primary">
            Layanan Kami
          </p>
          <h2 className="mt-3 font-serif text-3xl font-semibold tracking-tight md:text-4xl">
            Empat pilar, satu standar: profesional.
          </h2>
        </div>

        <div className="mt-14 grid gap-6 md:grid-cols-2">
          {services.map((s) => (
            <div
              key={s.title}
              className="flex gap-5 rounded-lg border bg-card p-6 transition-shadow hover:shadow-md md:p-8"
            >
              <div className="flex h-12 w-12 shrink-0 items-center justify-center rounded-md bg-primary text-primary-foreground">
                <s.icon className="h-6 w-6" />
              </div>
              <div>
                <h3 className="font-serif text-xl font-semibold">{s.title}</h3>
                <p className="mt-2 text-sm text-muted-foreground">{s.desc}</p>
                <ul className="mt-3 space-y-1.5">
                  {s.points.map((point) => (
                    <li key={point} className="flex items-center gap-2 text-sm">
                      <span className="h-1 w-1 rounded-full bg-primary" />
                      {point}
                    </li>
                  ))}
                </ul>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
