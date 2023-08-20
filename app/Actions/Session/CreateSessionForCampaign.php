<?php

namespace App\Actions\Session;

use App\Models\Campaign;
use App\Models\Session;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateSessionForCampaign
{
    use AsAction;

    public function handle(Campaign $campaign, array $data, array $with = []): Session
    {
        return $campaign->sessions()
            ->create([
                ...$data,
                'creator_id' => auth()->user()->getAuthIdentifier()
            ])
            ->load($with);
    }
}
