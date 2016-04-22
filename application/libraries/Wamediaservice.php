<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Windows Azure Mediservice library
 * using windowsazure php sdk for accessing Windowsazure media service
 * @package     Digital agency app
 * @subpackage  Libraries
 * @category    Windows Azure Media Service
 * @version     1.0
 * @required    Windowsazure php sdk
 * 
 */


    require_once 'WindowsAzure\WindowsAzure.php';

    use WindowsAzure\Common\ServicesBuilder;
    use WindowsAzure\Common\Internal\MediaServicesSettings;
    use WindowsAzure\MediaServices\Models\Asset;
    use WindowsAzure\MediaServices\Models\AccessPolicy;
    use WindowsAzure\MediaServices\Models\AssetFile;
    use WindowsAzure\MediaServices\Models\Locator;
    use WindowsAzure\MediaServices\Models\Job;
    use WindowsAzure\MediaServices\Models\Task;
    use WindowsAzure\MediaServices\Models\TaskOptions;
    use WindowsAzure\MediaServices\Models\JobTemplate;
    use WindowsAzure\MediaServices\Models\TaskTemplate;
   

class Wamediaservice {
    
    const ACCESS_POLICY_NAME    = 'AccessPolicy';
    const SAS_ACCESS_POLICY_NAME    = 'SasAccessPolicy';
    
    // Active service builder insatnce
    private $_serviceBuilder;

    // Active media sevices REST proxy instance
    private $_mediaService;    
    
    
    public function __construct($wamedia_config) {
        
        $this->initMediaServicesProxy($wamedia_config['account_name'], $wamedia_config['account_key']);
    }
    
    /**
     * Initializes Media Services REST proxy
     *
     * @param string $accountName Media Services account name
     * @param string $accessKey   Primary or secondary access key
     *
     * @return null
     */
    private function initMediaServicesProxy($accountName, $accessKey) {
        
        if(empty($accountName) || empty($accessKey)){
            error_log(__CLASS__ ." ".__METHOD__." ACCOUNT_NAME or ACCOUNT_KEY is missing : ");
            return false;
        }
        $this->_serviceBuilder = ServicesBuilder::getInstance();
        $this->_mediaService = $this->_serviceBuilder->createMediaServicesService(
            new MediaServicesSettings(
                $accountName,
                $accessKey
            )
        );
    }    
    
    /*   
    public function uploadFileToMediaService($name, $video, $sas_jobname, $asset_name, $sas_asset_name){

        $uploadedAsset = $this->uploadFileToAsset($name, $video, $asset_name);
        error_log('uploaded val '. print_r($uploadedAsset, true));
        
        // create job for encoding 
        // Run encoding for SAS
        $sasJob = $this->createJob(
                $sas_jobname,
                $uploadedAsset,
                $this->createTask($sas_asset_name),
                'H264 Broadband SD 4x3'
        );
        error_log('sas job '. $sasJob);
        return array(
            'showState' => isset($name)
        );
    } */
    
 
    
    /**
     * Summary of uploadFileToMediaService
     * @param mixed $asset_name 
     * @param mixed $name
     * @param mixed $video      
     * @return mixed
     */    
    public function uploadFileToMediaService($asset_name, $name, $video){
        $uploadedAsset = $this->uploadFileToAsset($name, $video, $asset_name);
        error_log('uploaded val '. print_r($uploadedAsset, true));
        return $uploadedAsset;
    }
    
    /**
     * Summary of sasCreateJob
     * @param mixed $sas_jobname 
     * @param mixed $sas_asset_name 
     * @param mixed $uploaded_asset 
     * @return mixed
     */
    public function sasCreateJob($sas_jobname, $sas_asset_name, $uploaded_asset){
        // create job for encoding 
        // Run encoding for SAS
        $sasJob = $this->createJob(
                $sas_jobname,
                $uploaded_asset,
                $this->createTask($sas_asset_name),
                'H264 Broadband SD 4x3'
        );
        //error_log('sas job '. print_r($sasJob, true));
        return $sasJob;
    }    
    
    /**
     * Perform file upload to media services. Create asset and add file into it.
     *
     * @param string $name    File name
     * @param string $video File contents
     *
     * @return Asset Created asset with a file in it
     */
    private function uploadFileToAsset($name, $video, $asset_name) {
        // Create asset
        $asset = new Asset(Asset::OPTIONS_NONE);
        $asset->setName($asset_name);
        $asset = $this->_mediaService->createAsset($asset);

        // Create access policy
        $access = new AccessPolicy(self::ACCESS_POLICY_NAME);
        $access->setDurationInMinutes(10);
        $access->setPermissions(AccessPolicy::PERMISSIONS_WRITE);
        $access = $this->_mediaService->createAccessPolicy($access);

        // Create locator
        $locator = new Locator($asset, $access, Locator::TYPE_SAS);
        $locator->setStartTime(new \DateTime('now -5 minutes'));
        $locator = $this->_mediaService->createLocator($locator);

        // Upload file
        $this->_mediaService->uploadAssetFile($locator, $name, $video);
        $this->_mediaService->createFileInfos($asset);

        // Clean after upload
        $this->_mediaService->deleteLocator($locator);
        $this->_mediaService->deleteAccessPolicy($access);

        // get access policy id and locator id.

                                        
       // error_log(' a id '.$locator->getAccessPolicyId().' locator id '.$locator->getId());
        return $asset;
    }    
    
    /*
     * Generate file info for all files in asset
     * for more inforamtion please check with windows azure php sdk
     */
    public function createFileInfos($asset){
        if(!isset($asset) || empty($asset)){
            return false;
        }
        return $this->_mediaService->createFileInfos($asset);;
    }
    /**
     * Summary of Get Asset Write URL
     * @param string $filename
     * @return array saswriteurl, assetid, accesspolicyid, locatorid
     */    
    public function getAssetSasUrl($filename){
        // Create asset
        $asset = new Asset(Asset::OPTIONS_NONE);
        $asset->setName($filename);
        $asset = $this->_mediaService->createAsset($asset);
        // Create access policy
        $access = new AccessPolicy(self::ACCESS_POLICY_NAME);        
        $access->setDurationInMinutes(60*24);
        $access->setPermissions(AccessPolicy::PERMISSIONS_WRITE);
        $access = $this->_mediaService->createAccessPolicy($access);
        // Create locator
        $locator = new Locator($asset, $access, Locator::TYPE_SAS);
        $locator->setStartTime(new \DateTime('now -5 minutes'));
        $locator    =   $this->_mediaService->createLocator($locator);
                     
        return array(
            "sasWriteUrl"   =>  $locator->getBaseUri().'/'.$filename.$locator->getContentAccessComponent(),
            "assetId"       => $locator->getAssetId(),
            "accessPolicyId"=> $locator->getAccessPolicyId(),
            "locatorId"     => $locator->getId()
                );
    }
    
    /**
     * Summary of Get Asset details by asset id
     * @param string $assetid
     * @return asset details
     */
	public function getAsset($assetid){
		return	$this->_mediaService->getAsset($assetid);
	}
    
    public function deleteLocator($locator){
        return $this->_mediaService->deleteLocator($locator);
    }

    public function deleteAccessPolicy($access){
        return $this->_mediaService->deleteAccessPolicy($access);
    }    
    
    /**
     * Get Locator.
     *
     * @param WindowsAzure\MediaServices\Models\Locator|string $locator Locator data
     * or locator Id
     *
     * @return WindowsAzure\MediaServices\Models\Locator
     */
    public function getLocator($locator){
        return	$this->_mediaService->getLocator($locator);
    }
    
    /**
     * Get AccessPolicy.
     *
     * @param WindowsAzure\MediaServices\Models\AccessPolicy|string $accessPolicy A
     * AccessPolicy data or AccessPolicy Id
     *
     * @return WindowsAzure\MediaServices\Models\AccessPolicy
     */    
    public function getAccessPolicy($accessPolicy){
        return	$this->_mediaService->getAccessPolicy($accessPolicy);
    }    
    
   /**
     * Create task XML
     *
     * @param string $outputAssetName Output asset name
     *
     * @return string
     */
    private function createTask($outputAssetName) {
        
        return '<?xml version="1.0" encoding="utf-8"?>
            <taskBody>
                <inputAsset>JobInputAsset(0)</inputAsset>
                <outputAsset
                    assetCreationOptions="0"
                    assetName="' . $outputAssetName . '"
                >JobOutputAsset(0)</outputAsset>
            </taskBody>';
    }

    /**
     * Create encoding job
     *
     * @param string $name       Job name
     * @param Asset  $inputAsset Asset to process
     * @param string $taskXml    Task XML represenation
     * @param string $encode     Preset configuration name
     *
     * @return Job
     */
    private function createJob($name, $uploadedAsset, $taskXml, $encode) {

        $mediaProcessor = $this->_mediaService->getLatestMediaProcessor(
            'Windows Azure Media Encoder'
        );
        
        $task = new Task($taskXml, $mediaProcessor->getId(), TaskOptions::NONE);
        $task->setConfiguration($encode);

        $job = new Job();
        $job->setName($name);

        $job = $this->_mediaService->createJob(
            $job,
            array($uploadedAsset),
            array($task)
        );

        return $job;
    }
    
    /**
     * Build encoding status message based on job state
     *
     * @param Job $job Job to build message for
     *
     * @return string
     */
    public function getJobStatusMessage($job){
        switch ($job->getState()) {
            case Job::STATE_FINISHED:
                //return 'Job "' . $job->getName() . '" finished. Media file would be accessible in %d seconds.';
                return true;
            case Job::STATE_QUEUED:
                //return 'Job "' . $job->getName() . '" is queued.';
                return false;
            case Job::STATE_PROCESSING:
                //return 'Job "' . $job->getName() . '" is processing.';
                return false;
            case Job::STATE_ERROR:
                //return 'Job "' . $job->getName() . '" finished with errors.';
                return false;
            case Job::STATE_CANCELED:
                //return 'Job "' . $job->getName() . '" is canceled.';
                return false;
            case Job::STATE_CANCELING:
                //return 'Job "' . $job->getName() . '" is canceling.';
                return false;
            case Job::STATE_SCHEDULED:
                //return 'Job "' . $job->getName() . '" is scheduled.';
                return false;
        }
    } 
    /**
     * Build encoding status message based on job state
     *
     * @param Job $job Job to build message for
     *
     * @return string
     */
    public function isJobCompleted($job){
        
        switch ($job->getState()) {            
            case Job::STATE_FINISHED:
                $val = true;
                break;
            case Job::STATE_QUEUED:
            case Job::STATE_PROCESSING:
            case Job::STATE_ERROR:
            case Job::STATE_CANCELED:
            case Job::STATE_CANCELING:
            case Job::STATE_SCHEDULED:
                $val = false;
                break;
            default:
                $val = false;
        }
        
        return $val;
    }     
    
    /**
     * Create URL for result of SAS job
     *
     * @return string
     */
    //private function getSasLocator($sas_asset_name, $currentMediaFile){
        
    //    error_log('calling getSasLocator '. $sas_asset_name .'s '.$currentMediaFile );
    //    $asset = $this->getAssetByName($sas_asset_name);
    //    error_log('got asset' .$asset->getName());
    //    $accessPolicy = new AccessPolicy(self::SAS_ACCESS_POLICY_NAME);
    //    $accessPolicy->setDurationInMinutes(1440);
    //    $accessPolicy->setPermissions(AccessPolicy::PERMISSIONS_READ);
    //    error_log('policy setting');
    //    $accessPolicy = $this->_mediaService->createAccessPolicy($accessPolicy);
        
    //    //$accessPolicy = $this->_mediaService->getAccessPolicy(self::SAS_ACCESS_POLICY_NAME);            
        
    //     error_log('access policy  '. print_r($accessPolicy, true));

    //    $locator = new Locator($asset, $accessPolicy, Locator::TYPE_SAS);
    //    $locator->setStartTime(new \DateTime('now -5 minutes'));
    //    $locator = $this->_mediaService->createLocator($locator);
    //    error_log('locator  '. print_r($locator, true));
    //    $fileInfo = pathinfo($currentMediaFile);
    //    error_log('file info  '. print_r($fileInfo, true));

    //    return $locator->getBaseUri().'/'.$fileInfo['filename'].'_H264_1800kbps_AAC_und_ch2_128kbps.mp4'.$locator->getContentAccessComponent();
    //}
    
    /**
     * Create URL for result of SAS job
     *
     * @return string
     */
    public function getSasLocator($sas_asset_name, $filename){
       // error_log('calling getSasLocator sas_asset_name '. $sas_asset_name .' and file_name '.$filename );
             
        $asset = $this->getAssetByName($sas_asset_name);
       // error_log('1 got asset' .$asset->getName());
       // error_log('2 got asset '. print_r($asset, true));
        if(!empty($asset)){
            $accessPolicy = new AccessPolicy(self::SAS_ACCESS_POLICY_NAME);
            //$accessPolicy->setDurationInMinutes(5256000); // mins of 10 Years {60mins*24hours*365days*10years}
            $accessPolicy->setDurationInMinutes(43200); // mins of 30 days {60mins*24hours*30days}
            $accessPolicy->setPermissions(AccessPolicy::PERMISSIONS_READ);
          //  error_log('policy setting');
            $accessPolicy = $this->_mediaService->createAccessPolicy($accessPolicy);
            
            //$accessPolicy = $this->_mediaService->getAccessPolicy(self::SAS_ACCESS_POLICY_NAME);        
          //  error_log('access policy  '. print_r($accessPolicy, true));

            $locator = new Locator($asset, $accessPolicy, Locator::TYPE_SAS);           
            $locator->setStartTime(new \DateTime('now -5 minutes'));        
            $locator = $this->_mediaService->createLocator($locator);
         //   error_log('locator TWO '. print_r($locator, true));
            
            return array('status'=>true, 'media_path'=>$locator->getBaseUri().'/'.$filename.'_H264_1800kbps_AAC_und_ch2_128kbps.mp4'.$locator->getContentAccessComponent());
        }else{
            return array('status'=>false);
        }
    }
    
    /**
     * Create URL for result of streaming job
     *
     * @return string
     */
    private function getStreamLocator(){
        
        $asset = $this->getAssetByName(DemoController::STREAM_ASSET_NAME);

        $accessPolicy = new AccessPolicy(DemoController::STREAM_ACCESS_POLICY_NAME);
        $accessPolicy->setDurationInMinutes(1440);
        $accessPolicy->setPermissions(AccessPolicy::PERMISSIONS_READ);
        $accessPolicy = $this->_mediaService->createAccessPolicy($accessPolicy);

        $locator = new Locator($asset, $accessPolicy, Locator::TYPE_ON_DEMAND_ORIGIN);
        $locator->setStartTime(new \DateTime('now -5 minutes'));
        $locator = $this->_mediaService->createLocator($locator);

        $fileInfo = pathinfo($_SESSION['currentMediaFile']);

        return  $locator->getPath() . $fileInfo['filename'] . '.ism/Manifest';
    }
    
    /*
     * Get all the assets
     */
    public function getAllAsset(){
       
        return $this->_mediaService->getAssetList();
    }
    
    /*
     * Get All the Access Policy
     */
    
    public function getAllAccessPolicy(){
        // only for testing
        return $this->_mediaService->getAccessPolicyList();
    }
    
    /*
     * Get All the Jobs
     */
    
    public function getAllJobs(){
        // only for testing
        return $this->_mediaService->getJobList();
    }  
    
    /**
     * Get asset object by name
     *
     * @param string $name Asset name
     *
     * @return Asset|NULL
     */
    private function getAssetByName($name){
        
        $assets = $this->_mediaService->getAssetList();
        foreach($assets as $asset) {
            if ($asset->getName() == $name) {
                return $asset;
            }
        }

        return null;
    }
    /**
     * Get job object by job name
     *
     * @param string $name Job name
     *
     * @return Job|NULL
     */
    private function getJobByName($name){
        
        $jobs = $this->_mediaService->getJobList();
        foreach($jobs as $job) {
            if ($job->getName() == $name) {
                return $job;
            }
        }

        return null;
    }
    
    /*
     * Get Job Object by job id
     * @param strin $jobid Job id
     * 
     * @return Job | Null
     */
    
    public function getJobByJobId($jobid){
        if(empty($jobid)){
            return false;
        }
        
        return $this->_mediaService->getJob($jobid);
        
    }
    
   
    
    public function getJobState($job){
        
        return $job->getState();
    }
    /**
     * Delete job. Job could be deleted at every moment. So method waits until
     * the job would move to the state it can be deleted
     *
     * @param Job $job Job object
     */
    private function deleteJob($job){
        
        $status = $this->_mediaService->getJobStatus($job);
        while ($status != Job::STATE_FINISHED && $status != Job::STATE_ERROR && $status != Job::STATE_CANCELED) {
            sleep(1);
            $status = $this->_mediaService->getJobStatus($job);
        }
        $this->_mediaService->deleteJob($job->getId());
    }
    
    
    public function uploadVideoFileInInstall($asset_name, $fileName, $video){
        
                // Create asset
        $asset = new Asset(Asset::OPTIONS_NONE);
        $asset->setName($asset_name);
        $asset = $this->_mediaService->createAsset($asset);

        // Create access policy
        $access = new AccessPolicy(self::ACCESS_POLICY_NAME);
        $access->setDurationInMinutes(60*24);
        $access->setPermissions(AccessPolicy::PERMISSIONS_WRITE);
        $access = $this->_mediaService->createAccessPolicy($access);

        // Create locator
        $locator = new Locator($asset, $access, Locator::TYPE_SAS);
        $locator->setStartTime(new \DateTime('now -5 minutes'));
        $locator = $this->_mediaService->createLocator($locator);
        
        // for storing 
        $assetId        = $locator->getAssetId();
        $sasWriteUrl    = $locator->getBaseUri().'/'.$fileName.$locator->getContentAccessComponent();
        $accessPolicyId =  $locator->getAccessPolicyId();
        $locatorId      = $locator->getId();
        // Upload file
        $this->_mediaService->uploadAssetFile($locator, $fileName, $video);
        $this->_mediaService->createFileInfos($asset);

        
        // Clean after upload
        $this->_mediaService->deleteLocator($locator);
        $this->_mediaService->deleteAccessPolicy($access);

        // get access policy id and locator id.

        return array(
            'asset'         => $asset,
            "assetId"       => $assetId,
            'sasWriteUrl'   => $sasWriteUrl,
            'accessPolicyId'=> $accessPolicyId,
            'locatorId'     => $locatorId
            
        );

        
        
    }
   
}

/* End of file Wamediaservice.php */
/* Location: ./application/libraries/Wamediaservice.php */