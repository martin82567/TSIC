@extends('layouts.admin')
@section('content')
<?php 
if(Auth::user()->type == 3){   
    $user_access = DB::table('user_access')->where('user_id',Auth::user()->id)->first(); 
    $access_e_learning = $user_access->access_e_learning;
}else{    
    $access_e_learning = 1;    
}

$is_super = 0;
if(Auth::user()->type == 1){
    $is_super = 1;
}else if(Auth::user()->type == 3 && Auth::user()->parent_id == 1){
    $is_super = 1;
}
?>
<div class="db-inner-content">
<div class="db-box">
    <div class="heading-sec">
        <div class="row align-items-center">
            <div class="col-md-6">
                <?php if(Request::segment(2) == 'e_learning'){?>
                <h3>Active Resource</h3>
                <?php }else{?>
                <h3>Inactive Resource</h3>
                <?php }?>
            </div>
            <div class="col-md-6">
                <div class="search-sec">
                    <form action="{{ route('admin.e_learning') }}" id="search_form">
                        <input type="text" placeholder="Search" id="search" name="search" value="<?php echo $search; ?>">
                        <input type="hidden" name="sort" id="sort" value="<?php if(!empty($sort_needed)){ echo $sort;  } ?>">
                    </form>
                </div>
                <div class="text-right">
            <?php $a = \Crypt::encrypt(0);?>            
            <?php if(!empty($access_e_learning)){ ?>
                <a class="btn btn-success btn-sm" href="<?php echo url('admin/e_learning/add/'.$a); ?>" >Add Resource</a>
            <?php } ?>
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
                            <?php if(!empty($is_super)){?>
                            <th>Affiliates</th>
                            <th>Created By</th>
                            <?php }?>
                            <th>Name
                                <span>
                                    <?php if(empty($sort_needed)){ ?>
                                        <a href="javascript:void(0);" onclick="sort_data('asc');">
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
                            <th>Type</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($e_learning_arr as $data)
                        <tr id="tr_<?php echo $data->id; ?>" <?php if(!empty($data->is_active)){ ?> class="table-active" <?php } ?>>
                            <?php if(!empty($is_super)){?>
                            <td>
                                <?php echo $data->affiliate_names; ?>
                            </td>
                            <td>
                                <?php echo $data->creator_name; ?>
                                <?php if($data->created_type == 1){?>
                                <span>( Super Admin )</span> 
                                <?php }else if($data->created_type == 2){?>
                                <span>( Affiliate )</span> 
                                <?php }else if($data->created_type == 3){?>
                                <span>( Staff )</span> 
                                <?php }?>
                            </td>
                            <?php }?>
                            <td><?php echo (strlen($data->name) > 75) ? substr($data->name,0,75).'...' : $data->name ;?>
                                <?php //echo (strlen($d->title) > 20)?substr($d->title,0,20).'...':$d->title;?>
                            </td>
                            <td>
                                <?php if($data->type == "image"){ ?> 
                                    <i class="fa fa-file-image-o" aria-hidden="true"></i>
                                <?php }else if($data->type == "video"){ ?>
                                    <i class="fa fa-video-camera" aria-hidden="true"></i> 
                                <?php }else if(($data->type == "url")){ ?> 
                                    <i class="fa fa-external-link-square" aria-hidden="true"></i>
                                <?php }else if(($data->type == "pdf")){?>
                                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                
                                <?php }?>                              
                            </td>
                            <td><span id="status_div_<?php echo $data->id; ?>"><?php echo !empty($data->is_active)?"Active":"Inactive"; ?></span></td>
                            <td><?php $eid = \Crypt::encrypt($data->id);?>
                                <a title="Edit" class="btn btn-success btn-sm" href="<?php echo url('admin/e_learning/add/'.$eid); ?>"><i class="fa fa-pencil"></i></a>
                                <?php if(!empty($data->is_active)){ ?>
                                    <a title="Make Inactive" id="action_a_<?php echo $data->id; ?>" class="btn btn-danger btn-sm" href="<?php echo url('admin/e_learning/changestatus/'.$data->id.'/'.Request::segment(2)); ?>"><i class="fa fa-times"></i></a><br>
                                <?php }else{ ?> 
                                    <a title="Make Active" id="action_a_<?php echo $data->id; ?>" class="btn btn-success btn-sm" href="<?php echo url('admin/e_learning/changestatus/'.$data->id.'/'.Request::segment(2)); ?>"><i class="fa fa-check"></i></a><br>
                                <?php } ?>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{ $e_learning_arr->links() }}
            </div>
        </div>
    </div>
</div>
</div>
<script>
    function sort_data(sort_val){
        $('#sort').val(sort_val);
        $('#search_form').submit();
    }
</script>
@endsection
