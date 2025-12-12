<?php
/**
 * Generador de XML para Facturación Electrónica SRI Ecuador
 */

require_once __DIR__ . '/../config.php';

class SRIXMLGenerator {
    
    private $infoTributaria = [];
    private $infoFactura = [];
    private $detalles = [];
    private $claveAcceso;
    
    /**
     * Configurar información tributaria
     */
    public function setInfoTributaria($datos) {
        $this->infoTributaria = [
            'ambiente' => $datos['ambiente'] ?? AMBIENTE,
            'tipoEmision' => $datos['tipoEmision'] ?? EMISION_NORMAL,
            'razonSocial' => $datos['razonSocial'] ?? EMPRESA_RAZON_SOCIAL,
            'nombreComercial' => $datos['nombreComercial'] ?? EMPRESA_NOMBRE_COMERCIAL,
            'ruc' => $datos['ruc'] ?? EMPRESA_RUC,
            'codDoc' => $datos['codDoc'] ?? TIPO_FACTURA,
            'estab' => $datos['estab'] ?? '001',
            'ptoEmi' => $datos['ptoEmi'] ?? '001',
            'secuencial' => str_pad($datos['secuencial'] ?? '1', 9, '0', STR_PAD_LEFT),
            'dirMatriz' => $datos['dirMatriz'] ?? EMPRESA_DIRECCION
        ];
        
        // Generar clave de acceso
        $fecha = obtenerFechaFormato($datos['fecha'] ?? null);
        $codigoNumerico = generarCodigoNumerico();
        
        $this->claveAcceso = generarClaveAcceso(
            $fecha,
            $this->infoTributaria['codDoc'],
            $this->infoTributaria['ruc'],
            $this->infoTributaria['ambiente'],
            $this->infoTributaria['estab'] . $this->infoTributaria['ptoEmi'],
            $this->infoTributaria['secuencial'],
            $codigoNumerico,
            $this->infoTributaria['tipoEmision']
        );
        
        $this->infoTributaria['claveAcceso'] = $this->claveAcceso;
    }
    
    /**
     * Configurar información de la factura
     */
    public function setInfoFactura($datos) {
        $this->infoFactura = [
            'fechaEmision' => $datos['fechaEmision'] ?? formatearFechaSRI(),
            'dirEstablecimiento' => $datos['dirEstablecimiento'] ?? EMPRESA_DIRECCION,
            'obligadoContabilidad' => $datos['obligadoContabilidad'] ?? EMPRESA_OBLIGADO_CONTABILIDAD,
            'tipoIdentificacionComprador' => $datos['tipoIdentificacionComprador'] ?? '05',
            'razonSocialComprador' => $datos['razonSocialComprador'] ?? '',
            'identificacionComprador' => $datos['identificacionComprador'] ?? '',
            'direccionComprador' => $datos['direccionComprador'] ?? '',
            'totalSinImpuestos' => number_format($datos['totalSinImpuestos'] ?? 0, 2, '.', ''),
            'totalDescuento' => number_format($datos['totalDescuento'] ?? 0, 2, '.', ''),
            'totalConImpuestos' => [],
            'propina' => number_format($datos['propina'] ?? 0, 2, '.', ''),
            'importeTotal' => number_format($datos['importeTotal'] ?? 0, 2, '.', ''),
            'moneda' => $datos['moneda'] ?? 'DOLAR'
        ];
        
        // Calcular impuestos
        if (isset($datos['impuestos']) && is_array($datos['impuestos'])) {
            foreach ($datos['impuestos'] as $impuesto) {
                $this->infoFactura['totalConImpuestos'][] = [
                    'codigo' => $impuesto['codigo'],
                    'codigoPorcentaje' => $impuesto['codigoPorcentaje'],
                    'baseImponible' => number_format($impuesto['baseImponible'], 2, '.', ''),
                    'valor' => number_format($impuesto['valor'], 2, '.', '')
                ];
            }
        }
    }
    
    /**
     * Agregar detalle a la factura
     */
    public function addDetalle($detalle) {
        $this->detalles[] = [
            'codigoPrincipal' => $detalle['codigoPrincipal'] ?? '',
            'codigoAuxiliar' => $detalle['codigoAuxiliar'] ?? '',
            'descripcion' => $detalle['descripcion'] ?? '',
            'cantidad' => number_format($detalle['cantidad'] ?? 1, 2, '.', ''),
            'precioUnitario' => number_format($detalle['precioUnitario'] ?? 0, 6, '.', ''),
            'descuento' => number_format($detalle['descuento'] ?? 0, 2, '.', ''),
            'precioTotalSinImpuesto' => number_format($detalle['precioTotalSinImpuesto'] ?? 0, 2, '.', ''),
            'impuestos' => $detalle['impuestos'] ?? []
        ];
    }
    
    /**
     * Generar XML de la factura
     */
    public function generarXML() {
        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;
        
        // Elemento raíz
        $factura = $xml->createElement('factura');
        $factura->setAttribute('id', 'comprobante');
        $factura->setAttribute('version', VERSION_XML_110);
        $xml->appendChild($factura);
        
        // Información tributaria
        $infoTributaria = $xml->createElement('infoTributaria');
        foreach ($this->infoTributaria as $key => $value) {
            $elemento = $xml->createElement($key, htmlspecialchars($value, ENT_XML1, 'UTF-8'));
            $infoTributaria->appendChild($elemento);
        }
        $factura->appendChild($infoTributaria);
        
        // Información de la factura
        $infoFactura = $xml->createElement('infoFactura');
        
        foreach ($this->infoFactura as $key => $value) {
            if ($key == 'totalConImpuestos') {
                $totalConImpuestos = $xml->createElement('totalConImpuestos');
                foreach ($value as $impuesto) {
                    $totalImpuesto = $xml->createElement('totalImpuesto');
                    foreach ($impuesto as $impKey => $impValue) {
                        $elemento = $xml->createElement($impKey, htmlspecialchars($impValue, ENT_XML1, 'UTF-8'));
                        $totalImpuesto->appendChild($elemento);
                    }
                    $totalConImpuestos->appendChild($totalImpuesto);
                }
                $infoFactura->appendChild($totalConImpuestos);
            } else {
                $elemento = $xml->createElement($key, htmlspecialchars($value, ENT_XML1, 'UTF-8'));
                $infoFactura->appendChild($elemento);
            }
        }
        $factura->appendChild($infoFactura);
        
        // Detalles
        $detallesElement = $xml->createElement('detalles');
        foreach ($this->detalles as $det) {
            $detalle = $xml->createElement('detalle');
            
            foreach ($det as $key => $value) {
                if ($key == 'impuestos') {
                    $impuestosElement = $xml->createElement('impuestos');
                    foreach ($value as $imp) {
                        $impuesto = $xml->createElement('impuesto');
                        foreach ($imp as $impKey => $impValue) {
                            $elemento = $xml->createElement($impKey, htmlspecialchars($impValue, ENT_XML1, 'UTF-8'));
                            $impuesto->appendChild($elemento);
                        }
                        $impuestosElement->appendChild($impuesto);
                    }
                    $detalle->appendChild($impuestosElement);
                } else {
                    $elemento = $xml->createElement($key, htmlspecialchars($value, ENT_XML1, 'UTF-8'));
                    $detalle->appendChild($elemento);
                }
            }
            
            $detallesElement->appendChild($detalle);
        }
        $factura->appendChild($detallesElement);
        
        return $xml;
    }
    
    /**
     * Guardar XML en archivo
     */
    public function guardarXML($nombreArchivo = null) {
        if ($nombreArchivo === null) {
            $nombreArchivo = $this->claveAcceso . '.xml';
        }
        
        $rutaCompleta = XML_PATH . $nombreArchivo;
        $xml = $this->generarXML();
        $xml->save($rutaCompleta);
        
        return $nombreArchivo;
    }
    
    /**
     * Obtener clave de acceso
     */
    public function getClaveAcceso() {
        return $this->claveAcceso;
    }
}
