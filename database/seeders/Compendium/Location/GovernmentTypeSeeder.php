<?php

namespace Database\Seeders\Compendium\Location;

use App\Enums\Compendium\Location\GovernmentType as GovernmentTypeEnum;
use App\Models\Compendium\Location\GovernmentType;
use Illuminate\Database\Seeder;

class GovernmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GovernmentType::upsert(
            collect(GovernmentTypeEnum::cases())->map(fn(GovernmentTypeEnum $case) => [
                'id' => $case->value,
                'name' => $case->label()
            ])->toArray(),
            ['id']
        );
    }
}
