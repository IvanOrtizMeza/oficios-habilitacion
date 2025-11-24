<?php

namespace App\Livewire\Actions;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\Browsershot\Browsershot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\Solicitud;

class RequestPdfGenerator
{

    public function handle(&$solicitud)
    {

        $solicitud->fecha_creacion_pdf = now();
        $iniciales = 'S.C./D.G.R/';
        $firmaData = (new SignedRequest)->handle($solicitud, $solicitud->fecha_creacion_pdf);

        $qrSvg = QrCode::format('svg')->size(200)->generate($firmaData['urlValidacion']);
        $qrCode = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
        //revisar
        $fechaExpedicion = Carbon::parse($solicitud->fecha_creacion_pdf)->format('d/m/Y');

        $anioExpedicion = Carbon::parse($solicitud->fecha_creacion_pdf)->format('Y');
        $ultimoFolioContenido = Solicitud::whereYear('fecha_creacion_pdf', $anioExpedicion)
                    ->orderBy('folio_num', 'desc')
                    ->first();
        $folioNumContenido = $ultimoFolioContenido ? $ultimoFolioContenido->folio_num + 1 : 1;
        $contenido = $iniciales . str_pad($folioNumContenido, 6, '0', STR_PAD_LEFT) . '/' . $anioExpedicion . '|' . $fechaExpedicion . '|' . $solicitud->rfc . '|' . 123456 . '|' . ($solicitud->estado == 1 ? 'OFICIO DE HABILITACIÓN / HABILITADO' : 'OFICIO DE HABILITACIÓN / NO HABILITADO');
        //revisar arriba
        
        $fechaEnLetras = $this->fechaATexto($solicitud);

        $anio = Carbon::parse($solicitud->fecha_creacion_pdf)->format('Y');
        
        $ultimoFolio = Solicitud::whereYear('fecha_creacion_pdf', $anio)
                        ->orderBy('folio_num', 'desc')
                        ->first();

        $folioNum = $ultimoFolio ? $ultimoFolio->folio_num + 1 : 1;
        $solicitud->folio_num = $folioNum;
        
        $folioDocumento = sprintf('S.C./D.G.R/%06d/%s', $folioNum, $anio);

        $html = view('PDF.oficio', [
            'solicitud' => $solicitud,
            'folioDocumento' => $folioDocumento,
            'qrCode' => $qrCode,
            'selloDigital' => $firmaData['selloDigital'],
            'estampadoTiempo' => $firmaData['estampadoTiempo'],
            'selloEstampadoTiempo' => $firmaData['selloEstampadoTiempo'],
            'contenido' => $contenido,
            'fechaEnLetras' => $fechaEnLetras,
        ])->render();

        
       // $fileName = "oficio_{$folioNum}_{$anio}.pdf";
       // $pdfPath = 'oficios/' . $fileName;
        $folioPadded = str_pad($folioNum, 6, '0', STR_PAD_LEFT);
        $carpetaAnio = "oficios/{$anio}";
        Storage::disk('local')->makeDirectory($carpetaAnio);
        $fileName = "oficio_{$folioPadded}_{$anio}.pdf";
        $pdfPath = "{$carpetaAnio}/{$fileName}";

        $pdfData = Browsershot::html($html)
            ->format('A4')
            ->margins(10, 10, 10, 10)
            ->fullPage()
            ->showBackground()
            ->pdf();
        //->save($pdfPath);
        Storage::disk('local')->put($pdfPath, $pdfData);
        return $pdfPath;
    }
    private function fechaATexto($solicitud)
    {
        $fecha = $solicitud->fecha_creacion_pdf;

        $dia = (int)$fecha->format('d');
        $meses = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];
        $mes = $meses[(int)$fecha->format('m')];
        $anio = (int)$fecha->format('Y');
        $formatter = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
        $diaTexto = $formatter->format($dia);
        $anioTexto = $formatter->format($anio);
        return "{$diaTexto} días del mes de {$mes} del año {$anioTexto}";
    }
}
