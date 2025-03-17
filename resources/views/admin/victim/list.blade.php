@extends('layouts.admin') @section('content')
<div class="db-inner-content">
    <div class="db-box">
        <div class="heading-sec">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <?php if(Request::segment(2) == 'inactivementee'){?>
                    <h3>Inactive Mentees</h3>
                    <?php }else{?>
                    <h3>Active Mentees</h3>
                    <?php }?>
                </div>

                <div class="col-md-6">
                    <div class="search-sec">
                        <?php if($type == 'active'){ ?>
                        <form action="{{ route('admin.mentee') }}" id="search_form">
                        <?php }else{ ?>
                        <form action="{{ route('admin.inactivementee') }}" id="search_form">
                        <?php } ?>
                        <input type="text" placeholder="Search" id="search" name="search" value="<?php echo $search; ?>">
                        <input type="hidden" name="sort" id="sort" value="<?php if(!empty($sort_needed)){ echo $sort;  } ?>">
                        <input type="hidden" name="column" id="column" value="<?php if(!empty($sort_needed)){ echo $column;  } ?>">
                        <?php if(empty($search)){ ?>

                        <?php }else{ ?>
                        <button type="button" onclick="remove_search();">
                                <i class="fa fa-close" style="color:red"></i>
                            </button>
                        <?php } ?>
                        </form>
                    </div>                    
                </div>
            </div>
        </div>
        <?php if(!empty(session('success_message'))){ ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
            <h4 style="margin-bottom: 0"><i class="icon fa fa-check"></i>
            <?php echo session('success_message'); Session::forget('success_message'); ?>
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
                                <?php if(!empty($is_affiliate_view)){?>
                                <th>Affiliate
                                    <span>
                                        <?php if(!empty($column) && $column != 'affiliate' || empty($sort_needed)){ ?>
                                            <a href="javascript:void(0);" onclick="sort_data('<?php echo $sort; ?>','affiliate');">
                                                <i class="fa fa-sort"></i>
                                            </a>
                                        <?php }else{ ?> 
                                            <?php if($sort == 'asc'){ ?> 
                                                <a href="javascript:void(0);" onclick="sort_data('desc','affiliate');" >
                                                    <i class="fa fa-sort-asc"></i>
                                                </a>
                                            <?php }else{ ?> 
                                                <a href="javascript:void(0);" onclick="sort_data('asc','affiliate');" >
                                                    <i class="fa fa-sort-desc"></i>
                                                </a>
                                            <?php } ?>
                                        <?php } ?>
                                    </span>
                                </th>
                                <?php }?>
                                <th>School
                                    <span>
                                        <?php if(!empty($column) && $column != 'school' || empty($sort_needed)){ ?>
                                            <a href="javascript:void(0);" onclick="sort_data('<?php echo $sort; ?>','school');">
                                                <i class="fa fa-sort"></i>
                                            </a>
                                        <?php }else{ ?> 
                                            <?php if($sort == 'asc'){ ?> 
                                                <a href="javascript:void(0);" onclick="sort_data('desc','school');" >
                                                    <i class="fa fa-sort-asc"></i>
                                                </a>
                                            <?php }else{ ?> 
                                                <a href="javascript:void(0);" onclick="sort_data('asc','school');" >
                                                    <i class="fa fa-sort-desc"></i>
                                                </a>
                                            <?php } ?>
                                        <?php } ?>
                                    </span>
                                </th>
                                <th>First Name
                                    <span>
                                        <?php if(!empty($column) && $column != 'firstname' || empty($sort_needed)){ ?>
                                            <a href="javascript:void(0);" onclick="sort_data('<?php echo $sort; ?>','firstname');">
                                                <i class="fa fa-sort"></i>
                                            </a>
                                        <?php }else{ ?> 
                                            <?php if($sort == 'asc'){ ?> 
                                                <a href="javascript:void(0);" onclick="sort_data('desc','firstname');" >
                                                    <i class="fa fa-sort-asc"></i>
                                                </a>
                                            <?php }else{ ?> 
                                                <a href="javascript:void(0);" onclick="sort_data('asc','firstname');" >
                                                    <i class="fa fa-sort-desc"></i>
                                                </a>
                                            <?php } ?>
                                        <?php } ?>
                                    </span>
                                </th>
                                <th>Last Name
                                    <span>
                                        <?php if(!empty($column) && $column != 'lastname' || empty($sort_needed)){ ?>
                                            <a href="javascript:void(0);" onclick="sort_data('<?php echo $sort; ?>','lastname');">
                                                <i class="fa fa-sort"></i>
                                            </a>
                                        <?php }else{ ?> 
                                            <?php if($sort == 'asc'){ ?> 
                                                <a href="javascript:void(0);" onclick="sort_data('desc','lastname');" >
                                                    <i class="fa fa-sort-asc"></i>
                                                </a>
                                            <?php }else{ ?> 
                                                <a href="javascript:void(0);" onclick="sort_data('asc','lastname');" >
                                                    <i class="fa fa-sort-desc"></i>
                                                </a>
                                            <?php } ?>
                                        <?php } ?>
                                    </span>
                                </th>
                                <th>Email
                                    <span>
                                        <?php if(!empty($column) && $column != 'email' || empty($sort_needed)){ ?>
                                            <a href="javascript:void(0);" onclick="sort_data('<?php echo $sort; ?>','email');">
                                                <i class="fa fa-sort"></i>
                                            </a>
                                        <?php }else{ ?> 
                                            <?php if($sort == 'asc'){ ?> 
                                                <a href="javascript:void(0);" onclick="sort_data('desc','email');" >
                                                    <i class="fa fa-sort-asc"></i>
                                                </a>
                                            <?php }else{ ?> 
                                                <a href="javascript:void(0);" onclick="sort_data('asc','email');" >
                                                    <i class="fa fa-sort-desc"></i>
                                                </a>
                                            <?php } ?>
                                        <?php } ?>
                                    </span>
                                </th>
                                <th>Last Login
                                    <span>
                                        <?php if(!empty($column) && $column != 'last_login' || empty($sort_needed)){ ?>
                                            <a href="javascript:void(0);" onclick="sort_data('<?php echo $sort; ?>','last_login');">
                                                <i class="fa fa-sort"></i>
                                            </a>
                                        <?php }else{ ?> 
                                            <?php if($sort == 'asc'){ ?> 
                                                <a href="javascript:void(0);" onclick="sort_data('desc','last_login');" >
                                                    <i class="fa fa-sort-asc"></i>
                                                </a>
                                            <?php }else{ ?> 
                                                <a href="javascript:void(0);" onclick="sort_data('asc','last_login');" >
                                                    <i class="fa fa-sort-desc"></i>
                                                </a>
                                            <?php } ?>
                                        <?php } ?>
                                    </span>
                                </th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($victim_arr as $user)

                            <?php 
                            $note_tracker = '';
                            $note_tracker_btn = '';
                            
                            if(empty($user->note_count)){
                                $note_tracker = "disabled";
                                $note_tracker_btn = "default";
                            }
                                                
                            ?>
                            <tr>
                                <?php if(!empty($is_affiliate_view)){?>
                                <td>{{ $user->admin_name }}</td>
                                <?php }?>
                                <td>{{ $user->school_name }}</td>
                                <td>{{ $user->firstname }}</td>
                                <td>{{ $user->lastname }}</td>
                                <td>{{ $user->email }}</td>
                                <td><?php echo !empty($user->last_activity_at)? date('m-d-Y', strtotime($user->last_activity_at)): '';?></td>
                                
                                <td>
                                    <?php echo !empty($user->platform_status)?'Active':'Inactive';  ?>
                                </td>
                                <td>
                                    <?php $mid = \Crypt::encrypt($user->id);?>
                                    <a class="btn btn-success btn-sm" href="<?php echo url('admin/mentee/add/'.$mid); ?>" data-toggle="tooltip" title="Edit Mentee"><i class="fa fa-edit"></i></a>
                                    <a class="btn btn-success btn-sm" href="<?php echo url('admin/viewtask/'.$mid); ?>" data-toggle="tooltip" title="View Tasks"><i class="fa fa-tasks" aria-hidden="true"></i></a>
                                    <a class="btn btn-success btn-sm" href="<?php echo url('admin/viewgoal/'.$mid); ?>" data-toggle="tooltip" title="View Goal"><i class="fa fa-bullseye"></i></a>
                                    
                                    
                                    
                                    <a class="btn btn-success btn-sm" href="<?php echo url('admin/mentee/view_report/'.$mid); ?>" data-toggle="tooltip" title="View Documents"><i class="fa fa-file" aria-hidden="true"></i></a>

                                    
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $victim_arr->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    
    function sort_data(sort_val,column) {
        $('#sort').val(sort_val);
        $('#column').val(column);
        $('#search_form').submit();
    }

    function remove_search() {
        $('#search').val('');
        $('#search_form').submit();
    }

</script>
@endsection
