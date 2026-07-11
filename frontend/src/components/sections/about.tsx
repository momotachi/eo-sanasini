import { Award, Users, MapPin, ShieldCheck } from "lucide-react";

const pillars = [
  {
    icon: Award,
    title: "Kurasi Premium",
    desc: "Setiap event dirancang dengan detail — dari konsep, eksekusi, hingga dokumentasi.",
  },
  {
    icon: Users,
    title: "Tim Berpengalaman",
    desc: "Tim yang sudah menangani event berskala nasional selama lebih dari satu dekade.",
  },
  {
    icon: ShieldCheck,
    title: "Eksekusi Terpercaya",
    desc: "Standar operasional yang teruji, dengan mitra logistik dan teknologi yang solid.",
  },
  {
    icon: MapPin,
    title: "Jangkauan Nasional",
    desc: "Pengalaman menangani kontingen dan peserta dari berbagai daerah di Indonesia.",
  },
];

export function About() {
  return (
    <section id="about" className="py-20 md:py-28">
      <div className="container">
        <div className="mx-auto max-w-2xl text-center">
          <p className="text-sm font-semibold uppercase tracking-[0.2em] text-primary">
            Tentang Kami
          </p>
          <h2 className="mt-3 font-serif text-3xl font-semibold tracking-tight md:text-4xl">
            Lebih dari sekadar penyelenggara event.
          </h2>
          <p className="mt-4 text-muted-foreground">
            EO Sanasini lahir dari kecintaan pada detail dan keinginan untuk
            menghadirkan pengalaman yang tidak terlupakan. Dengan portofolio
            yang mencakup Pekan Raya Indonesia, Festival Olahraga, hingga
            kejuaraan nasional, kami memahami apa yang dibutuhkan sebuah event
            untuk sukses.
          </p>
        </div>

        <div className="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
          {pillars.map((p) => (
            <div
              key={p.title}
              className="group rounded-lg border bg-card p-6 transition-shadow hover:shadow-md"
            >
              <div className="flex h-11 w-11 items-center justify-center rounded-md bg-accent text-accent-foreground transition-colors group-hover:bg-primary group-hover:text-primary-foreground">
                <p.icon className="h-5 w-5" />
              </div>
              <h3 className="mt-4 font-serif text-lg font-semibold">{p.title}</h3>
              <p className="mt-2 text-sm text-muted-foreground">{p.desc}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
