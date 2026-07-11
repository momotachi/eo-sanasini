import type { Event, EventStatus, EventType } from "@prisma/client";

// Pemetaan status -> tampilan
export const statusConfig: Record<EventStatus, { label: string; variant: "default" | "secondary" | "success" | "warning" | "destructive" | "outline" }> = {
  DRAFT: { label: "Draft", variant: "secondary" },
  REGISTRATION_OPEN: { label: "Pendaftaran Dibuka", variant: "warning" },
  UPCOMING: { label: "Segera Hadir", variant: "default" },
  ONGOING: { label: "Sedang Berlangsung", variant: "success" },
  COMPLETED: { label: "Selesai", variant: "outline" },
  CANCELLED: { label: "Dibatalkan", variant: "destructive" },
};

export const typeConfig: Record<EventType, { label: string }> = {
  CHAMPIONSHIP: { label: "Kejuaraan" },
  LEAGUE: { label: "Liga" },
  FESTIVAL: { label: "Festival" },
  MICE: { label: "Konferensi" },
  OTHER: { label: "Event" },
};

export function formatDate(date: Date): string {
  return new Intl.DateTimeFormat("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  }).format(date);
}

export function formatDateRange(start: Date, end: Date): string {
  if (start.toDateString() === end.toDateString()) return formatDate(start);
  return `${formatDate(start)} — ${formatDate(end)}`;
}

export function getEventUrl(event: Pick<Event, "slug">): string {
  return `/events/${event.slug}`;
}
