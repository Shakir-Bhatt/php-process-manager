<?php
/**
 * Created By Shakir.
 * Senior Web Developer.
 */
require_once "ManageRunningProcess.php";

class FileForManagingProcessId{
	
	// For process manager
	private $action;
	private $processName;
	private $command;

	function __construct(){
		# code...
	}

	public function startOfProgram($processName,$processId){
		$this->processName = $processName;
		ProcessManager::saveProcessId($processName,$processId);
		
		/*
         * Your code goes here
		*/	

		# Based on some condition if you want to stop process and remove process id
		if("some condition"){
			ProcessManager::removeProcessId($this->processName);
		}

		/*
         * Your code goes here
         * Other code part
		*/	

		
		# Call removeProcessId at the end of process 
		ProcessManager::removeProcessId($this->processName);
	}
}

/* The functionality of below code is:
 * Before we allow your code to perform any action
 * 1- First check if the process is already running.
 * 2- Update the entry in table if process is not running.
 * 3- If process if already running then stop.
*/

$processName = __FILE__;
echo "New Process Id = " . $newProcessId = getmypid();
$command = "php FileForManagingProcessId.php";
$response = ProcessManager::checkProcessName($processName);

if($response['status']){ 
	if($response['process_id']){
		$processIdResult = ProcessManager::getProcessIdFromDB($processName);
		processProcessIds($processName,$processIdResult,$command,$newProcessId);
	} else {
		startProcess($processName,$newProcessId,$command);
	}	
} else {
	startProcess($processName,$newProcessId,$command);
}

function startProcess($processName,$newProcessId,$command){
	$processIdResult = ProcessManager::getProcessIdFromDB($processName);
	processProcessIds($processName,$processIdResult,$command,$newProcessId);
} 

function processProcessIds($processName,$processIdResult,$command,$newProcessId){
	if($processIdResult['status'] && !is_null($processIdResult['process_id'])){
		if(ProcessManager::matchProcessId($command,$newProcessId,$processIdResult['process_id'])){
			exit("Process already in excution-");
		} else {
			$obj = new FileForManagingProcessId();
			$obj->startOfProgram($processName,$newProcessId);
		}
	} else {
		$obj = new FileForManagingProcessId();
		$obj->startOfProgram($processName,$newProcessId);
	}
}