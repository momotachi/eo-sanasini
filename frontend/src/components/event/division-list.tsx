import type { Division } from "@prisma/client";
import { Badge } from "@/components/ui/badge";

// konfigurasi format label
const formatLabel: Record<string, string> = {
  FULL_KNOCKOUT: "Knockout",
  GROUP_KNOCKOUT: "Grup → Knockout",
  ROUND_ROBIN: "Liga",
  SCORING: "Penilaian",
  NON_COMPETITIVE: "Non-kompetitif",
};

const genderLabel: Record<string, string> = {
  PUTRA: "Putra",
  PUTRI: "Putri",
  MIXED: "Campuran",
};

interface DivisionListProps {
  divisions: Division[];
}

export function DivisionList({ divisions }: DivisionListProps) {
  // kelompokkan berdasarkan disiplin (Kyorugi, Poomsae, dll)
  const grouped = divisions.reduce<Record<string, Division[]>>((acc, d) => {
    (acc[d.discipline] = acc[d.discipline] || []).push(d);
    return acc;
  }, {});

  if (divisions.length === 0) {
    return (
      <div className="rounded-lg border border-dashed p-8 text-center text-sm text-muted-foreground">
        Daftar kelas pertandingan akan diumumkan segera.
      </div>
    );
  }

  return (
    <div className="space-y-8">
      {Object.entries(grouped).map(([discipline, divs]) => (
        <div key={discipline}>
          <h3 className="mb-3 font-serif text-lg font-semibold">{discipline}</h3>
          <div className="grid gap-2 sm:grid-cols-2">
            {divs.map((d) => (
              <div
                key={d.id}
                className="flex items-center justify-between rounded-md border bg-card px-4 py-3"
              >
                <div>
                  <div className="font-medium">
                    {d.ageCategory} — {genderLabel[d.gender]}
                  </div>
                  <div className="text-sm text-muted-foreground">{d.className}</div>
                </div>
                <Badge variant="outline" className="shrink-0">
                  {formatLabel[d.format] || d.format}
                </Badge>
              </div>
            ))}
          </div>
        </div>
      ))}
    </div>
  );
}
