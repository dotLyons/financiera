<?php

namespace App\Console\Commands;

use App\Src\Credits\Actions\ApplyLateFeeAction;
use Illuminate\Console\Command;

class ApplyPenaltiesCommand extends Command
{
    /**
     * El nombre y firma del comando.
     *
     * @var string
     */
    protected $signature = 'credits:apply-penalties';

    /**
     * Descripción.
     *
     * @var string
     */
    protected $description = 'Aplica el 5% de interés a los créditos con mora superior a 7 días';

    /**
     * Ejecución.
     */
    public function handle(ApplyLateFeeAction $action)
    {
        $this->info('Iniciando proceso de cálculo de mora...');

        try {
            $action->execute();
            $this->info('Proceso finalizado con éxito.');
        } catch (\Exception $e) {
            $this->error('Ocurrió un error: ' . $e->getMessage());
        }
    }
}
