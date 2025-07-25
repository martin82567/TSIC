@extends('layouts.admin') @section('content')
<?php 
    $view_access = 0;
    if(Auth::user()->type == 1){
        $view_access = 1;
    }else if(Auth::user()->type == 3 && Auth::user()->parent_id == 1){
        $view_access = 1;
    }
?>
<div class="db-inner-content">
    <div class="db-box">
        <div class="heading-sec">
            <div class="row align-items-center">
                <div class="col-md-6">                    
                    <h3>Video Chats</h3>                    
                </div>

                <div class="col-md-6">
                    
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
                                <?php if(Auth::user()->type != 2){?>  
                                <th>Affiliate</th>
                                <?php }?>
                                <th>Sender</th>
                                <th>Sender Name</th>
                                <th>Receiver</th>
                                <th>Receiver Name</th>
                                <th>Duration (in minutes)</th>
                                <th>Status</th>
                                <th>Date</th>
                                <?php if(!empty($view_access)){?>
                                <th>Action</th>
                                <?php }?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
			                    if(!empty($data)){
                                    foreach ($data as $d) {

                                        $sender_name = '';
                                        $receiver_name = '';

                                        $unique_name = $d->unique_name;
                                        $explode_unique_name = explode("-", $unique_name);
                                        $sender_type = $explode_unique_name[0];
                                        $sender_id = $explode_unique_name[1];
                                        $receiver_type = $explode_unique_name[2];
                                        $receiver_id = $explode_unique_name[3];

                                        if($sender_type == 'mentor'){
                                            $sender_data = DB::table(MENTOR)->where('id',$sender_id)->first();
                                            $sender_name = $sender_data->firstname.' '.$sender_data->lastname;
                                        }else if($sender_type == 'mentee'){
                                            $sender_data = DB::table(MENTEE)->where('id',$sender_id)->first();
                                            $sender_name = $sender_data->firstname.' '.$sender_data->lastname;
                                        }
                                        if($receiver_type == 'mentor'){
                                            $receiver_data = DB::table(MENTOR)->where('id',$receiver_id)->first();
                                            $receiver_name = $receiver_data->firstname.' '.$receiver_data->lastname;
                                        }else if($receiver_type == 'mentee'){
                                            $receiver_data = DB::table(MENTEE)->where('id',$receiver_id)->first();
                                            $receiver_name = $receiver_data->firstname.' '.$receiver_data->lastname;
                                        }

                                        if($d->duration == ''){
                                            $status = 'In-progress';
                                        }else if($d->duration == 0){
                                            $status = 'Missed Call';
                                        }else{
                                            $status = 'Completed';
                                        }
                                        
                                                       
                    		?>
                            <tr>
                                <?php if(Auth::user()->type != 2){?>  
                                <td><?php echo $d->affiliate_name;?></td>
                                <?php }?>
                                <td><?php echo ucwords($sender_type);?></td>
                                <td><?php echo $sender_name; ?></td>                                
                                <td><?php echo ucwords($receiver_type);?></td>
                                <td><?php echo $receiver_name; ?></td>
                                <td><?php echo !empty($d->duration)?convert_sec_to_min($d->duration):' 0 ';?></td>
                                <td><?php echo $status;?></td>
                                <td><?php echo get_standard_datetime($d->created_at);?></td>
                                <?php if(!empty($view_access)){?>
                                <td><a href="{{url('/admin/videochat/details')}}/{{$d->room_sid}}" title="View Details"><i class="fa fa-eye"></i></a></td>
                                <?php }?>                                
                            </tr>
                            <?php     
                                    }
                                }   
                            ?>
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
