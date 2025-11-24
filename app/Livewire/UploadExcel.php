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
    public $showErrorsModal = false;
    public $erroresList = [];
    
    // Columnas y hoja esperadas
    //private $hojaEsperada = 'tableResultado';
    private $columnasEsperadas = ['DEPENDENCIA', 'RFC', 'HOMO', 'APELLIDO PATERNO', 'APELLIDO MATERNO', 'NOMBRE', 'AUTORIDAD SANCIONADORA', 'PUESTO', 'PERIODO', 'FECHA RESOLUCION', 'FECHA NOTIFICACION', 'FECHA INICIO', 'FECHA FIN'];

    private function getColumnLetter($index) {
        $letters = '';
        while ($index >= 0) {
            $letters = chr(65 + ($index % 26)) . $letters;
            $index = intval($index / 26) - 1;
        }
        return $letters;
    }    protected function rules()
    {
        return [
            'excelFile' => 'required|file|max:5120', // máximo 2 MB
        ];
    }
    protected function messages()
    {
        return [
            'excelFile.required' => 'Debes seleccionar un archivo Excel.',
            'excelFile.file' => 'El archivo debe ser válido.',
            'excelFile.mimes' => 'Solo se permiten archivos Excel (.xls, .xlsx).',
            'excelFile.max' => 'El archivo no puede superar los 2 MB.',
        ];
    }
    //validacion en la carga del archivo excel
    public function updatedExcelFile()
    {
        $archivo = $this->excelFile;

        try {
            $this->validateOnly('excelFile');

            $nombreArchivo = $this->excelFile->getClientOriginalName();
            $extension = strtolower($archivo->getClientOriginalExtension());
            $sizeKB = $archivo->getSize() / 1024;


            $datos = Excel::toArray([], $archivo);
            $hoja = $datos[0] ?? []; //tomo la primer hoja de excel, si no existe regreso un array vacio


            if (empty($hoja)) {

                Flux::toast('El archivo está vacío.', variant: 'danger', position: 'top-center');
                $this->reset('excelFile');
                return;
            }

            $hayContenido = false;
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
                $this->reset('excelFile');
                return;
            }

            $encabezados = array_map(
                fn($v) => strtoupper(trim((string) $v)),
                $hoja[0] ?? []
            );
            $indicesValidos = [];
            foreach ($encabezados as $idx => $header) {
                if ($header !== '') {
                    $indicesValidos[] = $idx;
                }
            }
            $encabezados = array_values(array_intersect_key($encabezados, array_flip($indicesValidos)));

            $columnasFaltantes = array_diff($this->columnasEsperadas, $encabezados);

            if (!empty($columnasFaltantes)) {

                $missing = implode(', ', $columnasFaltantes);
                Flux::toast("Encabezados faltantes: {$missing}", variant: 'danger', position: 'top-center');
                $this->reset('excelFile');
                return;
            }

            $columnasExtras = array_diff($encabezados, $this->columnasEsperadas);
            if (!empty($columnasExtras)) {
                $missing = implode(', ', $columnasExtras);
                Flux::toast("Encabezados no permitidos en el Excel: {$missing}", variant: 'danger', position: 'top-center');
                $this->reset('excelFile');
                return;
            }

            $errores = [];
            foreach ($hoja as $i => $fila) {
                if ($i === 0) continue; // Saltar encabezado

                $filaFiltrada = [];
                foreach ($indicesValidos as $idx) {
                    $filaFiltrada[] = trim((string)($fila[$idx] ?? ''));
                }
                $filaAsociativa = array_combine($encabezados, $filaFiltrada);
                $rowNum = $i + 2; // Fila 1: encabezado, fila 2: primera dato

                // Validar RFC
                $rfc = trim((string)($filaAsociativa['RFC'] ?? ''));
                $colIndexRfc = array_search('RFC', $encabezados);
                if ($colIndexRfc !== false) {
                    $colLetter = $this->getColumnLetter($colIndexRfc);
                    $valoresVacios = ['', 'NULL', 'N/A', '#N/A', '0'];
                    if (empty($rfc) || in_array(strtoupper($rfc), $valoresVacios)) {
                        $errores[] = "Celda {$colLetter}{$rowNum}: RFC vacío ('{$rfc}')";
                    } 
                    /*elseif (!preg_match('/^[A-Z&Ñ]{3,4}\d{6}[A-Z0-9]{3}$/iu', strtoupper($rfc))) {
                        $errores[] = "Celda {$colLetter}{$rowNum}: RFC inválido ('{$rfc}')";
                    }*/
                }

                // Validar Nombre
               /* $nombre = trim((string)($filaAsociativa['NOMBRE'] ?? ''));
                $colIndexNombre = array_search('NOMBRE', $encabezados);
                if ($colIndexNombre !== false) {
                    $colLetter = $this->getColumnLetter($colIndexNombre);
                    if (empty($nombre)) {
                        $errores[] = "Celda {$colLetter}{$rowNum}: Nombre vacío ('{$nombre}')";
                    }
                }*/
            }

            if (!empty($errores)) {
                $this->erroresList = $errores;
                $this->showErrorsModal = true;
                $this->reset('excelFile');
                return;
            }
        } catch (ValidationException $e) {
            $mensaje = implode(', ', $e->validator->errors()->all());
            Flux::toast(
                variant: 'danger',
                heading: 'Error',
                text: 'Archivo inválido: ' . $mensaje,
                position: 'top-center'
            );
            $this->removeFile();
            return;
        }
    }


    public function subir()
    {

        $path = $this->excelFile->store('excel_uploads', 'public');
        try {
            Excel::import(new SancionesFederalesImport, storage_path('app/public/' . $path));
            Flux::toast(
                variant: 'success',
                heading: 'Archivo Subido Correctamente',
                text: 'El archivo Excel ha sido procesado exitosamente.',
                position: 'top-center'
            );
            $this->reset('excelFile');
        } catch (ValidationException $e) {
            Flux::toast('Error en la importación del archivo: ' . $e->getMessage(), variant: 'danger', position: 'top-center');
        }
    }

    public function removeFile()
    {
        $this->reset('excelFile');
        $this->showErrorsModal = false;
        $this->erroresList = [];
    }
    public function render()
    {
        return view('livewire.upload-excel');
    }
}
