<?php

namespace App\Livewire;

use App\Events\ProcessRequest;
use App\Models\Solicitud;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use App\Livewire\Actions\RequestPdfGenerator;
use App\Livewire\Actions\SendEmail;
use App\Livewire\Actions\GetStatus;

class TableSolicitudes extends Component
{
    public array $rangoDate = [];
    
    public $solicitudesSimuladas = [];
    public $filtroActual = 0;
    use WithPagination;

    public $perPage = 5; // registros por página

    public $search = '';
    public $estadoFiltro = null; 
    public $selected = []; // guarda los ids de los registros que el usuario a seleccionado
    public $selectAll = false;

    protected $listeners = ['ModalSolicitud' => 'verSolicitud'];
    public $showConfirmModal = false;
    public $solicitudId; // ID que viene de la tabla
    public $solicitud;   // Aquí guardarás la info completa de la base de datos

    public function verSolicitud($id)
    {
        $this->solicitudId = $id;
        $this->solicitud = Solicitud::with('statusSolicitud')->find($this->solicitudId);
        $this->solicitud->fecha_envio_correo =
            Carbon::parse($this->solicitud->fecha_envio_correo)
            ->format('d-m-Y \a \l\a\s H:i \h\o\r\a\s');
        $this->showConfirmModal = true;
    }
   
    // Resetea la página al actualizar la búsqueda
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {

        // Si $selectAll es true:
        // Se llenan todos los IDs de $solicitudes en $selected, marcando todas las filas como seleccionadas.
        if ($value) {
            // aqui el array solicitudes lo convierto en una coleccion para usar pluck
            // pluck extrae solo los ids y toArray lo convierte de nuevo en array
            // en otras palabras convierto solicitudes en una coleccion y extraigo solo los ids con pluck y convierto esto en un nuevo array de ids
            $this->selected = $this->oficios->pluck('id')->toArray();
        } else { // si es falso vacio los selected
            $this->selected = [];
        }
    }

    public function getSolicitudesProperty()
    {
        return Solicitud::with('statusSolicitud')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%'.$this->search.'%')
                        ->orWhere('rfc', 'like', '%'.$this->search.'%');
                });
            })
             ->when($this->estadoFiltro, function ($query) {
                $query->where('estado', $this->estadoFiltro);
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);
    }

    // Livewire ejecuta automáticamente este método cuando se actualiza la propiedad $selectAll.
    /*public function firmarSeleccionados(){
        dd('selected');
        if (count($this->selected) === 0) {
            $this->dispatch('alert', [
                'type' => 'warning',
                'message' => 'No hay registros seleccionados'
            ]);
            return;
        }
        Oficio::whereIn('id', $this->selected)
            ->update(['status' => 2]);
            // Limpiamos la selección
        $this->selected = [];
        $this->dispatch('alert', ['type' => 'success', 'message' => 'Oficios firmados']);

    }*/
   
    public function procesar()
    {
        $pendientes = Solicitud::where('estado', 0)->get();
        $failed = [];
        foreach ($pendientes as $solicitud) {
            try {
                DB::transaction(function () use ($solicitud) {
                    //$estado = $this->determinarEstado($solicitud);
                    $estado = (new GetStatus())->handle($solicitud);
                    $solicitud->estado = $estado;
                    $solicitud->save();
                    event(new ProcessRequest($solicitud));
                    $pdfPath = (new RequestPdfGenerator())->handle($solicitud);
                    $emailResult = (new SendEmail())->handle($solicitud, $pdfPath);
                    //$this->enviarCorreo($solicitud, $pdfPath);
                    return true;
                });
            } catch (\Throwable $e) {
                $failed[] = [
                    'solicitud_id' => $solicitud->id,
                    'error' => $e->getMessage(),
                ];

            }
        }

        if(empty($failed)) {
            $this->toast('success', 'Solicitudes Procesadas Correctamente', 'Se enviaron los oficios de las solicitudes correspondientes al correo registrado.', 'top-center');
        }
        else {
            $text  = "Solicitudes Procesadas correctamente: " . (count($pendientes) - count($failed)) . "\n";
            $text .= "Errores en: " . count($failed) . " solicitudes\n\n";
            $text .= "Detalles de errores:\n";
            foreach($failed as $f) {
                $text .= "Solicitud ID {$f['solicitud_id']}: {$f['error']}\n";
            }
            $this->toast('warning', 'Error al procesar solicitudes', $text, 'top-center');
        }

    }

    private function toast ($variant = '', $heading = '', $text = '', $position = '')
    {
        Flux::toast(
            variant: $variant,
            heading: $heading,
            text: $text,
            position: $position
        );
    }
  
    public function guardarSolicitudes()
    {
        foreach ($this->solicitudesSimuladas as $solicitud) {

            Solicitud::updateOrCreate(
            ['rfc' => $solicitud['rfc']], // campo único para identificar
            [
            'nombre' => $solicitud['nombre'],
            'curp' => $solicitud['curp'],
            'email' => $solicitud['email'],
            ]
            );
        }

        Flux::toast(
        variant: 'success',
        heading: 'Solicitudes Almacenadas Correctamente',
        text: 'Las solicitudes fueron registradas correctamente.',
        position: 'top-center'
        );
    }

    public function mount()
    {
        $this->solicitudesSimuladas = [
            [
            'nombre' => 'Ivan Ortiz',
            'rfc' => 'IORM850505XX1',
            'curp' => '5551234567',
            'email' => 'eiom229012@gmail.com',
            ],
            [
            'nombre' => 'Yesenia Inacua',
            'rfc' => 'YESE920505XX0',
            'curp' => '5557654321',
            'email' => 'yesenia.inacua@morelos.gob.mx',
            ],
            [
            'nombre' => 'Lizbet Lagunas',
            'rfc' => 'LIOR920505XX0',
            'curp' => '5557654321',
            'email' => 'lizbethlagunascastaneda@gmail.com',
            ],
        ];
    }
    
    public function render()
    {
          
        return view('livewire.table-solicitudes', [
            'solicitudes' => $this->solicitudes, // llama automáticamente al getter
        ]);
        
    }
}