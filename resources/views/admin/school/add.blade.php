@extends('layouts.admin') @section('content')

<style type="text/css">
    .card-body {
        background: #27528c;
    }

    .card-header {
        background: #00a7a8;
    }

    .db-container .form-section .btn-link {
        color: #fff;
        text-decoration: none;
    }

    .db-container .form-section .accordion .btn-link:hover {
        color: #fff;

    }


    p.arrow {
        float: right;
        margin-top: 8px;
    }

    p.arrow {
        position: absolute;
        top: 5px;
        right: 20px;
        font-size: 20px;
        color: white;
        -webkit-animation: minus 0.5s;
    }

    @keyframes minus {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    [aria-expanded="false"]>span.expanded,
    [aria-expanded="true"]>span.collapsed {
        display: none;
    }

</style>
<div class="db-inner-content">
    <form role="form" action="{{ url('/admin/school/save') }}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}

        <input type="hidden" id="id" name="id" value="<?php echo !empty($school_arr->id)?$school_arr->id:0;?>">
        <input type="hidden" name="" id="auth_type" value="<?php echo Auth::user()->type; ?>">
        
        <div class="db-box">

            <div class="heading-sec">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h3>School</h3>
                    </div>
                    <div class="col-lg-6">
                        <a href="{{ url('/admin/active-school') }}" class="back-btn"><i class="fa fa-arrow-left"></i></a>
                    </div>
                </div>
            </div>
            <div class="box-inner">
                <div class="form-section">
                    <div class="row">
                        <div class="col-sm-8">
                            <h3><?php if(empty($school_arr->id)){?>Create<?php }else{?>Edit<?php }?> School</h3>
                        </div>
                        <div class="col-sm-4 text-right">
                            <div class="check active-toggle">
                                <label>
                                    <input type="checkbox" name="status" id="is_active" value="1"  <?php if((isset($school_arr->status) && ($school_arr->status == 1)) || !isset($school_arr->status)){ ?>checked=""<?php } ?>> <span id="check_title"> <?php if((isset($school_arr->status) && ($school_arr->status == 1)) || !isset($school_arr->status)){ ?>Active <?php }else{ ?>Inactive <?php } ?></span>
                                </label>
                            </div>
                        </div>
                    </div>
                          

                                    
                    <?php if(!empty(session('success_message'))){ ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
                        <h4 style="margin-bottom: 0"><i class="icon fa fa-check"></i>
                            <?php 
                                echo session('success_message'); 
                                Session::forget('success_message');
                            ?>
                        </h4>
                    </div>
                    <?php } ?>
                    <?php if(!empty(session('error_message'))){ ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
                        <h4 style="margin-bottom: 0"><i class="icon fa fa-warning"></i>
                            <?php 
                                echo session('error_message'); 
                                Session::forget('error_message');
                            ?>
                        </h4>
                    </div>
                    <?php } ?> @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
                        <!--{{ implode('', $errors->all(':message')) }}-->
                        @foreach ($errors->all() as $error)
                        <h4>{{ $error }}</h4>
                        @endforeach
                    </div>
                    @endif

                                        
                    <div class="row">                                                
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Name <sup style="color: #ff6c6c;">*</sup></label>
                                <input type="text" value="<?php echo !empty($school_arr->name)?$school_arr->name:'';?>" class="form-control" name="name" id="name" required="required">
                            </div>
                        </div>
                    </div>
                    <div class="row"> 
                        <?php if(Auth::user()->type == 2){?>
                        <div class="col-md-12">
                        <?php }else{?>
                        <div class="col-md-6">
                        <?php }?>
                            <div class="form-group">
                                <label>Address <sup style="color: #ff6c6c;">*</sup></label>
                                <textarea class="form-control" rows="1" name="address" id="autocomplete" onfocus="geolocate();"><?php echo !empty($school_arr->address)?$school_arr->address:'';?></textarea>
                                <input type="hidden" id="latitude" name="latitude" value="<?php echo !empty($school_arr->latitude)?$school_arr->latitude:'';?>">
                                <input type="hidden" id="longitude" name="longitude" value="<?php echo !empty($school_arr->longitude)?$school_arr->longitude:'';?>">
                            </div>
                        </div>
                        <?php if(Auth::user()->type != 2){?>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Select Affiliate <sup style="color: #ff6c6c;">*</sup></label>
                                <div class="select">
                                    <select name="agency_id" id="agency_id">
                                        <option value="">Choose an affiliate</option>
                                        <?php if(!empty($agency_arr)){ foreach($agency_arr as $ag){?>
                                        <option value="<?php echo $ag->id; ?>" <?php if(!empty($school_arr->agency_id) && ($school_arr->agency_id) == $ag->id){?> selected <?php }?>><?php echo $ag->name; ?></option>
                                        <?php }}?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <?php }else{?>
                        <input type="hidden" name="agency_id" value="<?php echo Auth::user()->id; ?>">
                        <?php }?>
                    </div>
                    <div class="row">                                                
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>City </label>
                                <input type="text" readonly="readonly" value="<?php echo !empty($school_arr->city)?$school_arr->city:'';?>" class="form-control" name="city" id="city">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>State </label>
                                <input type="text" readonly="readonly" value="<?php echo !empty($school_arr->state)?$school_arr->state:'';?>" class="form-control" name="state" id="state">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Zip </label>
                                <input type="text" maxlength="10" value="<?php echo !empty($school_arr->zip)?$school_arr->zip:'';?>" class="form-control" name="zip" id="zip" onkeyup="this.value = this.value.replace(/\D/g,'');" onkeydown="this.value = this.value.replace(/\D/g,'');">
                            </div>
                        </div>
                    </div>
                    
                    <div class="box-footer">
                        <button id="" type="submit" onclick="return formsubmit();" class="btn btn-success">Save</button>
                        <a href="{{ url('/admin/active-school') }}" class="btn btn-danger">Cancel</a>                       
                    </div>

                </div>

            </div>
        </div>
    </form>
</div>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/js/tempusdominus-bootstrap-4.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/css/tempusdominus-bootstrap-4.min.css" />

<style type="text/css">
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        margin: 0; 
    }
</style>
<script>
   
    function formsubmit() {
        var auth_type = $('#auth_type').val();
        var agency_id = $('#agency_id').val();
        var name = $('#name').val();        
        var autocomplete = $('#autocomplete').val();
        var latitude = $('#latitude').val();
        var longitude = $('#longitude').val();

        // alert(agency_id);
        
        if(name == ''){
            swal('Name is required');
            return false;
        }else if (autocomplete == '') {
            swal('Address is required');
            return false;
        }else if (autocomplete != '' && latitude == '' && longitude == '') {
            swal('Please put proper address');
            return false;
        }else if (agency_id == '') {
            swal('Please choose an agency');
            return false;
        }
        return true;
    }

    $('#is_active').click(function() {
        if ($(this).is(':checked'))
            $('#check_title').text('Active');
        else
            $('#check_title').text('Inactive');
    });

</script>
<script type="text/javascript">
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
            /** @type  {!HTMLInputElement} */(document.getElementById('autocomplete')),
            {types: ['geocode']});

        // When the user selects an address from the dropdown, populate the address
        // fields in the form.
        autocomplete.addListener('place_changed', fillInAddress);
    }

      

      

    function fillInAddress() {

        // Get the place details from the autocomplete object.
        var place = autocomplete.getPlace();

        $('#latitude').val(place.geometry.location.lat().toFixed(5));
        $('#longitude').val(place.geometry.location.lng().toFixed(5));

        var myLocation = new google.maps.LatLng(place.geometry.location.lat().toFixed(5) , place.geometry.location.lng().toFixed(5));
        var mapOptions = {
            zoom : 10,
            center : myLocation,
            draggable: true,
            mapTypeId : google.maps.MapTypeId.ROADMAP
        };


        /*For City Country*/

        var components = place.address_components;

        for (var i = 0, component; component = components[i]; i++) {
            if (component.types[0] == 'locality') {
                $('#city').val(component['long_name'])
            }
            if (component.types[0] == 'administrative_area_level_1') {
                $('#state').val(component['short_name'])
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
                });
                autocomplete.setBounds(circle.getBounds());
            });
        }
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBcicAVTh7Y8lz2x_1QGODkDq4w0lh0imk&libraries=places&callback=initAutocomplete" async defer></script>


@endsection
