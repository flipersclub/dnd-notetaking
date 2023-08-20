<?php

namespace App\Actions\Session;

use App\Models\Session;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateSession
{
    use AsAction;

    public function handle(Session $session, array $data, array $with = [])
    {
        $session->update($data);
        return $session->loadMissing($with);
    }
}
