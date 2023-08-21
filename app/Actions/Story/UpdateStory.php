<?php

namespace App\Actions\Story;

use App\Models\Compendium\Story;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateStory
{
    use AsAction;

    public function handle(Story $story, array $data, array $with = []): Story
    {
        $story->update($data);
        return $story->load($with);
    }
}
