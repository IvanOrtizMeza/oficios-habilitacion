<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\ValidationException;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SancionesFederalesImport;
use Flux\Flux;
class UploadExcel extends Component
{
    use WithFileUploads;

    public $excelFile;
    public $progress = 0;
      // Columnas y hoja esperadas
    private $hojaEsperada = 'tableResultado';
    private $columnasEsperadas = ['DEPENDENCIA', 'RFC', 'HOMO', 'APELLIDO PATERNO', 'APELLIDO MATERNO', 'NOMBRE', 'AUTORIDAD SANCIONADORA', 'PUESTO', 'PERIODO', 'FECHA RESOLUCION', 'FECHA NOTIFICACION', 'FECHA INICIO', 'FECHA FIN'];

    protected function rules()
    {
        return [
            'excelFile' => 'required|file',
        ];
    }
    protected function messages()
    {
        return [
            'excelFile.required' => 'Debes seleccionar un archivo Excel.',
            'excelFile.file' => 'El archivo debe ser válido.',
            //'excelFile.max' => 'El archivo no puede superar los 5 MB.',
            //'excelFile.mimetypes' => 'Solo se permiten archivos Excel (.xls, .xlsx).', // <- aquí
            //'excelFile.*' => 'El archivo debe ser de tipo Excel (.xls, .xlsx).',  
        ];
    }
    //validacion en la carga del archivo excel
    public function updatedExcelFile()
    {
        $archivo = $this->excelFile;
      
        if (!$archivo) {
            return;
        }
        try {
            // $this->validateOnly('excelFile');
            // Datos del archivo

            $extension = strtolower($archivo->getClientOriginalExtension());
            $sizeKB = $archivo->getSize() / 1024; // tamaño en KB
            // Validar extensión (.xls y .xlsx)
            if (!in_array($extension, ['xls', 'xlsx'])) {
                Flux::toast(
                    variant: 'danger',
                    heading: 'Archivo inválido',
                    text: 'Debes seleccionar un archivo Excel (.xls o .xlsx).',
                    position: 'top-center'
                );
                // Limpiar el input para evitar conflictos
                $this->reset('excelFile');
                return;
            }
            // Validar tamaño maximo (ejemplo: máximo 5 MB)
            if ($sizeKB > 5 * 1024) {
                Flux::toast(
                    variant: 'danger',
                    heading: 'Archivo demasiado grande',
                    text: 'El archivo no puede superar los 5 MB.',
                    position: 'top-center'
                );

                $this->reset('excelFile');
                return;
            }

            // leo el archivo excel
            $datos = Excel::toArray([], $this->excelFile);
            // leo la primera hoja
            $hoja = $datos[0] ?? [];

            // comprobar contenido
            foreach ($hoja as $fila) {
                foreach ($fila as $celda) {
                    if (!empty(trim((string) $celda))) {
                        $hayContenido = true;
                        break 2;
                    }
                }
            }
            if (!isset($hayContenido)) {
                Flux::toast('El archivo está vacío.', variant: 'danger', position: 'top-center');
                return;
            }

            // encabezados del excel para compararlos con los esperados
            $encabezados = array_map(
                fn ($v) => strtoupper(trim((string) $v)),
                $hoja[0] ?? []
            );
            $encabezados = array_filter($encabezados, fn ($v) => $v !== '');
            $encabezados = array_values($encabezados); // reindexar

            // Comparar con los esperados con los del archivo que se sube
            $columnasFaltantes = array_diff($this->columnasEsperadas, $encabezados);

            if (!empty($columnasFaltantes)) {
                $missing = implode(', ', $columnasFaltantes);
                Flux::toast("Encabezados faltantes: {$missing}", variant: 'danger', position: 'top-center');
                return;
            } 
            /*
            $columnCount = count($encabezados);
            foreach ($hoja as $i => $fila) {
                $hoja[$i] = array_slice($fila, 0, $columnCount);
            }
           
            foreach ($hoja as $i => $fila) {
                if ($i === 0) continue; // Saltar encabezado

                $filaAsociativa = array_combine($encabezados, $fila);

                $errores = [];

                $rfc = trim((string)($filaAsociativa['RFC'] ?? ''));

                if ($rfc === '') {
                    $errores[] = "RFC vacío";
                } elseif (strlen($rfc) !== 13) {
                    $errores[] = "RFC con longitud inválida";
                }
                 // Validar Nombre
                $nombre = trim((string)($filaAsociativa['NOMBRE'] ?? ''));
                if ($nombre === '') {
                    $errores[] = "Nombre vacío";
                }

                // Si hay errores → mostrar un solo mensaje
                if (!empty($errores)) {
                    $mensaje = "Fila " . ($i + 1) . ": " . implode(', ', $errores) . ".";
                    Flux::toast($mensaje, variant:'danger', position:'top-center');
                    $this->reset('excelFile');
                    return;
                }
            }*/
        }catch (Exception $e) {
          
            Flux::toast(
                variant: 'danger',
                heading: 'Error inesperado',
                text: 'Ocurrió un problema al procesar el archivo: ' . $e->getMessage(),
                position: 'top-center'
            );
            return;
        }
        $this->progress = 30;

        // Simular un proceso (por ejemplo validación o lectura del Excel)
        sleep(1);
        $this->progress = 60;

        // Simular el guardado
        sleep(1);
        $this->progress = 100;
    }

    public function removeFile()
    {
        $this->reset('excelFile');
    }
   
    public function subir()
    {
           
        $path = $this->excelFile->store('excel_uploads', 'public');
        Excel::import(new SancionesFederalesImport, storage_path('app/public/' . $path));
        Flux::toast (
            variant: 'success',
            heading: 'Archivo Subido Correctamente',
            text: 'El archivo Excel ha sido procesado exitosamente.',
            position: 'top-center'
        );
     

        $this->reset('excelFile');
    }
    public function render()
    {
        return view('livewire.upload-excel');
    }
}