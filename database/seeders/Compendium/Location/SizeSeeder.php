<?php

namespace Database\Seeders\Compendium\Location;

use App\Enums\LocationSize;
use App\Models\Compendium\Location\Size;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Size::upsert(
            collect(LocationSize::cases())->map(fn(LocationSize $case) => [
                'id' => $case->value,
                'name' => $case->label()
            ])->toArray(),
            ['id']
        );
    }
}
