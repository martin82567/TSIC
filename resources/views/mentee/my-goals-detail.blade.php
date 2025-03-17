@extends('layouts.apps')
@section('content')

    <div class="db-inner-content">
            <input type="hidden" id="id" name="id" value="<?php echo !empty($detail->id)?$detail->id:0;?>">

            <div class="db-box">

                <div class="heading-sec">
                    <div class="row">
                        <div class="col-lg-6">
                            <h3>Goal Details</h3>
                        </div>
                        <div class="col-lg-6">
                            @if($goal_status === 1 || $goal_status === 0)
                            <div class="modal fade" id="addNotes" tabindex="-1" role="dialog" aria-labelledby="addNotes" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form role="form" action="{{ url('/mentee/goal_note/save/' . $detail->id )}}" method="post" enctype="multipart/form-data" id="save_form">
                                            {{ csrf_field() }}
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Add Notes</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Title</label>
                                                        <input type="text" placeholder="Notes Title" class="form-control" name="title" id="title">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Description</label>
                                                           <textarea class="form-control" name="description"  id="description" placeholder="Notes Description"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary" id="save_btn" onclick="return saveNote();">Save</button>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="addFiles" tabindex="-1" role="dialog" aria-labelledby="addNotes" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form role="form" action="{{ url('/mentee/goal_files/save/' . $detail->id )}}" method="post" enctype="multipart/form-data" id="save_filesForm">
                                            {{ csrf_field() }}
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Add Files</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                        <div class="single-inp">
                                                            <label>Images <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" title="Upload upto 5 images only. For Image Only .png OR .jpg OR .jpeg. Allowed maximum file size is 10MB"></i></label>
                                                            <div id="img">
                                                                <input type="file" name="image[]" id="image" class="form-control pt-1"  accept="image/jpg, image/jpeg, image/png" multiple max="5">
                                                            </div>
                                                            <br>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary" id="save_files">Save</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="box-inner">
                    <div class="form-section">
                        <div class="row">
                            <div class="col-sm-8">
                                <h3>
                                </h3>
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
                        <?php } ?>
                        @if ($errors->any())
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
                                <h5 style="color: black">Title</h5>
                                </br>
                                <p style="color: black">{{ $detail->name }}</p>
                            </div>
                        </div>
                        </br>
                        </br>
                        <div class="row">
                            <div class="col-md-12">
                                <h5 style="color: black">Description</h5>
                                </br>
                                <p style="color: black">{{ $detail->description }}</p>
                            </div>
                        </div>
                        </br>
                        </br>
                        <div class="row">
                            <div class="col-md-6">
                                <h5 style="color:black;">Notes</h5>
                            </div>
                            <div class="col-md-6 float-right">
                                @if($goal_status === 1 || $goal_status === 0)
                                <a class="btn btn-success btn-sm float-right" data-toggle="modal" data-target="#addNotes">Add Notes</a>
                                @endif
                            </div>
                            </br>
                            <div class="col-md-12">
                                <div class="listing-table">
                                    <div class="table-responsive text-center">
                                    <table class="table table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Created At</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php if(!empty($notes)){
                                            foreach($notes as $note){
                                            ?>
                                                <tr>
                                                    <td>{{ $note->title }}</td>
                                                    <td>{{ $note->note }}</td>
                                                    <td><?php echo get_standard_datetime($note->created_date); ?></td>
                                                </tr>
                                        <?php }} else { ?>
                                        <tr>
                                            <td colspan="3" style="text-align: center;">No Notes found</td>
                                        </tr>
                                        <?php }?>
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </br>
                        </br>
                        <div class="row">
                            <div class="col-md-6">
                                <h5 style="color:black;">Files</h5>
                            </div>
                            <div class="col-md-6 float-right">
                                @if($goal_status === 1 || $goal_status === 0)
                                <a class="btn btn-success btn-sm float-right mr-2" data-toggle="modal" data-target="#addFiles">Add Files</a>
                                @endif
                            </div>
                            <div class="col-md-12">
                                </br>
                                <div class="row">
                                @foreach($files as $d)
                                    <div class="thumbnail col-md-2">
                                        <a href="{{ $base_path. '/' . $d->file_name}}" target="_blank" class="d-inline-block">
                                            <img src="{{ $base_path . '/' . $d->file_name }}" style="display: inline-block !important;">
                                            <a class="delete" onclick="return confirm('Are you sure, you want to delete this image?')" href="{{ url('/mentee/goal_files/delete/' . $d->id )}}" title="Delete Image"><i class="fa fa-minus pl-1"></i></a>
                                        </a>
                                    </div>
<!--                                    <div class="d-inline-block">
                                    <a href="{{ $base_path. '/' . $d->file_name}}" target="_blank" class="d-inline-block">
                                        <img src="{{ $base_path . '/' . $d->file_name }}" style="display: inline-block !important;">
                                        <a class="delete" onclick="return confirm('Are you sure, you want to delete this image?')" href="{{ url('/mentee/goal_files/delete/' . $d->id )}}"></a>
                                    </a>
                                    </div>-->
                                @endforeach
                                </div>
                            </div>
                        </div>
                        @if($goal_status === 1 || $goal_status === 0)
                        <div class="box-footer">
                            <form method="POST" action="{{ $goal_status === 0 ? url('/mentee/my_goals/goal_start/' . $detail->id ) : url('/mentee/my_goals/goal_start/' . $detail->id ) }}">
                                {{ csrf_field() }}
                            </br>
                                <button  type="submit" id="submit_btn" class="btn btn-success">{{ $goal_status === 0 ? 'Start Goal' : 'Mark as Complete' }}</button>
                            </form>
                        </div>
                        @endif

                    </div>

                </div>
            </div>
    </div>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/js/tempusdominus-bootstrap-4.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/css/tempusdominus-bootstrap-4.min.css" />

    <script type="text/javascript">

        function saveNote() {
            var title = $('#title').val();
            var description = $('#description').val();

            console.log(title);

            if(title == ''){
                swal('Title is required');
                return false;
            }
            else if (description == '') {
                swal('Please add description');
                return false;
            }
            // $('#save_btn').prop('disabled', true);
            $('#save_form').submit();
        }

        /*$(function(){
            $("#save_files").click(function(){
                var $fileUpload = $("input[type='file']");
                if (parseInt($fileUpload.get(0).files.length) > 5){
                    alert("You are only allowed to upload a maximum of 5 files");
                } else {
                    $('#save_filesForm').submit();
                }
            });
        });*/

        $("#image").on("change", function() {
            if ($("#image")[0].files.length > 5) {
                $('#save_files').prop('disabled', true);
                alert("You can select only 5 images");
            } else {
                $('#save_files').prop('disabled', false);
                $("#save_filesForm").submit();
            }
        });

    </script>
    <style>
        .thumbnail {
            width:180px;
            height:150px;
            position:relative;
        }

        .thumbnail a img {
            width: 100%;
            max-width:100%;
            max-height:100%;
        }

        .thumbnail .delete{
            color: white;
            display:block;
            width:20px;
            height:20px;
            position:absolute;
            top:0;
            right:15px;
            background-color: #F07622;
            overflow:hidden;
        }
    </style>
@endsection
