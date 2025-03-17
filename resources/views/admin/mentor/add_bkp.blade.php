@extends('layouts.admin') @section('content')


<div class="db-inner-content">
    <form role="form" action="{{ route('admin.mentor.save')}}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" id="id" name="id" value="<?php echo !empty($mentor->id)?$mentor->id:0;?>">        
        <div class="db-box">
            <div class="heading-sec">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h3>
                            <?php if(empty($mentor->id)){?>Create<?php }else{?>Edit<?php }?> Mentor
                        </h3>
                    </div>
                    <div class="col-lg-6">                        
                        <a href="{{ url('/admin/mentor') }}" class="back-btn"><i class="fa fa-arrow-left"></i></a>
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
                            <h3>Create Mentor                                
                            </h3>
                        </div>
                        <div class="col-sm-4 text-right">
                            <div class="check active-toggle">
                                <label>
                                <input type="checkbox" name="is_active" id="is_active" value="1"  <?php if((isset($mentor->is_active) && ($mentor->is_active == 1)) || !isset($mentor->is_active)){ ?>checked=""<?php } ?>> <span id="check_title"> <?php if((isset($mentor->is_active) && ($mentor->is_active == 1)) || !isset($mentor->is_active)){ ?>Active <?php }else{ ?>Inactive <?php } ?></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-xl-4 col-md-4">
                            <div class="single-inp">
                                <label>First Name <sup>*</sup></label>
                                <div class="inp">
                                    <input type="text" id="firstname" name="firstname" value="<?php echo !empty($mentor->firstname)?$mentor->firstname:'';?>" maxlength="60" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-4">
                            <div class="single-inp">
                                <label>Middle Name </label>
                                <div class="inp">
                                    <input type="text" id="middlename" name="middlename" value="<?php echo !empty($mentor->middlename)?$mentor->middlename:'';?>" maxlength="60">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-4">
                            <div class="single-inp">
                                <label>Last Name <sup>*</sup></label>
                                <div class="inp">
                                    <input type="text" id="lastname" name="lastname" value="<?php echo !empty($mentor->lastname)?$mentor->lastname:'';?>" maxlength="60" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">                        
                        <div class="col-xl-4 col-md-4">
                            <div class="single-inp">
                                <label>Email <sup>*</sup></label>
                                <div class="inp">
                                    <input type="email" id="email" name="email" value="<?php echo !empty($mentor->email)?$mentor->email:'';?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-4">
                            <div class="single-inp">
                                <label>Password</label>
                                <div class="inp">
                                    <input type="password" id="password" name="password">
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-md-4">
                            <div class="single-inp">
                                <label>Phone No</label>
                                <div class="inp">
                                    <input type="text" id="phone" name="phone" value="<?php echo !empty($mentor->phone)?$mentor->phone:'';?>">
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <?php if(Auth::user()->type == 1){?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Choose an affiliate <sup>*</sup></label>
                                <div class="select">
                                    <select class="" name="assigned_by" required="">
                                        <option value="">Choose an affiliate</option>
                                        <?php if(!empty($agencies)){ foreach($agencies as $ag){?>
                                        <option value="<?php echo $ag->id; ?>"  <?php if(!empty($mentor->assigned_by)){   if($mentor->assigned_by == $ag->id){ echo 'selected';    }  } ?>><?php echo $ag->name; ?></option>
                                        <?php }}?>
                                    </select>
                                </div>                                
                            </div>
                        </div>
                    </div>
                    <?php }else{?>
                    <input type="hidden" name="assigned_by" value="<?php echo Auth::user()->id; ?>">
                    <?php }?>
                    
                    <div class="profile-form">
                        <div class="row align-items-center mb-3">
                            <div class="col-md-6">
                                <div class="single-inp">

                                    <div class="file-upload">
                                        <div class="file-select">
                                            <div class="file-select-button" id="fileName"><img src="http://209.59.156.100/~tsicdev/assets/images/upload_icon.png"></div>
                                            <div class="file-select-name" id="">Click here to upload profile pic</div>
                                            <input type="file" name="image" id="image">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if(!empty($mentor->image)){?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Picture Url </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control input-lg" value="<?php echo !empty($mentor->image)?$mentor->image:''; ?>">
                                        <ul>
                                            <li><a href="<?php echo url('public/uploads/mentor_pic/'.$mentor->image); ?>" target="_blank"><i class="fa fa-eye"></i></a></li>
                                            <!-- <li><a href="<?php //echo url('/admin/agency/remove_pic/'.$mentor->id.'/'.$mentor->image.'/'.$menu_type); ?>"><i class="fa fa-trash"></i></a></li> -->
                                        </ul>
                                    </div>
                                </div>
                                
                            </div>
                            <?php }?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="check">
                                <label>
                                    <input type="checkbox" name="is_chat_mentee" <?php if((isset($mentor->is_chat_mentee) && ($mentor->is_chat_mentee == 1)) ){ ?> checked=""<?php } ?> value="1">Allow Mentee Chat</label>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="check">
                                <label>
                                    <input type="checkbox" name="is_chat_staff" <?php if((isset($mentor->is_chat_staff) && ($mentor->is_chat_staff == 1)) ){ ?> checked=""<?php } ?> value="1">Allow Staff Chat</label>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-success">Submit</button>                            
                        <a href="{{ url('/admin/mentor') }}" class="btn btn-danger">Cancel</a>
                    </div>
                </div>

            </div>


    </form>
    </div>
    </div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#phone').mask('(000) 000-0000'); 
    });

</script>
    @endsection
