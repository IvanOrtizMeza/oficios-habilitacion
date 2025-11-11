<?php

namespace App\Imports;

use App\Models\SancionesFederal;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
HeadingRowFormatter::default('slug');
class SancionesFederalesImport implements ToModel,WithHeadingRow
{
    //WithHeadingRow usa la primera fila del Excel como encabezado.
    
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new SancionesFederal([
           'dependencia'=> $row['dependencia'] ?? null,
            'rfc'=> $row['rfc'] ?? null,
            'homo'=> $row['homo'] ?? null,
            'apellido_paterno'=> $row['apellido_paterno'] ?? null,
            'apellido_materno'=> $row['apellido_materno'] ?? null,
            'nombre'=> $row['nombre'] ?? null,
            'autoridad_sancionadora'=> $row['autoridad_sancionadora'] ?? null,
            'puesto'=> $row['puesto'] ?? null,
            'periodo'=> $row['periodo'] ?? null,
            // Todas las fechas pasan por la función parseFecha()
            'fecha_resolucion' => $this->parseFecha($row['fecha_resolucion'] ?? null),
            'fecha_notificacion' => $this->parseFecha($row['fecha_notificacion'] ?? null),
            'fecha_inicio' => $this->parseFecha($row['fecha_inicio'] ?? null),
            'fecha_fin' => $this->parseFecha($row['fecha_fin'] ?? null),
        ]);
    }
    private function parseFecha($valor)
    {
        if (empty($valor)) {
            return null;
        }

        // 🔹 Si es numérico (formato Excel)
        if (is_numeric($valor)) {
            try {
                return Date::excelToDateTimeObject($valor)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        // 🔹 Si contiene "/"
        if (str_contains($valor, '/')) {
            try {
                return Carbon::createFromFormat('d/m/Y', $valor)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        // 🔹 Si contiene "-"
        if (str_contains($valor, '-')) {
            try {
                return Carbon::createFromFormat('Y-m-d', $valor)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        // 🔹 En cualquier otro caso, regresamos null
        return null;
    }
}