"use client";

import { useState } from "react";
import type { Division } from "@prisma/client";
import { BracketView } from "./bracket-view";

const genderLabel: Record<string, string> = {
  PUTRA: "Putra",
  PUTRI: "Putri",
  MIXED: "Campuran",
};

const formatLabel: Record<string, string> = {
  FULL_KNOCKOUT: "Knockout",
  GROUP_KNOCKOUT: "Grup → Knockout",
  ROUND_ROBIN: "Liga",
  SCORING: "Penilaian",
  NON_COMPETITIVE: "Non-kompetitif",
};

export function BracketExplorer({ divisions }: { divisions: Division[] }) {
  // hanya divisi yang punya format kompetitif
  const competitive = divisions.filter(
    (d) => d.format !== "NON_COMPETITIVE"
  );
  const [selected, setSelected] = useState<string>(competitive[0]?.id || "");

  if (competitive.length === 0) {
    return (
      <div className="rounded-lg border border-dashed p-8 text-center text-sm text-muted-foreground">
        Tidak ada kelas kompetitif.
      </div>
    );
  }

  const selectedDivision = competitive.find((d) => d.id === selected);

  return (
    <div className="space-y-4">
      {/* Selector */}
      <div className="flex flex-wrap gap-2">
        {competitive.map((d) => (
          <button
            key={d.id}
            onClick={() => setSelected(d.id)}
            className={`rounded-full border px-3 py-1 text-xs font-medium transition-colors ${
              selected === d.id
                ? "border-primary bg-primary text-primary-foreground"
                : "bg-card text-muted-foreground hover:border-primary hover:text-foreground"
            }`}
          >
            {d.discipline} · {d.ageCategory} {genderLabel[d.gender]} {d.className}
          </button>
        ))}
      </div>

      {/* Format badge */}
      {selectedDivision && (
        <div className="text-xs text-muted-foreground">
          Format: <span className="font-medium text-foreground">{formatLabel[selectedDivision.format]}</span>
        </div>
      )}

      {/* Bracket */}
      {selected && <BracketView divisionId={selected} />}
    </div>
  );
}
