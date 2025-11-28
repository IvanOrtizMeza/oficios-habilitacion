<?php

namespace App\Livewire\Actions;
use Carbon\Carbon;
use RuntimeException;
use App\Models\Solicitud;
class SignedRequest
{
    /**
     * Generate the PDF for the given solicitud.
     */
    public function handle($solicitud,$fechaCreacionPdf)    
    {
       
        $endpoint = 'https://firma-dev.morelos.gob.mx/WSFirmaOficio';
        $keyPath = storage_path('app/keys/ROPG890310N82.key');

        if (! is_readable($keyPath)) {
            throw new \RuntimeException('Archivo .key no encontrado');
        }
        if (filesize($keyPath) === 0) {
            throw new \RuntimeException("El archivo .key existe pero está vacío: $keyPath");
        }
        $contenido = file_get_contents($keyPath);
        if ($contenido === false || strlen($contenido) === 0) {
            throw new \RuntimeException('No se pudo leer el contenido del archivo .key');
        }
        $llave = base64_encode($contenido);
        $token = '7f0ba7dd-43f0-4565-a067-51d898d10771';
        $usuario = 'ROPG890310N82';
        $fraseClavePrivada = 'MariaXimena94';
        $tipoDocumento = '7';

        $anio = Carbon::parse($solicitud->fecha_creacion_pdf)->format('Y');
        $ultimoFolio = Solicitud::whereYear('fecha_creacion_pdf', $anio)
                    ->orderBy('folio_num', 'desc')
                    ->first();
        $folioNum = $ultimoFolio ? $ultimoFolio->folio_num + 1 : 1;
        $folioDocumento = sprintf('S.C./D.G.R/%06d/%s', $folioNum, $anio);
        
        $fechaPDF = $solicitud->fecha_creacion_pdf ?? $fechaCreacionPdf;
        $fechaDocumento = $fechaPDF->format('Y-m-d H:i:s');
        $contenido = 
        'No Folio: ' . $folioDocumento . "\n" .
        'Fecha de creacion oficio o fecha actual: ' . Carbon::now()->format('d/m/Y') . "\n" .
        'RFC: ' . $solicitud->rfc . "\n" .
        'Nombre: ' . $solicitud->nombre . "\n" .
        'Línea captura: linea captura' . "\n" .
        'Estado: ' . ($solicitud->estado == 1 ? 'HABILITADO' : 'NO HABILITADO') . "\n\n";
        $destinatarioNombre = $solicitud->nombre;
        $destinatarioRfc = $solicitud->rfc;
        $destinatarioCurp = $solicitud->curp;
      
        //$uuid = '';
        $soapEnvelope = <<<XML
        <?xml version="1.0" encoding="UTF-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://servicioweb/">
            <soapenv:Header/>   
            <soapenv:Body>
                <ser:firmaDatos>
                    <token>{$token}</token>
                    <usuario>{$usuario}</usuario>
                    <clavePrivada>{$llave}</clavePrivada>
                    <fraseClavePrivada>{$fraseClavePrivada}</fraseClavePrivada>
                    <DatosFirma>
                        <tipoDocumento>{$tipoDocumento}</tipoDocumento>
                        <folioDocumento>{$folioDocumento}</folioDocumento>
                        <fechaDocumento>{$fechaDocumento}</fechaDocumento>
                        <contenido>{$contenido}</contenido> //cadena original a firmar
                        <destinatario>
                            <nombreCompleto>{$destinatarioNombre}</nombreCompleto>
                            <rfc>{$destinatarioRfc}</rfc>
                            <curp>{$destinatarioCurp}</curp>
                        </destinatario>
        XML;
                        $soapEnvelope .= <<<XML
                    </DatosFirma>
                </ser:firmaDatos>
            </soapenv:Body>
        </soapenv:Envelope>
        XML;

        
        $keyContent = file_get_contents($keyPath);
        $boundary  = 'MIME_boundary_' . bin2hex(random_bytes(8));
        $startCid  = 'envelope@soap';

        
        $multipartBody  = '';
        $multipartBody .= "--{$boundary}\r\n";
        $multipartBody .= "Content-Type: text/xml; charset=UTF-8\r\n";
        $multipartBody .= "Content-Transfer-Encoding: 8bit\r\n";
        $multipartBody .= "Content-ID: <{$startCid}>\r\n\r\n";
        $multipartBody .= $soapEnvelope . "\r\n";

        $multipartBody .= "--{$boundary}\r\n";
        $multipartBody .= "Content-Type: application/x-x509-ca-cert\r\n";
        $multipartBody .= "Content-Transfer-Encoding: binary\r\n";
        $multipartBody .= "Content-Disposition: attachment; filename=\"certificado.cer\"\r\n\r\n";


        $multipartBody .= "--{$boundary}\r\n";
        $multipartBody .= "Content-Type: application/octet-stream\r\n";
        $multipartBody .= "Content-Transfer-Encoding: binary\r\n";
        $multipartBody .= "Content-Disposition: attachment; filename=\"llave.key\"\r\n\r\n";
        $multipartBody .= $keyContent . "\r\n";

        $multipartBody .= "--{$boundary}--\r\n";
        
        $contentType = "multipart/related; type=\"text/xml\"; start=\"<{$startCid}>\"; boundary=\"{$boundary}\"";

        
        $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
            "Content-Type: {$contentType}",
            "SOAPAction: \"\"", // binding indica soapAction vacío
            "Expect:" // evitar '100-continue'
            ],
            CURLOPT_POSTFIELDS => $multipartBody,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 90,
            CURLOPT_SSL_VERIFYPEER => false, // DESACTIVA SSL para pruebas locales
            CURLOPT_SSL_VERIFYHOST => false,
            // Si requieres cookie de sesión del portal:
            // CURLOPT_COOKIE => "JSESSIONID=TU_SESION_AQUI",
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err      = curl_error($ch);
        curl_close($ch);
        if ($response === false) {
            throw new RuntimeException("Error al firmar: {$err}");
        }
    

        if ($httpCode < 200 || $httpCode >= 300) {
            throw new RuntimeException("HTTP {$httpCode}:\n{$response}");
        }
        
        $xml = @simplexml_load_string($response);
        if ($xml === false) {
            throw new RuntimeException("No se pudo parsear la respuesta SOAP:\n{$response}");
        }
        
        $xml->registerXPathNamespace('S', 'http://schemas.xmlsoap.org/soap/envelope/');
        $xml->registerXPathNamespace('ns2', 'http://servicioweb/');

        $uuidNodes = $xml->xpath('//ns2:firmaDatosResponse/return/uuid');
        $uuid      = $uuidNodes && isset($uuidNodes[0]) ? (string)$uuidNodes[0] : null;
        
        $urlNodes = $xml->xpath('//ns2:firmaDatosResponse/return/urlValidacion');
        $urlValidacion = $urlNodes && isset($urlNodes[0]) ? (string)$urlNodes[0] : null;
        // selloDigital
        $selloNodes = $xml->xpath('//ns2:firmaDatosResponse/return/selloDigital');
        $selloDigital = $selloNodes && isset($selloNodes[0]) ? (string)$selloNodes[0] : null;
        
        // estampadoTiempo
        $estampadoTiempoNodes = $xml->xpath('//ns2:firmaDatosResponse/return/estampadoTiempo');
        $estampadoTiempo = $estampadoTiempoNodes && isset($estampadoTiempoNodes[0]) ? (string)$estampadoTiempoNodes[0] : null;

        // selloEstampadoTiempo
        $selloETNodes = $xml->xpath('//ns2:firmaDatosResponse/return/selloEstampadoTiempo');
        $selloEstampadoTiempo = $selloETNodes && isset($selloETNodes[0]) ? (string)$selloETNodes[0] : null;
     
        return [
            'urlValidacion' => $urlValidacion,
            'selloDigital' => $selloDigital,
            'estampadoTiempo' => $estampadoTiempo,
            'selloEstampadoTiempo' => $selloEstampadoTiempo,
            'fechaDocumento'=> $fechaDocumento,
            'folioDocumento'=>$folioDocumento,
            'fechaPDF'=> $fechaPDF,
        ];

    }
}