<?php

namespace App\Actions\Language;

use App\Models\Compendium\Language;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateLanguage
{
    use AsAction;

    public function handle(Language $language, array $data, array $with = []): Language
    {
        $language->update($data);
        return $language->load($with);
    }
}
