<?php


class DbHandler {
	private $conn;
	function __construct() {
		require_once dirname(__FILE__).'/DbConnect.php';
// opening db connection
		$db=new DbConnect();
		$this->conn=$db->connect();
	}
	/* ------------- `users` table method ------------------ */
/**
 * Creating new user
 * @param String $name User full name
 * @param String $email User login email id
 * @param String $password User login password
 */
public function createUser($name,$email,$password) {
	require_once 'PassHash.php';
	$response=array();
// First check if user already existed in db
	if(!$this->isUserExists($email)) {
// Generating password hash
		$password_hash=PassHash::hash($password);
// Generating API key
		$api_key=$this->generateApiKey();
// insert query
		$stmt=$this->conn->prepare("INSERT INTO users(name, email, password_hash, api_key, status) values(?, ?, ?, ?, 1)");
		$stmt->bind_param("ssss",$name,$email,$password_hash,$api_key);
		$result=$stmt->execute();
		$stmt->close();
// Check for successful insertion
		if($result) {
// User successfully inserted
			return USER_CREATED_SUCCESSFULLY;
		} else {
// Failed to create user
			return USER_CREATE_FAILED;
		}
	} else {
// User with same email already existed in the db
		return USER_ALREADY_EXISTED;
	}
	return $response;
}
/**
 * Creating new user
 * @param String $sSiteName Site name
 * @param Integer $iUserId User id
 * @param String $sAddress Site Address
 * @param Long $fLog Site Longitude
 * @param Long $fLat Site Latitude
 */
public function createSiteDef($sSiteName,$iUserId,$sAddress,$fLog,$fLat) {
// require_once 'PassHash.php';
	$response=array();
// First check if site already existed in db
	if(!$this->isSiteExists($sSiteName)) {
// Generating password hash
// insert query
		$stmt=$this->conn->prepare("INSERT INTO tblSitesDef(sSiteName, iUserId, sAddress, fLongitude, fLatitude, bActive ) values(?, ?, ?, ?, ?, 1)");
		$stmt->bind_param("sisdd",$sSiteName,$iUserId,$sAddress,$fLog,$fLat);
		$result=$stmt->execute();
		$stmt->close();
// Check for successful insertion
		if($result) {
// User successfully inserted
			return SITEDEF_CREATED_SUCCESSFULLY;
		} else {
// Failed to create user
			return SITEDEF_CREATE_FAILED;
		}
	} else {
// User with same email already existed in the db
		return SITEDEF_ALREADY_EXISTED;
	}
	return $response;
}
/*
 * Create a New server path for the main funtion aka FlowControl2.0 or another
 * @param Integer $iSiteId Site name
 * @param Integer $iUserId User id
 * @param String $sPath Server Path to app
 */
public function createServiceYield($iSiteId,$iUserId, $iServerIpId, $sPath) {
	$response=array();
	if($this->isSiteExistsById($iSiteId)) {
		
		if($this->isServerIPExistsById($iServerIpId)) {
			
		
	//insert query
		$stmt=$this->conn->prepare("INSERT INTO tblServiceYield(iSiteId, iUserId, sPath, iServerId, bActive) values(?, ?, ?, ?, 1)");
		if($stmt!==FALSE) {
			$stmt->bind_param("iisi",$iSiteId,$iUserId,$sPath, $iServerIpId);
			$result=$stmt->execute();
			$stmt->close();
			if($result) {
			// User successfully inserted
				return CREATED_SUCCESSFULLY;
			} else {
			// Failed to create user
				error_log("Error DB ".$stmt->error,0);
				return CREATE_FAILED;
			}
		} else {
			return CREATE_FAILED_SQL;
		}
		} else {
	//
			return DONT_EXITS;
		}
	} else {
		return DONT_EXITS;
	}
}
/**
 * 
 * @param type $iSorianaId
 * @param type $iScursal
 * @param type $iFolio
 * @param type $sFactura
 * @param type $dtFecha
 * @param type $bReqFiscales
 * @param type $dSubTotal
 * @param type $dTotal
 * @param type $dTotalIva
 * @param type $dOtrosImpuestos
 * @return array
 */
public function crearDoc($iSorianaId, $iScursal, $iFolio, $sFactura, $dtFecha, $bReqFiscales, $dSubTotal,
        $dTotal, $dTotalIva, $dOtrosImpuestos, $iReporteId)
    {
    
    
    try
    {
            $query = "INSERT INTO tblSorianaDocumentos (iSorianaId, iScursal, iFolio, sFactura, dtFecha, bReqFiscales,
                    dSubTotal, dTotal, dTotalIva, dOtrosImpuestos, iReporteId ) 
                    VALUES ('{$iSorianaId}', '{$iScursal}', '{$iFolio}', '{$sFactura}', '{$dtFecha}', '{$bReqFiscales}', "
                    . "'{$dSubTotal}', '{$dTotal}', '{$dTotalIva}', '{$dOtrosImpuestos}', '{$iReporteId}')";

            $stmt = $this -> conn -> query($query) or die("Error execution the Query " . $this -> conn -> error);
            $newRow = $this -> conn -> insert_id;
            //$stmt->close();
            return $newRow;
    }
        catch (Exception $e){
        throw  $e;
    }
        catch (mysqli_sql_exception $e){
        throw $e;
    }
   
    
}


public function crearArticulo($iDocumentoId, $sCodigo, $sDescripcion, $iCantidad, $iCantidadEmpaque, $iPedido, $sEstatus)
    {
    
    
    try
    {
        $query = "INSERT INTO tblSorianaArticulos (iDocumentoId, sCodigo, sDescripcion, iCantidad, iCantidadEmpaque, iPedido,
                sEstatus ) 
                VALUES ('{$iDocumentoId}', '{$sCodigo}', '{$sDescripcion}', '{$iCantidad}', '{$iCantidadEmpaque}', '{$iPedido}', "
                . "'{$sEstatus}')";

        $stmt = $this -> conn -> query($query) or die("Error execution the Query " . $this->conn->error);
        $newRow = $this -> conn -> insert_id;
        //$stmt->close();
        return $newRow;
    }
        catch (Exception $e){
        throw  $e;
    }
        catch (mysqli_sql_exception $e){
        throw $e;
    }
   
    
}
/**
 * Create new Report
 * @param type $iFolio
 * @param type $iDocumentos
 * @param type $iPaginas
 * @param type $iSorianaId
 */
public function createReporte($iFolio, $iDocumentos, $iPaginas, $iSorianaId, $dtFecha)
    {
        try
        {
            $response=array();
    if (! $this -> isFolioExist($iFolio) )
        {
        // insert query
            $stmt = $this -> conn-> prepare("INSERT INTO tblSorianaReporte (iFolio, iDocumentos, iPaginas, iSorianaId, dtFecha ) values(?, ?, ?, ?, ? )");
                    if($stmt !== FALSE) 
                        {
                            $dtFechaC = $dtFecha -> format("Y-m-d H:i:s");
                            $stmt->bind_param("iiiis", $iFolio, $iDocumentos, $iPaginas, $iSorianaId, $dtFechaC );
                            $result = $stmt -> execute();
                            $newRow = $this -> conn -> insert_id;
                            $stmt->close();
                            // Check for successful insertion
                            if($result) 
                                {
                                // Folio successfully inserted
                                    return $newRow;
                                } 
                                else 
                                {
                                    // Failed to create Folio
                                    //error_log("Error DB ". $stmt -> error,0);
                                    //return CREATE_FAILED;
                                    throw  $stmt -> error;
                                }

                        } 
                          else 
                           {
                              return CREATE_FAILED_SQL;
                           }
            } 
            else 
            {
              return ALREADY_EXISTED;
            }

            return $response;
        } catch (Exception $ex) {
            throw  $ex;
        }
    
}

public function masUnDia($iUserId)
    {
    try
    {
        $response=array();
   
        // insert query
            $stmt = $this -> conn-> prepare("UPDATE tblDataSoriana SET dtFecha = ADDDATE(dtFecha, 1) WHERE iUserId = ?");
		if($stmt !== FALSE) 
                    {
			$stmt-> bind_param("i", $iUserId);
                        $result = $stmt-> execute();
                        $stmt-> store_result();
                        //$num_rows = $stmt -> num_rows;
                        $stmt-> close();
                        if($result) 
                                {
                                // Folio successfully inserted
                                    return CREATED_SUCCESSFULLY;
                                } 
                                else 
                                {
                                    // Failed to create Folio
                                    //error_log("Error DB ". $stmt -> error,0);
                                    //return CREATE_FAILED;
                                    throw  $stmt -> error;
                                }
                        
                              
                    } 
                      else 
                       {
                        throw new ErrorException("smmt error");
                       }
        

	return CREATE_FAILED;
        
    } catch (Exception $ex) {
        throw $ex;
    }
    
}

/**
 * 
 * @param type $iDocId
 * @param type $sCodigo
 * @param type $sDes
 * @param type $iCantidad
 * @param type $iCanEmpaque
 * @param type $iPedido
 * @param type $sEstatus
 * @return array
 */
public function createArticulo($iDocId, $sCodigo, $sDes, $iCantidad, $iCanEmpaque, $iPedido, $sEstatus)
    {
    $response=array();
    if (! $this->isArticuloExist($iDocId, $sCodigo) )
        {
        // insert query
            $stmt = $this -> conn-> prepare("INSERT INTO tblSorianaArticulos (iDocumentoId, sCodigo, sDescripcion, iCantidad, iCantidadEmpaque, iPedido, sEstatus ) values(?, ?, ?, ?, ?, ?, ?)");
		if($stmt !== FALSE) 
                    {
			$stmt->bind_param("issiiis", $iDocId, $sCodigo, $sDes, $iCantidad, $iCanEmpaque, $iPedido, $sEstatus );
			$result = $stmt -> execute();
			$stmt->close();
                        // Check for successful insertion
			if($result) 
                            {
                            // Folio successfully inserted
				return CREATED_SUCCESSFULLY;
                            } 
                            else 
                            {
                                // Failed to create Folio
                                error_log("Error DB ". $stmt -> error,0);
                                return CREATE_FAILED;
                            }
                              
                    } 
                      else 
                       {
                          return CREATE_FAILED_SQL;
                       }
        } 
        else 
        {
          return ALREADY_EXISTED;
        }

	return $response;
}


/**
 * Checking user login
 * @param String $email User login email id
 * @param String $password User login password
 * @return boolean User login status success/fail
 */
public function checkLogin($email,$password) {
// fetching user by email
	$stmt=$this->conn->prepare("SELECT password_hash FROM users WHERE email = ?");
	$stmt->bind_param("s",$email);
	$stmt->execute();
	$stmt->bind_result($password_hash);
	$stmt->store_result();
	if($stmt->num_rows>0) {
// Found user with the email
// Now verify the password
		$stmt->fetch();
		$stmt->close();
		if(PassHash::check_password($password_hash,$password)) {
// User password is correct
			return TRUE;
		} else {
// user password is incorrect
			return FALSE;
		}
	} else {
		$stmt->close();
// user not existed with the email
		return FALSE;
	}
}
/**
 * Checking for duplicate user by email address
 * @param String $email email to check in db
 * @return boolean
 */
private function isUserExists($email) {
	$stmt=$this->conn->prepare("SELECT id from users WHERE email = ?");
	$stmt->bind_param("s",$email);
	$stmt->execute();
	$stmt->store_result();
	$num_rows=$stmt->num_rows;
	$stmt->close();
	return $num_rows>0;
}
/**
 * Checking for duplicate site by site name
 * @param String $sSiteName Site name to check in db
 * @return boolean
 */
private function isSiteExists($sSiteName) {
	$stmt=$this->conn->prepare("SELECT iId from tblSitesDef WHERE sSiteName = ?");
	$stmt->bind_param("s",$sSiteName);
	$stmt->execute();
	$stmt->store_result();
	$num_rows=$stmt->num_rows;
	$stmt->close();
	return $num_rows>0;
}
/**
 * Validating if iFolio Already Exist
 * @param type $iFolio
 * @return type boolean
 */
private function  isFolioExist($iFolio){
    try {
        $stmt=$this->conn->prepare("SELECT iFolio FROM tblSorianaReporte WHERE iFolio = ? AND bActive = 1");
	$stmt->bind_param("i",$iFolio);
	$stmt->execute();
	$stmt->store_result();
	$num_rows=$stmt-> num_rows;
	$stmt->close();
	return $num_rows > 0;
        
    } catch (Exception $exc) {
        throw $exc;
    }

    
}
/**
 * Validating if Document exist
 * @param type $iFolio
 * @return type
 */
private function  isDocumentExist($iFolio){
    $stmt=$this->conn->prepare("SELECT iFolio FROM tblSorianaDocumentos WHERE iFolio = ? AND bActive = 1");
	$stmt->bind_param("i",$iFolio);
	$stmt->execute();
	$stmt->store_result();
	$num_rows=$stmt-> num_rows;
	$stmt->close();
	return $num_rows > 0;
}
/**
 * Validating if Articulo exist 
 * @param type $iDocId
 * @param type $sCodigo
 * @return type
 */
private function  isArticuloExist($iDocId, $sCodigo){
    $stmt=$this->conn->prepare("SELECT iDocumentoId, sCodigo  FROM tblSorianaArticulos WHERE iDocumentoId = ? AND sCodigo = ? AND bActive = 1");
	$stmt->bind_param("is", $iDocId, $sCodigo);
	$stmt->execute();
	$stmt->store_result();
	$num_rows=$stmt-> num_rows;
	$stmt->close();
	return $num_rows > 0;
}

/**
 * Validating Site Id
 * if the iSiteId exits we can use this in the other tables
 * @param Integer $iSiteId
 * @return boolean
 */
public function isSiteExistsById($iSiteId) {
	$stmt=$this->conn->prepare("SELECT iFolio FROM tblSorianaReporte WHERE iFolio = ?");
	$stmt->bind_param("i",$iSiteId);
	$stmt->execute();
	$stmt->store_result();
	$num_rows=$stmt->num_rows;
	$stmt->close();
	return $num_rows>0;
}
/**
 * Validating Server Id
 * if the $iServerId exits we can use this in the other tables
 * @param Integer $iServerId
 * @return boolean
 */
public function isServerIPExistsById($iServerId){
	$stmt=$this->conn->prepare("SELECT iId FROM tblServerIP WHERE iId = ?");
	$stmt->bind_param("i",$iServerId);
	$stmt->execute();
	$stmt->store_result();
	$num_rows=$stmt->num_rows;
	$stmt->close();
	return $num_rows>0;
}
/**
 * Fetching user by email
 * @param String $email User email id
 */
public function getUserByEmail($email) {
	$stmt=$this->conn->prepare("SELECT name, email, api_key, status, created_at FROM users WHERE email = ?");
	$stmt->bind_param("s",$email);
	if($stmt->execute()) {
		$user=$stmt->get_result()->fetch_assoc();
		$stmt->close();
		return $user;
	} else {
		return NULL;
	}
}

/**
 * Fetching user by User Id
 * @param Int $iId User  id
 */
public function getUserById($iId) {
	$stmt=$this->conn->prepare("SELECT * FROM tblUsers WHERE iId = ?");
	$stmt->bind_param("i",$iId);
	if($stmt->execute()) {
		$user=$stmt->get_result()->fetch_assoc();
		$stmt->close();
		return $user;
	} else {
		return NULL;
	}
}

/**
 * Fetching user by User User Login
 * @param String $sLogin User Login
 */
public function getUserByUser($sLogin) {
	$stmt=$this->conn->prepare("SELECT * FROM tblUsers WHERE sUser = ?");
	$stmt->bind_param("s",$sLogin);
	if($stmt->execute()) {
		$user=$stmt->get_result()->fetch_assoc();
		$stmt->close();
		return $user;
	} else {
		return NULL;
	}
}


/**
 * Fetching Last N Reports that are active
 * @param Int $iNum Number of reports to return order by last first
 */
public function getReports($iNum, $iSorianaId) {
	$stmt=$this->conn->prepare("SELECT * FROM `tblSorianaReporte` WHERE iDocumentos > 0 AND bActive = 1 AND iSorianaId = ? ORDER BY dtCreateDate DESC LIMIT  ?");
	$stmt->bind_param("ii",$iSorianaId, $iNum);
	if($stmt->execute()) {
		$user=$stmt->get_result()->fetch_assoc();
		$stmt->close();
		return $user;
	} else {
		return NULL;
	}
}


/**
 * Fetching Soriana Info api key
 * @param Int $user_id user id primary key in user table
 */
public function getApiSorianaInfo($user_id) {
	$stmt=$this-> conn-> prepare("SELECT iUserId, sUserWebService, sPasswordWebService FROM tblSorianaData WHERE iUserId = ? AND bActive = 1");
	$stmt->bind_param("i",$user_id);
	if($stmt->execute()) {
		$api_key = $stmt -> get_result();
		$stmt->close();
		return $api_key;
	} 
        else {
		return NULL;
	}
}
/**
 * Fetching user id by api key
 * @param String $api_key user api key
 */
public function getUserId($api_key) {
	$stmt=$this->conn->prepare("SELECT id FROM users WHERE api_key = ?");
	$stmt->bind_param("s",$api_key);
	if($stmt->execute()) {
		$user_id=$stmt->get_result()->fetch_assoc();
		$stmt->close();
		return $user_id;
	} else {
		return NULL;
	}
}

/**
 * Get the Id for the document with folio
 * @param type $iFolio
 * @return type id
 */
public function getDocumentId($iFolio) {
	$stmt=$this->conn->prepare("SELECT iId FROM tblSorianaDocumentos WHERE $iFolio = ? AND bActive = 1");
	$stmt->bind_param("i",$iFolio);
	if($stmt->execute()) {
		$iId = $stmt -> get_result() -> fetch_assoc();
		$stmt -> close();
		return $iId;
                } else {
                        return NULL;
                }
}

/**
 * 
 * @return type DataSet
 */
public function getSorianaData() {
	$stmt = $this -> conn -> prepare("SELECT iUserId, dtFecha FROM tblDataSoriana WHERE bActive = 1");
	//$stmt->bind_param("i",$iFolio);
	if($stmt->execute()) {
		$aData = $stmt -> get_result() ;
		$stmt -> close();
		return $aData;
                } else {
                        return NULL;
                }
}
/**
 * Validating user api key
 * If the api key is there in db, it is a valid key
 * @param String $api_key user api key
 * @return boolean
 */
public function isValidApiKey($api_key) {
	$stmt=$this->conn->prepare("SELECT id from users WHERE api_key = ?");
	$stmt->bind_param("s",$api_key);
	$stmt->execute();
	$stmt->store_result();
	$num_rows=$stmt->num_rows;
	$stmt->close();
	return $num_rows>0;
}
/**
 * Generating random Unique MD5 String for user Api key
 */
private function generateApiKey() {
	return md5(uniqid(rand(),true));
}
/* ------------- `tasks` table method ------------------ */
/**
 * Creating new task
 * @param String $user_id user id to whom task belongs to
 * @param String $task task text
 */
public function createTask($user_id,$task) {
	$stmt=$this->conn->prepare("INSERT INTO tasks(task) VALUES(?)");
	$stmt->bind_param("s",$task);
	$result=$stmt->execute();
	$stmt->close();
	if($result) {
// task row created
// now assign the task to user
		$new_task_id=$this->conn->insert_id;
		$res=$this->createUserTask($user_id,$new_task_id);
		if($res) {
// task created successfully
			return $new_task_id;
		} else {
// task failed to create
			return NULL;
		}
	} else {
// task failed to create
		return NULL;
	}
}
/**
 * Fetching single task
 * @param String $task_id id of the task
 */
public function getTask($task_id,$user_id) {
	$stmt=$this->conn->prepare("SELECT t.id, t.task, t.status, t.created_at from tasks t, user_tasks ut WHERE t.id = ? AND ut.task_id = t.id AND ut.user_id = ?");
	$stmt->bind_param("ii",$task_id,$user_id);
	if($stmt->execute()) {
		$task=$stmt->get_result()->fetch_assoc();
		$stmt->close();
		return $task;
	} else {
		return NULL;
	}
}
/**
 * Fetching all the Sites
 */
public function getAllSiteDef() {
	$stmt=$this->conn->prepare("SELECT tSites.* FROM tblSitesDef tSites WHERE tSites.bActive = 1");
	$stmt->execute();
	$tasks=$stmt->get_result();
	$stmt->close();
	return $tasks;
}
/**
 * Fetching all the IPs
 */
public function getAllSitesIP() {
	$stmt=$this->conn->prepare("SELECT tIPs.* FROM tblServerIP tIPs WHERE tIPs.bActive = 1");
	$stmt->execute();
	$tasks=$stmt->get_result();
	$stmt->close();
	return $tasks;
}

 /***
 * Fetching the yields details for a particular server/Date
 * @param Int iServer id of the server
 * @param Int dtDate of the data
 */
 public function getYieldIdByServerDate($iServerId, $dtDate){
 	$stmt=$this->conn->prepare("SELECT * FROM `tblYieldsDet` WHERE iIdYieldO = (SELECT iId FROM `tblYieldOverall` WHERE dEntryDate = FROM_UNIXTIME( ? ) AND iServerId = ? )");
 	$dtDate = $dtDate/1000;
	$stmt->bind_param("ii", $dtDate, $iServerId);
	$stmt->execute();
	$tasks=$stmt->get_result();
	$stmt->close();
	return $tasks;
 } 
 
 
/**
 * Deleting a task
 * @param String $task_id id of the task to delete
 */
	public function deleteTask($user_id,$task_id) {
		$stmt=$this->conn->prepare("DELETE t FROM tasks t, user_tasks ut WHERE t.id = ? AND ut.task_id = t.id AND ut.user_id = ?");
		$stmt->bind_param("ii",$task_id,$user_id);
		$stmt->execute();
		$num_affected_rows=$stmt->affected_rows;
		$stmt->close();
		return $num_affected_rows>0;
	}
/* ------------- `user_tasks` table method ------------------ */
/**
 * Function to assign a task to user
 * @param String $user_id id of the user
 * @param String $task_id id of the task
 */
	public function createUserTask($user_id,$task_id) {
		$stmt=$this->conn->prepare("INSERT INTO user_tasks(user_id, task_id) values(?, ?)");
		$stmt->bind_param("ii",$user_id,$task_id);
		$result=$stmt->execute();
		$stmt->close();
		return $result;
	}
	
		
/**
 * Validating Site Id, Server Id, and Year, Week, Day already exist
 * if the record we should not sabe this record to avoid duplicate records
 * @param Integer $iSiteId
 * @return boolean
 */
	public function isYieldOverallExistBy($iSiteId, $iServerId, $iDayNumber, $iYearNumber) {
		try
		{
				$query ="SELECT iId FROM tblYieldOverall WHERE  iSiteId = '{$iSiteId}' AND iServerId = '{$iServerId}' AND iDayNumber = '{$iDayNumber}'  
			AND iYearNumber = '{$iYearNumber}'  ";
			
			$stmt = $this -> conn-> query( $query ) or die("Error execution the Query " . $this->conn->error);
			//$stmt->bind_param("ss", $iSiteId, $iIdServer);
			
				
				$num = $stmt -> num_rows;
				
				//$stmt->close();
				return $num;
			
			
		}
		catch (Exception $e){
			var_dump('Error Overall -> ' . $e->getMessage() );
		}
	
	}
	/**
	 * Function to create a new Calculate Yield Overall by site
	 */
	 public function createYieldOverall($iSiteId, $iServerId, $iDayNumber, $iWeekNumber, $iMonthNumber, $iYearNumber, $dEntryDate, 
	 $iSumTotalHandle, $iSumTotalPass, $iSumTotalFail, $dAvgTotalYield, $iSumTotalHardFailure, $iSumTotalNTF, $iSumPrimeHandle, 
	 $iSumPrimePass, $iSumPrimeFail, $dAvgPrimeYield, $iSumPrimeHardFailure, $iSumPrimeNTF ){
	 	
		try
		{
			$query = "INSERT INTO tblYieldOverall (iSiteId, iServerId, iDayNumber, iWeekNumber, iMonthNumber, iYearNumber, dEntryDate, 
		 	iSumTotalHandle, iSumTotalPass, iSumTotalFail, dAvgTotalYield, iSumTotalHardFailure, iSumTotalNTF, iSumPrimeHandle, 
		 	iSumPrimePass, iSumPrimeFail, dAvgPrimeYield, iSumPrimeHardFailure, iSumPrimeNTF) values('{$iSiteId}', '{$iServerId}', '{$iDayNumber}', '{$iWeekNumber}', '{$iMonthNumber}',
		 	 '{$iYearNumber}', '{$dEntryDate}', '{$iSumTotalHandle}', '{$iSumTotalPass}', '{$iSumTotalFail}', '{$dAvgTotalYield}', '{$iSumTotalHardFailure}', '{$iSumTotalNTF}', '{$iSumPrimeHandle}', 
		 	 '{$iSumPrimePass}', '{$iSumPrimeFail}', '{$dAvgPrimeYield}', '{$iSumPrimeHardFailure}', '{$iSumPrimeNTF}' )";
			
			
			$stmt = $this->conn->query($query) or die("Error execution the Query " . $this->conn->error);
			$newRow = $this->conn->insert_id;
			//$stmt->close();
			return $newRow;
		}
		catch (Exception $e){
			var_dump('Error -> ' . $e->getMessage() );
		}catch (mysqli_sql_exception $e){
			var_dump('SQL Error -> ' . $e->getMessage() );
		}
		
	 	
	 }
	 
	 
	 /**
	 * Function to create a new Calculate Yield Overall by site
	 */
	 public function createYield($iIdYieldO, $sPartNumber, $sProcess, $sStation, 
	 $iTotalHandle, $iTotalPass, $iTotalFail, $dTotalYield, $iTotalHardFailure, $iTotalNTF, $iPrimeHandle, 
	 $iPrimePass, $iPrimeFail, $dPrimeYield, $iPrimeHardFailure, $iPrimeNTF ){
	 	
		try
		{
			$query = "INSERT INTO tblYieldsDet (iIdYieldO, sPartNumber, sProcess, sStation,  
		 	iTotalHandle, iTotalPass, iTotalFail, dTotalYield, iTotalHardFailure, iTotalNTF, iPrimeHandle, 
		 	iPrimePass, iPrimeFail, dPrimeYield, iPrimeHardFailure, iPrimeNTF) values('{$iIdYieldO}', '{$sPartNumber}', '{$sProcess}', '{$sStation}', 
		 	 '{$iTotalHandle}', '{$iTotalPass}', '{$iTotalFail}', '{$dTotalYield}', '{$iTotalHardFailure}', '{$iTotalNTF}', '{$iPrimeHandle}', 
		 	 '{$iPrimePass}', '{$iPrimeFail}', '{$dPrimeYield}', '{$iPrimeHardFailure}', '{$iPrimeNTF}' )";
			
			
			$stmt = $this->conn->query($query) or die("Error execution the Query " . $this->conn->error);
			$newRow = $this->conn->insert_id;
			//$stmt->close();
			return $newRow;
		}
		catch (Exception $e){
			var_dump('Error -> ' . $e->getMessage() );
		}catch (mysqli_sql_exception $e){
			var_dump('SQL Error -> ' . $e->getMessage() );
		}
		
	 	
	 }
}

