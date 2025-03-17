<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class DBController extends Controller
{
    //
    public function download($folder='',$filename='')
    {      
    	// die('Hi'); 
        $s3Client = Storage::cloud()->getAdapter()->getClient();

        if(!empty($folder) && !empty($filename)){
        	if($folder != 'tsiclive'){
        		echo 'Folder name should be <strong>tsiclive</strong>';
        		exit;
        	}

        	$cmd = $s3Client->getCommand('GetObject', [
	            'Bucket' => '17dat5a86baa4ea5dtta',
	            'Key'    => $folder.'/'.$filename
	        ]);
	        
	        $request = $s3Client->createPresignedRequest($cmd, '+60 minutes');

	        $presignedUrl = (string) $request->getUri();


	        $filename = $presignedUrl;
	        Header("Location: ".$filename); exit();
        }else{
        	echo 'Please mention <strong>Folder</strong> and <strong>Filename</strong> at URI paramter ';
        }
        

    }
}
