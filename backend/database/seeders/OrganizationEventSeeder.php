<?php

namespace Database\Seeders;

use App\Models\Contingent;
use App\Models\Division;
use App\Models\Event;
use App\Models\EventConfig;
use App\Models\Organization;
use App\Models\ScheduleItem;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class OrganizationEventSeeder extends Seeder
{
    public function run(): void
    {
        // Organization
        $org = Organization::updateOrCreate(
            ['slug' => 'eo-sanasini'],
            [
                'name' => 'EO Sanasini',
                'tagline' => 'Event Organizer, MICE & Travel Agency',
                'about' => 'Event Organizer berpengalaman sejak 2009, menghadirkan kejuaraan olahraga, festival, dan konferensi berskala nasional dengan standar eksekusi profesional.',
                'website' => 'https://www.instagram.com/eosanasini/',
                'instagram' => 'eosanasini',
                'email' => 'hello@sanasini.id',
                'phone' => '+62 21 0000 0000',
                'address' => 'Jakarta, Indonesia',
            ]
        );
        $this->command->info("  ✓ Organization: {$org->name}");

        // Event — Taekwondo (sport)
        $event = Event::updateOrCreate(
            ['slug' => 'sanasini-taekwondo-championship-2026'],
            [
                'organization_id' => $org->id,
                'name' => 'Sanasini Taekwondo Championship 2026',
                'type' => 'CHAMPIONSHIP',
                'category' => 'SPORT',
                'modules' => ['registration' => true, 'schedule' => true, 'gallery' => false, 'certificate' => false],
                'status' => 'REGISTRATION_OPEN',
                'description' => 'Kejuaraan Taekwondo nasional yang mempertemukan atlet dari berbagai perguruan dan kontingen. Mempertandingkan Kyorugi (pertandingan) dan Poomsae (jurus).',
                'start_date' => '2026-08-15 08:00:00',
                'end_date' => '2026-08-17 20:00:00',
                'venue' => 'Istora Senayan',
                'address' => 'Jl. Gerbang Pemuda No.3, Jakarta Pusat',
                'contact_name' => 'Sekretariat Panitia',
                'contact_phone' => '+62 812 0000 0000',
                'contact_email' => 'taekwondo@sanasini.id',
                'is_public' => true,
            ]
        );
        $this->command->info("  ✓ Event: {$event->name}");

        EventConfig::updateOrCreate(
            ['event_id' => $event->id],
            [
                'registration_type' => 'HYBRID',
                'bronze_per_division' => 2,
                'age_categories' => ['Pre-Teen', 'Cadet', 'Junior', 'Senior'],
                'disciplines' => ['Kyorugi', 'Poomsae'],
            ]
        );

        // Contingents
        $contingents = [
            ['name' => 'Taekwondo Club Nusantara', 'type' => 'CLUB'],
            ['name' => 'Garuda Taekwondo Academy', 'type' => 'CLUB'],
            ['name' => 'Cendekia Martial Arts', 'type' => 'CLUB'],
            ['name' => 'Provinsi DKI Jakarta', 'type' => 'PROVINCE'],
            ['name' => 'Provinsi Jawa Barat', 'type' => 'PROVINCE'],
            ['name' => 'Provinsi Banten', 'type' => 'PROVINCE'],
        ];
        foreach ($contingents as $c) {
            Contingent::firstOrCreate(['event_id' => $event->id, 'name' => $c['name']], $c);
        }
        $this->command->info('  ✓ 6 kontingen');

        // Divisions
        $divisions = [
            ['discipline' => 'Kyorugi', 'age_category' => 'Cadet', 'gender' => 'PUTRA', 'class_name' => 'Kelas -33kg', 'format' => 'GROUP_KNOCKOUT'],
            ['discipline' => 'Kyorugi', 'age_category' => 'Cadet', 'gender' => 'PUTRA', 'class_name' => 'Kelas -41kg', 'format' => 'GROUP_KNOCKOUT'],
            ['discipline' => 'Kyorugi', 'age_category' => 'Cadet', 'gender' => 'PUTRI', 'class_name' => 'Kelas -29kg', 'format' => 'GROUP_KNOCKOUT'],
            ['discipline' => 'Kyorugi', 'age_category' => 'Junior', 'gender' => 'PUTRA', 'class_name' => 'Kelas -45kg', 'format' => 'GROUP_KNOCKOUT'],
            ['discipline' => 'Kyorugi', 'age_category' => 'Junior', 'gender' => 'PUTRA', 'class_name' => 'Kelas -55kg', 'format' => 'GROUP_KNOCKOUT'],
            ['discipline' => 'Kyorugi', 'age_category' => 'Junior', 'gender' => 'PUTRI', 'class_name' => 'Kelas -42kg', 'format' => 'GROUP_KNOCKOUT'],
            ['discipline' => 'Kyorugi', 'age_category' => 'Senior', 'gender' => 'PUTRA', 'class_name' => 'Kelas -58kg', 'format' => 'GROUP_KNOCKOUT'],
            ['discipline' => 'Kyorugi', 'age_category' => 'Senior', 'gender' => 'PUTRA', 'class_name' => 'Kelas -68kg', 'format' => 'GROUP_KNOCKOUT'],
            ['discipline' => 'Kyorugi', 'age_category' => 'Senior', 'gender' => 'PUTRI', 'class_name' => 'Kelas -46kg', 'format' => 'GROUP_KNOCKOUT'],
            ['discipline' => 'Poomsae', 'age_category' => 'Cadet', 'gender' => 'PUTRI', 'class_name' => 'Individual', 'format' => 'SCORING'],
            ['discipline' => 'Poomsae', 'age_category' => 'Junior', 'gender' => 'PUTRA', 'class_name' => 'Individual', 'format' => 'SCORING'],
            ['discipline' => 'Poomsae', 'age_category' => 'Senior', 'gender' => 'MIXED', 'class_name' => 'Tim (Pair)', 'format' => 'SCORING'],
        ];
        foreach ($divisions as $d) {
            Division::firstOrCreate(
                ['event_id' => $event->id, 'discipline' => $d['discipline'], 'age_category' => $d['age_category'], 'gender' => $d['gender'], 'class_name' => $d['class_name']],
                $d
            );
        }
        $this->command->info('  ✓ 12 kelas pertandingan');

        // Venues
        $venueA = Venue::firstOrCreate(['event_id' => $event->id, 'name' => 'Mat A'], ['area' => 'Arena Utama']);
        $venueB = Venue::firstOrCreate(['event_id' => $event->id, 'name' => 'Mat B'], ['area' => 'Arena Utama']);
        $this->command->info('  ✓ 2 venue');

        // Schedule
        $schedule = [
            ['time' => '2026-08-15 08:00:00', 'title' => 'Registrasi & Weigh-in', 'venue_id' => $venueA->id, 'notes' => 'Seluruh kontingen'],
            ['time' => '2026-08-15 09:30:00', 'title' => 'Technical Meeting', 'venue_id' => $venueA->id],
            ['time' => '2026-08-15 10:00:00', 'title' => 'Upacara Pembukaan', 'venue_id' => $venueA->id],
            ['time' => '2026-08-15 13:00:00', 'title' => 'Penyisihan Kyorugi Cadet & Junior (Mat A)', 'venue_id' => $venueA->id],
            ['time' => '2026-08-15 13:00:00', 'title' => 'Penyisihan Poomsae Cadet (Mat B)', 'venue_id' => $venueB->id],
            ['time' => '2026-08-16 09:00:00', 'title' => 'Penyisihan Kyorugi Senior (Mat A)', 'venue_id' => $venueA->id],
            ['time' => '2026-08-16 13:00:00', 'title' => 'Babak Semi-final Kyorugi', 'venue_id' => $venueA->id],
            ['time' => '2026-08-17 09:00:00', 'title' => 'Final Poomsae (Mat B)', 'venue_id' => $venueB->id],
            ['time' => '2026-08-17 13:00:00', 'title' => 'Final Kyorugi (Mat A)', 'venue_id' => $venueA->id],
            ['time' => '2026-08-17 18:00:00', 'title' => 'Upacara Penutupan & Penghargaan', 'venue_id' => $venueA->id],
        ];
        foreach ($schedule as $s) {
            ScheduleItem::firstOrCreate(['event_id' => $event->id, 'time' => $s['time'], 'title' => $s['title']], $s);
        }
        $this->command->info('  ✓ 10 item jadwal');
    }
}
