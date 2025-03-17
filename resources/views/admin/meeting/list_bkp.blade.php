@extends('layouts.admin') @section('content')
<div class="db-inner-content">
    <div class="db-box">
        <div class="heading-sec">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h3>Sessions</h3>
                </div>

                <div class="col-lg-6">
                    <div class="search-sec" <?php if(Auth::user()->type == 1){?> style="width: 100%" <?php } ?>>                        
                        <form action="{{ route('admin.agency.meeting') }}" id="search_form">                            
                            <input type="text" placeholder="Search" id="search" name="search" value="<?php echo $search; ?>">
                                
                            <?php if(empty($search)){ ?>

                            <?php }else{ ?>
                            <button type="button" onclick="remove_search();">
                                <i class="fa fa-close" style="color:red"></i>
                            </button>
                            <?php } ?>
                        </form>
                    </div>
                    <?php if(Auth::user()->type == 2){?>
                    <div class="text-right">
                        <?php $a = \Crypt::encrypt(0);?>                        
                        <a class="btn btn-success btn-sm" href="<?php echo url('admin/agency/meeting/add/'.$a); ?>">Add Session</a>                        
                    </div>
                    <?php } ?>
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
                                <!-- <th>Mentor</th> -->
                                <th>Created From</th>  
                                <?php if(Auth::user()->type == 1){?>                              
                                <th>Affiliate</th>  
                                <?php }?>                              
                                <th>Title</th>                                
                                <th>Schedule</th>
                                <th>Place</th>                                
                                <th>Action</th>                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($data)){ foreach($data as $d){
                                $is_datetime_valid = true;
                                if($d->schedule_time < date('Y-m-d H:i:s')){
                                    $is_datetime_valid = false;
                                }
                                ?>                            
                            <tr>
                                <!-- <td></td> -->                            
                                <td><?php echo ($d->created_by_type == 'mentor')?'Mentor (App)':'Affiliate (Web)';?></td>
                                <?php if(Auth::user()->type == 1){?>
                                <td><?php echo $d->name; ?></td>        
                                <?php }?>
                                <td><?php echo (strlen($d->title)>30)?substr($d->title,0,30).'...':$d->title; ?></td>
                                <td><?php echo date('m-d-Y H:i', strtotime($d->schedule_time)); ?></td>
                                <td><?php echo (strlen($d->address)>30)?substr($d->address,0,30).'...':$d->address; ?></td>
                                <td>
                                    <?php $mid = \Crypt::encrypt($d->id);?>
                                    <?php if($d->created_by_type == '' && Auth::user()->type == 2 && $is_datetime_valid){?>
                                    <a class="btn btn-success btn-sm" href="<?php echo url('admin/agency/meeting/add/'.$mid); ?>" data-toggle="tooltip" title="Edit Session"><i class="fa fa-edit"></i></a>
                                    <?php }?>
                                    <a href="{{url('/admin/agency/view_meeting')}}?id={{$mid}}" title="View Session"><i class="fa fa-eye"></i></a>
                                </td>
                                
                            </tr>
                            <?php }}else{?>
                            <tr>
                                <td colspan="5" style="text-align: center;">No session found</td>
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
    
    
    function remove_search() {
        $('#search').val('');
        $('#search_form').submit();
    }

</script>
@endsection
