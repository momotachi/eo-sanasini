import { Quote } from "lucide-react";

const testimonials = [
  {
    quote:
      "Tim Sanasini sangat profesional. Event kami berjalan lancar dari awal hingga akhir, dengan detail yang benar-benar diperhatikan.",
    author: "Koord. Festival",
    role: "Pekan Raya Indonesia",
  },
  {
    quote:
      "Pengalaman mereka menangani kejuaraan terasa. Sistem pendaftaran online dan klasemen live-nya memudahkan seluruh kontingen.",
    author: "Ketua Kontingen",
    role: "Festival Olahraga",
  },
  {
    quote:
      "Komitmen pada kualitas jelas terlihat. Eksekusi tepat waktu, komunikatif, dan hasil dokumentasi sangat baik.",
    author: "Manajer Event",
    role: "Klien Korporat",
  },
];

export function Testimonials() {
  return (
    <section className="py-20 md:py-28">
      <div className="container">
        <div className="mx-auto max-w-2xl text-center">
          <p className="text-sm font-semibold uppercase tracking-[0.2em] text-primary">
            Testimoni
          </p>
          <h2 className="mt-3 font-serif text-3xl font-semibold tracking-tight md:text-4xl">
            Apa kata mereka tentang kami.
          </h2>
        </div>

        <div className="mt-14 grid gap-6 md:grid-cols-3">
          {testimonials.map((t, i) => (
            <figure
              key={i}
              className="flex flex-col justify-between rounded-lg border bg-card p-6"
            >
              <Quote className="h-8 w-8 text-primary/30" />
              <blockquote className="mt-4 text-sm leading-relaxed text-foreground/90">
                "{t.quote}"
              </blockquote>
              <figcaption className="mt-6 border-t pt-4">
                <div className="font-serif font-semibold">{t.author}</div>
                <div className="text-xs text-muted-foreground">{t.role}</div>
              </figcaption>
            </figure>
          ))}
        </div>
      </div>
    </section>
  );
}
