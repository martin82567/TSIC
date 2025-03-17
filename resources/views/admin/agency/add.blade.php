@extends('layouts.admin') @section('content')


<div class="db-inner-content">
    <form role="form" action="{{ route('admin.agency.save')}}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" id="id" name="id" value="<?php echo !empty($user_details->id)?$user_details->id:0;?>">
        
        <input type="hidden" name="type" value="<?php echo $menu_type; ?>" />
        <div class="db-box">
            <div class="heading-sec">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h3>
                            <?php if($menu_type == 'agency'){?>Affiliate
                            <?php }else{?>Staff
                            <?php }?>
                        </h3>
                    </div>
                    <div class="col-lg-6">
                        <?php if($menu_type == 'agency'){?>
                        <a href="{{ url('/admin/agency') }}" class="back-btn"><i class="fa fa-arrow-left"></i></a>
                        <?php }else{?>
                        <a href="{{ url('/admin/user') }}" class="back-btn"><i class="fa fa-arrow-left"></i></a>
                        <?php }?>
                    </div>
                </div>
            </div>
            <?php if(!empty(session('success_message'))){ ?>
            <div class="alert alert-<?php echo session('success_color'); ?> alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
                <h4 style="margin-bottom: 0"><i class="icon fa fa-<?php echo session('success_icon'); ?>"></i>
                    <?php 
                                    echo session('success_message'); 
                                    Session::forget('success_message');
                                ?>
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
                    <div class="row mb-2 clearfix ">
                        <div class="col-sm-8">
                            <h3>
                                <?php if(empty($user_details->id)){?>
                                Create
                                <?php }else{?>
                                Edit
                                <?php }?>
                                <?php if($menu_type == 'agency'){?>Affiliate
                                <?php }else{?>Staff
                                <?php }?>
                            </h3>
                        </div>
                        <div class="col-sm-4 text-right">
                            <div class="check active-toggle">
                                <label>
                                    <input type="checkbox" name="is_active" id="is_active" value="1"  <?php if((isset($user_details->is_active) && ($user_details->is_active == 1)) || !isset($user_details->is_active)){ ?>checked=""<?php } ?>> <span id="check_title"> <?php if((isset($victim->status) && ($victim->status == 1)) || !isset($victim->status)){ ?>Active <?php }else{ ?>Inactive <?php } ?></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <?php if($menu_type == 'user'){
                        if(Auth::user()->type == 1){
                        ?>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Choose an affiliate or admin <sup>*</sup> </label>
                            <div class="select">
                                <select name="parent_id" id="parent_id" required="required" style="color: #333;">
                                    <option value="">Choose one</option>
                                    <?php if(!empty($agency_arr)){ foreach($agency_arr as $ag){?>
                                    <option value="<?php echo $ag->id; ?>" <?php if(!empty($user_details->parent_id) && ($user_details->parent_id == $ag->id)){?> selected <?php }?>><?php echo $ag->name; ?></option>
                                    <?php }}?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php }}?>

                    <div class="row mb-3">
                        <div class="col-xl-4 col-md-4">
                            <div class="single-inp">
                                <label>Name <sup>*</sup></label>
                                <div class="inp">
                                    <input type="text" id="name" name="name" value="<?php echo !empty($user_details->name)?$user_details->name:'';?>" maxlength="60" required>
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

                    <?php if($menu_type == 'agency'){?>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cell Phone  </label>
                                <input class="form-control" type="text" id="cell_phone" name="cell_phone" value="<?php echo !empty($user_details->cell_phone)?$user_details->cell_phone:'';?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Work Phone  </label>
                                <input class="form-control" type="text" id="work_phone" name="work_phone" value="<?php echo !empty($user_details->work_phone)?$user_details->work_phone:'';?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Website </label>
                                <input class="form-control" type="text" id="website" name="website" value="<?php echo !empty($user_details->website)?$user_details->website:'';?>" onblur="is_url('website');">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Fax  </label>
                                <input class="form-control" type="text" id="fax" name="fax" value="<?php echo !empty($user_details->fax)?$user_details->fax:'';?>">
                            </div>
                        </div>
                    </div>
                    <?php }?>

                    <?php if($menu_type == 'user'){?>
                    <label>User Permissions</label>
                    <div class="row mb-3">
                        <div class="col-lg-6">
                            <div class="check">
                                <label>
                                    <input type="checkbox" name="access_mentor" value="1" <?php if((isset($user_details->access_mentor) && ($user_details->access_mentor == 1)) ){ ?> checked=""<?php } ?> >Allow Mentor                                        
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="check">
                                <label>
                                    <input type="checkbox" name="access_victim" value="1" <?php if((isset($user_details->access_victim) && ($user_details->access_victim == 1)) ){ ?> checked=""<?php } ?> >Allow Mentee                                        
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="check">
                                <label>
                                    <input type="checkbox" name="access_goal_task_challenge" value="1" <?php if((isset($user_details->access_goal_task_challenge) && ($user_details->access_goal_task_challenge == 1)) ){ ?> checked=""<?php } ?> >Allow Goal/Task                                       
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="check">
                                <label>
                                    <input type="checkbox" name="access_e_learning" value="1" <?php if((isset($user_details->access_e_learning) && ($user_details->access_e_learning == 1)) ){ ?> checked=""<?php } ?> >Allow Resource                                        
                                </label>
                            </div>
                        </div> 

                        <div class="col-lg-6">
                            <div class="check">
                                <label>
                                    <input type="checkbox" name="access_meeting" value="1" <?php if((isset($user_details->access_meeting) && ($user_details->access_meeting == 1)) ){ ?> checked=""<?php } ?> >Allow Session                                        
                                </label>
                            </div>
                        </div>                        
                        
                    </div>
                    <?php }?>



                    <div class="profile-form">
                        <div class="row align-items-center mb-3">
                            <div class="col-md-6">
                                <div class="single-inp">

                                    <div class="file-upload">
                                        <div class="file-select">
                                            <div class="file-select-button" id="fileName"><img src="{{url('/assets/images/upload_icon.png')}}"></div>
                                            <div class="file-select-name" id="">Click here to upload profile image</div>
                                            <input type="file" name="profile_pic" id="profile_pic">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if(!empty($user_details->profile_pic)){?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Profile Picture </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control input-lg" value="<?php echo !empty($user_details->profile_pic)?$user_details->profile_pic:''; ?>">
                                        <ul>
                                            <li>
                                                <a href="<?php echo config('app.aws_url');  ?>agency_pic/{{$user_details->profile_pic}}" target="_blank"><i class="fa fa-eye"></i></a>
                                            </li>
                                            <li>
                                                <a href="<?php echo url('/admin/agency/remove_pic/'.$user_details->id.'/'.$user_details->profile_pic.'/'.$menu_type); ?>"><i class="fa fa-trash"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                
                            </div>
                            <?php }?>
                        </div>
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
                    <?php if($menu_type != 'user'){?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description / Notes</label>
                                <textarea class="form-control" rows="2" name="description" placeholder="Enter Description" id="description"><?php echo !empty($user_details->description)?$user_details->description:""; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3" style="display:none;">
                            <div class="form-group">
                                <label for="">State <span style="color: red;">*</span></label>
                                <div class="select">
                                    <select name="state" id="state" onchange="return getcity(this.value);">
                                        <option value="">Choose A State</option>
                                        <?php foreach($states as $state_name){?>
                                        <option <?php if(isset($user_details->state) && ($user_details->state == $state_name->state_code)){ echo "selected"; } ?> value="<?php echo $state_name->state_code;?>"><?php echo $state_name->state;?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3" style="display:none;">
                            <div class="form-group">
                                <label>City </label>
                                <div class="select">
                                    <select name="city" id="city">
                                                        <option value="">Choose A City</option> 
                                                        <?php if(!empty($user_details->city)){?> 
                                                        <option value="<?php echo $user_details->city; ?>" selected><?php echo $user_details->city; ?></option>
                                                        <?php }?>
                                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3" style="display:none;">
                            <div class="form-group">
                                <label>Country </label>
                                <input class="form-control" readonly="readonly" id="country" name="country" type="text" value="<?php echo !empty($user_details->country)?$user_details->country:" USA "; ?>">
                            </div>
                        </div>

                    </div>


                    <div class="row">
                        <?php $timezone_access = 0;
                        if(Auth::user()->type == 1){
                            $timezone_access = 1;
                        }else if(Auth::user()->type == 3 && Auth::user()->created_by == 1){
                            $timezone_access = 1;
                        }
                        ?>
                        <?php if(!empty($timezone_access)){?>
                            <div class="col-md-3">
                            <?php }else{?>
                            <div class="col-md-6">
                                <?php }?>
                                <div class="form-group">
                                    <label>Zip Code </label>
                                    <input class="form-control" id="zipcode" name="zipcode" type="text" value="<?php echo !empty($user_details->zipcode)?$user_details->zipcode:" "; ?>">
                                </div>
                            </div>

                            <?php if(!empty($timezone_access)){?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Timezones <span style="color: red;">*</span> </label>
                                    <div class="select">
                                        <select name="timezone" required="required">
                                            <option value="">Select a timezone</option>
                                            @if(!empty($timezones))
                                            @foreach($timezones as $tz)
                                            <option value="{{ $tz->value }}" @if(!empty($user_details->timezone)) @if($user_details->timezone == $tz->value) selected @endif @endif  >{{ $tz->name }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php }?>
                        
                        <?php }?>                         
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box-footer">
                                <button type="submit" class="btn btn-success">Submit</button>
                                <?php if($menu_type == "agency"){ ?>

                                <a href="{{ url('/admin/agency') }}" class="btn btn-danger">Cancel</a>
                                <?php }else{ ?>
                                <a href="{{ url('/admin/user') }}" class="btn btn-danger">Cancel</a>
                                <?php } ?>
                            </div>
                        </div>
                        
                    </div>

                        
                    

                


    </form>
    </div>
    </div>
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


        var id = $('#id').val();
        
        
        if(id != '' || id != 0){
            if(parent_id == 1){
                $('#access_staff_div').show();
            }else{
                $('#access_staff_div').hide();
            }
        }

    });

    $('#parent_id').on('change', function(){
        var parent_id = $('#parent_id').val();
        console.log(parent_id);
        
        if(parent_id == 1){
            $('#access_staff_div').show();
            
        }else{
            $('#access_staff_div').hide();
            $('#access_agency').attr('value', '0');
        }
    });

</script>


<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBcicAVTh7Y8lz2x_1QGODkDq4w0lh0imk&libraries=places&callback=initAutocomplete" type="text/javascript"></script>
    @endsection
