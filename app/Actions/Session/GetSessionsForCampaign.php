<?php

namespace App\Actions\Session;

use App\Models\Campaign;
use App\Models\Session;
use Illuminate\Database\Eloquent\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class GetSessionsForCampaign
{
    use AsAction;

    public function handle(Campaign $campaign, array $with = []): Collection
    {
        return $campaign->sessions()
            ->with($with)
            ->get();
    }
}
