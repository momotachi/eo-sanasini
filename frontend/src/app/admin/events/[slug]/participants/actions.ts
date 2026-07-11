"use server";

import { prisma } from "@/lib/prisma";
import { revalidatePath } from "next/cache";

export async function updateParticipantStatus(
  eventSlug: string,
  participantId: string,
  status: "APPROVED" | "REJECTED" | "WITHDRAWN"
) {
  await prisma.participant.update({
    where: { id: participantId },
    data: { status },
  });
  revalidatePath(`/admin/events/${eventSlug}/participants`);
  revalidatePath(`/admin/events/${eventSlug}`);
  revalidatePath(`/admin`);
  return { success: true };
}

export async function deleteParticipant(eventSlug: string, participantId: string) {
  await prisma.participant.delete({ where: { id: participantId } });
  revalidatePath(`/admin/events/${eventSlug}/participants`);
  revalidatePath(`/admin/events/${eventSlug}`);
  return { success: true };
}
