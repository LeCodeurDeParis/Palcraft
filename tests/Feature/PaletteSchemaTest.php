<?php

namespace Tests\Feature;

use App\Models\Block;
use App\Models\Palette;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PaletteSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_palette_blocks_are_ordered_by_position()
    {
        $palette = Palette::factory()->create();
        $blocks = Block::factory()->count(5)->create();

        foreach ($blocks->reverse()->values() as $index => $block) {
            $palette->blocks()->attach($block, ['position' => $index + 1]);
        }

        $this->assertSame(
            $blocks->reverse()->pluck('id')->values()->all(),
            $palette->blocks()->pluck('blocks.id')->all(),
        );
    }

    public function test_a_position_can_only_hold_one_block()
    {
        $palette = Palette::factory()->create();
        $palette->blocks()->attach(Block::factory()->create(), ['position' => 1]);

        $this->expectException(QueryException::class);

        $palette->blocks()->attach(Block::factory()->create(), ['position' => 1]);
    }

    public function test_a_block_cannot_appear_twice_in_a_palette()
    {
        $palette = Palette::factory()->create();
        $block = Block::factory()->create();
        $palette->blocks()->attach($block, ['position' => 1]);

        $this->expectException(QueryException::class);

        $palette->blocks()->attach($block, ['position' => 2]);
    }

    public function test_position_must_be_between_one_and_five()
    {
        if (DB::getDriverName() !== 'pgsql') {
            $this->markTestSkipped('The position CHECK constraint is only created on PostgreSQL.');
        }

        $palette = Palette::factory()->create();

        $this->expectException(QueryException::class);

        $palette->blocks()->attach(Block::factory()->create(), ['position' => 6]);
    }

    public function test_users_can_favorite_palettes_of_other_users()
    {
        $palette = Palette::factory()->public()->create();
        $fan = User::factory()->create();

        $fan->favoritePalettes()->attach($palette);

        $this->assertTrue($palette->favoritedBy->contains($fan));
        $this->assertSame(1, Palette::public()->count());
    }

    public function test_deleting_a_user_deletes_their_palettes_and_pivot_rows()
    {
        $owner = User::factory()->create();
        $palette = Palette::factory()->for($owner)->create();
        $palette->blocks()->attach(Block::factory()->create(), ['position' => 1]);
        User::factory()->create()->favoritePalettes()->attach($palette);

        $owner->delete();

        $this->assertDatabaseMissing('palettes', ['id' => $palette->id]);
        $this->assertDatabaseMissing('block_palette', ['palette_id' => $palette->id]);
        $this->assertDatabaseMissing('favorites', ['palette_id' => $palette->id]);
        $this->assertDatabaseCount('blocks', 1);
    }
}
