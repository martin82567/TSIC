@extends('layouts.admin') @section('content')
<div class="db-inner-content">
    <div class="db-box">
        <div class="heading-sec">
            <div class="row align-items-center">
                <div class="col-md-6">                    
                    <h3><?php if(Request::segment(2) == 'mentor'){?>Active <?php }else{?>Inactive<?php }?> Mentors</h3>                    
                </div>

                <div class="col-md-6">
                    <div class="search-sec">
                        <?php if($type == 'active'){ ?>
                        <form action="{{ route('admin.mentor') }}" id="search_form">
                            <?php }else{ ?>
                            <form action="{{ route('admin.inactivementor') }}" id="search_form">
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
                    <!-- <div class="text-right">
                        <?php //$a = \Crypt::encrypt(0);?>
                        <a class="btn btn-success btn-sm" href="<?php //echo url('admin/mentor/add/'.$a); ?>">Add Mentor</a>
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
        <div class="box-inner">

            <?php 
            // echo '<pre>'; echo $sort_needed;
            // echo '<pre>'; echo $column;
            // echo '<pre>'; echo $sort;
            ?>

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
                                <th>Mentee Chat</th>
                                <th>Staff Chat</th>
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
                            @foreach ($mentor_arr as $user)

                            <?php 
			                    $note_tracker = '';
			                    $note_tracker_btn = '';			                    
                    		?>
                            <tr>
                                <?php if(!empty($is_affiliate_view)){?>
                                <td>{{ $user->admin_name}}</td>
                                <?php }?>
                                <td>{{ $user->firstname }}</td>
                                <td>{{ $user->lastname }}</td>
                                <td>{{ $user->email }}</td>
                                <td><?php if($user->is_chat_mentee == 1){ echo 'Yes'; }else{ echo 'No'; } ?></td>
                                <td><?php if($user->is_chat_staff == 1){ echo 'Yes'; }else{ echo 'No'; } ?></td>
                                <td><?php echo date('m-d-Y', strtotime($user->last_activity_at));?></td>
                                <td>
                                    <?php echo !empty($user->is_active)?"Active":"Inactive"; ?>
                                </td>
                                <td>
                                    <?php $mid = \Crypt::encrypt($user->id);?>
                                    <a class="btn btn-success btn-sm" href="<?php echo url('admin/mentor/add/'.$mid); ?>" data-toggle="tooltip" title="Edit Mentor"><i class="fa fa-edit"></i></a>
                                    <a href="<?php echo url('admin/mentor/session/'.$mid); ?>" title="View Session"><i class="fa fa-calendar"></i></a>
                                    <div class="modal" id="tracker_<?php echo $user->id; ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">

                                                <!-- Modal Header -->
                                                <div class="modal-header" style="color:black;">
                                                    <h4 class="modal-title">View Notes Track</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>

                                                <!-- Modal body -->
                                                <div class="modal-body" style="color:black;">
                                                    <div id="tracker_div_<?php echo $user->id; ?>">
                                                    </div>
                                                </div>

                                                <!-- Modal footer -->
                                                <div class="modal-footer" style="color:black;">
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <?php if(!empty($user->is_active)){?>
                                    <a title="Make Inactive" href="<?php echo url('admin/mentor/changestatus/'.$user->id.'/'.Request::segment(2)); ?>" id="a_<?php echo $user->id ; ?>" class="btn btn-danger btn-sm"><i class="fa fa-times"></i></a>
                                    <?php }else{?>
                                    <a title="Make Active" href="<?php echo url('admin/mentor/changestatus/'.$user->id.'/'.Request::segment(2)); ?>" id="a_<?php echo $user->id ; ?>" class="btn btn-success btn-sm"><i class="fa fa-check"></i></a>
                                    <?php }?>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $mentor_arr->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function viewtracker(user_id) {

        $.ajax({
            url: "{{ url('/admin/mentee/viewtracker') }}",
            type: "POST",
            data: {
                'user_id': user_id,
                "_token": "{{ csrf_token() }}",
            },
            dataType: "html",
            success: function(data) {
                $('#tracker_div_' + user_id).html(data);
                $('#tracker_' + user_id).modal('show');

            }
        });

        // return false;
    }

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
