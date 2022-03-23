<?php

namespace App\Console\Commands;

use App\Models\Alumno;
use App\Services\PagoColegiaturaDocumentosService;
use Illuminate\Console\Command;

class GenerarPagoSemanal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generar-pago:semanal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera los pagos semanales de los alumnos inscritos';

     /**
     * Servicio para calcular la colegiatura
     *
     * @var  App\Services\PagoColegiaturaService
     */
    protected $pcs;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PagoColegiaturaDocumentosService $pcds)
    {
        parent::__construct();

        $this->pcds = $pcds;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        # NOTE: LAZYCOLLECTION https://laravel.com/docs/8.x/collections#lazy-collection-methods

        Alumno::query()->alumno()->semanal()->whereHas('grupos')->with('alumno')
            ->cursor()
            ->each(function($alumno){
                $this->pcds->setAlumno($alumno);
                $this->pcds->semanal();
            });


        $this->line('Pago Semanal generado correctamente');

        return 0;
    }
}
