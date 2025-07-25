@extends('layouts.admin') 
@section('content')
<?php 
    $add_session = 0;
    if(Auth::user()->type == 2){
        $add_session = 1;
    }else if(Auth::user()->type == 3 && Auth::user()->parent_id != 1){
        $add_session = 1;
    }

    if(Auth::user()->type == 1){
        $agency_id = Auth::user()->id;
    }else if(Auth::user()->type == 2){
        $agency_id = Auth::user()->id;
    }else if(Auth::user()->type == 3){
        $agency_id = Auth::user()->parent_id;
    }

    $timezone = !empty(Auth::user()->timezone)?Auth::user()->timezone:'America/New_York';
    date_default_timezone_set($timezone);
    $current_datetime = date('Y-m-d H:i:s');
    // echo $current_datetime; die;
?>
<div class="db-inner-content">
    <div class="db-box">
        <div class="heading-sec">
            <div class="row align-items-center">
                <div class="col-lg-3">
                    <h3>Sessions</h3>
                    <?php if(!empty($add_session)){?>
                    <div class="pt-3">
                        <?php $a = \Crypt::encrypt(0);?>                        
                        <a class="btn btn-success btn-sm" href="<?php echo url('admin/agency/meeting/add/'.$a); ?>">Add Session</a>                        
                    </div>
                    <?php } ?>
                </div>

                <div class="col-lg-9">
                    <div class="search-sec search-w100-sec" <?php if(Auth::user()->type == 1){?> style="width: 100%" <?php } ?>>                        
                        <form action="{{ route('admin.agency.meeting') }}" id="search_form">
                            <input type="hidden" name="search_added" id="search_added" value="<?php if(!empty($search_added)){ echo $search_added;  } ?>">
                            <div class="search_input">
                                <div class="text_search_div">
                                    <input type="text" placeholder="Search" id="search" name="search_text" value="<?php echo $search_text; ?>">
                                </div>
                                

                                <input type="hidden" name="sort" id="sort" value="<?php if(!empty($sort_needed)){ echo $sort;  } ?>">
                                <input type="hidden" name="column" id="column" value="<?php if(!empty($sort_needed)){ echo $column;  } ?>">
                                    
                                <?php //if(empty($search_text)){ ?>

                                <?php //}else{ ?>
                                <!-- <button type="button" onclick="remove_search();">
                                    <i class="fa fa-close" style="color:red"></i>
                                </button> -->
                                <?php //} ?>

                                <div class="search_select">
                                    <div class="select_main">
                                        <select name="search_year" id="search_year">
                                            <option value="">Choose Year</option>
                                            <?php $years = range(date('Y'), 2019); 
                                            foreach($years as $y){?>
                                            <option value="<?php echo $y;?>" <?php if(!empty($search_year) && $search_year == $y){?> selected <?php }?>><?php echo $y;?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                    <div class="select_main">
                                        <select name="search_month" id="search_month">
                                            <option value="">Choose Month</option>
                                            <?php for($m=1; $m<=12; ++$m){ 
                                                $month_val = date('m', mktime(0, 0, 0, $m, 1)); 
                                                $month_name = date('F', mktime(0, 0, 0, $m, 1)); 
                                            ?>
                                            <option value="<?php echo $month_val; ?>"<?php if(!empty($search_month) && $search_month == $month_val){?> selected <?php }?>><?php  echo $month_name;?></option>
                                            <?php }?>
                                        </select>
                                    </div>
                                    <div class="select_main">
                                        <select name="search_status" id="search_status">
                                            <option value="">Choose Status</option>
                                            <option value="0" <?php if(isset($search_status) && $search_status == '0'){?> selected <?php }?>>Pending</option>
                                            <option value="1" <?php if(isset($search_status) && $search_status == '1'){?> selected <?php }?>>Accepted</option>
                                        </select>
                                    </div>
                                    
                                </div>
                            </div>
                            
                            <div class="search_buttons">
                                <a class="btn btn-success btn-sm" href="javascript:void(0);" id="submit_btn" onclick="select_search();">Search</a>    
                                <input type="reset" value="Reset" onclick="remove_search();"> 
                            </div>
                              

                            
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
        <?php if(!empty(session('success_message'))){ ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
            <h4 style="margin-bottom: 0"><i class="icon fa fa-check"></i>
                <?php echo session('success_message'); Session::forget('success_message');?>
            </h4>
        </div>
        <?php } ?>
        <?php if(!empty(session('error_message'))){ ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
            <h4 style="margin-bottom: 0"><i class="icon fa fa-warning"></i>
                <?php echo session('error_message'); Session::forget('error_message');?>
            </h4>
        </div>
        <?php } ?>
        <div class="box-inner">

            <div class="listing-table">
                <div class="table-responsive text-center">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Mentor
                                    <span>
                                        <?php if(!empty($column) && $column != 'mentor' || empty($sort_needed)){ ?>
                                            <a href="javascript:void(0);" onclick="sort_data('<?php echo $sort; ?>','mentor');">
                                                <i class="fa fa-sort"></i>
                                            </a>
                                        <?php }else{ ?> 
                                            <?php if($sort == 'asc'){ ?> 
                                                <a href="javascript:void(0);" onclick="sort_data('desc','mentor');" >
                                                    <i class="fa fa-sort-asc"></i>
                                                </a>
                                            <?php }else{ ?> 
                                                <a href="javascript:void(0);" onclick="sort_data('asc','mentor');" >
                                                    <i class="fa fa-sort-desc"></i>
                                                </a>
                                            <?php } ?>
                                        <?php } ?>
                                    </span>
                                </th>
                                <th>Mentee
                                    <span>
                                        <?php if(!empty($column) && $column != 'mentee' || empty($sort_needed)){ ?>
                                            <a href="javascript:void(0);" onclick="sort_data('<?php echo $sort; ?>','mentee');">
                                                <i class="fa fa-sort"></i>
                                            </a>
                                        <?php }else{ ?> 
                                            <?php if($sort == 'asc'){ ?> 
                                                <a href="javascript:void(0);" onclick="sort_data('desc','mentee');" >
                                                    <i class="fa fa-sort-asc"></i>
                                                </a>
                                            <?php }else{ ?> 
                                                <a href="javascript:void(0);" onclick="sort_data('asc','mentee');" >
                                                    <i class="fa fa-sort-desc"></i>
                                                </a>
                                            <?php } ?>
                                        <?php } ?>
                                    </span>
                                </th>
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
                                <th>Title</th>                                
                                <th>Schedule
                                    <span>
                                        <?php if(!empty($column) && $column != 'schedule' || empty($sort_needed)){ ?>
                                            <a href="javascript:void(0);" onclick="sort_data('<?php echo $sort; ?>','schedule');">
                                                <i class="fa fa-sort"></i>
                                            </a>
                                        <?php }else{ ?> 
                                            <?php if($sort == 'asc'){ ?> 
                                                <a href="javascript:void(0);" onclick="sort_data('desc','schedule');" >
                                                    <i class="fa fa-sort-asc"></i>
                                                </a>
                                            <?php }else{ ?> 
                                                <a href="javascript:void(0);" onclick="sort_data('asc','schedule');" >
                                                    <i class="fa fa-sort-desc"></i>
                                                </a>
                                            <?php } ?>
                                        <?php } ?>
                                    </span>
                                </th>
                                <th>Place</th>                                
                                <th>Status
                                    <span>
                                        <?php if(!empty($column) && $column != 'status' || empty($sort_needed)){ ?>
                                            <a href="javascript:void(0);" onclick="sort_data('<?php echo $sort; ?>','status');">
                                                <i class="fa fa-sort"></i>
                                            </a>
                                        <?php }else{ ?> 
                                            <?php if($sort == 'asc'){ ?> 
                                                <a href="javascript:void(0);" onclick="sort_data('desc','status');" >
                                                    <i class="fa fa-sort-asc"></i>
                                                </a>
                                            <?php }else{ ?> 
                                                <a href="javascript:void(0);" onclick="sort_data('asc','status');" >
                                                    <i class="fa fa-sort-desc"></i>
                                                </a>
                                            <?php } ?>
                                        <?php } ?>
                                    </span>
                                </th>                                
                                <th>Action</th>                                
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                if(!empty($data)){ 
                                    foreach($data as $d){
                                        $is_datetime_valid = true;
                                        if($d->schedule_time < $current_datetime){
                                            $is_datetime_valid = false;
                                        }

                                        $status = '';
                                        if($d->status == 0){
                                            $status = "Pending";
                                        }else if($d->status == 1){
                                            $status = "Accepted";
                                        }else if($d->status == 3){
                                            $status = "Cancelled";
                                        }
                            ?>                            
                            <tr>
                                <td><?php echo $d->mentor_firstname.' '.$d->mentor_lastname ;?></td> 
                                <td><?php echo $d->mentee_firstname.' '.$d->mentee_lastname ;?></td> 
                                <?php if(!empty($is_affiliate_view)){?>
                                <td><?php echo $d->name; ?></td>        
                                <?php }?>
                                <td><?php echo (strlen($d->title)>20)?substr($d->title,0,20).'...':$d->title; ?></td>
                                <td><?php echo get_standard_datetime($d->schedule_time); ?></td>
                                <td><?php echo (!empty($d->address))?substr($d->address,0,20).'...':$d->school_type; ?></td>

                                <!-- <td><?php //echo (strlen($d->address)>20)?substr($d->address,0,20).'...':$d->address; ?></td> -->

                                <td><?php echo $status; ?></td>
                                <td>
                                    <?php $mid = \Crypt::encrypt($d->id);?>
                                    <?php if(($agency_id == $d->agency_id) && ($d->created_by_type == '') && (!empty($add_session)) && $is_datetime_valid){?>
                                    <a class="btn btn-success btn-sm" href="<?php echo url('admin/agency/meeting/add/'.$mid); ?>" data-toggle="tooltip" title="Edit Session"><i class="fa fa-edit"></i></a>
                                    <?php }?>
                                    <a href="{{url('/admin/agency/view_meeting')}}?id={{$mid}}"  title="View Session"><i class="fa fa-eye"></i></a>
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
    
    function sort_data(sort_val,column) {
        $('#sort').val(sort_val);
        $('#column').val(column);
        $('#search_form').submit();
    }
    
    function remove_search() {
        $('#search').val('');
        $('#search_year').val('');
        $('#search_month').val('');
        $('#search_status').val('');
        $('#search_added').val('');
        $('#search_form').submit();
    }

    function select_search()
    {
        var search = $('#search').val();
        var search_year = $('#search_year').val();
        var search_month = $('#search_month').val();
        var search_status = $('#search_status').val();

        if(search_year == '' && search_month == '' && search_status == ''){
            if(search == ''){
                swal('Please add some text');
                return false;
            }
        }else{
            if(search_year == '' && search_month != ''){
                swal('Please choose year');
                return false;
            }
        }
        $('#search_added').val('1');
        $('#submit_btn').attr('disabled', true);
        $('#search_form').submit(); 
    }

    $('form input#search'). keydown(function (e) {
        if (e. keyCode == 13) {
            e. preventDefault();
            return false;
        }
    });

</script>
@endsection
