import { ScheduleItem } from "@prisma/client";
import { formatDate } from "@/lib/event";

export function ScheduleTimeline({ items }: { items: ScheduleItem[] }) {
  if (items.length === 0) {
    return (
      <div className="rounded-lg border border-dashed p-8 text-center text-sm text-muted-foreground">
        Jadwal lengkap akan dirilis mendekati hari-H.
      </div>
    );
  }

  // kelompokkan per hari
  const byDay = items.reduce<Record<string, ScheduleItem[]>>((acc, item) => {
    const day = formatDate(item.time);
    (acc[day] = acc[day] || []).push(item);
    return acc;
  }, {});

  return (
    <div className="space-y-8">
      {Object.entries(byDay).map(([day, dayItems]) => (
        <div key={day}>
          <h3 className="mb-4 font-serif text-lg font-semibold text-primary">
            {day}
          </h3>
          <div className="relative space-y-4 border-l-2 border-border pl-6">
            {dayItems.map((item) => (
              <div key={item.id} className="relative">
                <span className="absolute -left-[27px] top-1.5 h-3 w-3 rounded-full border-2 border-background bg-primary" />
                <div className="flex flex-col sm:flex-row sm:items-baseline sm:gap-3">
                  <span className="shrink-0 font-mono text-sm font-medium text-primary">
                    {new Intl.DateTimeFormat("id-ID", {
                      hour: "2-digit",
                      minute: "2-digit",
                    }).format(item.time)}
                  </span>
                  <div>
                    <div className="font-medium">{item.title}</div>
                    {item.notes && (
                      <div className="text-sm text-muted-foreground">
                        {item.notes}
                      </div>
                    )}
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      ))}
    </div>
  );
}
