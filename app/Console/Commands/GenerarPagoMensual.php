<?php

namespace App\Console\Commands;

use App\Models\Alumno;
use App\Services\PagoColegiaturaDocumentosService;
use Illuminate\Console\Command;

class GenerarPagoMensual extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generar-pago:mensual';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera los pagos mensuales de los alumnos inscritos';

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

        Alumno::query()->alumno()->mensual()->whereHas('especialidades', function($q){
            return $q->where('status','Activo');
        })->with('alumno')
            ->cursor()
            ->each(function($alumno){
                $this->pcds->setAlumno($alumno);
                $this->pcds->mensual();
            });


        $this->line('Pago Mensual generado correctamente');

        return 0;
    }
}
