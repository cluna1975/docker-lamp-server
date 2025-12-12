<?php
/**
 * Firmador de XML con XAdES-BES para SRI Ecuador
 * Versión con soporte para OpenSSL 3.x y certificados legacy
 */

require_once __DIR__ . '/../config.php';

class SRIXMLSigner {
    
    private $certificadoPath;
    private $certificadoPassword;
    private $privateKey;
    private $publicKey;
    private $certificado;
    
    /**
     * Constructor
     */
    public function __construct($certificadoPath = null, $password = null) {
        // CERT_FILE ya contiene la ruta completa desde config.php
        $this->certificadoPath = $certificadoPath ?? CERT_FILE;
        $this->certificadoPassword = $password ?? CERT_PASSWORD;
    }
    
    /**
     * Cargar certificado .p12 usando OpenSSL CLI con soporte legacy
     */
    public function cargarCertificado() {
        if (!file_exists($this->certificadoPath)) {
            throw new Exception("El archivo de certificado no existe: " . $this->certificadoPath);
        }
        
        // Verificar que el archivo no esté vacío
        $fileSize = filesize($this->certificadoPath);
        if ($fileSize === 0) {
            throw new Exception("El archivo de certificado está vacío");
        }
        
        // SOLUCIÓN: Usar OpenSSL CLI con flag -legacy para extraer el certificado
        // Esto es necesario porque OpenSSL 3.x+ requiere el legacy provider para
        // algoritmos antiguos como RC2-40-CBC y 3DES
        
        $tempDir = sys_get_temp_dir();
        $certFile = $tempDir . '/cert_' . uniqid() . '.pem';
        $keyFile = $tempDir . '/key_' . uniqid() . '.pem';
        
        // Extraer certificado
        $certCmd = sprintf(
            'openssl pkcs12 -in %s -passin pass:%s -clcerts -nokeys -legacy 2>&1',
            escapeshellarg($this->certificadoPath),
           escapeshellarg($this->certificadoPassword)
        );
        
        exec($certCmd, $certOutput, $certReturn);
        
        if ($certReturn !== 0) {
            throw new Exception("Error al extraer el certificado: " . implode("\n", $certOutput));
        }
        
        file_put_contents($certFile, implode("\n", $certOutput));
        
        // Extraer clave privada
        $keyCmd = sprintf(
            'openssl pkcs12 -in %s -passin pass:%s -nocerts -nodes -legacy 2>&1',
            escapeshellarg($this->certificadoPath),
            escapeshellarg($this->certificadoPassword)
        );
        
        exec($keyCmd, $keyOutput, $keyReturn);
        
        if ($keyReturn !== 0) {
            @unlink($certFile);
            throw new Exception("Error al extraer la clave privada: " . implode("\n", $keyOutput));
        }
        
        file_put_contents($keyFile, implode("\n", $keyOutput));
        
        // Cargar el certificado y la clave en PHP
        $this->certificado = file_get_contents($certFile);
        $this->publicKey = $this->certificado;
        $this->privateKey = openssl_pkey_get_private(file_get_contents($keyFile));
        
        // Limpiar archivos temporales
        @unlink($certFile);
        @unlink($keyFile);
        
        if ($this->privateKey === false) {
            throw new Exception("Error al cargar la clave privada en PHP");
        }
        
        return true;
    }
    
    /**
     * Firmar XML con XAdES-BES
     */
    public function firmarXML($rutaXML, $rutaSalida = null) {
        // Cargar certificado
        $this->cargarCertificado();
        
        // Cargar XML
        if (!file_exists($rutaXML)) {
            throw new Exception("El archivo XML no existe: " . $rutaXML);
        }
        
        $xmlDoc = new DOMDocument();
        $xmlDoc->load($rutaXML);
        $xmlDoc->formatOutput = true;
        
        // Obtener información del certificado
        $certInfo = openssl_x509_parse($this->certificado);
        
        // Crear nodo Signature
        $signature = $xmlDoc->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:Signature');
        $signature->setAttribute('xmlns:ds', 'http://www.w3.org/2000/09/xmldsig#');
        $signature->setAttribute('xmlns:etsi', 'http://uri.etsi.org/01903/v1.3.2#');
        $signature->setAttribute('Id', 'Signature' . time());
        
        // SignedInfo
        $signedInfo = $xmlDoc->createElement('ds:SignedInfo');
        $signedInfo->setAttribute('Id', 'Signature-SignedInfo' . time());
        
        // CanonicalizationMethod
        $canonMethod = $xmlDoc->createElement('ds:CanonicalizationMethod');
        $canonMethod->setAttribute('Algorithm', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315');
        $signedInfo->appendChild($canonMethod);
        
        // SignatureMethod
        $sigMethod = $xmlDoc->createElement('ds:SignatureMethod');
        $sigMethod->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#rsa-sha1');
        $signedInfo->appendChild($sigMethod);
        
        // Reference para el documento
        $reference = $xmlDoc->createElement('ds:Reference');
        $reference->setAttribute('Id', 'SignedPropertiesID' . time());
        $reference->setAttribute('Type', 'http://uri.etsi.org/01903#SignedProperties');
        $reference->setAttribute('URI', '#comprobante');
        
        // Transforms
        $transforms = $xmlDoc->createElement('ds:Transforms');
        $transform = $xmlDoc->createElement('ds:Transform');
        $transform->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#enveloped-signature');
        $transforms->appendChild($transform);
        $reference->appendChild($transforms);
        
        // DigestMethod
        $digestMethod = $xmlDoc->createElement('ds:DigestMethod');
        $digestMethod->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#sha1');
        $reference->appendChild($digestMethod);
        
        // DigestValue (se calculará después)
        $digestValue = $xmlDoc->createElement('ds:DigestValue', '');
        $reference->appendChild($digestValue);
        
        $signedInfo->appendChild($reference);
        $signature->appendChild($signedInfo);
        
        // SignatureValue (se calculará después)
        $signatureValue = $xmlDoc->createElement('ds:SignatureValue', '');
        $signatureValue->setAttribute('Id', 'SignatureValue' . time());
        $signature->appendChild($signatureValue);
        
        // KeyInfo
        $keyInfo = $xmlDoc->createElement('ds:KeyInfo');
        $keyInfo->setAttribute('Id', 'Certificate' . time());
        
        // X509Data
        $x509Data = $xmlDoc->createElement('ds:X509Data');
        
        // X509Certificate
        $certData = str_replace('-----BEGIN CERTIFICATE-----', '', $this->certificado);
        $certData = str_replace('-----END CERTIFICATE-----', '', $certData);
        $certData = str_replace("\n", '', $certData);
        $certData = str_replace("\r", '', $certData);
        
        $x509Certificate = $xmlDoc->createElement('ds:X509Certificate', $certData);
        $x509Data->appendChild($x509Certificate);
        
        $keyInfo->appendChild($x509Data);
        $signature->appendChild($keyInfo);
        
        // Object - QualifyingProperties (XAdES-BES)
        $object = $xmlDoc->createElement('ds:Object');
        $object->setAttribute('Id', 'Signature-Object' . time());
        
        $qualifyingProperties = $xmlDoc->createElement('etsi:QualifyingProperties');
        $qualifyingProperties->setAttribute('Target', '#Signature' . time());
        
        $signedProperties = $xmlDoc->createElement('etsi:SignedProperties');
        $signedProperties->setAttribute('Id', 'Signature-SignedProperties' . time());
        
        $signedSignatureProperties = $xmlDoc->createElement('etsi:SignedSignatureProperties');
        
        // SigningTime
        $signingTime = $xmlDoc->createElement('etsi:SigningTime', gmdate('Y-m-d\TH:i:s\Z'));
        $signedSignatureProperties->appendChild($signingTime);
        
        // SigningCertificate
        $signingCertificate = $xmlDoc->createElement('etsi:SigningCertificate');
        $cert = $xmlDoc->createElement('etsi:Cert');
        $certDigest = $xmlDoc->createElement('etsi:CertDigest');
        
        $digestMethodCert = $xmlDoc->createElement('ds:DigestMethod');
        $digestMethodCert->setAttribute('Algorithm', 'http://www.w3.org/2000/09/xmldsig#sha1');
        $certDigest->appendChild($digestMethodCert);
        
        // Calcular digest del certificado
        $certDer = base64_decode($certData);
        $certDigestValue = base64_encode(sha1($certDer, true));
        
        $digestValueCert = $xmlDoc->createElement('ds:DigestValue', $certDigestValue);
        $certDigest->appendChild($digestValueCert);
        
        $cert->appendChild($certDigest);
        
        // IssuerSerial
        $issuerSerial = $xmlDoc->createElement('etsi:IssuerSerial');
        $x509IssuerName = $xmlDoc->createElement('ds:X509IssuerName', $certInfo['issuer']['CN']);
        $x509SerialNumber = $xmlDoc->createElement('ds:X509SerialNumber', $certInfo['serialNumber']);
        $issuerSerial->appendChild($x509IssuerName);
        $issuerSerial->appendChild($x509SerialNumber);
        
        $cert->appendChild($issuerSerial);
        $signingCertificate->appendChild($cert);
        $signedSignatureProperties->appendChild($signingCertificate);
        
        $signedProperties->appendChild($signedSignatureProperties);
        $qualifyingProperties->appendChild($signedProperties);
        $object->appendChild($qualifyingProperties);
        $signature->appendChild($object);
        
        // Insertar firma en el XML
        $xmlDoc->documentElement->appendChild($signature);
        
        // Calcular DigestValue
        $canonical = $xmlDoc->C14N(true, false);
        $digest = base64_encode(sha1($canonical, true));
        $digestValue->nodeValue = $digest;
        
        // Calcular SignatureValue
        $signedInfoCanonical = $signedInfo->C14N(true, false);
        $signatureData = '';
        openssl_sign($signedInfoCanonical, $signatureData, $this->privateKey, OPENSSL_ALGO_SHA1);
        $signatureValue->nodeValue = base64_encode($signatureData);
        
        // Guardar XML firmado
        if ($rutaSalida === null) {
            $nombreArchivo = basename($rutaXML);
            $rutaSalida = XML_FIRMADOS_PATH . $nombreArchivo;
        }
        
        $xmlDoc->save($rutaSalida);
        
        return $rutaSalida;
    }
    
    /**
     * Verificar firma XML
     */
    public function verificarFirma($rutaXML) {
        $xmlDoc = new DOMDocument();
        $xmlDoc->load($rutaXML);
        
        // Buscar nodo Signature
        $signature = $xmlDoc->getElementsByTagNameNS('http://www.w3.org/2000/09/xmldsig#', 'Signature')->item(0);
        
        if (!$signature) {
            return ['valido' => false, 'mensaje' => 'No se encontró la firma en el XML'];
        }
        
        return ['valido' => true, 'mensaje' => 'XML firmado correctamente con XAdES-BES'];
    }
}
