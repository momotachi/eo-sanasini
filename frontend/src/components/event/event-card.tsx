import Link from "next/link";
import type { Event } from "@prisma/client";
import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Calendar, MapPin } from "lucide-react";
import { statusConfig, typeConfig, formatDateRange } from "@/lib/event";

interface EventCardProps {
  event: Event;
}

export function EventCard({ event }: EventCardProps) {
  const status = statusConfig[event.status];
  const type = typeConfig[event.type];

  return (
    <Link href={`/events/${event.slug}`} className="group block">
      <Card className="h-full overflow-hidden transition-all hover:-translate-y-1 hover:shadow-lg">
        {/* poster / gradient placeholder */}
        <div className="relative aspect-[16/9] overflow-hidden bg-gradient-to-br from-primary/90 to-primary/70">
          {event.posterUrl ? (
            // eslint-disable-next-line @next/next/no-img-element
            <img
              src={event.posterUrl}
              alt={event.name}
              className="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
            />
          ) : (
            <div className="flex h-full items-center justify-center p-6 text-center">
              <span className="font-serif text-xl font-semibold text-primary-foreground">
                {event.name}
              </span>
            </div>
          )}
          <div className="absolute left-3 top-3 flex gap-2">
            <Badge variant="secondary" className="bg-background/90 backdrop-blur">
              {type.label}
            </Badge>
          </div>
          <div className="absolute right-3 top-3">
            <Badge variant={status.variant} className="bg-background/90 backdrop-blur">
              {status.label}
            </Badge>
          </div>
        </div>

        <div className="p-5">
          <h3 className="font-serif text-lg font-semibold leading-tight tracking-tight group-hover:text-primary">
            {event.name}
          </h3>
          {event.description && (
            <p className="mt-2 line-clamp-2 text-sm text-muted-foreground">
              {event.description}
            </p>
          )}
          <div className="mt-4 flex flex-col gap-1.5 text-xs text-muted-foreground">
            <div className="flex items-center gap-1.5">
              <Calendar className="h-3.5 w-3.5" />
              {formatDateRange(event.startDate, event.endDate)}
            </div>
            {event.venue && (
              <div className="flex items-center gap-1.5">
                <MapPin className="h-3.5 w-3.5" />
                {event.venue}
              </div>
            )}
          </div>
        </div>
      </Card>
    </Link>
  );
}
