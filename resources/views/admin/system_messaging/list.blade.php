@extends('layouts.admin') 
@section('content')
<?php 
    $is_actionable = false;
    if(Auth::user()->type == 1){
        $is_actionable = true;
    }else if(Auth::user()->type == 2){
        $is_actionable = true;
    }else if(Auth::user()->type == 3 && Auth::user()->parent_id == 1){
        $is_actionable = true;
    }

    
    if(Auth::user()->type == 1){
        $created_by = Auth::user()->id;
    }else if(Auth::user()->type == 2){
        $created_by = Auth::user()->id;
    }else if(Auth::user()->type == 3 && Auth::user()->parent_id == 1){
        $created_by = Auth::user()->parent_id;
    }
?>
<div class="db-inner-content">
    <div class="db-box">
        <div class="heading-sec">
            <div class="row align-items-center">
                <div class="col-md-6">                    
                    <h3>System Messaging</h3> 
                </div>

                <div class="col-md-6">
                    <div class="search-sec">                        
                        <form action="{{ url('/admin/system-messaging/list') }}" id="search_form">
                            <input type="text" placeholder="Search" id="search" name="search" value="<?php echo $search; ?>">
                            
                            <?php if(empty($search)){ ?>

                            <?php }else{ ?>
                            <button type="button" onclick="remove_search();">
                                    <i class="fa fa-close" style="color:red"></i>
                                </button>
                            <?php } ?>
                        </form>
                    </div>
                    <?php if($is_actionable){?>
                    <div class="text-right">
                        <?php $a = \Crypt::encrypt(0);?>                        
                        <a class="btn btn-success btn-sm" href="{{url('/admin/system-messaging/add')}}?id={{$a}}">Add Message</a>
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
                                <th>Message</th>
                                <th>Apps</th>
                                <th>Started From</th>
                                <th>Ended At</th> 
                                <th>Status</th> 
                                <?php if($is_actionable){?>
                                <th>Action</th>          
                                <?php }?>                      
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
			                    if(!empty($data)){
                                    foreach ($data as $d) {                                      
                    		?>
                            <tr>
                                
                                <td><?php echo $d->message; ?></td>
                                <td><?php echo $d->app_titles; ?></td>
                                <td><?php echo date('m-d-y H:i a', strtotime($d->start_datetime)); ?></td>
                                <td><?php echo date('m-d-y H:i a', strtotime($d->end_datetime)); ?></td> 
                                <td>
                                    <?php echo !empty($d->is_expired)?'<span class="text-warning">Expired</span>':'<span class="text-success">Active</span>';?>
                                </td>
                                <?php if($is_actionable){?>                               
                                <td>
                                    <?php if($d->created_by == $created_by){?>
                                    <?php if(empty($d->is_expired)){?>
                                    <a href="{{url('/admin/system-messaging/add')}}?id={{Crypt::encrypt($d->id)}}" title="Edit"><i class="fa fa-pencil"></i></a>
                                    <?php }?>
                                    <a href="{{url('/admin/system-messaging/delete')}}?id={{Crypt::encrypt($d->id)}}" onclick="return confirm('Are you sure?');" title="Remove"><i class="fa fa-trash"></i></a>
                                    <?php if(empty($d->is_expired)){?>
                                    <a href="{{url('/admin/system-messaging/expire')}}?id={{Crypt::encrypt($d->id)}}" onclick="return confirm('Are you sure?');" title="Expire"><i class="fa fa-toggle-off"></i></a>
                                    <?php }?>
                                    <?php }?>
                                </td>
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

    function remove_search() {
        $('#search').val('');
        $('#search_form').submit();
    }
</script>
@endsection
