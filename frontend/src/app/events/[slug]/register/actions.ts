"use server";

import { prisma } from "@/lib/prisma";
import { participantSchema } from "@/lib/validations";
import { revalidatePath } from "next/cache";

export type RegisterResult =
  | { success: true; participantId: string }
  | { success: false; error: string; fieldErrors?: Record<string, string> };

export async function registerParticipant(
  eventSlug: string,
  formData: FormData
): Promise<RegisterResult> {
  // parse
  const raw = {
    name: formData.get("name"),
    gender: formData.get("gender"),
    birthDate: formData.get("birthDate"),
    email: formData.get("email"),
    phone: formData.get("phone"),
    contingentId: formData.get("contingentId"),
    divisionId: formData.get("divisionId"),
  };

  const parsed = participantSchema.safeParse(raw);
  if (!parsed.success) {
    const fieldErrors: Record<string, string> = {};
    for (const issue of parsed.error.issues) {
      fieldErrors[issue.path[0] as string] = issue.message;
    }
    return {
      success: false,
      error: "Data tidak valid",
      fieldErrors,
    };
  }

  const data = parsed.data;

  // pastikan event & division exist
  const event = await prisma.event.findUnique({
    where: { slug: eventSlug },
    select: { id: true, status: true },
  });
  if (!event) return { success: false, error: "Event tidak ditemukan" };
  if (event.status === "COMPLETED" || event.status === "CANCELLED") {
    return { success: false, error: "Pendaftaran event ini sudah ditutup" };
  }

  // cek duplikasi: atlet sama di divisi yang sama
  const existing = await prisma.participant.findFirst({
    where: {
      name: { equals: data.name, mode: "insensitive" },
      divisionId: data.divisionId,
    },
  });
  if (existing) {
    return {
      success: false,
      error: "Peserta dengan nama tersebut sudah terdaftar di kelas ini",
      fieldErrors: { name: "Nama sudah terdaftar di kelas ini" },
    };
  }

  // buat
  const participant = await prisma.participant.create({
    data: {
      divisionId: data.divisionId,
      contingentId: data.contingentId,
      name: data.name,
      gender: data.gender,
      birthDate: new Date(data.birthDate),
      email: data.email || null,
      phone: data.phone || null,
      status: "PENDING",
    },
  });

  revalidatePath(`/events/${eventSlug}`);
  revalidatePath(`/admin/events/${eventSlug}/participants`);

  return { success: true, participantId: participant.id };
}
