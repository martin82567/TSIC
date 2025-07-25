@extends('layouts.admin')
@section('content')
<?php 
$is_keyword_allow_access = false;
if(Auth::user()->type == 2 && Request::segment(2) == 'user'){
    $is_keyword_allow_access = true;
}
?>
<div class="db-inner-content">
<div class="db-box">
    <div class="heading-sec">
        <div class="row align-items-center">
            <div class="col-md-6">
                <?php if($type == 'agency'){?>
                    <?php if(Request::segment(2) == 'inactiveagencies'){?>
                        <h3>Inactive Affiliate</h3>
                    <?php }else{?>
                        <h3>Active Affiliate</h3>
                    <?php }?>
                <?php }else{?>
                    <?php if(Request::segment(2) == 'inactiveuser'){?>
                        <h3>Inactive Staff</h3>
                    <?php }else{?>
                        <h3>Active Staff</h3>
                    <?php }?>
                <?php }?>

            </div>
           
            <div class="col-md-6">
               
                        <div class="search-sec">
                            <?php if($type == 'agency'){
                                    if(Request::segment(2) == 'inactiveagencies'){?>
                                <form action="{{ route('admin.inactiveagencies') }}" id="search_form">
                            <?php }else{
                                ?>
                                <form action="{{ route('admin.agency') }}" id="search_form">
                                <?php 
                            }}else{
                                if(Request::segment(2) == 'inactiveuser'){
                                ?>
                                <form action="{{ route('admin.inactiveuser') }}" id="search_form">
                            <?php }else{?>
                                <form action="{{ route('admin.user') }}" id="search_form">
                            <?php }}?>
                                <input type="text" placeholder="Search" id="search" name="search" value="<?php echo $search; ?>">
                                <input type="hidden" name="sort1" id="sort1" value="<?php if(!empty($sort_needed) && !empty($sort1)){ echo $sort1;  } ?>">
                                <input type="hidden" name="sort2" id="sort2" value="<?php if(!empty($sort_needed) && !empty($sort2)){ echo $sort2;  } ?>">
                                <input type="hidden" name="column_name1" id="column_name1" value="<?php if(!empty($column_name1)){ echo $column_name1;  } ?>">
                                <input type="hidden" name="column_name2" id="column_name2" value="<?php if(!empty($column_name2)){ echo $column_name2;  } ?>">
                                <?php if(empty($search)){ ?>
                                <?php }else{ ?>
                                    <button type="button" onclick="remove_search();">
                                        <i class="fa fa-close" style="color:red"></i>
                                    </button>
                                <?php } ?>
                                
                            </form>
                        </div>
                         <div class="text-right">
            <?php
            $a = \Crypt::encrypt(0);
            $user_access = DB::table('user_access')->where('user_id',Auth::user()->id)->first();
            ?>
            <?php if(Auth::user()->type != 3 || $user_access->access_agency == 1){?>
            <?php if($type == 'agency'){?>
            <a class="btn btn-success btn-sm" href="<?php echo url('admin/agency/add/'.$a.'?menu_type=agency'); ?>" >Add Affiliate</a>
            <?php }else{?>
            <a class="btn btn-success btn-sm" href="<?php echo url('admin/agency/add/'.$a.'?menu_type=user'); ?>" >Add Staff</a>
            <?php }?>
            <?php }?>
        </div>
                    
                                   
            </div>
        </div>
    </div>
    <?php if(!empty(session('success_message'))){ 

        
        
        ?>
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
                            <!-- <th>First Name</th>
                            <th>Middle Name</th>
                            <th>Last Name</th> -->
                            <th>
                                Name
                                <span>
                                    <?php if(empty($sort1)){ ?>
                                        <a href="javascript:void(0);" onclick="sort_data('asc','name',1);">
                                            <i class="fa fa-sort"></i>
                                        </a>
                                    <?php }else{ ?> 
                                        <?php if($sort1 == 'asc'){ ?> 
                                            <a href="javascript:void(0);" onclick="sort_data('desc','name',1);" >
                                                <i class="fa fa-sort-asc"></i>
                                            </a>
                                        <?php }else{ ?> 
                                            <a href="javascript:void(0);" onclick="sort_data('asc','name',1);" >
                                                <i class="fa fa-sort-desc"></i>
                                            </a>
                                        <?php } ?>
                                    <?php } ?>
                                </span>
                            </th>
                            <?php if(Auth::user()->type == 1){
                                if($type == 'user'){?>
                            <th>
                            Affiliate
                            <span>
                                <?php if(empty($sort2)){ ?>
                                    <a href="javascript:void(0);" onclick="sort_data('asc','agency_name',2);">
                                        <i class="fa fa-sort"></i>
                                    </a>
                                <?php }else{ ?> 
                                    <?php if($sort2 == 'asc'){ ?> 
                                        <a href="javascript:void(0);" onclick="sort_data('desc','agency_name',2);" >
                                            <i class="fa fa-sort-asc"></i>
                                        </a>
                                    <?php }else{ ?> 
                                        <a href="javascript:void(0);" onclick="sort_data('asc','agency_name',2);" >
                                            <i class="fa fa-sort-desc"></i>
                                        </a>
                                    <?php } ?>
                                <?php } ?>
                            </span>
                            </th>
                            <?php } }?>
                            <th>Email</th>                               
                            <th>Status</th>
                            <?php if($is_keyword_allow_access){?>
                            <th>Allow Keyword Alerts</th>
                            <?php }?>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($user_arr as $user)
                        <tr>
                            <?php
                            if(Auth::user()->type == 1){
                                if($type == 'user'){
                                    if(!empty($user->parent_id)){
                                        $agency_name_val = DB::table('admins')->select('name')->where('id',$user->parent_id)->first();
                                        $agency_name = $agency_name_val->name;
                                    }else{
                                        $agency_name = '';                                      
                                    }
                                }
                            } 
                            ?>
                            <td>{{ $user->name }}</td>
                            <?php if(Auth::user()->type == 1){
                                if($type == 'user'){?>
                            <td><?php echo $agency_name; ?></td>
                            <?php }}?>
                            <td>{{ $user->email }}</td>                             
                            <td>
                                <?php if(empty($user->is_active)){?>
                                <span id="span_<?php echo $user->id; ?>">Inactive</span>
                                <?php }else{?>
                                <span id="span_<?php echo $user->id; ?>">Active</span>
                                <?php }?>
                            </td>
                            <?php if($is_keyword_allow_access){?>
                            <td>
                                <?php if(empty($user->is_allow_keyword_notification)){?>
                                <a href="{{url('/admin/agency/staff_keyword_access')}}/{{$user->id}}/{{$user->parent_id}}">
                                    <i class="fa fa-times"></i>
                                </a>
                                <?php }else{?>
                                <a href="{{url('/admin/agency/staff_keyword_access')}}/{{$user->id}}/{{$user->parent_id}}">
                                    <i class="fa fa-check"></i>
                                </a>
                                <?php }?>
                            </td>
                            <?php }?>
                            <td>
                                <?php $uid = \Crypt::encrypt($user->id);?>
                                <?php if($type == 'agency'){?>
                                <a title="Edit" class="btn btn-success btn-sm" href="<?php echo url('admin/agency/add/'.$uid.'?menu_type=agency'); ?>"><i class="fa fa-pencil"></i></a>
                                <?php }else{?>
                                <a title="Edit" class="btn btn-success btn-sm" href="<?php echo url('admin/agency/add/'.$uid.'?menu_type=user'); ?>"><i class="fa fa-pencil"></i></a>
                                <?php }?>

                                <?php if(!empty($user->is_active)){  ?>
                                <a title="Make Inactive" href="<?php echo url('admin/agency/changestatus/'.$user->id.'/'.Request::segment(2).'?menu_type='.$type.''); ?>" id="" class="btn btn-danger btn-sm" ><i class="fa fa-times"></i></a>
                                <?php }else{ ?>
                                <a title="Make Active" href="<?php echo url('admin/agency/changestatus/'.$user->id.'/'.Request::segment(2).'?menu_type='.$type.''); ?>" id="" onclick="" class="btn btn-success btn-sm"><i class="fa fa-check"></i></a>
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
<script type="text/javascript">
    function statuschange(id){ 
        $('#box_overlay').show();
        $.ajax({
          url: '<?php echo url('/'); ?>/admin/agency/statuschangeajax',
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
                  }else{
                      $('#'+a_id).html("Make Inactive");
                      $('#'+a_id).removeClass("btn-success");
                      $('#'+a_id).addClass("btn-danger");
                      $('#'+span_id).html("Active");
                      $('#'+span_id).removeClass("bg-red");
                      $('#'+span_id).addClass("bg-green");
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
    function sort_data(sort_val,value,no){
        if(no == 1){
            $('#sort1').val(sort_val);
            $('#column_name1').val(value);
        }else{
            $('#sort2').val(sort_val);
            $('#column_name2').val(value);
        }
        $('#search_form').submit();
    }
    function remove_search(){
        $('#search').val('');
        $('#column_name1').val('');
        $('#column_name2').val('');
        $('#search_form').submit();
    }
</script>
</div>
</div>
@endsection
