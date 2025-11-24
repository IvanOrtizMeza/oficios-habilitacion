<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" content="width=device-width, initial-scale=1.0">
    <title>Oficio </title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-5">
            {{--  <img src="{{ public_path('img/morelosimg.svg') }}" alt="Logo" width="140"> --}}
            <img src="{{ public_path('img/imgContraloria.png') }}" style="width: 220px; height: 40px;">
            <div class="text-right text-sm leading-tight">
                <div><span class="font-semibold ">Depto:</span> DIR.GRAL. DE RESP.</div>
                <div><span class="font-semibold ">Oficio No:</span> {{ $folioDocumento }}</div>
            </div>
        </div>
         <div class="text-right text-sm" >
            <span class="font-semibold ">Asunto:</span> OFICIO DE HABILITACIÓN O CONSTANCIA DE NO INHABILITACIÓN
        </div>
       
        <div class="text-left text-sm">
            <span class="font-semibold uppercase">A quien corresponda:</span>
        </div>
       
        <div class="text-left text-sm mt-2">
            PRESENTE:
        </div>
        <div class="text-justify text-sm mt-3 ">
            <p style="font-size: small">
                Con fundamento en los artículos 1, 3, 8, 9 fracción X, 13, 14 fracción VIII, XXII, XXIV, y 31 fracciones III, VI y VII de la Ley
                Orgánica de la Administración Pública del Estado Libre y Soberano de Morelos; 1, 3, 4, 5 fracción VIII, 10 fracción XVII, XLVII
                y 19 fracción III del Reglamento Interior de la Secretaria de la Contraloría vigente, 10 de los lineamientos para la expedición
                de oficio de habilitación o constancia de no inhabilitación de la Secretaría de la Contraloría; en relación con el Convenio
                Federación – Estado celebrado con el objeto de Coordinar Acciones para Asegurar el Cumplimiento Estricto de las
                Disposiciones que contiene las Leyes Federal y Estatal de Responsabilidades de los Servidores Públicos, en cuanto a la
                obligación de estos para no seleccionar, contratar, nombrar o designar a quienes se encuentran inhabilitados para ocupar un
                empleo, cargo o comisión en el servicio público; así como la Cláusula Décima Quinta del Acuerdo de Coordinación
                Federación – Estado que tiene por objeto la realización de un programa de coordinación especial denominado
                “Fortalecimiento del Sistema Estatal de Control y Evaluación de la Gestión Pública, y colaboración en materia de
                Transparencia y Combate a la Corrupción”
            </p>
        </div>
        <div class="text-center text-sm font-semibold mt-3">
            <p>
            -------------------------HACE CONSTAR-------------------------
            </p>
        </div>
        <div class="text-justify text-sm mt-3">
            <p style="font-size: small">
               Que revisado que fue el Padrón de Servidores Públicos que se lleva en esta Dirección General de Responsabilidades y de la
                Secretaría de la Contraloría, así como el padrón de inhabilitados que emite la Secretaría Anticorrupción y Buen Gobierno,<strong><u> @if($solicitud->estado == 1)NO  @endif
                SE ENCONTRO RESOLUCION DE INHABILITACION @if($solicitud->estado == 1) ALGUNA  @endif QUE @if($solicitud->estado == 1) IMPIDA @elseif($solicitud->estado == 2) IMPIDE @endif  AL C. <strong><u>{{ strtoupper($solicitud->nombre) }}</u></strong> con
                Registro Federal de Contribuyentes {{ strtoupper ($solicitud->rfc) }}</u></strong> desempeñar empleo, cargo o comisión en la Administración Pública
                Federal, Estatal o Municipal.
            </p>
        </div>
        <div class="text-justify text-sm mt-3">
            <p style="font-size: small">
                Siendo preciso señalar que la información anterior se proporciona en atención a la base de datos que se tiene en esta
                Dirección General de Responsabilidades de la Secretaría de la Controlaría y en la Secretaría Anticorrupción y Buen
                Gobierno, con independencia de que exista en trámite alguna queja, denuncia o procedimiento administrativo que se siga en
                contra de la persona antes mencionada, sobre los que no se haya dictado hasta esta fecha, determinación o resolución
                definitiva, quedando la Secretaría de la Contraloría a disposición de quien corresponda en caso de requerir mayor
                información.
            </p>
        </div>
        <div class="text-justify text-sm mt-3">
            <p style="font-size: small">
                Cabe aclarar que en base al contenido de los ordenamientos arriba señalados, la Secretaría Anticorrupción y Buen Gobierno,
                es la responsable de operar la base de datos que contiene el Padrón de Servidores Públicos Inhabilitados.

            </p>
        </div>
        <div class="text-justify text-sm mt-3">
            <p style="font-size: small">
                El presente oficio tendrá vigencia de treinta días naturales a partir de su expedición.
            </p>
        </div>
        <div class="text-justify text-sm mt-2">
            <p style="font-size: small">
                Se expidió la presente a petición del (a) interesado (a) a los {{$fechaEnLetras}}
        </div>
        <div class="text-center text-sm mt-5">
             <span class="font-semibold ">ATENTAMENTE:</span>
        </div>
        <div class="text-center text-sm mt-2">
             <span class="font-semibold  ">LICENCIADO GERARDO ANTONIO RODRÍGUEZ PÉREZ</span>
        </div>
        <div class="text-center text-sm mt-2">
             <span class="font-semibold  ">DIRECTOR GENERAL DE RESPONSABILIDADES</span>
        </div>
        <div style="display: flex; align-items: center; gap: 15px; margin-top: 20px;">
            <!-- QR a la izquierda -->
            <div style="flex: 0 0 auto;">
                <img src="{{ $qrCode }}" alt="Código QR" width="150">
            </div>
            <!-- Texto a la derecha -->
             <div style="font-size: 10px; word-break: break-all;">
                <p><strong>Sello Digital:</strong> {{ $selloDigital }}</p>
                  <p><strong>Cadena Original:</strong> {{ $contenido }}</p>
                <p><strong>Estampado de Tiempo:</strong> {{ $estampadoTiempo }}</p>
                <p><strong>Sello Digital GEM:</strong> {{ $selloEstampadoTiempo }}</p>
            </div>
        </div>
    </body>
</html>
<!DOCTYPE html>
<html lang="es">
