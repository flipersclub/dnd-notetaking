<?php

namespace App\Actions\Story;

use App\Models\Compendium\Story;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteStory
{
    use AsAction;

    public function handle(Story $story): bool
    {
        return $story->delete();
    }
}
