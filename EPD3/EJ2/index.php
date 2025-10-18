<?php
function generarCodigoActividad(string $actividad, int $longitud): string {
    if ($longitud <= 0) return '';
        $limpio = preg_replace('/[^A-Za-z0-9]/', '', $actividad);
        $prefijo = strtoupper(substr($limpio, 0, 3));
        $prefijo = str_pad($prefijo, 3, 'X');
        $sufijo = strtoupper(uniqid('', true));
        $sufijo = preg_replace('/[^A-Za-z0-9]/', '', $sufijo);
        $codigo = $prefijo . $sufijo;
        return substr($codigo, 0, $longitud);
}
echo generarCodigoActividad("yoga", 8), "\n";       /* YOG67A2F */
echo generarCodigoActividad("spinning", 10), "\n";  /* SPI67A2F1B */
echo generarCodigoActividad("natacion", 6), "\n";   /* NAT67A */
    
        
