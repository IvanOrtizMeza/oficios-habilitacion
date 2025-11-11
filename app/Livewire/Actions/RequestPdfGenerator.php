<?php

namespace App\Livewire\Actions;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\Browsershot\Browsershot;
use Carbon\Carbon;
class RequestPdfGenerator
{
    /**
     * Generate the PDF for the given solicitud.
     */
    public function handle($solicitud)
    {
       
        $firmaData = (new SignedRequest)->handle($solicitud);
       
        $qrSvg = QrCode::format('svg')->size(200)->generate($firmaData['urlValidacion']);
        $qrCode = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
        $contenido = '||' . 'OF-' . date('Y') . '-' . str_pad($solicitud->id, 3, '0', STR_PAD_LEFT) . '|'. Carbon::now()->format('d/m/Y') . '|' . $solicitud->rfc . '|' . 123456 . '|' . ($solicitud->estado == 1 ? 'CONSTANCIA DE HABILITACION' : 'CONSTANCIA DE NO HABILITACION') . '||';
        $fechaEnLetras = $this->fechaATexto(Carbon::now());
        $folioDocumento = 'OF-' . date('Y') . '-' . str_pad($solicitud->id, 3, '0', STR_PAD_LEFT);
        $html = view('PDF.oficio', [
            'solicitud' => $solicitud,
            'folioDocumento' => $folioDocumento,
            'qrCode' => $qrCode,
            'selloDigital' => $firmaData['selloDigital'],
            'estampadoTiempo' => $firmaData['estampadoTiempo'],
            'selloEstampadoTiempo' => $firmaData['selloEstampadoTiempo'],
            'contenido' =>$contenido,
            'fechaEnLetras' => $fechaEnLetras,
        ])->render();
      
        $pdfPath = storage_path("app/public/oficio_{$solicitud->id}.pdf");
             
        Browsershot::html($html)
            ->format('A4')
            ->margins(10,10,10,10)
            ->fullPage()
            ->showBackground()
            ->save($pdfPath);

        return $pdfPath; 

    }
     private function fechaATexto($fecha = null)
    {
        $fecha = $fecha ? Carbon::parse($fecha) : Carbon::today();
        
        $dia = (int)$fecha->format('d');
        $meses = [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
            5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
            9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
        ];
        $mes = $meses[(int)$fecha->format('m')];
        $anio = (int)$fecha->format('Y');
        $formatter = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
        $diaTexto = $formatter->format($dia);
        $anioTexto = $formatter->format($anio);
        return "{$diaTexto} días del mes de {$mes} del año {$anioTexto}";
    }
}