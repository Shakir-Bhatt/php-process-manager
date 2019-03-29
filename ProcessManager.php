<?php
/**
 * Created By : Shakir 
 * Senior Web Developer
 */
class ProcessManager {
	private static $fileName;
	private static $processId;

	public static function matchProcessId($command,$newProcessId,$dbProcessId){

		self::$processId = $dbProcessId;
		
		$cmd = "pgrep -f '" . $command . "'";

		$wcoutput = shell_exec($cmd);
        $processIdsToMatch =(explode(PHP_EOL,rtrim($wcoutput,PHP_EOL)));
        $match = false;
        self::debug($processIdsToMatch);
        foreach ($processIdsToMatch as $value) {
        	if($value == $dbProcessId){
        		$match = true;
        		break;
        	} 
        }

        if($match){
        	return true;
        } else {
        	self::removeProcessId($command);
        	return false;
        }
	}

	public function getProcessName($processName){
		$processName = basename($processName);
		return ltrim($processName,"php");
	}

	public static function checkProcessName($processName){
		self::debug("Checking process name in db");
		$processName = self::getProcessName($processName);

		$SELECT_QRY = "SELECT process_name,process_id FROM process_manager WHERE process_name = '$processName'";
		$result = submit_query($SELECT_QRY,SELECT_QRY);
		if($result->num_rows > 0){
			while ($row = $result->fetch_assoc()) {
				if(is_null($row['process_id'])){
					return ['status' => true ,'process_id' => false];
				} else {
					return ['status' => true ,'process_id' => true];
				}
			}
		} else {
			return ['status' => false];
		}	
	}

	public static function saveProcessId($processName,$processId){

		self::$processId = $processId;

		$processName = self::getProcessName($processName);
		$INSERT_QRY = "INSERT INTO process_manager (process_name,process_id) VALUES ('$processName','$processId') ON DUPLICATE KEY UPDATE
			process_id = VALUES(`process_id`)";

		$result = submit_query($INSERT_QRY,INSERT_QRY);
		if($result['status']){
			self::debug("Process ID Updated");
		} else {
			self::debug($result['message']);
		}	
	}

	public static function getProcessIdFromDB($processName){

		$processName = self::getProcessName($processName);
		$INSERT_QRY = "SELECT * FROM process_manager WHERE process_name = '$processName'";
		self::debug($INSERT_QRY);
		$result = submit_query($INSERT_QRY,SELECT_QRY);
		if($result->num_rows > 0){
			while ($row = $result->fetch_assoc()) {
				return ['status' => true ,'process_id' => $row['process_id']];
			}
		} else {
			return ['status' => false ,'process_id' => null];
		}	
	}

	public static function removeProcessId($processName){
		$processName = self::getProcessName($processName);
		$UPDATE_QRY = "UPDATE process_manager SET process_id = NULL WHERE process_name = '$processName'";
		$result = submit_query($UPDATE_QRY,UPDATE_QRY);
		if($result['status']){
			try {
				$processToKill = self::$processId;
				$cmd = "kill -9 $processToKill";
				shell_exec($cmd);
			} catch(\Exception $e){
				self::debug($e->getMessage());
			}

		} else {
			self::debug($result['message']);
		}
	}

	public static function debug($message,$exit=false){
		echo "<pre>";
		echo "\n";
		print_r($message);
		if($exit){
			exit;
		}
	}
}
