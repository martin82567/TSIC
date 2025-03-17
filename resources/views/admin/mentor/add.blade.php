@extends('layouts.admin') 
@section('content')
<div class="db-inner-content">
    <form role="form" action="{{ route('admin.mentor.save')}}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" id="id" name="id" value="<?php echo !empty($mentor->id)?$mentor->id:0;?>">        
        <input type="hidden" id="assigned_by" name="assigned_by" value="<?php echo !empty($mentor->assigned_by)?$mentor->assigned_by:0;?>">        
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
                    </div>                    
                    <div class="row mb-3">                        
                        <div class="col-xl-6 col-md-6">
                            <div class="single-inp">
                                <label>Email <sup>*</sup></label>
                                <div class="inp">
                                    <input type="email" id="email" name="email" value="<?php echo !empty($mentor->email)?$mentor->email:'';?>" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <div class="single-inp">
                                <label>Password</label>
                                <div class="inp">
                                    <input type="password" id="password" name="password" minlength="6">
                                </div>
                            </div>
                        </div>                        
                    </div>
                    
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="check">
                                <label>
                                    <input type="checkbox" name="is_chat_mentee" <?php if((isset($mentor->is_chat_mentee) && ($mentor->is_chat_mentee == 1)) ){ ?> checked=""<?php } ?> value="1">Allow Mentee Chat</label>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="check">
                                <label>
                                    <input type="checkbox" name="is_chat_staff" <?php if((isset($mentor->is_chat_staff) && ($mentor->is_chat_staff == 1)) ){ ?> checked=""<?php } ?> value="1">Allow Staff Chat</label>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="check">
                                <label>
                                    <input type="checkbox" name="is_chat_video" <?php if((isset($mentor->is_chat_video) && ($mentor->is_chat_video == 1)) ){ ?> checked=""<?php } ?> value="1">Allow Video Chat</label>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="check">
                                <label>
                                    <input type="checkbox" name="platform_status" <?php if((isset($mentor->platform_status) && ($mentor->platform_status == 1)) ){ ?> checked=""<?php } ?> value="1">Platform Status</label>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-success">Submit</button>                            
                        <a href="{{ url('/admin/mentor') }}" class="btn btn-danger">Cancel</a>
                    </div>
                </div>

            </div>

        </div>
    </form>    
</div>
@endsection
