@extends('layouts.admin') @section('content')

<?php if(!empty(session('error_message'))){ ?>
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
    <h4 style="margin-bottom: 0">
        <?php
                                echo session('error_message');
                                Session::forget('error_message');
                            ?>
    </h4>
</div>
<?php } ?>
<?php
    $choose_agency = 0;
    if(Auth::user()->type == 1){
        $choose_agency = 1;
    }else if(Auth::user()->type == 3){

        if(Auth::user()->parent_id == 1){
            $choose_agency = 1;
        }
    }

    $created_by = Auth::user()->id;
    if(Auth::user()->type == 3 && Auth::user()->parent_id != 1){
        $created_by = Auth::user()->parent_id;
    }
?>
<div class="db-inner-content">
    <div class="db-box">
        <div class="heading-sec">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <h3>Goal / Assignment </h3>

                    <a href="{{ url('/admin/goaltask') }}" class="back-btn" style="display: inline;float: right;"><i class="fa fa-arrow-left"></i></a>
                </div>
            </div>
        </div>
        <?php if(isset($msg) && isset($msg_color)){ ?>
        <div class="alert alert-<?php echo $msg_color; ?> alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
            <h4 style="margin-bottom: 0"><i class="icon fa fa-check"></i>
                <?php echo $msg; ?>
            </h4>
        </div>
        <?php } ?>
        <div class="box-inner">

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
            <div class="form-section">
                <?php if(!empty($goaltask_details)){ ?>
                <h3>Edit Goal / Assignment </h3>
                <span>
                <?php //echo ucwords($goaltask_details->type); ?></span>
                <?php }else{ ?>
                <h3>Create Goal / Assignment </h3>

                <div class="row">
                    <div class="col-xl-6 col-md-6">
                        <div class="single-inp">
                            <label style="color: #000;">Type <sup>*</sup></label>
                            <div class="select">
                                <select name="type" id="type" onchange="showgoaltaskdiv(this.value);">
                                    <option value="goal" <?php if(isset($goaltask_details->type) && ($goaltask_details->type == 'goal')){ ?> selected
                                        <?php } ?>>Goal</option>
                                    <option value="task" <?php if((isset($goaltask_details->type) && ($goaltask_details->type == 'task'))  || !isset($goaltask_details->type)){ ?> selected
                                        <?php } ?>>Assignment</option>
                                    <!-- <option value="challenge" <?php //if(isset($goaltask_details->type) && ($goaltask_details->type == 'challenge')){ ?> selected
                                        <?php //} ?>>Challenge</option> -->
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="db-box db-hider" id="goal" <?php if(!empty($goaltask_details->type) && ($goaltask_details->type == "goal")){ ?>style="display:block;"
        <?php }else{ ?>style="display:none;"
        <?php } ?>>
        <form role="form" action="{{ route('admin.goaltask.save')}}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" id="type" name="type" value="goal">
            <input type="hidden" id="id" name="id" value="<?php echo !empty($goaltask_details->id)?$goaltask_details->id:0;?>">
            <div class="box-inner">
                <div class="form-section">
                    <div class="row">
                        <div class="col-sm-8">
                            <h3>Create Goal</h3>
                        </div>
                        <div class="col-sm-4 text-right">
                            <div class="check active-toggle">
                                <label>
                            <input type="checkbox" name="status" id="status"  value="1"  <?php if((isset($goaltask_details->status) && ($goaltask_details->status == 1)) || !isset($goaltask_details->status)){ ?>checked=""<?php } ?>> <span id="check_title"> <?php if((isset($goaltask_details->status) && ($goaltask_details->status == 1)) || !isset($goaltask_details->status)){ ?>Active <?php }else{ ?>Inactive <?php } ?></span>
                            </label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-md-6">
                            <div class="single-inp">
                                <label>Goal Name <sup>*</sup></label>
                                <div class="inp">
                                    <input type="text" placeholder="What is your goal?" id="name" name="name" value="<?php echo !empty($goaltask_details->name)?$goaltask_details->name:'';?>" required>
                                </div>
                            </div>
                        </div>
                        <?php if(empty($goaltask_details)){ if(!empty($choose_agency)){?>
                        <div class="col-xl-6 col-md-6">
                            <div class="single-inp">
                                <label style="color: #000;">Choose an affiliate <sup>*</sup></label>
                                <div class="select">
                                    <select name="created_by" id="created_by" required="required">
                                        <option value="">Choose one</option>
                                        <?php if(!empty($affiliates)){ foreach($affiliates as $af){?>
                                        <option value="<?php echo $af->id; ?>"><?php echo $af->name; ?></option>
                                        <?php }}?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <?php }else{?>


                        <input type="hidden" name="created_by" value="<?php echo $created_by ; ?>">
                        <?php } }?>
                    </div>



                    <div class="row">
                        <div class="col-xl-5 col-md-5">
                            <div class="single-inp">
                                <label>Start Date <sup>*</sup></label>
                                <div class="inp">
                                    <input type="text" readonly="readonly" class="form-control datepicker" name="start_date" id="start_date1" value="<?php echo (!empty($goaltask_details->start_date) && ($goaltask_details->start_date != '0000-00-00'))?date('m-d-Y', strtotime($goaltask_details->start_date)):''; ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-5 col-md-5">
                            <div class="single-inp">
                                <label>End Date <sup>*</sup></label>
                                <div class="inp">
                                    <input type="text" readonly="readonly" class="form-control datepicker" name="end_date" id="end_date1" value="<?php echo (!empty($goaltask_details->end_date) && ($goaltask_details->end_date != '0000-00-00'))?date('m-d-Y', strtotime($goaltask_details->end_date)):''; ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-2">
                            <div class="single-inp">
                                <label>Points <sup>*</sup></label>
                                <div class="inp">
                                    <input type="text" maxlength="2" onkeyup="this.value = this.value.replace(/\D/g,'');" onkeydown="this.value = this.value.replace(/\D/g,'');" name="point" id="point" value="<?php echo !empty($goaltask_details->point)?$goaltask_details->point:'';?>" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-12 col-md-12">
                            <div class="single-inp">
                                <label>Description <sup>*</sup></label>
                                <div class="inp">
                                    <textarea id="description" name="description" class="form-control" required><?php echo !empty($goaltask_details->description)?$goaltask_details->description:''; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h3>Files</h3>
                    <?php if(!empty($task_files)){ ?>
                    <div class="form-group">
                        <div class="listing-table">
                        <div class="table-responsive text-center">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Document Name</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; foreach($task_files as $file_details){ ?>
                                <tr>
                                    <th scope="row">
                                        <?php echo $i; ?>
                                    </th>
                                    <td>
                                        <?php echo $file_details->file_name; ?>
                                    </td>
                                    <td><a class="btn btn-success" href="<?php echo config('app.aws_url'); ?>goaltask/{{$file_details->file_name}}" target="_blank">VIEW</a>&nbsp;&nbsp;
                                        <a class="btn btn-danger btn-sm" href="<?php echo url('/admin/goaltask/delete_file/'.$file_details->id.'/'.$file_details->goaltask_id.'/'.$file_details->file_name); ?>" >DELETE</a></td>
                                </tr>
                                <?php $i++; } ?>
                            </tbody>
                        </table>
                        </div>
                        </div>
                    </div>

                    <?php } ?>


                    <div class="input-group control-group after-add-more row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>File Upload</label>
                                <div class="file-upload">
                                    <div class="file-select">
                                        <div class="file-select-button" id="fileName"><img src="{{url('assets/images/upload_icon.png')}}"></div>
                                        <div class="file-select-name" id="">Click files to upload</div>
                                        <input type="file" name="images[]" id="chooseFileunique123" onchange="changefilename('chooseFileunique123');">
                                    </div>
                                </div>
                                <div class="input-group-btn">
                                    <button class="btn btn-success add-more" type="button"><i class="glyphicon glyphicon-plus"></i> Add</button>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="copy-fields hide clearfix">
                        <div class="control-group input-group row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>File Upload</label>
                                    <div class="file-upload">
                                        <div class="file-select">
                                            <div class="file-select-button" id="fileName"><img src="{{url('assets/images/upload_icon.png')}}"></div>
                                            <div class="file-select-name" id="">Click files to upload</div>
                                            <input type="file" name="images[]" id="chooseFile" onchange="changefilename('chooseFileunique');">
                                        </div>
                                    </div>

                                    <div class="input-group-btn">
                                        <button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer mt-3">
                        <button type="submit" onclick="chkfrm();" class="btn btn-success">Submit</button>
                        <a href="{{ url('/admin/goaltask') }}" class="btn btn-danger">Cancel</a>

                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php ################################################################################################################################################################ ?>

    <div class="db-box db-hider" id="task" <?php if((!empty($goaltask_details->type) && ($goaltask_details->type == "task")) || empty($goaltask_details->type)){ ?>style="display:block;"
        <?php }else{ ?>style="display:none;"
        <?php } ?>>
        <form role="form" action="{{ route('admin.goaltask.save')}}" method="post" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" id="type" name="type" value="task">
            <input type="hidden" id="id" name="id" value="<?php echo !empty($goaltask_details->id)?$goaltask_details->id:0;?>">
            <div class="box-inner">
                <div class="form-section">
                    <div class="row">
                        <div class="col-sm-8">
                            <h3>Create Assignment</h3>
                        </div>
                        <div class="col-sm-4 text-right">
                            <div class="check active-toggle">
                                <label>
                            <input type="checkbox" name="status" id="status" value="1"  <?php if((isset($goaltask_details->status) && ($goaltask_details->status == 1)) || !isset($goaltask_details->status)){ ?>checked=""<?php } ?>> <span id="check_title"> <?php if((isset($goaltask_details->status) && ($goaltask_details->status == 1)) || !isset($goaltask_details->status)){ ?>Active <?php }else{ ?>Inactive <?php } ?></span>
                            </label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-xl-6 col-md-6">
                            <div class="single-inp">
                                <label>Assignment Name <sup>*</sup></label>
                                <div class="inp">
                                    <input type="text" placeholder="What is your assignment?" id="name" name="name" value="<?php echo !empty($goaltask_details->name)?$goaltask_details->name:'';?>" required>
                                </div>
                            </div>
                        </div>
                        <?php if(empty($goaltask_details)){ if(!empty($choose_agency)){?>
                        <div class="col-xl-6 col-md-6">
                            <div class="single-inp">
                                <label style="color: #000;">Choose an affiliate <sup>*</sup></label>
                                <div class="select">
                                    <select name="created_by" id="created_by" required="required">
                                        <option value="">Choose one</option>
                                        <?php if(!empty($affiliates)){ foreach($affiliates as $af){?>
                                        <option value="<?php echo $af->id; ?>"><?php echo $af->name; ?></option>
                                        <?php }}?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <?php }else{?>
                        <input type="hidden" name="created_by" value="<?php echo $created_by ; ?>">
                        <?php } }?>
                    </div>



                    <div class="row mb-3" style="display:none;">
                        <div class="col-xl-6 col-md-6">
                            <div class="single-inp">
                                <label>Dead Line </label>
                                <div class="inp">
                                    <div class="input-group date" data-provide="datepicker">
                                        <input type="text" class="form-control" id="dead_line" name="dead_line" value="<?php echo !empty($goaltask_details->dead_line)?$goaltask_details->dead_line:'';?>">
                                        <div class="input-group-addon">
                                            <span class="glyphicon glyphicon-th"></span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-xl-5 col-md-5">
                            <div class="single-inp">
                                <label>Start Date <sup>*</sup></label>
                                <div class="inp">
                                    <input type="text" readonly="readonly" class="form-control datepicker" name="start_date" id="start_date3" value="<?php echo (!empty($goaltask_details->start_date) && ($goaltask_details->start_date != '0000-00-00'))?date('m-d-Y', strtotime($goaltask_details->start_date)):''; ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-5 col-md-5">
                            <div class="single-inp">
                                <label>End Date <sup>*</sup></label>
                                <div class="inp">
                                    <input type="text" readonly="readonly" class="form-control datepicker" name="end_date" id="end_date3" value="<?php echo (!empty($goaltask_details->end_date) && ($goaltask_details->end_date != '0000-00-00'))?date('m-d-Y', strtotime($goaltask_details->end_date)):''; ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-md-2">
                            <div class="single-inp">
                                <label>Points <sup>*</sup></label>
                                <div class="inp">
                                    <input type="text" maxlength="2" onkeyup="this.value = this.value.replace(/\D/g,'');" onkeydown="this.value = this.value.replace(/\D/g,'');" name="point" id="point" value="<?php echo !empty($goaltask_details->point)?$goaltask_details->point:'';?>" required>
                                </div>
                            </div>
                        </div>

                    </div>


                    <div class="row mb-3">
                        <div class="col-xl-12 col-md-12">
                            <div class="single-inp">
                                <label>Description <sup>*</sup></label>
                                <div class="inp">
                                    <textarea id="description" name="description" class="form-control" required><?php echo !empty($goaltask_details->description)?$goaltask_details->description:'';?></textarea>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row mb-3" style="display:none;">
                        <div class="col-xl-6 col-md-6">
                            <label>Reminder </label>
                            <select name="reminder" class="form-control">
                            <option value="1" <?php if((isset($goaltask_details->reminder) && ($goaltask_details->reminder == 1)) || !isset($goaltask_details->reminder)){ ?> selected
                                <?php } ?>>Yes</option>
                            <option value="0" <?php if(isset($goaltask_details->reminder) && ($goaltask_details->reminder == 0)){ ?> selected
                                <?php } ?>>No</option>
                        </select>
                        </div>
                        <div class="col-xl-6 col-md-6">
                            <label>Frequancy </label>
                            <select name="frequency" class="form-control">
                            <option value="">Select Frequency</option>
                            <?php if(!empty($frequency_arr)){ ?>
                            <?php foreach($frequency_arr as $freq){  ?>
                            <option value="<?php echo $freq->id; ?>" <?php if(!empty($goaltask_details->frequency) && ($goaltask_details->frequency == $freq->id)){ ?>selected=""
                                <?php } ?>>
                                <?php echo $freq->name ?>
                            </option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                        </div>

                    </div>

                    <h3>Files</h3>
                    <?php if(!empty($task_files)){ ?>
                    <div class="form-group">
                        <div class="listing-table">
                        <div class="table-responsive text-center">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Document Name</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; foreach($task_files as $file_details){ ?>
                                <tr>
                                    <th scope="row">
                                        <?php echo $i; ?>
                                    </th>
                                    <td>
                                        <?php echo $file_details->file_name; ?>
                                    </td>
                                    <td><a class="btn btn-success" href="<?php echo config('app.aws_url'); ?>goaltask/{{$file_details->file_name}}" target="_blank">VIEW</a>&nbsp;&nbsp;
                                        <a class="btn btn-danger btn-sm" href="<?php echo url('/admin/goaltask/delete_file/'.$file_details->id.'/'.$file_details->goaltask_id.'/'.$file_details->file_name); ?>" >DELETE</a></td>
                                </tr>
                                <?php $i++; } ?>
                            </tbody>
                        </table>
                        </div>
                        </div>
                    </div>

                    <?php } ?>



                    <div class="input-group control-group after-add-more1 row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>File Upload</label>
                                <div class="file-upload">
                                    <div class="file-select" >
                                        <div class="file-select-button" id="fileName"><img src="{{url('assets/images/upload_icon.png')}}"></div>
                                        <div class="file-select-name"  for="chooseFile">Click files to upload</div>

                                        <input type="file" name="images[]" id="chooseFileunique" onchange="changefilename('chooseFileunique');">
                                    </div>
                                </div>
                                <div class="input-group-btn">
                                    <button class="btn btn-success add-more1" type="button"><i class="glyphicon glyphicon-plus"></i> Add</button>
                                </div>
                            </div>
                        </div>
                    </div>




                    <div class="copy-fields1 hide clearfix">
                        <div class="control-group input-group row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>File Upload</label>
                                    <div class="file-upload">
                                        <div class="file-select" >
                                            <div class="file-select-button" id="fileName"><img src="{{url('assets/images/upload_icon.png')}}"></div>
                                            <div class="file-select-name" id="" for="chooseFile">Click files to upload</div>
                                            <input type="file" name="images[]" id="chooseFile" onchange="changefilename('chooseFileunique');">
                                        </div>
                                    </div>

                                    <div class="input-group-btn">
                                        <button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer mt-3">
                        <button type="submit" onclick="chkfrm();" class="btn btn-success">Submit</button>
                        <a href="{{ url('/admin/goaltask') }}" class="btn btn-danger">Cancel</a>

                    </div>
                </div>
            </div>
        </form>
    </div>

</div>

    <div id="sandbox-container" style="display:none;">
        <input type="text" type="text" class="form-control datepickstart" />
        <input type="text" type="text" class="form-control datepickend" />
    </div>


    <script type="text/javascript">
        $(function() {
            $(".datepicker").datepicker({
                changeMonth: true,
                changeYear: true,
                yearRange: '1950:<?php echo (date('Y')+10);?>',
                dateFormat: 'mm-dd-yy'
            });
        });



        function chkfrm() {

            var type = $('#type').val();
            var created_by = $('#created_by').val();



            if (type == 'goal') {
                var start_date1 = $('#start_date1').val();
                var end_date1 = $('#end_date1').val();

                if (start_date1 == '') {
                    swal("Startdate is required");
                    return true;
                }
                if (end_date1 == '') {
                    swal("Enddate is required");
                    return true;
                }

                start_date1 = new Date(start_date1).getTime();
                end_date1 = new Date(end_date1).getTime();

                var diff = end_date1 - start_date1;

                if (diff < 0) {
                    swal("Enddate should be greater than StartDate");
                    return true;
                }

            } else if (type == 'challenge') {
                var start_date2 = $('#start_date2').val();
                var end_date2 = $('#end_date2').val();

                if (start_date2 == '') {
                    swal("Startdate is required");
                    return true;
                }
                if (end_date2 == '') {
                    swal("Enddate is required");
                    return true;
                }

                start_date2 = new Date(start_date2).getTime();
                end_date2 = new Date(end_date2).getTime();

                var diff = end_date2 - start_date2;

                if (diff < 0) {
                    swal("Enddate should be greater than StartDate");
                    return true;
                }


            } else {
                var start_date3 = $('#start_date3').val();
                var end_date3 = $('#end_date3').val();

                if (start_date3 == '') {
                    swal("Startdate is required");
                    return true;
                }
                if (end_date3 == '') {
                    swal("Enddate is required");
                    return true;
                }

                start_date3 = new Date(start_date3).getTime();
                end_date3 = new Date(end_date3).getTime();

                var diff = end_date3 - start_date3;

                if (diff < 0) {
                    swal("Enddate should be greater than StartDate");
                    return true;
                }
            }

        }

        function makeid(length) {
           var result           = '';
           var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
           var charactersLength = characters.length;
           for ( var i = 0; i < length; i++ ) {
              result += characters.charAt(Math.floor(Math.random() * charactersLength));
           }
           return result;
        }

        function changefilename(unique_id){

            var file = $('#'+unique_id)[0].files[0].name;
            //alert(file);
            $('#'+unique_id).prev('div').text(file);
        }

        $(document).ready(function() {

            var rand_var = '<?php echo rand(); ?>';

            console.log(rand_var);


            //here first get the contents of the div with name class copy-fields and add it to after "after-add-more" div class.
            $(".add-more").click(function() {
                var html = $(".copy-fields").html();
                //id="chooseFile"
                var unique_id = makeid(20);
                var new_html = html.replace('id="chooseFile"', 'id="'+unique_id+'"');
                new_html = new_html.replace('chooseFileunique', unique_id);
                console.log(html);

                $(".after-add-more").after(new_html);

            });

            $(".add-more1").click(function() {
                var html = $(".copy-fields1").html();
                //id="chooseFile"
                var unique_id = makeid(20);
                var new_html = html.replace('id="chooseFile"', 'id="'+unique_id+'"');
                new_html = new_html.replace('chooseFileunique', unique_id);
                console.log(html);

                $(".after-add-more1").after(new_html);

            });

            //here it will remove the current value of the remove button which has been pressed
            $("body").on("click", ".remove", function() {
                $(this).parents(".control-group").remove();
            });


            var date = new Date();
            //date.setDate(date.getDate()-1);
            $('.datepickstart').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'mm-dd-yyyy'
            });

        });
        // $('#chooseFile').change(function() {
        //   var i = $(this).prev('div').clone();
        //   var file = $('#chooseFile')[0].files[0].name;
        //   $(this).prev('div').text(file);
        // });
        $('#status').click(function() {
            if ($(this).is(':checked'))
                $('#check_title').text('Active');
            else
                $('#check_title').text('Inactive');
        });

    </script>
    @endsection
