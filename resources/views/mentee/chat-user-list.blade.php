@extends('layouts.apps') 
@section('content')
<div class="db-inner-content">
    <div class="db-box">
        <div class="heading-sec">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <?php if($type == 'st'){?>
                    <h3>Staff List</h3>
                    <?php }else if($type == 'mm'){?>
                    <h3>Mentor List</h3>
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
                                        $chat_message_url = "/mentee/chat/get_staff_chatcode?staff_id=".Crypt::encrypt($d->id);
                                    }else if($type == 'mm'){
                                        $user_name = $d->firstname.' '.$d->middlename.' '.$d->lastname;
                                        $chat_message_url = "/mentee/chat/get_mentor_chatcode?mentor_id=".Crypt::encrypt($d->id);
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
                                    <!-- <a href="{{url('/mentee/videochat/initiate')}}?mentor_id={{$d->id}}" class="btn btn-success"><i class="fa fa-video-camera" aria-hidden="true"></i></a> -->
                                        <a href="{{url('/mentee/videochat/initiate')}}?mentor_id={{ \Crypt::encrypt($d->id) }}" class="btn btn-success"><i class="fa fa-video-camera" aria-hidden="true"></i></a>
                                    <?php }?>
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
    

</script>
@endsection
