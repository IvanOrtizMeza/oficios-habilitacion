<?php

namespace App\Livewire;

use App\Events\ProcessRequest;
use App\Models\Solicitud;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Component;
use Livewire\WithPagination;
use App\Livewire\Actions\RequestPdfGenerator;
use App\Livewire\Actions\SendEmail;
use App\Livewire\Actions\GetStatus;
use Exception;
use Illuminate\Support\Facades\Storage;

class TableSolicitudes extends Component
{
    public $readonly = true;
    public $email = '';
    public array $rangoDate = [];

    public $solicitudesSimuladas = [];

    use WithPagination;

    public $perPage = 5; // registros por página

    public $search = '';
    public $estadoFiltro = null;

    protected $listeners = [
        'ModalPdf' => 'verPDF',
        'ModalSolicitud' => 'verSolicitud',
    ];
    public $showConfirmModal = false;
    public $showConfirmModalPDF = false;
    public $solicitudId; // ID que viene de la tabla
    public $solicitud;   // Aquí guardarás la info completa de la base de datos
    public $pathPDF = null;
    public function verPDF($id)
    {
       
        try {
            $this->solicitudId = $id;
            $this->solicitud = Solicitud::find($id);
            $this->pathPDF = Storage::temporaryUrl($this->solicitud->pdf_path, now()->addMinutes(30));
            $this->showConfirmModalPDF = true;
        } catch (ModelNotFoundException $e) {
            $this->toast(
                'danger',
                'Error',
                'Ocurrio un error al ver el oficio. ' . $e->getMessage(),
                'top-center'
            );
            $this->showConfirmModalPDF = false;
        }
    }
    public function verSolicitud($id)
    {
        try {
            $this->solicitudId = $id;
            $this->loadSolicitud();
            $this->email = $this->solicitud->email;
            $this->readonly = true;
            $this->showConfirmModal = true;
        } catch (Exception $e) {
            $this->readonly = false;
            $this->showConfirmModal = false;
            $this->toast(
                'warning',
                'Ocurrio un problema',
                'Oucrrio un problema al ver la solicitud ' . $e->getMessage(),
                'top-center'
            );
        }
    }
    private function loadSolicitud()
    {
        try {

            $this->solicitud = Solicitud::findOrFail($this->solicitudId);

            return $this->solicitud;
        } catch (ModelNotFoundException $e) {
            $this->toast(
                'danger',
                'Error',
                'La solicitud que intenta ver no existe. ' . $e->getMessage(),
                'top-center'
            );
            $this->showConfirmModal = false;
            return null;
        }
    }
    public function descargarPDF()
    {
        $this->showConfirmModalPDF = false;
        return redirect()->route('oficio.descargar', $this->solicitudId);
    }
    public function getSolicitudesProperty()
    {
        return Solicitud::with('statusSolicitud')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                        ->orWhere('rfc', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->estadoFiltro, function ($query) {
                $query->where('estado', $this->estadoFiltro);
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);
    }

    public function procesar()
    {    // 1. Verificar si hay datos importados en la tabla del Excel
        /*$result = SancionesFederal::count() === 0;
        if( $result ) {
           $this->toast('warning', 'Error al procesar solicitudes', 'La base de datos esta vacia.', 'top-center');
           return;
        }*/
        $pendientes = Solicitud::where('estado', 0)->get();

        if ($pendientes->isEmpty()) {
            $this->toast('warning', 'No hay solicitudes para porocesar', 'Todas las solicitudes estan procesadas.', 'top-center');
            return;
        }
        $failed = [];
        foreach ($pendientes as $solicitud) {
            try {
                $fullPath =   DB::transaction(function () use ($solicitud) {

                    $solicitud->estado = (new GetStatus())->handle($solicitud);
                    event(new ProcessRequest($solicitud));
                    $filePath = (new RequestPdfGenerator())->handle(solicitud: $solicitud);
                    $solicitud->pdf_path = $filePath;
                    $solicitud->save();
                    return $filePath;
                });

                $emailOk = (new SendEmail())->handle($solicitud, $fullPath);
                if ($emailOk) {
                    $solicitud->update([
                        'correo_enviado' => 1,
                        'fecha_envio_correo' => now(),
                    ]);

                    event(new ProcessRequest($solicitud));
                } else {
                    $failed[] = [
                        'solicitud_id' => $solicitud->id,
                        'error' => 'Error al enviar el correo'
                    ];
                }
            } catch (\Throwable $e) {
                $failed[] = [
                    'solicitud_id' => $solicitud->id,
                    'error' => $e->getMessage() . 'File:' . $e->getFile() . '' . $e->getLine(),
                ];
            }
        }

        if (empty($failed)) {
            $this->toast('success', 'Solicitudes Procesadas Correctamente', 'Se enviaron los oficios de las solicitudes correspondientes al correo registrado.', 'top-center');
        } else {
            $total = count($pendientes);
            $errors = count($failed);
            $ok = $total - $errors;
            $text  = "Solicitudes procesadas correctamente: $ok\n";
            $text .= "Errores en: $errors solicitudes\n";
            $text .= "Detalles de errores:\n";

            foreach ($failed as $f) {
                $text .= "Solicitud ID {$f['solicitud_id']}: {$f['error']}\n";
            }
            $this->toast('warning', 'Error al procesar solicitudes', $text, 'top-center');
        }
    }

    private function toast($variant = '', $heading = '', $text = '', $position = '')
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
    public function accionCorreo()
    {
        try {
            return $this->readonly
                ? $this->habilitarEdicion()
                : $this->enviarEmail();
        } catch (Exception $e) {
            Flux::toast(
                variant: 'warning',
                heading: 'Ocurrio un problema',
                text: 'Error habilitar el campo de texto.' . $e->getMessage(),
                position: 'top-center'
            );
        }
    }
    public function habilitarEdicion()
    {
        $this->readonly = false;
    }
    public function enviarEmail()
    {
        try {
            $this->renviarEmail();
            $this->readonly = true;
        } catch (Exception $e) {
            Flux::toast(
                variant: 'warning',
                heading: 'Ocurrio un problema',
                text: 'Error al renviar el oficio.' . $e->getMessage(),
                position: 'top-center'
            );
        }
    }

    public function renviarEmail()
    {

        try {
            $solicitud = Solicitud::findOrFail($this->solicitudId);

            $nuevoEmail = $this->email;
            $fullPdfPath =  $solicitud->pdf_path;
            $emailResult = (new SendEmail())->handle($solicitud, $fullPdfPath, $nuevoEmail);

            if (!$emailResult) {
                $this->toast('danger', 'Error', 'Ocurrio un error al enviar el correo', 'top-center');
                return;
            }
            $solicitud->update([
                'correo_enviado' => 1,
                'fecha_envio_correo' => now(),
            ]);
            event(new ProcessRequest($solicitud));
            $this->toast('success', 'Envio Exitoso', 'El archivo PDF fue enviado con exito.', 'top-center');
            $this->showConfirmModal = false;
        } catch (ModelNotFoundException $e) {
            $this->toast('danger', 'Error', 'La solicitud que intenta reenviar no existe.', 'top-center');
            $this->logError($e->getMessage());
            $this->showConfirmModal = false;
        } catch (\Throwable $e) {

            $this->toast('danger', 'Error Inesperado', 'Ocurrió un problema durante el renvio.' . $e->getMessage(), 'top-center');
            $this->showConfirmModal = false;
        }
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
