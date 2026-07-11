// Seed super admin user + admin default
import { PrismaClient, GlobalRole } from "@prisma/client";
import bcrypt from "bcryptjs";

const prisma = new PrismaClient();

async function main() {
  console.log("👤 Seeding admin users...");

  const users = [
    {
      email: "superadmin@sanasini.id",
      name: "Super Admin (Programmer)",
      password: "SuperAdmin2026!",
      role: GlobalRole.SUPER_ADMIN,
    },
    {
      email: "admin@sanasini.id",
      name: "Admin EO Sanasini",
      password: "AdminSanasini2026!",
      role: GlobalRole.ADMIN,
    },
  ];

  for (const u of users) {
    const passwordHash = await bcrypt.hash(u.password, 10);
    const created = await prisma.user.upsert({
      where: { email: u.email },
      update: { role: u.role, passwordHash }, // update password hash juga
      create: { email: u.email, name: u.name, passwordHash, role: u.role },
    });
    console.log(`  ✓ ${created.role}: ${created.email} / ${u.password}`);
  }

  console.log("\n✅ Seed admin selesai!");
}

main()
  .catch((e) => { console.error(e); process.exit(1); })
  .finally(() => prisma.$disconnect());
