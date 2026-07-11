import { Medal } from "@lucide-react";
import type { Contingent } from "@prisma/client";

// ranking klasemen kontingen — auto dihitung
export interface ContingentStanding {
  contingent: Pick<Contingent, "id" | "name" | "logoUrl">;
  gold: number;
  silver: number;
  bronze: number;
  total: number;
  rank: number;
}

const medalIcon = {
  gold: "🥇",
  silver: "🥈",
  bronze: "🥉",
};

export function MedalTable({ standings }: { standings: ContingentStanding[] }) {
  if (standings.length === 0) {
    return (
      <div className="rounded-lg border border-dashed p-8 text-center text-sm text-muted-foreground">
        Belum ada medali yang dibagikan.
      </div>
    );
  }

  return (
    <div className="overflow-hidden rounded-lg border">
      <table className="w-full text-sm">
        <thead className="bg-secondary/50">
          <tr className="text-left">
            <th className="px-4 py-3 font-semibold">#</th>
            <th className="px-4 py-3 font-semibold">Kontingen</th>
            <th className="px-3 py-3 text-center font-semibold" title="Gold">🥇</th>
            <th className="px-3 py-3 text-center font-semibold" title="Silver">🥈</th>
            <th className="px-3 py-3 text-center font-semibold" title="Bronze">🥉</th>
            <th className="px-4 py-3 text-center font-semibold">Total</th>
          </tr>
        </thead>
        <tbody className="divide-y">
          {standings.map((s) => (
            <tr
              key={s.contingent.id}
              className={s.rank === 1 ? "bg-accent/40" : ""}
            >
              <td className="px-4 py-3 font-medium">
                {s.rank === 1 ? "🏆" : ""} {s.rank}
              </td>
              <td className="px-4 py-3 font-medium">{s.contingent.name}</td>
              <td className="px-3 py-3 text-center font-semibold text-primary">{s.gold}</td>
              <td className="px-3 py-3 text-center text-muted-foreground">{s.silver}</td>
              <td className="px-3 py-3 text-center text-muted-foreground">{s.bronze}</td>
              <td className="px-4 py-3 text-center font-bold">{s.total}</td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
