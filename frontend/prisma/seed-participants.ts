// Seed peserta approved untuk testing bracket generation
import { PrismaClient, ParticipantStatus } from "@prisma/client";

const prisma = new PrismaClient();

const firstNames = ["Budi", "Andi", "Rizki", "Fajar", "Dimas", "Agus", "Hendra", "Wawan", "Yoga", "Reza", "Bayu", "Ilham"];
const lastNames = ["Santoso", "Pratama", "Wijaya", "Nugroho", "Saputra", "Kurniawan", "Halim", "Setiawan"];

async function main() {
  console.log("👥 Seeding peserta approved...");

  // ambil semua division
  const divisions = await prisma.division.findMany({
    where: { format: { in: ["GROUP_KNOCKOUT", "FULL_KNOCKOUT", "ROUND_ROBIN"] } },
  });
  const contingents = await prisma.contingent.findMany();

  if (divisions.length === 0 || contingents.length === 0) {
    console.log("Run seed.ts dulu!");
    return;
  }

  let total = 0;
  for (const div of divisions) {
    // 4-8 peserta per division
    const count = 4 + Math.floor(Math.random() * 5);
    for (let i = 0; i < count; i++) {
      const fn = firstNames[Math.floor(Math.random() * firstNames.length)];
      const ln = lastNames[Math.floor(Math.random() * lastNames.length)];
      const cont = contingents[Math.floor(Math.random() * contingents.length)];

      await prisma.participant.create({
        data: {
          divisionId: div.id,
          contingentId: cont.id,
          name: `${fn} ${ln}`,
          gender: div.gender,
          birthDate: new Date(2008 + Math.floor(Math.random() * 6), Math.floor(Math.random() * 12), 1 + Math.floor(Math.random() * 28)),
          email: `${fn.toLowerCase()}.${ln.toLowerCase()}@test.id`,
          phone: `0812${Math.floor(1000000 + Math.random() * 8999999)}`,
          status: ParticipantStatus.APPROVED,
          seed: i + 1,
        },
      });
      total++;
    }
  }

  console.log(`✅ ${total} peserta approved dibuat di ${divisions.length} divisi`);
}

main()
  .catch((e) => { console.error(e); process.exit(1); })
  .finally(() => prisma.$disconnect());
