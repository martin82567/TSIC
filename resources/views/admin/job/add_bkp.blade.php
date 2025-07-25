@extends('layouts.admin')

@section('content')
<div class="db-menu-toggle">
    <button type="button" class="db-menu-toggle-btn"><i class="fa fa-bars"></i></button>
</div>

<div class="db-inner-content">
    <form role="form" action="{{ route('admin.job.save')}}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" id="id" name="id" value="<?php echo !empty($user_details->id)?$user_details->id:0;?>">
        <div class="db-box">
            <div class="heading-sec">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h3>Job</h3>
                        <p>Please complete the details for creating a Job</p>
                    </div>
                    <div class="col-lg-6">
                        <a href="{{ url('/admin/job') }}" class="back-btn"><i class="fa fa-arrow-left"></i></a>
                    </div>
                </div>
            </div>
            <?php if(!empty(session('success_message'))){ ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
                <h4 style="margin-bottom: 0"><i class="icon fa fa-check"></i>
                </h4>
            </div>
            <?php } ?>
            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
                <!--{{ implode('', $errors->all(':message')) }}-->
                @foreach ($errors->all() as $error)
                <h4>
                    {{ $error }}</h4>
                @endforeach
            </div>
            @endif

            <div class="box-inner">
                <div class="form-section">
                    <h3>Create job</h3>

                    <div id="first_div">

                        <div class="col-lg-12 text-right">
                            <br/>
                                <div class="check active-toggle">
                                    <label>
                                    <input type="checkbox" name="status" id="" value="1"  <?php if((isset($user_details->status) && ($user_details->status == 1)) || !isset($user_details->status)){ ?>checked=""<?php } ?>> <span id="check_title"> <?php if((isset($user_details->status) && ($user_details->status == 1)) || !isset($user_details->status)){ ?>Active <?php }else{ ?>Inactive <?php } ?></span>
                                    </label>
                                </div>
                        </div> 

                        <div class="row mb-3">
                            <div class="col-xl-4 col-md-6">
                                <div class="single-inp">
                                    <label>Job Title <sup>*</sup></label>
                                    <div class="inp">
                                        <input type="text" id="job_title" name="job_title" value="<?php echo !empty($user_details->job_title)?$user_details->job_title:'';?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-md-6">
                                <div class="single-inp">
                                    <label>Company <sup>*</sup></label>
                                    <div class="inp">
                                        <input type="text" id="company" name="company" value="<?php echo !empty($user_details->company)?$user_details->company:'';?>" required>
                                    </div>
                                </div>
                            </div>



                            <div class="col-xl-4 col-md-6">
                                <div class="single-inp">
                                    <label>Location <sup>*</sup></label>
                                    <div class="inp">
                                        <textarea class="form-control location" rows="1" name="location" placeholder="Enter location name" id="autocomplete" onFocus="geolocate();" required><?php echo !empty($user_details->location)?$user_details->location:""; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="row mb-3">
                            <div class="col-xl-6 col-md-6">
                                <div class="single-inp">
                                    <label>Job Type <sup>*</sup></label>
                                    <div class="inp">
                                        <div class="select">
                                            <select style="width: 100%;" name="job_type" id="state" required>
                                                <option value="">Choose Job Type</option>
                                                <?php foreach($jobtype as $type_name){?>
                                                <option <?php if(isset($user_details->job_type) && ($user_details->job_type == $type_name->name)){ echo "selected"; } ?> value="
                                                    <?php echo $type_name->name;?>">
                                                    <?php echo $type_name->name;?>
                                                </option>
                                                <?php }?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-6">
                                <div class="single-inp">
                                    <label>How many hires do you want to make for this positions? <sup>*</sup></label>
                                    <div class="inp">
                                        <?php 
                                            $no_of_postion = array('1','2','3','4','5','6','7','8','9','10');
                                        ?>
                                        <div class="select">
                                            <select name="no_of_postion" id="no_of_postion" required>
                                                <option value="">Please Select</option>
                                                <?php foreach($no_of_postion as $noofpostion){?>
                                                <option <?php if(isset($user_details->job_type) && ($user_details->no_of_postion == $noofpostion)){ echo "selected"; } ?> value="
                                                    <?php echo $noofpostion;?>">
                                                    <?php echo $noofpostion;?>
                                                </option>
                                                <?php }?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-xl-12 col-md-6">
                                <div class="single-inp">
                                    <label>Application URL <sup>*</sup></label>
                                    <div class="inp">
                                        <input type="url" id="application_url" name="application_url" value="<?php echo !empty($user_details->application_url)?$user_details->application_url:'';?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <label>JOB DESCRIPTION</label><br>

                        <div class="row mb-3">

                            <div class="col-xl-6 col-md-6">
                                <div class="single-inp">
                                    <label>Job Summary<sup>*</sup></label><br>

                                    <div class="inp">
                                        <textarea class="form-control" rows="2" name="summary" placeholder="Enter Job Summary" id="summary" required><?php echo !empty($user_details->summary)?$user_details->summary:""; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-6">
                                <div class="single-inp">
                                    <label>Responsibilities &amp; Duties <sup>*</sup></label><br>

                                    <div class="inp">
                                        <textarea class="form-control" rows="2" name="responsibilities" placeholder="Enter Job Responsibilities" id="responsibilities" required><?php echo !empty($user_details->responsibilities)?$user_details->responsibilities:""; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row mb-3">

                            <div class="col-xl-6 col-md-6">
                                <div class="single-inp">
                                    <label>Required Experience Skills & Qualification <sup>*</sup></label><br>

                                    <div class="inp">
                                        <textarea class="form-control" rows="2" name="skills_qualification" placeholder="Enter Required Experience Skills & Qualification" id="skills_qualification" required><?php echo !empty($user_details->skills_qualification)?$user_details->skills_qualification:""; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-6">
                                <div class="single-inp">
                                    <label>Benefits <sup>*</sup></label><br>
                                    <div class="inp">
                                        <textarea class="form-control" rows="2" name="benefits" plabenefitsceholder="Enter Job Benefits" id="benefits" required><?php echo !empty($user_details->benefits)?$user_details->benefits:""; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row mb-3">
                            <div class="col-xl-4 col-md-3">
                                <div class="single-inp">
                                    <label>How do you want to receive applications?<sup>*</sup></label>
                                    <div class="inp">
                                        <div class="select">
                                            <select name="application_type" id="application_type" required>
                                                <option value="">Select</option>
                                                <option <?php if(isset($user_details->application_type) && ($user_details->application_type == 'email')){ echo "selected"; } ?> value="email">Email</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-md-3">
                                <div class="single-inp">
                                    <label>Applications for this job will sent to following Email Address</label>
                                    <div class="inp">
                                        <input type="email" id="email_to_notify" name="email_to_notify" value="<?php echo !empty($user_details->email_to_notify)?$user_details->email_to_notify:'';?>">
                                    </div>
                                </div>
                            </div>


                            <div class="col-xl-4 col-md-3">
                                <div class="single-inp">
                                    <label>Do you want applicants to submit a resume?<sup>*</sup></label>
                                    <div class="inp">
                                        <div class="select">
                                            <select name="if_resume" id="if_resume" required>
                                                <option value="">Select</option>
                                                <option <?php if(isset($user_details->if_resume) && ($user_details->if_resume == 'yes')){ echo "selected"; } ?> value="yes">Yes</option>
                                                <option <?php if(isset($user_details->if_resume) && ($user_details->if_resume == 'no')){ echo "selected"; } ?> value="no">No</option>
                                                <option <?php if(isset($user_details->if_resume) && ($user_details->if_resume == 'optional')){ echo "selected"; } ?> value="optional">Optional</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>

                        <div class="row mb-3 after-add-more" style="display: none;" id="applcsnEmail">
                            <div class="col-xl-4 col-md-6">
                                <div class="single-inp">
                                    <label>Applications for this job will sent to following Email Address (es)</label>
                                    <div class="inp">
                                        <input type="text" id="applicants_email" name="applicants_email[]" value="<?php echo !empty($user_details->applicants_email)?$user_details->applicants_email:'';?>">
                                    </div>
                                </div>
                                <div class="input-group-btn">
                                    <button class="btn btn-success add-more" type="button"><i class="glyphicon glyphicon-plus"></i> +Add additional email address</button>
                                </div>
                            </div>

                            <div class="copy-fields hide clearfix">
                                <div class="control-group input-group" style="margin-top:10px">
                                    <div class="col-md-4">
                                        <input type="text" name="applicants_email[]" class="form-control">
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group-btn">
                                            <button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                    <div id="nextdiv" style="display:none;">
                        <label>Applicant Qualification <sup>*</sup></label><br>
                        <div class="bordered-sec">
                            <div class="row">
                                <div class="col-xl-2 col-md-6">
                                    <div class="single-inp">
                                        Minimum 
                                        <div class="inp">
                                            <div class="select">
                                                <select name="yr_of_exp" id="yr_of_exp" required>
                                                    <option value="">Select</option>
                                                    <option <?php if(isset($job_experience->yr_of_exp) && ($job_experience->yr_of_exp == '1')){ echo "selected"; } ?> value="1">1 Year</option>
                                                    <option <?php if(isset($job_experience->yr_of_exp) && ($job_experience->yr_of_exp == '2')){ echo "selected"; } ?> value="2">2 Year</option>
                                                    <option <?php if(isset($job_experience->yr_of_exp) && ($job_experience->yr_of_exp == '3')){ echo "selected"; } ?> value="3">3 Year</option>
                                                    <option <?php if(isset($job_experience->yr_of_exp) && ($job_experience->yr_of_exp == '4')){ echo "selected"; } ?> value="4">4 Year</option>
                                                    <option <?php if(isset($job_experience->yr_of_exp) && ($job_experience->yr_of_exp == '5')){ echo "selected"; } ?> value="5">5 Year</option>
                                                    <option <?php if(isset($job_experience->yr_of_exp) && ($job_experience->yr_of_exp == '6')){ echo "selected"; } ?> value="6">6 Year</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-1 col-md-6">of </div>
                                <div class="col-xl-2 col-md-6">
                                    <div class="single-inp">
                                        <div class="inp">
                                            <input type="text" id="exp_type" name="exp_type" value="<?php echo !empty($job_experience->exp_type)?$job_experience->exp_type:'';?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-2 col-md-6">experience </div>
                                <div class="col-xl-4 col-md-6">
                                    <div class="">
                                        <div class="inp">
                                            <input type="radio" <?php if(isset($job_experience->exp_cat) && ($job_experience->exp_cat == 'preferred')){ echo "checked"; } ?> name="exp_cat" value="preferred" required> Preferred
                                            <input type="radio" <?php if(isset($job_experience->exp_cat) && ($job_experience->exp_cat == 'required')){ echo "checked"; } ?> name="exp_cat" value="required" required> Required
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bordered-sec">
                            <div class="row">
                                <div class="col-xl-4 col-md-6">
                                    <div class="single-inp">
                                        <label>Minimum level of education: <sup>*</sup></label>
                                    </div>
                                </div>
                            
                                <div class="col-xl-4 col-md-6">
                                    <div class="single-inp">
                                        <div class="inp">
                                            <div class="select">
                                                <select name="min_education" id="min_education" required>
                                                    <option value="">Select</option>
                                                    <option <?php if(isset($job_education->min_education) && ($job_education->min_education == 'secondary')){ echo "selected"; } ?> value="secondary">Secondary (10th Pass)</option>
                                                    <option <?php if(isset($job_education->min_education) && ($job_education->min_education == 'higher_secondary')){ echo "selected"; } ?> value="higher_secondary">Higher Secondary (12th Pass)</option>
                                                    <option <?php if(isset($job_education->min_education) && ($job_education->min_education == 'graduate')){ echo "selected"; } ?> value="graduate">Graduate</option>
                                                    <option <?php if(isset($job_education->min_education) && ($job_education->min_education == 'btech')){ echo "selected"; } ?> value="btech">B-Tech</option>
                                                    <option <?php if(isset($job_education->min_education) && ($job_education->min_education == 'mba')){ echo "selected"; } ?> value="mba">MBA</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-6">
                                    <div class="">
                                        <div class="inp">
                                            <input type="radio" <?php if(isset($job_education->edu_cat) && ($job_education->edu_cat == 'preferred')){ echo "checked"; } ?> name="edu_cat" value="preferred" required> Preferred
                                            <input type="radio" <?php if(isset($job_education->edu_cat) && ($job_education->edu_cat == 'required')){ echo "checked"; } ?> name="edu_cat" value="required" required> Required
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bordered-sec">
                            <div class="row">
                                <div class="col-xl-4 col-md-6">
                                    <div class="single-inp">
                                        <label>Location in <sup>*</sup></label>
                                    </div>
                                </div>
                                    <!-- <span id="locationName"></span> -->
                                
                                <div class="col-xl-4 col-md-6">
                                    <div class="single-inp">
                                        <div class="inp">
                                            <input type="text" id="locationName" name="preferred_location" value="<?php echo !empty($job_preferred_location->preferred_location)?$job_preferred_location->preferred_location:'';?>" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-6">
                                    <div class="">
                                        <div class="inp">
                                            <input type="radio" <?php if(isset($job_preferred_location->location_cat) && ($job_preferred_location->location_cat == 'preferred')){ echo "checked"; } ?> name="location_cat" value="preferred" required> Preferred
                                            <input type="radio" <?php if(isset($job_preferred_location->location_cat) && ($job_preferred_location->location_cat == 'required')){ echo "checked"; } ?> name="location_cat" value="required" required> Required
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bordered-sec" style="display:none;">
                            <div class="row" >
                                <div class="col-xl-2 col-md-6">
                                    <div class="single-inp">
                                        Valid
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-6">
                                    <div class="single-inp">

                                        <div class="inp">
                                            <input type="text" id="licence" name="licence" value="<?php echo !empty($job_preferred_licence->licence)?$job_preferred_licence->licence:'';?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-2 col-md-6">Licence or Certification </div>
                                <div class="col-xl-4 col-md-6">
                                    <div class="">
                                        <div class="inp">
                                            <input type="radio" <?php if(isset($job_preferred_licence->licence_cat) && ($job_preferred_licence->licence_cat == 'preferred')){ echo "checked"; } ?> name="licence_cat" value="preferred"> Preferred
                                            <input type="radio" <?php if(isset($job_preferred_licence->licence_cat) && ($job_preferred_licence->licence_cat == 'required')){ echo "checked"; } ?> name="licence_cat" value="required"> Required
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bordered-sec">
                            <div class="row">
                                <div class="col-xl-4 col-md-6">
                                    <div class="single-inp">
                                            <label>Speaks the following language <sup>*</sup></label>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-6">
                                    <div class="single-inp">

                                        <div class="inp">
                                            <input type="text" id="language" name="language" value="<?php echo !empty($job_preferred_language->language)?$job_preferred_language->language:'';?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-6">
                                    <div class="">
                                        <div class="inp">
                                            <input type="radio" <?php if(isset($job_preferred_language->language_cat) && ($job_preferred_language->language_cat == 'preferred')){ echo "checked"; } ?> name="language_cat" value="preferred" required> Preferred
                                            <input type="radio" <?php if(isset($job_preferred_language->language_cat) && ($job_preferred_language->language_cat == 'required')){ echo "checked"; } ?> name="language_cat" value="required" required> Required
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bordered-sec">
                            <div class="row">
                                <div class="col-xl-6 col-md-6">
                                    <div class="single-inp">
                                        <label>Start Date <sup>*</sup></label>
                                        <div class="inp">                                            
                                            <input type="text" class="form-control datepicker" name="start_date" value="<?php echo !empty($user_details->start_date)?date('m-d-Y', strtotime($user_details->start_date)):''; ?>" required readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-md-6">
                                    <div class="single-inp">
                                        <label>End Date <sup>*</sup></label>
                                        <div class="inp">                                            
                                            <input type="text" class="form-control datepicker" name="end_date" value="<?php echo !empty($user_details->end_date)?date('m-d-Y', strtotime($user_details->end_date)):''; ?>" required readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="row">
                            <div class="col-xl-6 col-md-6">
                                <div class="form-group">
                                    <label>Status</label>     
                                        <div class="select">                         
                                            <select name="status">
                                            <option value="1" <?php if((isset($user_details->status) && ($user_details->status == 1)) || !isset($user_details->status)){ ?> selected <?php } ?>>Active</option>
                                            <option value="0" <?php if(isset($user_details->status) && ($user_details->status == 0)){ ?> selected <?php } ?>>Inactive</option> 
                                            </select>      
                                        </div> 
                                </div>
                            </div>
                        </div> -->

                    </div>


                    <?php

if(empty($user_details->latitude) && empty($user_details->longitude)){

$ip  = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
$url = "http://freegeoip.net/json/$ip";
$url = "http://api.ipstack.com/".$ip."?access_key=12eb68bfd242153290fd874dce2f9f58";
$ch  = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
$data = curl_exec($ch);
curl_close($ch);

if ($data) {
    $location = json_decode($data);

    $lat = $location->latitude;
    $lon = $location->longitude;
}
}else{
    $lat = $user_details->latitude;
    $lon = $user_details->longitude;
}
   ?>




                    <script type="text/javascript">
                        // this a jQuery event that is fired when document is ready. It allows us to start using useful jQuery selectors
                        $(document).ready(function() {
                            /*$('#start_timediv').datetimepicker({format: 'YYYY-MM-DD HH:mm:ss'});
                            $('#end_timediv').datetimepicker({format: 'YYYY-MM-DD HH:mm:ss'});*/
                            /*var start_time = $('#start_time').val();
                            var end_time = $('#end_time').val();
                            // 

                            if(start_time != ''){
                              $('#start_timediv').datetimepicker({format: 'MM-DD-YYYY HH:mm:ss',minDate: start_time});
                            }else{
                              $('#start_timediv').datetimepicker({format: 'MM-DD-YYYY HH:mm:ss',minDate: new Date()});
                            }

                            if(end_time != ''){
                              $('#end_timediv').datetimepicker({format: 'MM-DD-YYYY HH:mm:ss',minDate: end_time});
                            }else{
                              $('#end_timediv').datetimepicker({format: 'MM-DD-YYYY HH:mm:ss',minDate: new Date()});
                            }*/


                            // create a Google Maps map variable
                            var map;

                            // create a location variables
                            //    var myLocation = new google.maps.LatLng(45.53 , -73.62);
                            var myLocation = new google.maps.LatLng('<?php echo $lat; ?>', '<?php echo $lon; ?>');

                            // create a pop-up window variable
                            var myInfoWindow = new google.maps.InfoWindow();

                            // A function to create the marker and InfoWindow
                            function createMarkerAndInfoWindow(location, name, html) {
                                // create marker for location provided
                                var marker = new google.maps.Marker({
                                    position: location,
                                    map: map,
                                    title: 'This is the default tooltip!',
                                    draggable: true
                                });

                                // Add listener on market which will show infoWindow when clicked
                                google.maps.event.addListener(marker, "click", function() {
                                    myInfoWindow.setContent(html);
                                    myInfoWindow.open(marker.getMap(), marker);
                                });

                                // Add listener on 'drag end' event, add logitude and latitude to beginning of table
                                google.maps.event.addListener(marker, 'dragend', function(evt) {
                                    $('#latitude').val(evt.latLng.lat().toFixed(5));
                                    $('#longitude').val(evt.latLng.lng().toFixed(5));
                                    //        var textToInsert = '';  
                                    //        textToInsert = '<tr><td>' + evt.latLng.lat().toFixed(5) + '</td><td>' + evt.latLng.lng().toFixed(5) + '</td></tr>';     
                                    //        $("#myTable tbody").prepend(textToInsert);
                                });

                                // listener on drag event
                                google.maps.event.addListener(marker, 'dragstart', function(evt) {
                                    // nothing for now
                                });

                                return marker;
                            }

                            function initialize() {
                                var mapOptions = {
                                    zoom: 10,
                                    center: myLocation,
                                    draggable: true,
                                    mapTypeId: google.maps.MapTypeId.ROADMAP
                                };

                                // create the map
                                map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

                                // add marker
                                var marker = createMarkerAndInfoWindow(myLocation, "myMarkerName",
                                    "My Location");
                                marker.setMap(map);

                            }

                            // add a listener to the window object, which as soon as the load event is 
                            // triggered (i.e. "the page has finished loading") executes the function "initialize"
                            google.maps.event.addDomListener(window, 'load', initialize);



                            setTimeout(function() {}, 1000);

                        }); // end document ready


                        var placeSearch, autocomplete;
                        var componentForm = {
                            street_number: 'short_name',
                            route: 'long_name',
                            locality: 'long_name',
                            administrative_area_level_1: 'short_name',
                            country: 'long_name',
                            postal_code: 'short_name'
                        };

                        function getApplicationEmail(vals) {
                            if (vals == 'email') {
                                $('#applcsnEmail').show();
                            } else {
                                $('#applcsnEmail').hide();
                            }

                        }

                        function initAutocomplete() {
                            // Create the autocomplete object, restricting the search to geographical
                            // location types.
                            autocomplete = new google.maps.places.Autocomplete(
                                /** @type {!HTMLInputElement} */
                                (document.getElementById('autocomplete')), {
                                    types: ['geocode']
                                });

                            // When the user selects an address from the dropdown, populate the address
                            // fields in the form.
                            autocomplete.addListener('place_changed', fillInAddress);

                        }


                        function createMarkerAndInfoWindow1(location, name, html) {
                            // create marker for location provided
                            var marker = new google.maps.Marker({
                                position: location,
                                map: map,
                                title: 'This is the default tooltip!',
                                draggable: false
                            });

                            // Add listener on market which will show infoWindow when clicked
                            google.maps.event.addListener(marker, "click", function() {
                                myInfoWindow.setContent(html);
                                myInfoWindow.open(marker.getMap(), marker);
                            });

                            // Add listener on 'drag end' event, add logitude and latitude to beginning of table
                            google.maps.event.addListener(marker, 'dragend', function(evt) {
                                $('#latitude').val(evt.latLng.lat().toFixed(5));
                                $('#longitude').val(evt.latLng.lng().toFixed(5));
                                //        var textToInsert = '';  
                                //        textToInsert = '<tr><td>' + evt.latLng.lat().toFixed(5) + '</td><td>' + evt.latLng.lng().toFixed(5) + '</td></tr>';     
                                //        $("#myTable tbody").prepend(textToInsert);
                            });

                            // listener on drag event
                            google.maps.event.addListener(marker, 'dragstart', function(evt) {
                                // nothing for now
                            });

                            return marker;
                        }

                        function fillInAddress() {
                            // Get the place details from the autocomplete object.
                            var place = autocomplete.getPlace();
                            //var lat = place.geometry.location.lat();
                            //var lat = place.geometry.location.lng();

                            $('#latitude').val(place.geometry.location.lat().toFixed(5));
                            $('#longitude').val(place.geometry.location.lng().toFixed(5));

                            var myLocation = new google.maps.LatLng(place.geometry.location.lat().toFixed(5), place.geometry.location.lng().toFixed(5));
                            var mapOptions = {
                                zoom: 10,
                                center: myLocation,
                                draggable: false,
                                mapTypeId: google.maps.MapTypeId.ROADMAP
                            };

                            $('#map-canvas').html('');
                            // create the map
                            map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

                            // add marker
                            var marker = createMarkerAndInfoWindow1(myLocation, "myMarkerName",
                                "My Location");

                            /*for (var component in componentForm) {
                              document.getElementById(component).value = '';
                              document.getElementById(component).disabled = false;
                            }

                            // Get each component of the address from the place details
                            // and fill the corresponding field on the form.
                            for (var i = 0; i < place.address_components.length; i++) {
                              var addressType = place.address_components[i].types[0];


                              if (componentForm[addressType]) {
                                var val = place.address_components[i][componentForm[addressType]];
                                document.getElementById(addressType).value = val;

                              }
                            }*/

                        }

                        // Bias the autocomplete object to the user's geographical location,
                        // as supplied by the browser's 'navigator.geolocation' object.
                        function geolocate() {

                            if (navigator.geolocation) {
                                navigator.geolocation.getCurrentPosition(function(position) {
                                    var geolocation = {
                                        lat: position.coords.latitude,
                                        lng: position.coords.longitude
                                    };
                                    var circle = new google.maps.Circle({
                                        center: geolocation,
                                        radius: position.coords.accuracy
                                        //radius: parseInt(document.getElementById('jurisdiction_radius').value)*1609.34
                                    });
                                    autocomplete.setBounds(circle.getBounds());
                                });
                            }

                        }

                        function addCircle() {

                            if (document.getElementById('jurisdiction_radius').value != '' && document.getElementById('latitude').value != '' && document.getElementById('longitude').value != '') {
                                var redious = parseFloat(document.getElementById('jurisdiction_radius').value) * 1609.34;

                                var myLocation = {
                                    lat: parseFloat(document.getElementById('latitude').value),
                                    lng: parseFloat(document.getElementById('longitude').value)
                                };

                                var mapOptions = {
                                    zoom: 10,
                                    center: myLocation,
                                    draggable: false,
                                    mapTypeId: google.maps.MapTypeId.ROADMAP
                                };

                                $('#map-canvas').html('');
                                // create the map
                                map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);



                                // Add the circle for this city to the map.    
                                var cityCircle = new google.maps.Circle({
                                    strokeColor: '#FF0000',
                                    strokeOpacity: 0.8,
                                    strokeWeight: 2,
                                    fillColor: '#FF0000',
                                    fillOpacity: 0.35,
                                    map: map,
                                    center: myLocation,
                                    radius: redious,
                                    draggable: false
                                });

                                var marker = new google.maps.Marker({
                                    position: myLocation,
                                    map: map
                                });

                            }

                        }

                    </script>
                    <script type="text/javascript">
                        $(document).ready(function() {

                            //here first get the contents of the div with name class copy-fields and add it to after "after-add-more" div class.
                            $(".add-more").click(function() {
                                var html = $(".copy-fields").html();
                                $(".after-add-more").after(html);
                            });
                            //here it will remove the current value of the remove button which has been pressed
                            $("body").on("click", ".remove", function() {
                                $(this).parents(".control-group").remove();
                            });

                        });

                    </script>


                    <script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBcicAVTh7Y8lz2x_1QGODkDq4w0lh0imk&libraries=places&callback=initAutocomplete" type="text/javascript"></script>





                    <div class="box-footer">
                        <a href="{{ url('/admin/job') }}" class="btn btn-danger">Cancel</a>
                        <button id="submit_id" type="submit" class="btn btn-success pull-right" style="display:none;margin-left:10px;">Save</button>
                        <button id="btn_id" type="button" class="btn btn-success pull-right" onclick="next_fields();">Next</button>
                    </div>
                </div>

            </div>



        </div>
    </form>
</div>

<script>
    $(function() {
        $( ".datepicker" ).datepicker({
            dateFormat: 'mm-dd-yy'

        });
    });

    function next_fields() {
        $('#first_div').hide();
        $('#nextdiv').show();
        var locationName = $('.location').val();
        $('#locationName').val(locationName);
        document.getElementById('btn_id').setAttribute('onclick', 'prev_fields()')
        $('#btn_id').html('Previous');
        $('#submit_id').show();
    }

    function prev_fields() {
        $('#first_div').show();
        $('#nextdiv').hide();
        document.getElementById('btn_id').setAttribute('onclick', 'next_fields()')
        $('#btn_id').html('Next');
        $('#submit_id').show();
    }
    $(document).ready(function() {
        CKEDITOR.replace('summary');
        CKEDITOR.replace('responsibilities');
        CKEDITOR.replace('skills_qualification');
        CKEDITOR.replace('benefits');
    });

</script>
@endsection
