@extends('layouts.apps')
@section('content')
<!-- <p>Mentor</p> -->
<div class="db-inner-content">
    <div class="db-box">
        <div class="heading-sec">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <?php if($type == 'st'){?>
                    <h3>Staff List</h3>
                    <?php }else if($type == 'mm'){?>
                    <h3>Mentee List</h3>
                    <?php }?>
                </div>
                <div class="col-lg-6">
                    <div class="search-sec">

                    </div>

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
        <?php } ?>
        <div class="box-inner">

            <div class="listing-table">
                <div class="table-responsive text-center">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if(!empty($data)){
                                    foreach($data as $d){

                                    $user_name = "";
                                    $chat_message_url = "";
                                    if($type == 'st'){
                                        $user_name = $d->name;
                                        $chat_message_url = "/mentor/chat/get_staff_chatcode?staff_id=".Crypt::encrypt($d->id);
                                    }else if($type == 'mm'){
                                        $user_name = $d->firstname.' '.$d->middlename.' '.$d->lastname;
                                        $chat_message_url = "/mentor/chat/get_mentee_chatcode?mentee_id=".Crypt::encrypt($d->id);
                                    }


                            ?>
                            <tr>
                                <td>
                                    <?php echo $user_name ;?>
                                    <?php if(!empty($d->unread_chat_count)){?>
                                    <span class="badge bg-green chat-unread"><?php echo $d->unread_chat_count;?></span>
                                    <?php }?>
                                </td>
                                <td>
                                    <a href="{{$chat_message_url}}" class="btn btn-success">Go</a>
                                    <?php if($type == 'mm'){?>
                                    <?php $mid = \Crypt::encrypt($d->id);?>
                                    <a href="{{url('/mentor/videochat/initiate')}}?mentee_id={{$mid}}" class="btn btn-success"><i class="fa fa-video-camera" aria-hidden="true"></i></a>
                                    <!-- <a href="javascript:void(0);" onclick="call_mentee('menteemodal_<?php //echo $d->id; ?>');" class="btn btn-success"><i class="fa fa-video-camera" aria-hidden="true"></i></a> -->
                                    <?php }?>
                                    <!-- Modal -->
                                    <div class="modal fade" id="menteemodal_<?php echo $d->id; ?>" tabindex="-1" role="dialog" aria-labelledby="menteemodalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="menteemodalLabel">Video Call With <?php echo $user_name ;?> </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            ...
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-success" data-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-success">Save changes</button>
                                        </div>
                                        </div>
                                    </div>
                                    </div>

                                </td>
                            </tr>
                            <?php }}else{?>
                            <tr>
                                <td colspan="2" style="text-align: center;">No staff found</td>
                            </tr>
                            <?php }?>
                        </tbody>
                    </table>
                    {{ $data->links() }}
                </div>
            </div>
        </div>
    </div>
</div>




<script>
    function call_mentee(id){
        $('#'+id).modal('show')
    }
</script>
@endsection
