Process Manager
============

  The process manager code is helpful for managing process using process id. 
  Sometime you want your code to executed by cron in intervals and the taken by process is not certian or sometime process gets stock and does't compelete in specified time interval. In that case when next cron call runs the file it overrides the exixting process and you may get incorrect data.

## List of files:
1. ProcessManager.php : contains the code for managing process IDS.
2. FileForManagingProcessId.php : Demo file and there is also code in the file which handles ProcessManager.php files code.
3. process_manager.sql
 

  For that I have written a code file which manages the process ids.

## The process is described as:
1. Beofore the actual process is started, the current executing process is matched with process table, ( which we manage as db), if no entry is found in table which is certain in first time, the the process name is saved in table along with process ID and process is allowed to run.
2. If process exists in table, it may have process Id or Null.
    1. If process ID is Null in table, then process is allowed to run.
    2. If process ID exists the the process, the process ID is compared with the list of process IDS currently present is process pool/list.
        1. If the process ID from table match in the process pool/list, which means the process ID from table is currently in execution and the new process is not allowed to run.
        2. Else the process ID from table is removed from the table and the process is allowed to run.
    3. If process name exists and process ID is NUll, process is allowed to run.
    


## Useage:
1. Import process_manager.sql file to table.

2. Include ProcessManager.php file in your code file.
```php
    require_once "ManageRunningProcess.php";
```
3. Put below code before your code file is executed

```php
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
```
2. config folder : Contains database interaction file and connection files and other helper files.
3. controller: Contains all controller files which are corresponding for each html code file.
4. include: Contains file which are used at more then one page.
5. layouts: Here main layout file resides. layout file is used to render other view files.
6. routes:  Contains Routes.php file where we define all routes, view and controllers to use.
7. storage: Contians files which are generated by code e.g logs and other files.  
8. views:   All the html containing file are put under this folder.

The whole process is handeled by index.php. Any request generated by project goes through this file.


## Functions

1. Saves process ID in table 
```php
    ProcessManager::saveProcessId($processName,$processId);
```
2. Remove process Id.
```php
    ProcessManager::removeProcessId($processName);
```    

