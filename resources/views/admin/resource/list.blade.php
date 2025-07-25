@extends('layouts.admin')

@section('content')
<div class="db-inner-content">
<div class="db-box">
    <div class="heading-sec">
        <div class="row align-items-center">
            <div class="col-md-6">
                <?php if(Request::segment(2) == 'inactiveresource'){?>
                <h3>Inactive Resources</h3>
                <?php }else{?>
                <h3>Active Resources</h3> 
                <?php }?>
            </div>
           
            <div class="col-md-6">
                        <div class="search-sec">
                            <?php if($type == 'active'){ ?> 
                                <form action="{{ route('admin.resource') }}" id="search_form">
                            <?php }else{ ?>
                                <form action="{{ route('admin.inactiveresource') }}" id="search_form">
                            <?php } ?>
                            
                                <input type="text" placeholder="Search" id="search" name="search" value="<?php echo $search; ?>">
                                <input type="hidden" name="sort" id="sort" value="<?php if(!empty($sort_needed)){ echo $sort;  } ?>">
                                <?php if(empty($search)){ ?>
                                    
                                <?php }else{ ?>
                                    <button type="button" onclick="remove_search();">
                                        <i class="fa fa-close" style="color:red"></i>
                                    </button>
                                <?php } ?>
                            </form>
                        </div>
                     <div class="text-right">
            <?php $a = \Crypt::encrypt(0);?>
            <a class="btn btn-success btn-sm" href="<?php echo url('admin/resource/add/'.$a); ?>" >Add Resource</a>
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
                            ?></h4>
                            </div>
                            <?php } ?>
    <div class="box-inner">
     
        <div class="listing-table">
            <div class="table-responsive text-center">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <?php if(Auth::user()->type == 1){?>
                            <th>Agency / Staff</th>                   
                            <?php }?>         
                            <th>Name
                                <span>
                                    <?php if(empty($sort_needed)){ ?>
                                        <a href="javascript:void(0);" onclick="sort_data('<?php echo $sort; ?>');">
                                            <i class="fa fa-sort"></i>
                                        </a>
                                    <?php }else{ ?> 
                                        <?php if($sort == 'asc'){ ?> 
                                            <a href="javascript:void(0);" onclick="sort_data('desc');" >
                                                <i class="fa fa-sort-asc"></i>
                                            </a>
                                        <?php }else{ ?> 
                                            <a href="javascript:void(0);" onclick="sort_data('asc');" >
                                                <i class="fa fa-sort-desc"></i>
                                            </a>
                                        <?php } ?>
                                    <?php } ?>
                                </span>
                            </th>                            
                            <th>Email</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($user_arr as $user)
                        <?php
                            if(Auth::user()->type == 1){                            
                                if(!empty($user->parent_id)){
                                    $agency_name_val = DB::table('admins')->select('name')->where('id',$user->added_by)->first();
                                    $agency_name = $agency_name_val->name;
                                }else{
                                    $agency_name = '';                                      
                                }
                                
                            }  
                        ?>
                        <tr>
                            <?php if(Auth::user()->type == 1){?>
                            <td><?php echo $agency_name ; ?></td>
                            <?php }?>
                            <td><?php echo $user->name; ?></td>
                            <td><a class="btn btn-success" href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                            </td>
                            <td>{{ $user->address }}</td>
                            <td>
                                <?php if(empty($user->is_active)){?>
                                <span id="span_<?php echo $user->id; ?>">Inactive</span>
                                <?php }else{?>
                                <span id="span_<?php echo $user->id; ?>">Active</span>
                                <?php }?>
                            </td>
                            <td><?php $uid = \Crypt::encrypt($user->id);?>

                                <?php if(Auth::user()->id == $user->parent_id || Auth::user()->id == $user->added_by){ ?>
                                    <a class="btn btn-success btn-sm" href="<?php echo url('admin/resource/add/'.$uid); ?>"><i class="fa fa-pencil"></i></a>
                                <?php }else if(Auth::user()->type == 1){ ?>
                                    <a class="btn btn-success btn-sm" href="<?php echo url('admin/resource/add/'.$uid); ?>"><i class="fa fa-pencil"></i></a>
                                <?php }else{ ?>
                                    No action it is added By admin.
                                <?php } ?>

                                <?php if(!empty($user->is_active)){  ?>
                                <a title="Make Inactive" href="<?php echo url('admin/resource/changestatus/'.$user->id.'/'.Request::segment(2)); ?>" id="a_<?php echo $user->id; ?>"  class="btn btn-danger btn-sm" ><i class="fa fa-times"></i></a>
                                <?php }else{ ?>
                                <a title="Make Active" href="<?php echo url('admin/resource/changestatus/'.$user->id.'/'.Request::segment(2)); ?>" id="a_<?php echo $user->id; ?>"  class="btn btn-success btn-sm"><i class="fa fa-check"></i></a>
                                <?php } ?>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{ $user_arr->links() }}
            </div>
        </div>
    </div>
<script>
    function sort_data(sort_val){
        $('#sort').val(sort_val);
        $('#search_form').submit();
    }
</script>
<script type="text/javascript">
    
        // var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        function statuschange(id){ 
          $('#box_overlay').show();
        $.ajax({
          url: '<?php echo url('/'); ?>/admin/resource/changestatusajax',
          type: "post",
          dataType: 'json',
          data: {'id':id, '_token': '<?php echo csrf_token(); ?>' },
          success: function(data){
            
                if(data.success ==true)
                {
                  var span_id = "span_"+id;
                  var a_id = "a_"+id;
                  
                  var span_id_val = $('#'+span_id).html();
                  var a_id_val = $('#'+a_id).html();
        
                  if(data.message == "Inactive"){                   
                      $('#'+a_id).html("Make Active");
                      $('#'+a_id).removeClass("btn-danger");
                      $('#'+a_id).addClass("btn-success");
                      $('#'+span_id).html("Inactive");
                      $('#'+span_id).removeClass("bg-green");
                      $('#'+span_id).addClass("bg-red");
                      // $('#status').text('Inactive');
                  }else{
                      $('#'+a_id).html("Make Inactive");
                      $('#'+a_id).removeClass("btn-success");
                      $('#'+a_id).addClass("btn-danger");
                      $('#'+span_id).html("Active");
                      $('#'+span_id).removeClass("bg-red");
                      $('#'+span_id).addClass("bg-green");
                      // $('#status').text('Inactive');
                  }
                  
                }
                else
                {
                  
                }
                $('#box_overlay').hide();
          }
        });      
        }
    
</script>  
<script>
    function sort_data(sort_val){
        $('#sort').val(sort_val);
        $('#search_form').submit();
    }
    function remove_search(){
        $('#search').val('');
        $('#search_form').submit();
    }
</script>    
</div>
</div>
@endsection
