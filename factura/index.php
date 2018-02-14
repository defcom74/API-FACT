<?php

require __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/include/DbHandler.php';
require_once __DIR__ . '/include/PassHash.php';

require_once __DIR__ . '/include/apiSoriana.php';



$app = new \Slim\App();

// User id from db - Global Variable
$user_id = NULL;

/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields) {
	$error = false;
	$error_fields = "";
	
	$request_params = $_REQUEST;
	// Handling PUT request params
	if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
		$app = \Slim\Slim::getInstance();
		parse_str($app -> request() -> getBody(), $request_params);
	}
	foreach ($required_fields as $field) {
		if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
			$error = true;
			$error_fields .= $field . ', ';
		}
	}

	if ($error) {
		// Required field(s) are missing or empty
		// echo error json and stop the app
		$response = array();
		$app = \Slim\Slim::getInstance();
		$response["error"] = true;
		$response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
		echoRespnse(400, $response);
		$app -> stop();
	}
}

/**
 * Validating email address
 */
function validateEmail($email) {
	$app = \Slim\Slim::getInstance();
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$response["error"] = true;
		$response["message"] = 'Email address is not valid >> ' . $email . ' <<';
		echoRespnse(400, $response);
		$app -> stop();
	}
}

/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoRespnse($status_code, $response, $objApp) {
	//$app = \Slim\App::getInstance();
	// Http response code
	//$app -> status($status_code);
        
        
	// setting response content type to json
	//$app -> contentType('application/json');
        $objApp -> withHeader('Content-type', 'application/json');
        $body = $objApp -> getBody();
	$body -> write( json_encode($response)) ;
}

function echoResponse($sStatusCode, $objResponse, $objText){
    $objResponse -> write(json_encode($objText));
    $newResponse = $objResponse -> withHeader('Content-type', 'application/json'); 
    return $newResponse;
}


$app -> get('/foo', function () {
    echo "Hello aqui";
});

// Asociamos una URL a una función deduciendo el parámetro name
$app -> get('/hello/{name}', function ($request, $response, $args) {
    echo "Hola " . $args['name'];

    
});

$app -> get('/SorianaInfo/{iId}', function ($request, $response, $args) {
    $res = array();
    $db = new DbHandler();
    $result = $db -> getApiSorianaInfo($args['iId']);
    
    $res["error"] = false;
    $res["UserInfo"] = array();
    
    // looping through result and preparing tasks array
	while ($task = $result -> fetch_assoc()) {
		$tmp = array();
		$tmp["iUserId"] = $task["iUserId"];
		$tmp["sUserWebService"] = $task["sUserWebService"];
		$tmp["sPasswordWebService"] = $task["sPasswordWebService"];
		

		array_push($res["UserInfo"], $tmp);
		
	}

	return echoResponse(200, $response, $res);
});

$app -> get('/ComprobarAcceso/{iNumProveedor}/{sClaveServicio}', function($request, $response, $args){
     try {
            $res = array();
            
            $apiWs = new apiSoriana();
            $tmp = $apiWs -> wfComprobarAcceso($args['iNumProveedor'],$args['sClaveServicio']);
            $res["error"] = false;
            $res["ComprobarAcceso"] = $tmp;
            return echoResponse(200, $response, $res);
            
        } catch (Exception $ex) {
            var_dump('Error General -> ' . $ex -> getMessage() . $ex);
        }
  
    

});

$app -> get('/CrearReporte/{iNumProveedor}/{sClaveServicio}/{sFecha}', function($request, $response, $args){
     try {
            $res = array();
            
            $apiWs = new apiSoriana();
            $tmp = $apiWs -> wfCrearReporte($args['iNumProveedor'], $args['sClaveServicio'],  $args['sFecha']);
            $res["error"] = false;
            $res["CrearReporte"] = $tmp;
            return echoResponse(200, $response, $res);
            
        } catch (Exception $ex) {
            var_dump('Error General -> ' . $ex -> getMessage() . $ex);
        }
  
    

});


$app -> get('/ObtenerDocumentos/{iNumProveedor}/{sClaveServicio}/Folio/{iFolio}/{iCantidadPaginas}/{iNumeroPagina}/{iCantidadDocumentos}', function($request, $response, $args){
     try {
            $res = array();
            
            $apiWs = new apiSoriana();
            $tmp = $apiWs -> wfObtenerDocumentos($args['iNumProveedor'], $args['sClaveServicio'],  $args['iNumeroPagina'],  $args['iFolio'],  $args['iCantidadDocumentos'],  $args['iCantidadPaginas']);
            $res["error"] = false;
            $res["ObtenerDocumentos"] = $tmp;
            return echoResponse(200, $response, $res);
            
        } catch (Exception $ex) {
            var_dump('Error General -> ' . $ex -> getMessage() . $ex);
        }
  
});

$app -> get('/getSorianaMultiple/{iNum}', function($request, $response, $args){
    try {
        $res = array();
            
            $apiWs = new DbHandler();
            $result = $apiWs -> getSorianaData();
            $res["error"] = false;
            $res["SorianaDatos"] = array();
            $res["Datos"] = array();
            $res["Fechas"] = array();
            $res["Date-diff"] = array();
            
            // looping through result and preparing tasks array
            while ($task = $result -> fetch_assoc()) {
                    $tmp = array();
                    $tmp["iUserId"] = $task["iUserId"];
                    $tmp["dtFecha"] = $task["dtFecha"];
                    
                    $iUserId = (int) $tmp["iUserId"];
                    $dtFecha = new DateTime ($tmp["dtFecha"]);
                    $dtActual = new DateTime();
                    $dtDiff = $dtActual -> diff($dtFecha);
                    $tmp["Date-Diff"] = (int) $dtDiff ->format('%R%a');
                    $iNum = (int) $args["iNum"];
                    
                    for ($iCont = 0; $iCont < $iNum; $iCont++)
                    {
                    if ($tmp["Date-Diff"] < 0)
                    {
                        $sDate = $dtFecha -> format("Ymd");
                        $resDatos = getDatos($iUserId, $sDate, $dtFecha);
                        if (count($resDatos) > 0) {
                            array_push($res["Datos"], $resDatos);
                            array_push($res["Fechas"], $sDate);
                            array_push($res["Date-diff"], $tmp["Date-Diff"]);
                            $resDb = $apiWs -> masUnDia($iUserId);
                            
                                $tmp["Date-Diff"]++;
                                $dtFecha -> add(new DateInterval("P1D"));
                                sleep(5);
                            
                            
                        }
                        
                    }
                    }
                    
                    

                    array_push($res["SorianaDatos"], $tmp);
                    if ($iNum > 0) {
                        sleep(10);
                    }

            }

            return echoResponse(200, $response, $res);
        
    } catch (Exception $exc) {
        //var_dump('Error General -> ' . $ex -> getMessage() . $ex);
        echo $exc->getTraceAsString();
    }

});

$app -> get('/getSorianaData', function($request, $response, $args){
     try {
            $res = array();
            
            $apiWs = new DbHandler();
            $result = $apiWs -> getSorianaData();
            $res["error"] = false;
            $res["SorianaDatos"] = array();
            $res["Datos"] = array();
            
            // looping through result and preparing tasks array
            while ($task = $result -> fetch_assoc()) {
                    $tmp = array();
                    $tmp["iUserId"] = $task["iUserId"];
                    $tmp["dtFecha"] = $task["dtFecha"];
                    
                    $iUserId = (int) $tmp["iUserId"];
                    $dtFecha = new DateTime ($tmp["dtFecha"]);
                    $dtActual = new DateTime();
                    $dtDiff = $dtActual -> diff($dtFecha);
                    
                    $tmp["Date-Diff"] = (int) $dtDiff ->format('%R%a');
                    if ($tmp["Date-Diff"] < -1)
                    {
                        $sDate = $dtFecha -> format("Ymd");
                        $resDatos = getDatos($iUserId, $sDate, $dtFecha);
                        if (count($resDatos) > 0) {
                            array_push($res["Datos"], $resDatos);
                            $resDb = $apiWs -> masUnDia($iUserId);
                            
                        }
                        
                    }
                    

                    array_push($res["SorianaDatos"], $tmp);
                    sleep(10);

            }

            return echoResponse(200, $response, $res);
            
        } catch (Exception $ex) {
            var_dump('Error General -> ' . $ex -> getMessage() . $ex);
        }
  
});

function getDatos($iId, $sDate, $dtFecha){
      try {
            $res = array();
            $db = new DbHandler();
            $iSorianaId = $iId;
            $result = $db -> getApiSorianaInfo( $iSorianaId );
            while ($task = $result -> fetch_assoc()) {
    		$tmp = array();
    		$tmp["iUserId"] = $task["iUserId"];
    		$tmp["sUserWebService"] = $task["sUserWebService"];
    		$tmp["sPasswordWebService"] = $task["sPasswordWebService"];
				
            }
            if ($tmp != NULL)
            {
                $iUserId = $tmp["iUserId"];
                $sUserWS = $tmp["sUserWebService"];
                $sPassWS = $tmp["sPasswordWebService"];
                //comprobar clave
                $apiSor = new apiSoriana();
                if ($apiSor -> wfComprobarAcceso($sUserWS, $sPassWS)) {
                    //var_dump("Acceso permitido");
                    
                    $xmlReporte = $apiSor -> wfCrearReporte($sUserWS, $sPassWS, $sDate );
                    
                    $res["CrearReporte"] = $xmlReporte;
                    
                    $res["Folio"] = array();
                    
                    $iFolio = (int) $xmlReporte -> Folio;
                    $iCantidadDocumentos = (int) $xmlReporte -> CantidadDocumentos;
                    $iCantidadPaginas = (int) $xmlReporte -> CantidadPaginas;
                    $res["iCantidadDocumentos"] = $iCantidadDocumentos;
                    $res["iCantidadPaginas"] = $iCantidadPaginas;
                    $resDb = $db -> createReporte($iFolio, $iCantidadDocumentos, $iCantidadPaginas, $iSorianaId, $dtFecha); 
                    if( $resDb > 0)
                    {
                        $iNumPagina = 1;
                        $xmlDocumentos = $apiSor -> wfObtenerDocumentos($sUserWS, $sPassWS, $iNumPagina, $iFolio, $iCantidadDocumentos, $iCantidadPaginas);
                        $res["Documentos"] = $xmlDocumentos  ;
                        if ($iCantidadDocumentos > 0) 
                            {
                                     //$res["Articulo"] = $aArticulos;
                                    
                                    $iNumDoc = count( $xmlDocumentos -> Acuse);
                                    $res["iAcuse"] = $iNumDoc;
                                    
                                    
                                    for ($iDoc = 0 ; $iDoc < $iNumDoc; $iDoc++)
                                    {
                                        if ($iNumDoc > 1) {
                                            $aDoc = $xmlDocumentos -> Acuse[$iDoc];
                                        }
                                        else
                                        {
                                            $aDoc = $xmlDocumentos -> Acuse;
                                        }
                                        
                                        //
                                        $iProveedor = $aDoc -> Proveedor;
                                        $iSucursal = $aDoc -> Sucursal;
                                        $iFolioFolio = $aDoc -> Folio;
                                        $sFactura = $aDoc-> Factura;
                                        $sFechaFactura = $aDoc-> FechaFactura;
                                        $bCumpleRequisitosFiscales = ($aDoc-> CumpleRequisitosFiscales === 'true');
                                        $dSubtotalFactura = $aDoc-> SubtotalFactura;
                                        $dTotalIvaFactura = $aDoc-> TotalIvaFactura;
                                        $dTotalOtrosImpuestosFactura = $aDoc-> TotalOtrosImpuestosFactura;
                                        $dTotalFactura = $aDoc-> TotalFactura;
                                        array_push ($res["Folio"] , $iFolioFolio);
                                        
                                        $aArticulos = array();
                                        $aArticulos = $aDoc-> Articulos;
                                        
                                        
                                        $iDocId = $db -> crearDoc($iSorianaId, $iSucursal, $iFolioFolio, $sFactura, $sFechaFactura, $bCumpleRequisitosFiscales, $dSubtotalFactura, $dTotalFactura, $dTotalIvaFactura, $dTotalOtrosImpuestosFactura, $resDb);
                                        if ($iDocId > 0)
                                        {
                                            
                                            $iNumArr = count( $aArticulos);
                                            for($iNum = 0 ; $iNum < $iNumArr; $iNum++)
                                            {
                                                if ($iNumArr > 1) {
                                                    $aArt = $aArticulos -> Articulo[$iNumArr];
                                                }
                                                else
                                                {
                                                    $aArt = $aArticulos -> Articulo;
                                                }
                                                //$aArt = array();
                                                
                                                $res["Articulo"] = $aArt;
                                                $sCodigo = sprintf("%d", $aArt -> Codigo);
                                                $sDescripcion = $aArt -> Descripcion;
                                                $iCantidadRecibida = $aArt ->  CantidadRecibida;
                                                $iCapacidadEmpaque = $aArt -> CapacidadEmpaque;
                                                $iPedido = $aArt -> Pedido;
                                                $sEstatus = $aArt -> Estatus;
                                                $iArticuloId = $db ->crearArticulo($iDocId, $sCodigo, $sDescripcion, $iCantidadRecibida, $iCapacidadEmpaque, $iPedido, $sEstatus);
                                                

                                            }
                                            
                                        }
                                        else
                                        {
                                            $res["error"] = true;
                                            $res["info"] = array();
                                            $tmp = array();
                                            $tmp["sMsg"] = "Document with folio " . $iFolioFolio . " error ";
                                            $res["info"] = $tmp;
                                           return $res; 
                                        }
                                          
                                        
                                        
                                    }
                              
                            //return echoResponse(200, $response, $res);
                    
                    
                        return $res;
                        //var_dump("Acceso permitido " . $iFolio);
                        }
                        else {
                            $res["error"] = true;
                            $res["info"] = array();
                            $tmp = array();
                            $tmp["sMsg"] = "Cantidad de Documentos  " . $iCantidadDocumentos;
                            $res["info"] = $tmp;
                            return $res  ;
                        }
                    }
                    elseif( $resDb == ALREADY_EXISTED)
                    {
                            $res["error"] = true;
                            $res["info"] = array();
                            $tmp = array();
                            $tmp["sMsg"] = "Folio " . $iFolio . " ya existe";
                            $res["info"] = $tmp;
                            return  $res  ;
                    }
                    else 
                        {
                            $res["error"] = true;
                            $res["info"] = array();
                            $tmp = array();
                            $tmp["sMsg"] = "Error Create error folio " . $iFolio;
                            $res["info"] = $tmp;
                            return $res  ;
                        }
                    
                    
                    
                }
            }
            else {
                $res["error"] = true;
                $res["info"] = array();
                $tmp = array();
		        $tmp["sMsg"] = "Soriana Id dont exist " . $args['iId'];
                $res["info"] = $tmp;
                return $res  ;

           }
        
            
            
        } catch (Exception $ex) {
            var_dump('Error General -> ' . $ex -> getMessage() . $ex);
        }
}
        



$app -> get('/SorianaByDate/{iId}/Date/{sDate}', function($request, $response, $args){
   try{
       
       $iSorianaId = $args['iId'];
       $sDate = $args['sDate'];
       $res = getDatos($iSorianaId, $sDate);
       return echoResponse(200, $response, $res);
       
       
   } catch (Exception $ex) {
       var_dump('Error GeneralSorianaByDate -> ' . $ex -> getMessage() . $ex);
   }
  
    

});


//****************************web page api

$app -> get('/Web/UserById/{iId}', function($request, $response, $arg){
    try{
        $iId = (int) $request->getAttribute('iId');
        $res = array();
        $db = new DbHandler();

        $res["error"] = false;
        $res["UserById"] = array();
       

        $result = $db -> getUserById($iId);


         // looping through result and preparing tasks array
            while ($task = $result -> fetch_assoc()) {
                    $tmp = array();
                    $tmp = $task;
                   

                    array_push($res["UserById"], $tmp);
                    //sleep(10);

            }
        return echoResponse(200, $response, $res);

    } catch (Exception $ex) {
       var_dump('Error Web UserbyId -> ' . $ex -> getMessage() . $ex);
   }
});



$app -> get('/Web/SorianaDataByUserId/{iId}', function($request, $response, $arg){
    try{
        $iId = (int) $request->getAttribute('iId');
        $res = array();
        $db = new DbHandler();

        $res["error"] = false;
        $res["UserById"] = array();
       

        $result = $db -> getSorianaDataByUserId($iId);


         // looping through result and preparing tasks array
            while ($task = $result -> fetch_assoc()) {
                    $tmp = array();
                    $tmp = $task;
                   

                    array_push($res["UserById"], $tmp);
                    //sleep(10);

            }
        return echoResponse(200, $response, $res);

    } catch (Exception $ex) {
       var_dump('Error Web UserbyId -> ' . $ex -> getMessage() . $ex);
   }
});

$app -> get('/Web/ReportsBySorinanaId/{iId}', function($request, $response, $arg){
    try{
        $iId =  (int) $request->getAttribute('iId');
        $res = array();
        $db = new DbHandler();
        $res["error"] = false;
        $res["Report"] = array();

        $result = $db -> getSorianaReports(20, $iId);

        // looping through result and preparing tasks array
            while ($task = $result -> fetch_assoc()) {
                    $tmp = array();
                    $tmp = $task;
                   
                    array_push($res["Report"], $tmp);
                    //sleep(10);

            }
        return echoResponse(200, $response, $res);

    } catch (Exception $ex) {
       var_dump('Error Web UserbyId -> ' . $ex -> getMessage() . $ex);
   }
});


$app -> get('/Web/DocumentsByReportId/{iId}/DocId/{iDocId}/SorianaId/{iSorId}', function($request, $response, $arg){
    try{
        $iReporteId =  (int) $request->getAttribute('iId');
        $iSorianaId =  (int) $request->getAttribute('iSorId');
        $iDocId =  (int) $request->getAttribute('iDocId');
        $res = array();
        $db = new DbHandler();
        $res["error"] = false;
        $res["Docs"] = array();

        $result = $db -> getSorianaDocs($iReporteId, $iDocId, $iSorianaId);

        // looping through result and preparing tasks array
            while ($task = $result -> fetch_assoc()) {
                    $tmp = array();
                    $tmp = $task;
                   
                    array_push($res["Docs"], $tmp);
                    //sleep(10);

            }
        return echoResponse(200, $response, $res);

    } catch (Exception $ex) {
       var_dump('Error Web UserbyId -> ' . $ex -> getMessage() . $ex);
   }
});


$app -> get('/Web/ArticulosByDocumentId/{iId}', function($request, $response, $arg){
    try{
        $iDocumentId =  (int) $request->getAttribute('iId');
       
        $res = array();
        $db = new DbHandler();
        $res["error"] = false;
        $res["Articulos"] = array();

        $result = $db -> getSorianaArticulos($iDocumentId);

        // looping through result and preparing tasks array
            while ($task = $result -> fetch_assoc()) {
                    $tmp = array();
                    $tmp = $task;
                   
                    array_push($res["Articulos"], $tmp);
                    //sleep(10);

            }
        return echoResponse(200, $response, $res);

    } catch (Exception $ex) {
       var_dump('Error Web UserbyId -> ' . $ex -> getMessage() . $ex);
   }
});


$app -> get('/Web/UserByLogin/{sLogin}', function($request, $response, $arg){
    try{
        $sLogin =  $request->getAttribute('sLogin');
        $res = array();
        $db = new DbHandler();
        $res["error"] = false;
        $res["Users"] = array();

        $result = $db -> getUserByUser($sLogin);
        // looping through result and preparing tasks array
            while ($task = $result -> fetch_assoc()) {
                    $tmp = array();
                    $tmp = $task;
                   
                    array_push($res["Users"], $tmp);
                    //sleep(10);

            }
        return echoResponse(200, $response, $res);

    } catch (Exception $ex) {
       var_dump('Error Web UserbyId -> ' . $ex -> getMessage() . $ex);
   }
});


$app -> run();

?>