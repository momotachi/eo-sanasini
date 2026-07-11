"use client";

import { useState } from "react";
import { useTransition } from "react";
import { updateParticipantStatus, deleteParticipant } from "@/app/admin/events/[slug]/participants/actions";
import { Check, X, Ban, Trash2, Loader2 } from "lucide-react";

export function ParticipantActions({
  eventSlug,
  participantId,
  currentStatus,
}: {
  eventSlug: string;
  participantId: string;
  currentStatus: string;
}) {
  const [pending, startTransition] = useTransition();
  const [busy, setBusy] = useState<string | null>(null);

  function act(fn: () => Promise<{ success: boolean }>, key: string) {
    setBusy(key);
    startTransition(async () => {
      await fn();
      setBusy(null);
    });
  }

  const btn = "inline-flex items-center justify-center rounded-md p-1.5 transition-colors disabled:opacity-50";

  return (
    <div className="flex items-center justify-end gap-1">
      {currentStatus !== "APPROVED" && (
        <button
          title="Setujui"
          disabled={pending}
          onClick={() => act(
            () => updateParticipantStatus(eventSlug, participantId, "APPROVED"),
            "approve"
          )}
          className={`${btn} text-emerald-600 hover:bg-emerald-50`}
        >
          {busy === "approve" ? <Loader2 className="h-4 w-4 animate-spin" /> : <Check className="h-4 w-4" />}
        </button>
      )}
      {currentStatus !== "REJECTED" && (
        <button
          title="Tolak"
          disabled={pending}
          onClick={() => act(
            () => updateParticipantStatus(eventSlug, participantId, "REJECTED"),
            "reject"
          )}
          className={`${btn} text-destructive hover:bg-destructive/10`}
        >
          {busy === "reject" ? <Loader2 className="h-4 w-4 animate-spin" /> : <X className="h-4 w-4" />}
        </button>
      )}
      {currentStatus !== "WITHDRAWN" && (
        <button
          title="Mundur"
          disabled={pending}
          onClick={() => act(
            () => updateParticipantStatus(eventSlug, participantId, "WITHDRAWN"),
            "withdraw"
          )}
          className={`${btn} text-muted-foreground hover:bg-secondary`}
        >
          {busy === "withdraw" ? <Loader2 className="h-4 w-4 animate-spin" /> : <Ban className="h-4 w-4" />}
        </button>
      )}
      <button
        title="Hapus"
        disabled={pending}
        onClick={() => {
          if (confirm("Hapus peserta ini permanen?")) {
            act(() => deleteParticipant(eventSlug, participantId), "delete");
          }
        }}
        className={`${btn} text-muted-foreground hover:bg-destructive/10 hover:text-destructive`}
      >
        {busy === "delete" ? <Loader2 className="h-4 w-4 animate-spin" /> : <Trash2 className="h-4 w-4" />}
      </button>
    </div>
  );
}
