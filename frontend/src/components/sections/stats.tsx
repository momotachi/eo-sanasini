const stats = [
  { value: "15+", label: "Tahun Pengalaman" },
  { value: "500+", label: "Event Ditangani" },
  { value: "50K+", label: "Peserta Terlibat" },
  { value: "100+", label: "Kontingen & Mitra" },
];

export function Stats() {
  return (
    <section className="border-b bg-secondary/40">
      <div className="container grid grid-cols-2 divide-x divide-border md:grid-cols-4">
        {stats.map((stat) => (
          <div key={stat.label} className="px-4 py-10 text-center">
            <div className="font-serif text-3xl font-semibold text-primary md:text-4xl">
              {stat.value}
            </div>
            <div className="mt-1 text-xs uppercase tracking-wider text-muted-foreground md:text-sm">
              {stat.label}
            </div>
          </div>
        ))}
      </div>
    </section>
  );
}
