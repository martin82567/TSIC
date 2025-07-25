<?php 

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use Hash;
use Crypt;
use File;
use DateTime;
use DateTimeZone;
use Illuminate\Contracts\Encryption\DecryptException;

class ReadXmlController extends Controller {

   protected $fileName ;
   // protected $fileName = public_path() . '/uploads/9-5-registraion.xml';
   protected $headerCol = [];
   protected $finalData = [];
   protected $formattedData = [];
   protected $staticData = [
    'device_type' => 'iOS',    
     'password' => '$2y$10$hKHAZnhZK/IEp.X7TRJ2yuPPXlcxQMt8m1EdXsYbESfWNGGuKuWRO',
     'created_by' => 61,
     'assigned_by' => 61,
     'status' => 1
   ];

   const MENTOR_TABLE = 'mentor';
   const MENTEE_TABLE = 'victims';

   public function __construct() {
       // $this->load();
        $this->fileName = public_path() . '/uploads/Keywords.xml';
        // $this->fileName = public_path() . '/uploads/9-5-registraion.xml';
   }
   public function load() {
        // $this->db_map_field();
       $readXml = simplexml_load_file($this->fileName);
       $this->doParse($readXml);
   }
   public function doParse($xmlObj) {
       $this->showHeaderArr($xmlObj);
       // $this->showDataArr($xmlObj);
   }
   public function showHeaderArr($xmlObj) {
      
    $i = 0;
    $keywords = array();
     // echo '<pre>'; print_r($xmlObj); die;
     foreach ($xmlObj->Table->Row as $eachRow) {
         
            // $this->formattedData = [];
            $j = 0;
             foreach($eachRow->Cell as $col)
             {
                $parseVal = @json_decode(@json_encode($col),1);
                // $this->mapToFormattedData($j, $parseVal);
                echo '<pre>'; print_r($parseVal);
                $keywords[] = $parseVal['Data'];
                $j++;
             }
             // $this->finalData[] = array_merge($this->formattedData, $this->staticData);
         
         $i++;
     }

     echo '<pre>'; print_r($keywords);

     foreach($keywords as $key => $value){
      // DB::table('keyword')->insert(['title'=>$value]);
     }




       // $header = $xmlObj->Table->Row[0];

       // foreach ($header as $h) {
       //    // echo '<pre>'; print_r($h); 
       //     foreach ($h->children() as $child) {
       //         // $parseVal = @json_decode(@json_encode($child),1);
       //         // $this->headerCol[] = $parseVal[0];
       //     }
       // }
   }
   public function showDataArr($xmlObj) {
       $i = 0;
       // echo '<pre>'; print_r($xmlObj); die;
       foreach ($xmlObj->Table->Row as $eachRow) {
           if ($i != 0) {
              $this->formattedData = [];
              $j = 0;
               foreach($eachRow->Cell as $col)
               {
                  $parseVal = @json_decode(@json_encode($col),1);
                  // $this->mapToFormattedData($j, $parseVal);
                  $j++;
               }
               // $this->finalData[] = array_merge($this->formattedData, $this->staticData);
           }
           $i++;
       }
       
       // $this->insertMentee();

   }
   

   public function db_map_field()
   {
        $arr_db = array('firstname'=>'Jillian','lastname'=>'','email'=>'jillian.mentor@test.com','phone'=>'','home_phone_number'=>'','emergency_contact_name'=>'','device_type'=>'iOS','is_chat_mentee'=>'1','is_chat_staff'=>'1','password'=>'$2y$10$hKHAZnhZK/IEp.X7TRJ2yuPPXlcxQMt8m1EdXsYbESfWNGGuKuWRO','created_by'=>'61','assigned_by'=>'61','is_active'=>'1');

        echo "<pre>";
        print_r($arr_db);




   }


   public function mapToFormattedData($j, $parseVal)
   {
       $dbMapField = $this->mapFields($this->headerCol[$j]);
       if($dbMapField != '') { 
           if($dbMapField === 'email' && empty($parseVal['Data'])) {
               $populatedField = strtolower($this->formattedData['firstname']);
               $populatedField .= (is_array($this->formattedData['lastname']) && count($this->formattedData['lastname']) < 1) ? '.'.strtolower($this->formattedData['lastname']) : '';
               $populatedField .= '.mentee@test.com';
           } else if($dbMapField === 'email' && !empty($parseVal['Data'])) {
               $populatedField = strtolower($this->formattedData['firstname']);
               $populatedField .= '.'.strtolower($this->formattedData['lastname']);
               $populatedField .= '.mentee@test.com';
           } else if( ($dbMapField === 'cell_phone_number' && !empty($parseVal['Data'])) || ($dbMapField === 'home_phone_number' && !empty($parseVal['Data'])) ) {
               $populatedField = $this->getFormattedPhoneNo($parseVal['Data']);
           } else {
               $populatedField = !empty($parseVal['Data']) ? $parseVal['Data'] : '';
           }
           $this->formattedData[$dbMapField] = $populatedField;
       }
       //$this->formattedData[$this->headerCol[$j]] = $parseVal['Data'];
   }
   public function mapFields($key)
   {
       $fields = [
           'Name' => 'firstname',
           'Last' => 'lastname',
           'Email' => 'email',
           'Work Phone Number' => 'cell_phone_number',
           'Cell Phone Number' => 'home_phone_number',
           'Emergency Contact Name' => 'emergency_contact_name'
       ];
       return (isset($fields[$key])) ? $fields[$key] : '';
   }
   public function getFormattedPhoneNo($phoneNumber)
   {
       $pattern = '/^(\d{3})(\d{3})(\d{4})$/';
       preg_match($pattern, $phoneNumber, $matches);
       return '('.$matches[1].') '.$matches[2].'-'.$matches[3];
   }

   public function insertMentor()
   {
     //DB::table(self::MENTOR_TABLE)->insert($this->finalData);    
   }

   public function insertMentee()
   {

     //DB::table(self::MENTEE_TABLE)->insert($this->finalData);    
   }





}
//Fire Off
// new AquaXmlParse();