import Link from "next/link";
import { getServerSession } from "next-auth";
import { authOptions } from "@/lib/auth";
import { redirect } from "next/navigation";
import { prisma } from "@/lib/prisma";
import { LayoutDashboard, Users, Trophy, CalendarClock, ChevronLeft, LogOut, ShieldCheck } from "lucide-react";
import { LogoutButton } from "@/components/auth/logout-button";

const adminNav = [
  { href: "/admin", label: "Dashboard", icon: LayoutDashboard },
  { href: "/admin/participants", label: "Peserta", icon: Users },
  { href: "/admin/matches", label: "Pertandingan", icon: Trophy },
  { href: "/admin/schedule", label: "Jadwal", icon: CalendarClock },
];

export default async function AdminLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const session = await getServerSession(authOptions);
  if (!session) redirect("/login?callbackUrl=/admin");

  // ambil info user dari DB untuk role up-to-date
  const user = await prisma.user.findUnique({
    where: { id: session.user.id },
    select: { name: true, email: true, role: true },
  });

  const role = user?.role || "STAF";
  const roleLabel: Record<string, string> = {
    SUPER_ADMIN: "Super Admin",
    ADMIN: "Admin",
    STAF: "Staf",
  };

  return (
    <div className="min-h-screen bg-secondary/20">
      <div className="flex">
        {/* Sidebar */}
        <aside className="sticky top-0 hidden h-screen w-60 shrink-0 flex-col border-r bg-card md:flex">
          <div className="p-5">
            <div className="flex items-center gap-2">
              <div className="flex h-8 w-8 items-center justify-center rounded-md bg-primary font-serif text-sm font-bold text-primary-foreground">
                A
              </div>
              <div>
                <div className="font-serif text-sm font-semibold">Admin Panel</div>
                <div className="text-[10px] uppercase tracking-wider text-muted-foreground">
                  EO Sanasini
                </div>
              </div>
            </div>
          </div>
          <nav className="flex-1 space-y-1 px-3">
            {adminNav.map((item) => (
              <Link
                key={item.href}
                href={item.href}
                className="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-secondary hover:text-foreground"
              >
                <item.icon className="h-4 w-4" />
                {item.label}
              </Link>
            ))}
          </nav>

          {/* User box + logout */}
          <div className="border-t p-3">
            <div className="flex items-center gap-2 rounded-md px-3 py-2">
              <div className="flex h-8 w-8 items-center justify-center rounded-full bg-accent text-accent-foreground">
                <ShieldCheck className="h-4 w-4" />
              </div>
              <div className="min-w-0 flex-1">
                <div className="truncate text-sm font-medium">{user?.name}</div>
                <div className="text-[10px] uppercase tracking-wider text-primary">
                  {roleLabel[role]}
                </div>
              </div>
            </div>
            <LogoutButton />
            <Link
              href="/"
              className="mt-1 flex items-center gap-1 px-3 py-2 text-xs text-muted-foreground hover:text-foreground"
            >
              <ChevronLeft className="h-3 w-3" />
              Kembali ke situs
            </Link>
          </div>
        </aside>

        {/* Content */}
        <main className="min-w-0 flex-1">
          {/* Mobile top bar */}
          <div className="flex items-center justify-between border-b bg-card px-4 py-3 md:hidden">
            <span className="font-serif font-semibold">Admin</span>
            <div className="flex items-center gap-3">
              <Link href="/" className="text-xs text-muted-foreground">← Situs</Link>
              <LogoutButton />
            </div>
          </div>
          <div className="p-6 md:p-8">{children}</div>
        </main>
      </div>
    </div>
  );
}
