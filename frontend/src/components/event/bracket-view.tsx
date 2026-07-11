"use client";

import { useEffect, useState } from "react";
import { Badge } from "@/components/ui/badge";

interface MatchData {
  id: string;
  status: string;
  round: string;
  groupLabel: string | null;
  participantA: { id: string; name: string; contingent: { name: string } | null } | null;
  participantB: { id: string; name: string; contingent: { name: string } | null } | null;
  winner: { id: string } | null;
  scoreA: { value?: string } | null;
  scoreB: { value?: string } | null;
}

interface BracketViewProps {
  divisionId: string;
}

export function BracketView({ divisionId }: BracketViewProps) {
  const [stages, setStages] = useState<Record<string, MatchData[]>>({});
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch(`/api/divisions/${divisionId}/bracket`)
      .then((r) => r.json())
      .then((data) => {
        setStages(data.stages || {});
        setLoading(false);
      })
      .catch(() => setLoading(false));
  }, [divisionId]);

  if (loading) {
    return <div className="py-8 text-center text-sm text-muted-foreground">Memuat bracket...</div>;
  }

  const stageKeys = Object.keys(stages);
  if (stageKeys.length === 0) {
    return (
      <div className="rounded-lg border border-dashed p-8 text-center text-sm text-muted-foreground">
        Bracket belum dibuat. Panitia akan generate setelah pendaftaran ditutup.
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {stageKeys.map((stage) => (
        <div key={stage}>
          <h4 className="mb-3 text-sm font-semibold uppercase tracking-wider text-muted-foreground">
            {stage}
          </h4>
          <div className="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
            {stages[stage].map((m) => (
              <MatchCard key={m.id} match={m} />
            ))}
          </div>
        </div>
      ))}
    </div>
  );
}

function MatchCard({ match }: { match: MatchData }) {
  return (
    <div className="overflow-hidden rounded-md border bg-card text-sm">
      <div className="flex items-center justify-between bg-secondary/40 px-3 py-1 text-[10px] text-muted-foreground">
        <span>{match.round.replace(/_/g, " ")}</span>
        {match.status === "COMPLETED" && <span className="text-emerald-600">Selesai</span>}
        {match.status === "BYE" && <span>BYE</span>}
      </div>
      <div className="divide-y">
        <ParticipantRow
          name={match.participantA?.name}
          contingent={match.participantA?.contingent?.name}
          score={match.scoreA?.value}
          isWinner={match.winner?.id === match.participantA?.id}
        />
        <ParticipantRow
          name={match.participantB?.name}
          contingent={match.participantB?.contingent?.name}
          score={match.scoreB?.value}
          isWinner={match.winner?.id === match.participantB?.id}
        />
      </div>
    </div>
  );
}

function ParticipantRow({
  name,
  contingent,
  score,
  isWinner,
}: {
  name: string | null | undefined;
  contingent: string | null | undefined;
  score: string | undefined;
  isWinner: boolean;
}) {
  return (
    <div className={`flex items-center justify-between px-3 py-2 ${isWinner ? "bg-emerald-50" : ""}`}>
      <div className="min-w-0">
        <div className={`truncate ${isWinner ? "font-semibold text-emerald-900" : name ? "" : "text-muted-foreground"}`}>
          {name || "—"}
        </div>
        {contingent && (
          <div className="truncate text-[10px] text-muted-foreground">{contingent}</div>
        )}
      </div>
      {score && (
        <span className="ml-2 shrink-0 font-mono text-xs text-muted-foreground">{score}</span>
      )}
      {isWinner && <span className="ml-1 shrink-0 text-emerald-600">✓</span>}
    </div>
  );
}
