<?php

class apiSoriana {
    public static $wsAcuseURL = 'http://www2.soriana.com/Proveedor/acuse_V02/Servicio.asmx?wsdl';
    
    public static function wfComprobarAcceso($iNumProveedor, $sClaveServicio){
        try {
            $wsAcuseURL = 'http://www2.soriana.com/Proveedor/acuse_V02/Servicio.asmx?wsdl';
            $parametros = array("NumeroProveedor" => $iNumProveedor, "ClaveServicio" => $sClaveServicio );
            $soap = new SoapClient($wsAcuseURL, array('features' => SOAP_WAIT_ONE_WAY_CALLS, 'trace' => 1));
            //$fcs = $soap ->__getFunctions();
            //var_dump($fcs);
            $res = $soap -> ComprobarAcceso($parametros);
            if (is_soap_fault( $res )){
                var_dump("SOAP fault: faultcode 3: {$res->faulcode}, faultstring: {$res->faultstring}", E_USER_ERROR);
            }
            //$sXML = new SimpleXMLElement($res);
            $sXML = $res -> ComprobarAccesoResult;
            $tmp = array();
            
            $tmp["res"] = $soap->__getLastResponse();
	    $tmp["request"] = $soap->__getLastRequest();
            $tmp["reqH"] = $soap->__getLastRequestHeaders();
            $tmp["sXML"] = $sXML;
            
            return $sXML;
            
        } catch (Exception $ex) {
            var_dump('Error General -> ' . $ex -> getMessage() . $ex);
        }
    }
    
      public static function wfCrearReporte($iNumProveedor, $sClaveServicio, $sFecha ){
        try {
            $wsAcuseURL = 'http://www2.soriana.com/Proveedor/acuse_V02/Servicio.asmx?wsdl';
            $parametros = array("NumeroProveedor" => $iNumProveedor, "ClaveServicio" => $sClaveServicio, 'Fecha' => $sFecha );
            $soap = new SoapClient($wsAcuseURL, array('features' => SOAP_WAIT_ONE_WAY_CALLS, 'trace' => 1));
            //$fcs = $soap ->__getFunctions();
            //var_dump($fcs);
            $res = $soap -> CrearReporte($parametros);
            if (is_soap_fault( $res )){
                var_dump("SOAP fault: faultcode 3: {$res->faulcode}, faultstring: {$res->faultstring}", E_USER_ERROR);
            }
            //$sXML = new SimpleXMLElement($res);
            $sXML = $res -> CrearReporteResult;
            $tmp = array();
            
            $tmp["res"] = $soap->__getLastResponse();
	    $tmp["request"] = $soap->__getLastRequest();
            $tmp["reqH"] = $soap->__getLastRequestHeaders();
            $tmp["sXML"] = $sXML;
            
            return $sXML;
            
        } catch (Exception $ex) {
            var_dump('Error General -> ' . $ex -> getMessage() . $ex);
        }
    }
    
    
      public static function wfObtenerDocumentos($iNumProveedor, $sClaveServicio, $iNumeroPagina, $iFolio, $iCantidadDocumentos, $iCantidadPaginas ){
        try {
            $wsAcuseURL = 'http://www2.soriana.com/Proveedor/acuse_V02/Servicio.asmx?wsdl';
            $subParametros = array("Folio" => $iFolio, "NumeroProveedor" => $iNumProveedor, 'CantidadDocumentos' => $iCantidadDocumentos, 'CantidadPaginas' => $iCantidadPaginas);
            $parametros = array("InfoReporte" => $subParametros,  "ClaveServicio" => $sClaveServicio, 'NumeroPagina' => $iNumeroPagina );
            $soap = new SoapClient($wsAcuseURL, array('features' => SOAP_WAIT_ONE_WAY_CALLS, 'trace' => 1));
            //$fcs = $soap ->__getFunctions();
            //var_dump($fcs);
            $res = $soap -> ObtenerDocumentos($parametros);
            if (is_soap_fault( $res )){
                var_dump("SOAP fault: faultcode 3: {$res->faulcode}, faultstring: {$res->faultstring}", E_USER_ERROR);
            }
            //$sXML = new SimpleXMLElement($res);
            $sXML = $res -> ObtenerDocumentosResult;
            $tmp = array();
            
            $tmp["res"] = $soap->__getLastResponse();
	    $tmp["request"] = $soap->__getLastRequest();
            $tmp["reqH"] = $soap->__getLastRequestHeaders();
            $tmp["sXML"] = $sXML;
            
            return $sXML;
            
        } catch (Exception $ex) {
            var_dump('Error General -> ' . $ex -> getMessage() . $ex);
        }
    }
    
    
    
}

