<?php

namespace App\Actions\Language;

use App\Models\Compendium\Language;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteLanguage
{
    use AsAction;

    public function handle(Language $language): bool
    {
        return $language->delete();
    }
}
