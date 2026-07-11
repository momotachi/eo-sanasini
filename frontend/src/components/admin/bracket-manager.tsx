"use client";

import { useState, useTransition, useEffect } from "react";
import { generateBracket, setMatchResult } from "@/app/admin/events/[slug]/matches/actions";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Loader2, RefreshCw, Users2 } from "lucide-react";

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

interface DivisionInfo {
  id: string;
  discipline: string;
  ageCategory: string;
  gender: string;
  className: string;
  format: string;
  participantCount: number;
  matchCount: number;
}

interface Match {
  id: string;
  status: string;
  round: string;
  groupLabel: string | null;
  participantA: { id: string; name: string } | null;
  participantB: { id: string; name: string } | null;
  winner: { id: string; name: string } | null;
  scoreA: { value?: string } | null;
  scoreB: { value?: string } | null;
}

export function BracketManager({
  eventSlug,
  division,
}: {
  eventSlug: string;
  division: DivisionInfo;
}) {
  const [pending, startTransition] = useTransition();
  const [generating, setGenerating] = useState(false);
  const [message, setMessage] = useState<{ type: "success" | "error"; text: string } | null>(null);
  const [matches, setMatches] = useState<Match[]>([]);

  // load matches
  useEffect(() => {
    fetch(`/api/divisions/${division.id}/matches`)
      .then((r) => r.json())
      .then((data) => setMatches(data.matches || []))
      .catch(() => {});
  }, [division.id, message]);

  function handleGenerate() {
    setGenerating(true);
    startTransition(async () => {
      const result = await generateBracket(eventSlug, division.id);
      setMessage({
        type: result.success ? "success" : "error",
        text: result.message,
      });
      setGenerating(false);
    });
  }

  return (
    <div className="rounded-lg border bg-card">
      <div className="flex items-center justify-between border-b p-4">
        <div>
          <div className="flex items-center gap-2">
            <span className="font-serif font-semibold">{division.discipline}</span>
            <Badge variant="outline">
              {division.ageCategory} {genderLabel[division.gender]} {division.className}
            </Badge>
            <Badge variant="secondary">{formatLabel[division.format]}</Badge>
          </div>
          <div className="mt-1 flex items-center gap-3 text-xs text-muted-foreground">
            <span className="flex items-center gap-1">
              <Users2 className="h-3 w-3" />
              {division.participantCount} peserta
            </span>
            <span>{division.matchCount} pertandingan</span>
          </div>
        </div>
        <Button
          size="sm"
          variant={division.matchCount > 0 ? "outline" : "default"}
          disabled={pending || division.participantCount < 2}
          onClick={handleGenerate}
        >
          {generating ? (
            <Loader2 className="h-4 w-4 animate-spin" />
          ) : division.matchCount > 0 ? (
            <RefreshCw className="h-4 w-4" />
          ) : null}
          {division.matchCount > 0 ? "Regenerate" : "Generate Bracket"}
        </Button>
      </div>

      {message && (
        <div
          className={`px-4 py-2 text-xs ${
            message.type === "success"
              ? "bg-emerald-50 text-emerald-800"
              : "bg-destructive/5 text-destructive"
          }`}
        >
          {message.text}
        </div>
      )}

      {division.participantCount < 2 && (
        <div className="px-4 py-3 text-xs text-muted-foreground">
          Butuh minimal 2 peserta approved untuk generate bracket.
        </div>
      )}

      {/* Matches list */}
      {matches.length > 0 && (
        <div className="divide-y">
          {matches.map((m) => (
            <MatchRow key={m.id} eventSlug={eventSlug} match={m} />
          ))}
        </div>
      )}
    </div>
  );
}

function MatchRow({ eventSlug, match }: { eventSlug: string; match: Match }) {
  const [pending, startTransition] = useTransition();

  function setResult(winnerId: string) {
    startTransition(async () => {
      await setMatchResult(eventSlug, match.id, winnerId);
    });
  }

  return (
    <div className="flex items-center gap-3 px-4 py-3">
      <div className="w-20 shrink-0">
        <Badge variant="outline" className="text-[10px]">
          {match.round.replace(/_/g, " ")}
        </Badge>
        {match.groupLabel && (
          <span className="ml-1 text-[10px] text-muted-foreground">
            Grup {match.groupLabel}
          </span>
        )}
      </div>

      <div className="flex min-w-0 flex-1 items-center gap-2">
        {/* Peserta A */}
        <button
          disabled={pending || match.status === "COMPLETED"}
          onClick={() => match.participantA && setResult(match.participantA.id)}
          className={`min-w-0 flex-1 truncate rounded-md border px-3 py-1.5 text-left text-sm transition-colors ${
            match.winner?.id === match.participantA?.id
              ? "border-emerald-500 bg-emerald-50 font-medium text-emerald-900"
              : "hover:border-primary"
          } ${match.status === "COMPLETED" ? "cursor-default" : ""}`}
        >
          {match.participantA?.name || <span className="text-muted-foreground">—</span>}
          {match.scoreA?.value && (
            <span className="ml-2 text-xs text-muted-foreground">{match.scoreA.value}</span>
          )}
        </button>

        <span className="text-xs text-muted-foreground">vs</span>

        <button
          disabled={pending || match.status === "COMPLETED"}
          onClick={() => match.participantB && setResult(match.participantB.id)}
          className={`min-w-0 flex-1 truncate rounded-md border px-3 py-1.5 text-left text-sm transition-colors ${
            match.winner?.id === match.participantB?.id
              ? "border-emerald-500 bg-emerald-50 font-medium text-emerald-900"
              : "hover:border-primary"
          } ${match.status === "COMPLETED" ? "cursor-default" : ""}`}
        >
          {match.participantB?.name || <span className="text-muted-foreground">—</span>}
          {match.scoreB?.value && (
            <span className="ml-2 text-xs text-muted-foreground">{match.scoreB.value}</span>
          )}
        </button>
      </div>

      {pending && <Loader2 className="h-4 w-4 animate-spin text-muted-foreground" />}
      {match.status === "BYE" && <Badge variant="outline" className="text-[10px]">BYE</Badge>}
      {match.status === "COMPLETED" && <Badge variant="success" className="text-[10px]">Selesai</Badge>}
    </div>
  );
}
