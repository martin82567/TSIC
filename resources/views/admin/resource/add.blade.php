@extends('layouts.admin') @section('content')


<div class="db-inner-content">
    <form role="form" action="{{ route('admin.resource.save')}}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" id="id" name="id" value="<?php echo !empty($user_details->id)?$user_details->id:0;?>">
        <div class="db-box">
            <div class="heading-sec">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <h3>Resource</h3>

                        <a href="{{ url('/admin/resource') }}" class="back-btn" style="display: inline;float: right;"><i class="fa fa-arrow-left"></i></a>
                    </div>
                </div>
            </div>
            <?php if(!empty(session('success_message'))){ ?>
            <div class="alert alert-<?php echo session('success_color');?> alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
                <h4 style="margin-bottom: 0"><i class="icon fa fa-<?php echo session('success_icon'); ?>"></i>
                    <?php echo session('success_message'); 
                                    Session::forget('success_message');?>
                </h4>
            </div>
            <?php } ?> @if ($errors->any())
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
                    <div class="row mb-3 float-right">
                        <div class="col-lg-12">
                            <div class="check active-toggle">
                                <label>
                            <input type="checkbox" name="is_active" id="is_active" value="1"  <?php if((isset($user_details->is_active) && ($user_details->is_active == 1)) || !isset($user_details->is_active)){ ?>checked=""<?php } ?>> <span id="check_title"> <?php if((isset($user_details->is_active) && ($user_details->is_active == 1)) || !isset($user_details->is_active)){ ?>Active <?php }else{ ?>Inactive <?php } ?></span>
                                                </label>
                            </div>
                        </div>
                    </div>
                    <h3>Create resource</h3>

                    <br>
                    <div class="row mb-3">
                        <div class="col-xl-4 col-md-4">
                            <div class="single-inp">
                                <label>Name <sup>*</sup></label>
                                <div class="inp">
                                    <input type="text" id="name" name="name" value="<?php echo !empty($user_details->name)?$user_details->name:'';?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-4">
                            <div class="single-inp">
                                <label>Email <sup>*</sup></label>
                                <div class="inp">
                                    <input type="email" id="email" name="email" value="<?php echo !empty($user_details->email)?$user_details->email:'';?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-4">
                            <div class="single-inp">
                                <label>Password <?php if(empty($user_details->password)){ ?> <sup>*</sup> <?php } ?></label>
                                <div class="inp">
                                    <input type="password" id="password" name="password" <?php if(empty($user_details->password)){ ?> required
                                    <?php } ?>>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="profile-form">
                        <div class="row mb-3">
                            <div class="col-xl-6 col-md-6">
                                <div class="single-inp">
                                    <label>Picture</label>
                                    <div class="file-upload">
                                        <div class="file-select">
                                            <div class="file-select-button" id="fileName"><img src="http://209.59.156.100/~tsicdev/assets/images/upload_icon.png"></div>
                                            <div class="file-select-name" id="">Click files to upload</div>
                                            <input type="file" name="profile_pic" id="profile_pic" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                       
                            <?php if(!empty($user_details->profile_pic)){?>
                            <div class="col-xl-6 col-md-6">
                                <div class="form-group">
                                    <label>Picture Url</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control input-lg" disabled="disabled" value="<?php echo $user_details->profile_pic; ?>">
                                        <ul>
                                            <li><a href="<?php echo url('public/uploads/resource_pic/'.$user_details->profile_pic);?>" target="_blank"><i class="fa fa-eye"></i></a></li>
                                            <li><a href="<?php echo url('/admin/resource/remove_pic/'.$user_details->id.'/'.$user_details->profile_pic); ?>"><i class="fa fa-trash"></i></a></li>
                                        </ul>
                                    </div>
                                </div>                            
                            </div>                            
                            <?php }?>
                        </div>
                    </div>




                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cell Phone  </label>
                                <input class="form-control" type="text" id="cell_phone" name="cell_phone" value="<?php echo !empty($user_details->cell_phone)?$user_details->cell_phone:'';?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Work Phone  <sup style="color: Red;">*</sup></label>
                                <input class="form-control" type="text" id="work_phone" name="work_phone" value="<?php echo !empty($user_details->work_phone)?$user_details->work_phone:'';?>" require>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Website </label>
                                <input class="form-control" type="url" id="website" name="website" value="<?php echo !empty($user_details->website)?$user_details->website:'';?>" onblur="is_url('website');">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fax  </label>
                                <input class="form-control" type="text" id="fax" name="fax" value="<?php echo !empty($user_details->fax)?$user_details->fax:'';?>">
                            </div>
                        </div>
                    </div>


                    <div class="row mb-3">
                        <div class="col-lg-6"></div>
                    </div>

                    <div class="form-group" style="display:none;">
                        <label>Map Type <span style="color: Red;">*</span></label>
                        <div class="radio">
                            <label>
                                            <input class="" name="map_type" id="map_type1" value="2" <?php if(!empty($user_details->map_type) && $user_details->map_type == "2"){ ?>  checked=""<?php } ?> type="radio" onclick="show_hide(2)">Polygon
                                        </label>
                            <label>
                                        <input class="" name="map_type" id="map_type2" value="1" checked="" type="radio" onclick="show_hide(1)">Circular
                                        </label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description / Notes  <span style="color: red;">*</span></label>
                                <textarea class="form-control" rows="2" name="description" placeholder="Enter Description" id="description" required><?php echo !empty($user_details->description)?$user_details->description:""; ?></textarea>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Zip Code </label>
                                <input class="form-control" id="zipcode" name="zipcode" type="text" value="<?php echo !empty($user_details->zipcode)?$user_details->zipcode:" "; ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Address <span style="color: red;">*</span></label>
                                <input class="form-control" value="<?php echo !empty($user_details->address)?$user_details->address:" "; ?>" type="text" id="autocomplete" placeholder="Search Address" onFocus="geolocate();"><br>
                                <textarea style="display: none;" class="form-control" id="address" rows="1" name="address" placeholder="Enter location name"><?php echo !empty($user_details->address)?$user_details->address:""; ?></textarea>
                                <input class="form-control" id="latitude" name="latitude" type="hidden" step="any" value="<?php echo !empty($user_details->latitude)?$user_details->latitude:" "; ?>" required>
                                <input class="form-control" id="longitude" name="longitude" type="hidden" step="any" value="<?php echo !empty($user_details->longitude)?$user_details->longitude:" "; ?>" required>
                                <input class="form-control" id="city" name="city" type="hidden" step="any" value="<?php echo !empty($user_details->city)?$user_details->city:" "; ?>">
                                <input class="form-control" id="state" name="state" type="hidden" step="any" value="<?php echo !empty($user_details->state)?$user_details->state:" "; ?>">
                                <input class="form-control" id="country" name="country" type="hidden" step="any" value="<?php echo !empty($user_details->country)?$user_details->country:" "; ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Resource Category <span style="color: red;">*</span></label>
                                <div class="select">
                                    <select name="resource_category" required>
                                                        <option value="">Select Resource Category</option>
                                                        <?php foreach($resource_category_details as $resource_category){ ?>
                                                            <option value="<?php echo $resource_category->id; ?>" <?php if(isset($user_details->resource_category) && ($user_details->resource_category == $resource_category->id)){ ?> selected="" <?php } ?>><?php echo $resource_category->name; ?></option>
                                                        <?php } ?>
                                                    </select>
                                </div>
                            </div>
                        </div>
                        
                    </div>


                    
                    <div class="form-group">
                       
                        <div class="listing-table">
                            <div class="table-responsive text-center">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Document Type</th>
                                            <th scope="col">Document Name</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(!empty($resource_files_details)){ ?>
                                        <?php $i = 1; foreach($resource_files_details as $file_details){ ?>

                                        <tr>
                                            <td scope="row">
                                                <?php echo $i; ?>
                                            </td>
                                            <td>
                                                <?php echo $file_details->document_type_name; ?>
                                            </td>
                                            <td>
                                                <?php echo $file_details->file_name; ?>
                                            </td>
                                            <td><a class="btn btn-success btn-sm" href="<?php echo url('public/uploads/documents/'.$file_details->file_name); ?>" target="_blank"><i class="fa fa-eye"></i></a></a>&nbsp;&nbsp;
                                                <a class="btn btn-danger btn-sm" href="<?php echo url('/admin/resource/delete_file/'.$file_details->id.'/'.$file_details->resource.'/'.$file_details->file_name); ?>"><i class="fa fa-trash"></i></a></a></td>
                                        </tr>

                                        <?php $i++; } ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    


                    <div class="input-group control-group after-add-more row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>File Upload</label>
                                <div class="select">
                                    <select name="document_type[]">
                                        <?php foreach($document_type_details as $document_type){  ?>
                                            <option value="<?php echo $document_type->id; ?>" ><?php echo $document_type->name; ?></option>
                                        <?php } ?>
                                        </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label>Upload Picture</label>
                                <div class="file-upload">
                                    <div class="file-select">
                                        <div class="file-select-button" id="fileName"><img src="{{url('/assets/images/upload_icon.png')}}"></div>
                                        <div class="file-select-name" id="">Click files to upload</div>
                                        <input type="file" name="images[]" id="chooseFile">
                                    </div>
                                </div>
                                <div class="input-group-btn">
                                    <button class="btn btn-success add-more" type="button"><i class="glyphicon glyphicon-plus"></i> Add</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="copy-fields hide clearfix">
                        <div class="control-group input-group row" style="margin-top:10px">

                            <div class="col-lg-4">
                                <label>File Upload</label>
                                <div class="select">
                                    <select name="document_type[]">
                                        <?php foreach($document_type_details as $document_type){ ?>
                                            <option value="<?php echo $document_type->id; ?>" ><?php echo $document_type->name; ?></option>
                                        <?php } ?>
                                        </select>
                                </div>
                            </div>
                            <div class="col-lg-5">

                                <div class="form-group">
                                    <label>Upload Picture</label>
                                    <div class="file-upload">
                                        <div class="file-select">
                                            <div class="file-select-button" id="fileName"><img src="{{url('/assets/images/upload_icon.png')}}"></div>
                                            <div class="file-select-name" id="">Click files to upload</div>
                                            <input type="file" name="images[]" id="chooseFile">
                                        </div>
                                    </div>
                                    <div class="input-group-btn">
                                        <button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
                                    </div>
                                </div>

                            </div>



                            <!-- <input type="file" name="images[]" class="form-control">
                                    <div class="input-group-btn"> 
                                    <button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
                                    </div> -->

                        </div>
                    </div>
                    <h3></h3>



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
                            
                            var placeSearch, autocomplete;
                            var componentForm = {
                                street_number: 'short_name',
                                route: 'long_name',
                                locality: 'long_name',
                                administrative_area_level_1: 'short_name',
                                country: 'long_name',
                                postal_code: 'short_name'
                            };



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


                            
                            function fillInAddress() {
                                // Get the place details from the autocomplete object.
                                var place = autocomplete.getPlace();
                                //var lat = place.geometry.location.lat();
                                //var lat = place.geometry.location.lng();

                                $('#latitude').val(place.geometry.location.lat().toFixed(5));
                                $('#longitude').val(place.geometry.location.lng().toFixed(5));
                                $('#address').val($('#autocomplete').val());

                                var myLocation = new google.maps.LatLng(place.geometry.location.lat().toFixed(5), place.geometry.location.lng().toFixed(5));
                                var mapOptions = {
                                    zoom: 10,
                                    center: myLocation,
                                    draggable: false,
                                    mapTypeId: google.maps.MapTypeId.ROADMAP
                                };

                                /*For City Country*/

                                $('#city').val('');
                                $('#state').val('');
                                $('#country').val('');

                                var components = place.address_components;

                                for (var i = 0, component; component = components[i]; i++) {
                                    if (component.types[0] == 'locality') {
                                        $('#city').val(component['long_name'])
                                        console.log('city:-' +component['long_name']);
                                    }
                                    if (component.types[0] == 'administrative_area_level_1') {
                                        $('#state').val(component['short_name'])
                                        console.log('state:-' +component['short_name']);
                                    }
                                    if (component.types[0] == 'country') {
                                        $('#country').val(component['short_name'])
                                        console.log('country:-' +component['short_name']);
                                    }
                                    
                                }

                                /*===*/

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
                                $('#cell_phone').mask('(000) 000-0000');
                                $('#work_phone').mask('(000) 000-0000');
                                $('#fax').mask('(000) 000-0000');
                            });


                            $('#is_active').click(function() {
                                if ($(this).is(':checked'))
                                    $('#check_title').text('Active');
                                else
                                    $('#check_title').text('Inactive');
                            });

                        </script>


                        <script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBcicAVTh7Y8lz2x_1QGODkDq4w0lh0imk&libraries=places&callback=initAutocomplete" type="text/javascript"></script>







                        <div class="box-footer">
                            <button type="submit" class="btn btn-success">Submit</button>
                            <a href="{{ url('/admin/resource') }}" class="btn btn-danger">Cancel</a>
                        </div>
                </div>

            </div>


    </form>
    </div>
</div>
@endsection
