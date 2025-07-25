@extends('layouts.admin') 
@section('content')
<?php 
$messaging_for = false;
if(Auth::user()->type == 2){
    $messaging_for = true;
}else if(Auth::user()->type == 3 && Auth::user()->parent_id != 1){
    $messaging_for = true;
}
?>
<div class="db-inner-content">
    <form role="form" action="{{ route('admin.profile.save')}}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" id="id" name="id" value="<?php echo !empty($user_details->id)?$user_details->id:0;?>">
        <div class="db-box">
            <div class="heading-sec">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h3>Profile</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?php 
                        if(!empty($system_messaging)){
                            $message = $system_messaging[0]->message;
                        }
                        if($messaging_for){
                            if(!empty($message)){
                        ?>
                        <div class="alert alert-danger" role="alert">
                            <strong><?php echo $message;?> (Expired at <?php echo date('m/d/y', strtotime($system_messaging[0]->end_datetime));?>)</strong>
                        </div>
                        <?php 
                            }                            
                        }
                        ?>
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
                    <h3>Profile</h3>
                    <div class="row mb-3">
                        <div class="col-xl-12 col-md-6">
                            <div class="form-group">
                                <div class="single-inp">
                                    <label>Name</label>
                                    <div class="inp">
                                        <input type="text" id="name" name="name" value="<?php echo !empty($user_details->name)?$user_details->name:'';?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row mb-3">
                        <div class="col-xl-6 col-md-6">
                            <div class="form-group">
                                <div class="single-inp">
                                    <label>Email </label>
                                    <div class="inp">
                                        <input type="email" id="email" name="email" value="<?php echo !empty($user_details->email)?$user_details->email:'';?>" required disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <div class="form-group">
                                <div class="single-inp">
                                    <label>Password <?php if(empty($user_details->password)){ ?> <sup>*</sup> <?php } ?></label>
                                    <div class="inp">
                                        <input type="password" id="password" name="password" <?php if(empty($user_details->password)){ ?> required
                                        <?php } ?>>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if(Auth::user()->type != 3){?>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Cell Phone  </label>
                                <input class="form-control" type="text" id="cell_phone" name="cell_phone" value="<?php echo !empty($user_details->cell_phone)?$user_details->cell_phone:'';?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="single-inp">
                                    <div class="inp">
                                        <label>Work Phone</label>
                                        <input class="form-control" type="text" id="work_phone" name="work_phone" value="<?php echo !empty($user_details->work_phone)?$user_details->work_phone:'';?>" required>
                                    </div>
                                </div>
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
                    <?php }?>
                    
                    

                    <div class="profile-form">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                               <label></label>                               
                                <div class="file-upload" style="width:100%;">
                                    <div class="file-select" style="overflow: initial;">
                                        <div class="file-select-button" id="fileName"><img src="{{ url('assets/images/upload_img.jpg')}}"></div>
                                        <div class="file-select-name" id=""></div>
                                        <input type="file" name="profile_pic" id="" value="<?php echo $user_details->profile_pic; ?>" >
                                    </div>
                                </div>
                               

                            </div>
                            <?php if(!empty(Auth::user()->profile_pic)){?>
                            <div class="col-lg-6">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label>Profile Picture </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control input-lg" disabled="disabled" value="<?php echo !empty(Auth::user()->profile_pic)?Auth::user()->profile_pic:'';?>">
                                        <ul>
                                            <li><a href="<?php echo config('app.aws_url');  ?>agency_pic/{{Auth::user()->profile_pic}}" target="_blank"><i class="fa fa-eye"></i></a></li>
                                            <li><a href="{{ url('/admin/removeprofilepic')}}/{{ Auth::user()->id}}/{{Auth::user()->profile_pic }}"><i class="fa fa-trash"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <?php }?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description / Notes </label>
                                <textarea class="form-control" rows="2" name="description" placeholder="Enter Description" id="description" ><?php echo !empty($user_details->description)?$user_details->description:""; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <?php if(Auth::user()->type == 2){?>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Timezones </label>
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
                                            
                        <div class="col-md-12">
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
                                                <div class="file-select-button" id="fileName"><img src="<?php echo url('/assets/images/upload_icon.png');?>"></div>
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
                                <div class="control-group input-group row mt-3">

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label>File Upload</label>
                                            <div class="select">
                                                <select name="document_type[]">
                                                <?php foreach($document_type_details as $document_type){ ?>
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
                                                    <div class="file-select-button" id="fileName"><img src="<?php echo url('/assets/images/upload_icon.png');?>"></div>
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
                        </div>
                    

                        <div class="col-md-12">
                            <div class="box-footer">
                                <button type="submit" class="btn btn-success">Submit</button>
                                <a href="{{ url('/admin/') }}" class="btn btn-danger">Cancel</a>

                            </div>
                        </div>
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
    });

</script>
@endsection
