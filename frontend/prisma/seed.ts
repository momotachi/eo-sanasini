import { PrismaClient, EventType, EventStatus, RegistrationType, CompetitionFormat, Gender, ContingentType } from "@prisma/client";

const prisma = new PrismaClient();

async function main() {
  console.log("🌱 Seeding EO Sanasini database...");

  // 1. Organization
  const org = await prisma.organization.upsert({
    where: { slug: "eo-sanasini" },
    update: {},
    create: {
      name: "EO Sanasini",
      slug: "eo-sanasini",
      tagline: "Event Organizer, MICE & Travel Agency",
      about:
        "Event Organizer berpengalaman sejak 2009, menghadirkan kejuaraan olahraga, festival, dan konferensi berskala nasional dengan standar eksekusi profesional.",
      website: "https://www.instagram.com/eosanasini/",
      instagram: "eosanasini",
      email: "hello@sanasini.id",
      phone: "+62 21 0000 0000",
      address: "Jakarta, Indonesia",
    },
  });
  console.log(`  ✓ Organization: ${org.name}`);

  // 2. Event — Taekwondo Championship
  const eventSlug = "sanasini-taekwondo-championship-2026";
  const startDate = new Date("2026-08-15T08:00:00+07:00");
  const endDate = new Date("2026-08-17T20:00:00+07:00");

  const event = await prisma.event.upsert({
    where: { slug: eventSlug },
    update: {},
    create: {
      name: "Sanasini Taekwondo Championship 2026",
      slug: eventSlug,
      type: EventType.CHAMPIONSHIP,
      status: EventStatus.REGISTRATION_OPEN,
      description:
        "Kejuaraan Taekwondo nasional yang mempertemukan atlet dari berbagai perguruan dan kontingen. Mempertandingkan Kyorugi (pertandingan) dan Poomsae (jurus) dengan format Grup → Knockout untuk Kyorugi dan penilaian juri untuk Poomsae.",
      startDate,
      endDate,
      venue: "Istora Senayan",
      address: "Jl. Gerbang Pemuda No.3, Jakarta Pusat",
      mapUrl: "https://maps.google.com/?q=Istora+Senayan",
      contactName: "Sekretariat Panitia",
      contactPhone: "+62 812 0000 0000",
      contactEmail: "taekwondo@sanasini.id",
      isPublic: true,
      organizationId: org.id,
    },
  });
  console.log(`  ✓ Event: ${event.name}`);

  // 3. Config
  await prisma.eventConfig.upsert({
    where: { eventId: event.id },
    update: {},
    create: {
      eventId: event.id,
      registrationType: RegistrationType.HYBRID,
      bronzePerDivision: 2, // standar World Taekwondo
      ageCategories: ["Pre-Teen", "Cadet", "Junior", "Senior"],
      disciplines: ["Kyorugi", "Poomsae"],
    },
  });
  console.log("  ✓ Event config (Hybrid, Kyorugi+Poomsae)");

  // 4. Kontingen (perguruan)
  const contingentsData = [
    { name: "Taekwondo Club Nusantara", type: ContingentType.CLUB },
    { name: "Garuda Taekwondo Academy", type: ContingentType.CLUB },
    { name: "Cendekia Martial Arts", type: ContingentType.CLUB },
    { name: "Provinsi DKI Jakarta", type: ContingentType.PROVINCE },
    { name: "Provinsi Jawa Barat", type: ContingentType.PROVINCE },
    { name: "Provinsi Banten", type: ContingentType.PROVINCE },
  ];
  const contingents = [];
  for (const c of contingentsData) {
    const cont = await prisma.contingent.create({
      data: { eventId: event.id, name: c.name, type: c.type },
    });
    contingents.push(cont);
  }
  console.log(`  ✓ ${contingents.length} kontingen`);

  // 5. Divisions (kelas pertandingan) — contoh subset
  const divisionsData = [
    // Kyorugi
    { discipline: "Kyorugi", ageCategory: "Cadet", gender: Gender.PUTRA, className: "Kelas -33kg", format: CompetitionFormat.GROUP_KNOCKOUT },
    { discipline: "Kyorugi", ageCategory: "Cadet", gender: Gender.PUTRA, className: "Kelas -41kg", format: CompetitionFormat.GROUP_KNOCKOUT },
    { discipline: "Kyorugi", ageCategory: "Cadet", gender: Gender.PUTRI, className: "Kelas -29kg", format: CompetitionFormat.GROUP_KNOCKOUT },
    { discipline: "Kyorugi", ageCategory: "Junior", gender: Gender.PUTRA, className: "Kelas -45kg", format: CompetitionFormat.GROUP_KNOCKOUT },
    { discipline: "Kyorugi", ageCategory: "Junior", gender: Gender.PUTRA, className: "Kelas -55kg", format: CompetitionFormat.GROUP_KNOCKOUT },
    { discipline: "Kyorugi", ageCategory: "Junior", gender: Gender.PUTRI, className: "Kelas -42kg", format: CompetitionFormat.GROUP_KNOCKOUT },
    { discipline: "Kyorugi", ageCategory: "Junior", gender: Gender.PUTRI, className: "Kelas -49kg", format: CompetitionFormat.GROUP_KNOCKOUT },
    { discipline: "Kyorugi", ageCategory: "Senior", gender: Gender.PUTRA, className: "Kelas -58kg", format: CompetitionFormat.GROUP_KNOCKOUT },
    { discipline: "Kyorugi", ageCategory: "Senior", gender: Gender.PUTRA, className: "Kelas -68kg", format: CompetitionFormat.GROUP_KNOCKOUT },
    { discipline: "Kyorugi", ageCategory: "Senior", gender: Gender.PUTRI, className: "Kelas -46kg", format: CompetitionFormat.GROUP_KNOCKOUT },
    { discipline: "Kyorugi", ageCategory: "Senior", gender: Gender.PUTRI, className: "Kelas -57kg", format: CompetitionFormat.GROUP_KNOCKOUT },
    // Poomsae
    { discipline: "Poomsae", ageCategory: "Cadet", gender: Gender.PUTRI, className: "Individual", format: CompetitionFormat.SCORING },
    { discipline: "Poomsae", ageCategory: "Junior", gender: Gender.PUTRA, className: "Individual", format: CompetitionFormat.SCORING },
    { discipline: "Poomsae", ageCategory: "Senior", gender: Gender.MIXED, className: "Tim (Pair)", format: CompetitionFormat.SCORING },
  ];
  const divisions = [];
  for (const d of divisionsData) {
    const div = await prisma.division.create({
      data: { eventId: event.id, ...d },
    });
    divisions.push(div);
  }
  console.log(`  ✓ ${divisions.length} kelas pertandingan`);

  // 6. Venue
  const venue1 = await prisma.venue.create({
    data: { eventId: event.id, name: "Mat A", area: "Arena Utama" },
  });
  const venue2 = await prisma.venue.create({
    data: { eventId: event.id, name: "Mat B", area: "Arena Utama" },
  });
  console.log(`  ✓ 2 venue (Mat A, Mat B)`);

  // 7. Schedule
  const scheduleData = [
    { time: new Date("2026-08-15T08:00:00+07:00"), title: "Registrasi & Weigh-in", notes: "Seluruh kontingen", venueId: venue1.id },
    { time: new Date("2026-08-15T09:30:00+07:00"), title: "Technical Meeting", notes: "Perwakilan kontingen", venueId: venue1.id },
    { time: new Date("2026-08-15T10:00:00+07:00"), title: "Upacara Pembukaan", venueId: venue1.id },
    { time: new Date("2026-08-15T13:00:00+07:00"), title: "Penyisihan Kyorugi Cadet & Junior (Mat A)", venueId: venue1.id },
    { time: new Date("2026-08-15T13:00:00+07:00"), title: "Penyisihan Poomsae Cadet (Mat B)", venueId: venue2.id },
    { time: new Date("2026-08-16T09:00:00+07:00"), title: "Penyisihan Kyorugi Senior (Mat A)", venueId: venue1.id },
    { time: new Date("2026-08-16T13:00:00+07:00"), title: "Babak Semi-final Kyorugi", venueId: venue1.id },
    { time: new Date("2026-08-17T09:00:00+07:00"), title: "Final Poomsae (Mat B)", venueId: venue2.id },
    { time: new Date("2026-08-17T13:00:00+07:00"), title: "Final Kyorugi (Mat A)", venueId: venue1.id },
    { time: new Date("2026-08-17T18:00:00+07:00"), title: "Upacara Penutupan & Penghargaan", venueId: venue1.id },
  ];
  for (const s of scheduleData) {
    await prisma.scheduleItem.create({ data: { eventId: event.id, ...s } });
  }
  console.log(`  ✓ ${scheduleData.length} item jadwal`);

  console.log("\n✅ Seed selesai!");
  console.log(`   Akses event di: /events/${eventSlug}`);
}

main()
  .catch((e) => {
    console.error(e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
