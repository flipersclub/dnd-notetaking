<?php

namespace App\Actions\Session;

use App\Models\Session;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteSession
{
    use AsAction;

    public function handle(Session $session): bool
    {
        return $session->delete();
    }
}
