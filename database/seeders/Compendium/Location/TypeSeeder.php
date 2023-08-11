<?php

namespace Database\Seeders\Compendium\Location;

use App\Enums\Compendium\Location\LocationType;
use App\Models\Compendium\Location\Type;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Type::upsert(
            collect(LocationType::cases())->map(fn(LocationType $case) => [
                'id' => $case->value,
                'name' => $case->label()
            ])->toArray(),
            ['id']
        );
    }
}
