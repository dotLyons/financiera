<?php

namespace App\Src\Credits\Actions;

use App\Src\Credits\Models\CreditsModel;
use Illuminate\Support\Facades\DB;

class ReassignPortfolioAction
{
    /**
     * Mueve la cartera activa de un cobrador a otro.
     */
    public function execute(int $fromCollectorId, int $toCollectorId): int
    {
        $count = CreditsModel::where('collector_id', $fromCollectorId)
            ->whereIn('status', ['active', 'refinanced'])
            ->update([
                'collector_id' => $toCollectorId
            ]);

        // Log::info("Se transfirieron $count créditos del ID $fromCollectorId al ID $toCollectorId"); Futura implementacion

        return $count;
    }
}
