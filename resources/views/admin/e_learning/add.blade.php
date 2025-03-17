@extends('layouts.admin')
@section('content')
<?php
$is_super = 0;
$is_super_id = 0;
if(Auth::user()->type == 1){
    $is_super = 1;
    $is_super_id = Auth::user()->id;
}else if(Auth::user()->type == 3 && Auth::user()->parent_id == 1){
    $is_super = 1;
    $is_super_id = Auth::user()->id;
}

$is_super_edit = false;
if(!empty($user_details) && ($user_details->added_by == $is_super_id)){
    $is_super_edit = true;
}else if(empty($user_details) && !empty($is_super)){
    $is_super_edit = true;
}

// echo '<pre>'; echo $is_super;
// echo '<pre>'; echo $is_super_id;
// echo '<pre>'; echo $is_super_edit;
// if(!empty($is_super) && $is_super_edit){
//     echo '<pre>'; echo 'Get Drodown';
// }
// die;
?>
<div class="db-inner-content">
    <form role="form" action="{{ route('admin.e_learning.save')}}" method="post" enctype="multipart/form-data" id="submit_form">
        {{ csrf_field() }}
        <input type="hidden" id="id" name="id" value="<?php echo !empty($user_details->id)?$user_details->id:0;?>">
        <div class="db-box">
            <div class="heading-sec">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <h3>Resource</h3>

                        <a href="{{ url('/admin/e_learning') }}" class="back-btn" style="display: inline;float: right;"><i class="fa fa-arrow-left"></i></a>
                    </div>
                </div>
            </div>
            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
                <!--{{ implode('', $errors->all(':message')) }}-->
                @foreach ($errors->all() as $error)
                <h4>{{ $error }}</h4>
                @endforeach
            </div>
            @endif
            <?php if(session('success_message')){ ?>
                <div class="alert alert-danger alert-dismissible">
                    <h4>
                        <?php
                            echo session('success_message');
                            Session::forget('success_message');
                        ?>
                    </h4>
                </div>
            <?php } ?>
            <div class="box-inner">
                <div class="form-section">
                    <div class="row">
                        <div class="col-sm-8">
                            <h3>Create Resource</h3>
                        </div>
                        <div class="col-sm-4 text-right">
                            <div class="check active-toggle">
                                <label>
                                <input type="checkbox" name="is_active" value="1"  <?php if((isset($user_details->is_active) && ($user_details->is_active == 1)) || !isset($user_details->is_active)){ ?>checked=""<?php } ?>> <span id="check_title"> <?php if((isset($user_details->is_active) && ($user_details->is_active == 1)) || !isset($user_details->is_active)){ ?>Active <?php }else{ ?>Inactive <?php } ?></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-xl-12 col-md-12">
                            <div class="single-inp">
                                <label>Name <sup>*</sup></label>
                                <div class="inp">
                                    <input type="text" id="name" name="name" maxlength="200" value="<?php echo !empty($user_details->name)?$user_details->name:'';?>" required>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" rows="2"  name="description" placeholder="Enter Description" id="description" required><?php echo !empty($user_details->description)?$user_details->description:""; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="display:none;">
                        <div class="col-md-12">
                            <div class="single-inp">
                                <label>Article Link</label>
                                <div class="inp">
                                    <input type="url" id="article_link" name="article_link" value="<?php echo !empty($user_details->article_link)?$user_details->article_link:'';?>" >
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-md-6">
                            <div class="form-group">
                                <label>Type</label>
                                <div class="select">
                                    <select name="type" id="type" onchange="change_type(this.value);">
                                    <option value="image" <?php if((isset($user_details->type) && ($user_details->type == 'image')) || !isset($user_details->type)){ ?> selected <?php } ?>>Image</option>
                                    <option value="video" <?php if(isset($user_details->type) && ($user_details->type == 'video')){ ?> selected <?php } ?>>Video</option>
                                    <option value="pdf" <?php if(isset($user_details->type) && ($user_details->type == 'pdf')){ ?> selected <?php } ?>>PDF</option>
                                    <option value="url" <?php if(isset($user_details->type) && ($user_details->type == 'url')){ ?> selected <?php } ?>>Url</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="single-inp">
                                <label>Image / Video / PDF / Url <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" title="For Image Only .png OR .jpg OR .jpeg. For Video Only .mp4 OR .3gp. For PDF Only .pdf. Allowed maximum file size is 10MB"></i></label>
                                <div id="img_vid">
                                <input type="file" name="image" id="image" class="form-control"  accept="">
                                <!-- <input type="file" name="image" id="image" class="form-control"  accept="image/jpg,image/png,image/jpeg,video/mp4,video/3gp,application/pdf"> -->
                                <!-- <input type="file" name="image" id="image" class="form-control"  accept=".mp4,.3gp,.png, .jpg,.pdf"> -->
                                </div>
                                <input type="url" id="url" name="url" value="<?php echo !empty($user_details->url)?$user_details->url:'';?>" placeholder="Add a url" style="display: none;">
                                <br>
                            </div>
                        </div>
                    </div>
                    <?php if(!empty($user_details->file)){ ?>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="single-inp">
                                <label>Uploaded File</label>
                                <div class="input-group">
                                    <input type="text" class="form-control input-lg" disabled="disabled" value="<?php echo $user_details->file;?>">
                                    <ul>
                                        <li><a href="<?php echo config('app.aws_url');  ?>e_learning/{{$user_details->file}}" target="_blank"><i class="fa fa-eye"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <?php

                        if($is_super_edit){
                    ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="music">Choose Affiliates <small style="color: red;">*</small>
                                    <a href="#" onclick="selectAllAffiliates()" class="bg-warning ml-2 px-2">Select All</a>
                                </label>
                                <select class="js-example-basic-multiple" id="affiliates" name="affiliates[]" multiple="multiple" required="required">
                                    <?php if(!empty($affiliate_arr)){
                                        foreach($affiliate_arr as $a){
                                    ?>
                                    <option value="<?php echo $a->id;?>" <?php if(!empty($affiliate_ids) && in_array($a->id,$affiliate_ids)){ ?> selected <?php } ?>><?php echo $a->name;?></option>
                                    <?php }
                                    }?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="check">
                                <label>
                                    <input type="checkbox" class="" id="selectall" onClick="selectAll(this)">Select All
                                </label>
                            </div>
                        </div>
                        <?php
                        	if(!empty($e_learning_users)){
                        		foreach($e_learning_users as $e){
                        			$id_arr[] = $e->user_type;
                        		}
                        	}
                        	foreach($user_types as $u){
                        		$checked = "";
                                if(!empty($id_arr)){
                                    if (in_array($u['id'], $id_arr)){
                                        $checked = "checked";
                                    }
                                }
                        ?>
                        <div class="col-lg-12">
                            <div class="check">
                                <label>
                                    <input type="checkbox" name="user_types[]" class="user_types" value="<?php echo $u['id'];?>" <?php echo $checked; ?>><?php echo $u['name'];?>
                                </label>
                            </div>
                        </div>
                        <?php }?>
                    </div>
                    <div class="box-footer">
                       <button type="submit" class="btn btn-success" onclick="return formsubmit();">Submit</button>
                        <a href="{{ url('/admin/e_learning') }}" class="btn btn-danger" >Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $( document ).ready(function() {
        CKEDITOR.replace( 'description' );

        var id = $('#id').val();
        var type = $('#type').val();

        if(id != 0){
            if(type == 'url'){
                $('#img_vid').hide();
                $('#url').show();
            }
        }
    });

    function selectAllAffiliates() {
        let allAffiliates = [
            <?php foreach($affiliate_arr as $a) { ?>
                '<?php echo $a->id; ?>',
            <?php } ?>
        ];
        $('#affiliates').select2().val(allAffiliates).trigger('change')
    }

    function change_type(val){
        console.log(val);
        if(val == "image"){
            $('#img_vid').show();
            $('#url').hide();
            $('#image').inputFileText( { remove: true } );
            $('#image').inputFileText({
                text: 'Select Image'
            });
            $('#image').attr("accept", "image/jpg,image/png,image/jpeg");
        }else if(val == "video"){
            $('#img_vid').show();
            $('#url').hide();
            $('#image').inputFileText( { remove: true } );
            $('#image').inputFileText({
                text: 'Select Video'
            });
            $('#image').attr("accept", "video/mp4,video/3gp");
        }else if(val == "pdf"){
            $('#img_vid').show();
            $('#url').hide();
            $('#image').inputFileText( { remove: true } );
            $('#image').inputFileText({
                text: 'Select PDF'
            });
            $('#image').attr("accept", "application/pdf");
        }else if(val == "url"){
            $('#img_vid').hide();
            $('#url').show();
        }
    }
    <?php if(!empty($user_details->type) && ($user_details->type == 'image')){ ?>
        $('#image').inputFileText({
            text: 'Select Image'
        });
        $('#image').attr("accept", "image/jpg,image/png,image/jpeg");
    <?php }else if(!empty($user_details->type) && ($user_details->type == 'video')){ ?>
        $('#image').inputFileText({
            text: 'Select Video'
        });
        $('#image').attr("accept", "video/mp4,video/3gp");
    <?php }else if(!empty($user_details->type) && ($user_details->type == 'pdf')){ ?>
        $('#image').inputFileText({
            text: 'Select PDF'
        });
        $('#image').attr("accept", "application/pdf");
    <?php }else if(!empty($user_details->type) && ($user_details->type == 'url')){?>
        $('#image').hide();
        $('#url').attr('placeholder', 'Add a url' );
    <?php }else{ ?>
        $('#image').inputFileText({
            text: 'Select Image'
        });
        $('#image').attr("accept", "image/jpg,image/png,image/jpeg");
    <?php } ?>


</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2();
    });

    function selectAll(source) {
        // console.log('Hi');
        checkboxes = document.getElementsByName('user_types[]');
        for(var i in checkboxes)
            checkboxes[i].checked = source.checked;

    }

    function formsubmit() {

        var name = $('#name').val();
        var users = $('#submit_form .user_types:checked').length;

        console.log(users);

        if(name == ''){
            swal('Name is required');
            return false;
        }
        else if (users == 0) {
            swal('Please add at least one user type');
            return false;
        }

        return true;

        $('#submit_btn').prop('disabled', true);
        $('#submit_form').submit();
    }
</script>


@endsection
