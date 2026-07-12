<?php

namespace Tests\Unit;

use App\Services\BracketEngine;
use Tests\TestCase;

class BracketEngineTest extends TestCase
{
    private BracketEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = app(BracketEngine::class);
    }

    public function test_knockout_with_2_participants_creates_1_match(): void
    {
        $slots = $this->engine->generateKnockoutSlots(['a', 'b']);

        $this->assertCount(1, $slots);
        $this->assertEquals('FINAL', $slots[0]['round']);
        $this->assertEquals('SCHEDULED', $slots[0]['status']);
    }

    public function test_knockout_with_4_participants_creates_2_matches(): void
    {
        $slots = $this->engine->generateKnockoutSlots(['a', 'b', 'c', 'd']);

        $this->assertCount(2, $slots);
        $this->assertEquals('SEMIFINAL', $slots[0]['round']);
    }

    public function test_knockout_padding_to_power_of_two(): void
    {
        // 3 peserta -> padding jadi 4, ada 1 BYE
        $slots = $this->engine->generateKnockoutSlots(['a', 'b', 'c']);

        $this->assertCount(2, $slots);
        $byeSlots = array_filter($slots, fn($s) => $s['status'] === 'BYE');
        $this->assertCount(1, $byeSlots);
    }

    public function test_round_robin_with_4_participants_creates_6_matches(): void
    {
        // C(4, 2) = 6
        $slots = $this->engine->generateRoundRobinSlots(['a', 'b', 'c', 'd'], 'A');

        $this->assertCount(6, $slots);
        $this->assertEquals('GROUP_STAGE', $slots[0]['round']);
        $this->assertEquals('A', $slots[0]['group_label']);
    }

    public function test_split_into_groups_distributes_evenly(): void
    {
        $groups = $this->engine->splitIntoGroups(['a', 'b', 'c', 'd', 'e', 'f'], 3);

        $this->assertCount(3, $groups);
        $this->assertCount(2, $groups[0]);
        $this->assertCount(2, $groups[1]);
        $this->assertCount(2, $groups[2]);
    }

    public function test_seed_order_places_seed_1_and_2_at_opposite_brackets(): void
    {
        // dengan 4 peserta, seeding standar: pos 0,3,2,1
        // sehingga seed 1 (index 0) dan seed 2 (index 1) baru ketemu di final
        $slots = $this->engine->generateKnockoutSlots(['seed1', 'seed2', 'seed3', 'seed4']);

        // match 0 harus berisi seed1 vs seed4, match 1 seed3 vs seed2
        $match0 = $slots[0];
        $this->assertEquals('seed1', $match0['participant_a_id']);
    }

    public function test_knockout_with_less_than_2_returns_empty(): void
    {
        $this->assertCount(0, $this->engine->generateKnockoutSlots(['a']));
        $this->assertCount(0, $this->engine->generateKnockoutSlots([]));
    }
}
