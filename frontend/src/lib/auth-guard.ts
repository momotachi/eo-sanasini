import { getServerSession } from "next-auth";
import { authOptions } from "@/lib/auth";
import { prisma } from "@/lib/prisma";
import type { GlobalRole } from "@prisma/client";

export interface SessionUser {
  id: string;
  name: string;
  email: string;
  role: GlobalRole;
}

/**
 * Ambil user yang sedang login (dengan role fresh dari DB).
 * Return null kalau belum login.
 */
export async function getCurrentUser(): Promise<SessionUser | null> {
  const session = await getServerSession(authOptions);
  if (!session?.user?.id) return null;

  const user = await prisma.user.findUnique({
    where: { id: session.user.id },
    select: { id: true, name: true, email: true, role: true, isActive: true },
  });

  if (!user || !user.isActive) return null;
  return user;
}

/**
 * Cek apakah user boleh akses admin area (super admin / admin / staf).
 */
export async function requireAdmin(): Promise<SessionUser> {
  const user = await getCurrentUser();
  if (!user) {
    throw new Error("UNAUTHORIZED");
  }
  return user;
}

/**
 * Cek apakah user boleh manage event tertentu.
 * - SUPER_ADMIN & ADMIN: boleh semua event
 * - STAF: hanya event yang ada di EventStaff
 */
export async function canManageEvent(
  user: SessionUser,
  eventId: string
): Promise<boolean> {
  if (user.role === "SUPER_ADMIN" || user.role === "ADMIN") return true;

  const assignment = await prisma.eventStaff.findUnique({
    where: {
      userId_eventId: { userId: user.id, eventId },
    },
  });
  return !!assignment;
}

/**
 * Cek apakah user punya role global tertentu (atau lebih tinggi).
 */
export function hasRole(user: SessionUser | null, ...roles: GlobalRole[]): boolean {
  if (!user) return false;
  return roles.includes(user.role);
}
