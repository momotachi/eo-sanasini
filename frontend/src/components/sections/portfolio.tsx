import { Trophy, Users, Calendar } from "lucide-react";

const portfolio = [
  {
    title: "Pekan Raya Indonesia",
    desc: "Festival nasional multi-aspek dengan ribuan pengunjung dan ratusan tenant.",
    metric: "10K+ pengunjung",
    metricIcon: Users,
    tag: "Festival",
  },
  {
    title: "Festival Olahraga (Feskul)",
    desc: "Penyelenggaraan festival olahraga dengan beragam cabang lomba antar-daerah.",
    metric: "Multi-cabang",
    metricIcon: Trophy,
    tag: "Olahraga",
  },
  {
    title: "Sanasini Taekwondo Championship",
    desc: "Kejuaraan taekwondo nasional dengan sistem pendaftaran online dan klasemen live.",
    metric: "Multi-kontingen",
    metricIcon: Calendar,
    tag: "Kejuaraan",
  },
];

export function Portfolio() {
  return (
    <section id="portfolio" className="border-y bg-secondary/30 py-20 md:py-28">
      <div className="container">
        <div className="mx-auto max-w-2xl text-center">
          <p className="text-sm font-semibold uppercase tracking-[0.2em] text-primary">
            Portofolio
          </p>
          <h2 className="mt-3 font-serif text-3xl font-semibold tracking-tight md:text-4xl">
            Event yang telah kami selenggarakan.
          </h2>
          <p className="mt-4 text-muted-foreground">
            Sepilihan event berskala besar yang menjadi bukti kapasitas dan
            pengalaman tim kami.
          </p>
        </div>

        <div className="mt-14 grid gap-6 md:grid-cols-3">
          {portfolio.map((p) => (
            <div
              key={p.title}
              className="group overflow-hidden rounded-lg border bg-card transition-all hover:-translate-y-1 hover:shadow-lg"
            >
              <div className="relative aspect-[4/3] bg-gradient-to-br from-primary/80 to-primary/50">
                <div
                  className="absolute inset-0 opacity-10"
                  style={{
                    backgroundImage:
                      "radial-gradient(circle at 1px 1px, currentColor 1px, transparent 0)",
                    backgroundSize: "20px 20px",
                  }}
                />
                <div className="absolute inset-0 flex flex-col items-center justify-center p-6 text-center text-primary-foreground">
                  <p.metricIcon className="h-10 w-10 opacity-90" />
                  <span className="mt-3 text-sm font-medium uppercase tracking-wider opacity-90">
                    {p.metric}
                  </span>
                </div>
              </div>
              <div className="p-5">
                <span className="text-xs font-medium uppercase tracking-wider text-primary">
                  {p.tag}
                </span>
                <h3 className="mt-1.5 font-serif text-lg font-semibold leading-tight">
                  {p.title}
                </h3>
                <p className="mt-2 text-sm text-muted-foreground">{p.desc}</p>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
