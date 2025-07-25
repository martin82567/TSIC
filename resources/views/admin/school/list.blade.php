@extends('layouts.admin') @section('content')
<div class="db-inner-content">
    <div class="db-box">
        <div class="heading-sec">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <?php if(Request::segment(2) == 'inactive-school'){?>
                    <h3>Inactive Schools</h3>
                    <?php }else{?>
                    <h3>Active Schools</h3>
                    <?php }?>
                </div>

                <div class="col-md-6">
                    <div class="search-sec">
                        <?php if($type == 'active'){ ?>
                        <form action="{{ url('/admin/active-school') }}" id="search_form">
                            <?php }else{ ?>
                            <form action="{{ url('/admin/inactive-school') }}" id="search_form">
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
                    <!-- <div class="text-right">
                        <?php //$a = \Crypt::encrypt(0);?>
                        <a class="btn btn-success btn-sm" href="<?php //echo url('admin/school/add/'.$a); ?>">Add School</a>
                    </div> -->
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
        <?php 
        $access_affiliate = 0;
        if(Auth::user()->type == 1){
            $access_affiliate = 1;
        }else if(Auth::user()->type == 3 && Auth::user()->parent_id == 1){
            $access_affiliate = 1;
        }
        ?>
        <div class="box-inner">
            <div class="listing-table">
                <div class="table-responsive text-center">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <?php if(!empty($access_affiliate)){?>
                                <th>Affiliate</th>
                                <?php }?>
                                <th>Name</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Zip</th>
                                <!-- <th>Created At</th> -->
                                <th>Status</th>
                                <!-- <th>Action</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($school_arr as $school)
                            <tr>
                                <?php if(!empty($access_affiliate)){?>
                                <td>{{ $school->affiliate_name }}</td>
                                <?php }?>
                                <td>{{ $school->name }}</td>
                                <td>{{ $school->city }}</td>
                                <td>{{ $school->state }}</td>
                                <td>{{ $school->zip }}</td>
                                <!-- <td>{{ date('m-d-Y H:i:s' , strtotime($school->created_at)) }}</td> -->
                                <td><?php echo !empty($school->status)?"Active":"Inactive"; ?></td>
                                <!-- <td>
                                    <?php //$mid = \Crypt::encrypt($school->id);?>
                                    <a class="btn btn-success btn-sm" href="<?php //echo url('admin/school/add/'.$mid); ?>" data-toggle="tooltip" title="Edit School"><i class="fa fa-edit"></i></a>
                                    
                                    <?php //if(!empty($school->status)){?>
                                    <a title="Make Inactive" href="<?php //echo url('admin/school/change_status/'.$school->id.'/'.Request::segment(2)); ?>" id="a_<?php //echo $school->id ; ?>" class="btn btn-danger btn-sm"><i class="fa fa-times"></i></a>
                                    <?php //}else{?>
                                    <a title="Make Active" href="<?php //echo url('admin/school/change_status/'.$school->id.'/'.Request::segment(2)); ?>" id="a_<?php //echo $school->id ; ?>" class="btn btn-success btn-sm"><i class="fa fa-check"></i></a>
                                    <?php //}?>
                                </td> -->
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $school_arr->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    
    function sort_data(sort_val) {
        $('#sort').val(sort_val);
        $('#search_form').submit();
    }

    function remove_search() {
        $('#search').val('');
        $('#search_form').submit();
    }

</script>
@endsection
