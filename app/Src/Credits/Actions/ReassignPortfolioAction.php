<?php

namespace App\Src\Credits\Actions;

use App\Src\Credits\Models\CreditsModel;

class ReassignPortfolioAction
{
    public function execute(int $fromCollectorId, int $toCollectorId, ?array $creditIds = null): int
    {
        $query = CreditsModel::where('collector_id', $fromCollectorId);

        if (!empty($creditIds)) {
            $query->whereIn('id', $creditIds);
        } else {
            $query->whereIn('status', ['active', 'refinanced']);
        }

        return $query->update([
            'collector_id' => $toCollectorId
        ]);
    }
}
