@extends('layouts.admin')

@section('content')
<div class="db-inner-content">
<div class="db-box">
    <div class="heading-sec">
        <div class="row align-items-center">
            <div class="col-lg-12">
                <h3 style="display: inline;">{{$type}}</h3>
            <a href="{{ url('/admin/mentee') }}" class="back-btn" style="display: inline;float: right;"><i class="fa fa-arrow-left"></i></a>
                  
            </div>
            <!--<div class="col-lg-4">
                <div class="row">
                    <div class="col-xl-12 col-sm-12">
                        <div class="search-sec">
                            <form action="{{ route('admin.goaltask') }}">
                                <input type="text" placeholder="Search" id="search" name="search">
                                <button type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>-->
        </div>
    </div>
    <?php if(!empty(session('error_message'))){ ?>
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
                                <h4 style="margin-bottom: 0">
                            <?php 
                                echo session('error_message'); 
                                Session::forget('error_message');
                            ?></h4>
                            </div>
                            <?php } ?>
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
        <!--<div class="text-right mb-3">
            <a class="btn btn-success btn-sm" href="<?php echo url('admin/goaltask/add/0'); ?>" >Add Goal / Task / Challenge</a>
        </div>-->
        <div class="listing-table">
            <div class="table-responsive text-center">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Mentee Name</th>
                            <th>{{$type}} Name</th>
                            <th>Note</th>
                            <th>Status</th>
                            <th>Begin Time</th>
                            <th>Completed Time</th>
                            <th>View User Uploaded Files</th>
                            <th>View Note</th>
                            <th>Update Point</th>
                        </tr>
                    </thead>
                    <tbody>
                    
                    @foreach ($data_list as $data)
                    <?php
                    $datalist = DB::table('goaltaskuserfiles')
                                    ->select('*')
                                    ->where('goaltaskuserfiles.goaltask_id',$data->id)
                                    ->where('goaltaskuserfiles.added_by',$data->victims_id)
                                    ->get();
                    if(!empty($datalist->toarray())){
                        $datalist = $datalist->toarray();
                    }else{
                        $datalist = array();
                    }  

                    ?>
                        <tr>
                            <td>{{ $data->firstname.' '.$data->middlename.' '.$data->lastname }}</td>
                            <td>{{ $data->name }}</td>
                            <td>{{ $data->note }}</td>
                            <td><?php if(empty($data->datastatus)){ echo "Not Started";}else if($data->datastatus == 1){ echo "Begin";}else if($data->datastatus == 2){ echo "Completed";}  ?></td>
                            <td>
                                <?php
                                if($data->begin_time != '0000-00-00 00:00:00' || $data->begin_time ==''){
                                    $begin_time = DateTime::createFromFormat("Y-m-d H:i:s" , $data->begin_time);                
                                    echo $begin_time->format('m-d-Y H:i:s'); 
                                }else{
                                    echo '';
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if($data->complated_time != '0000-00-00 00:00:00' || $data->complated_time ==''){
                                    $complated_time = DateTime::createFromFormat("Y-m-d H:i:s" , $data->complated_time);                
                                    echo $complated_time->format('m-d-Y H:i:s'); 
                                }else{
                                    echo '';
                                }
                                ?>
                            </td>
                            <td>
                                <?php if(!empty($datalist)){ ?>
                                    <!-- <a class="btn btn-success btn-sm" href="<?php echo url('admin/goaltask/add/'.$data->id); ?>">View User Uploaded Files</a> -->
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_<?php echo $data->id; ?>">View User Uploaded Files</button>
                                          
                                    <!-- The Modal -->
                                    <div class="modal" id="modal_<?php echo $data->id; ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" style="color:black;">User Uploaded Files</h4>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Date & Time</th>
                                                                <th>Filename</th>
                                                                <th>View</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php $i = 1; foreach($datalist as $data1){ ?>
                                                                <tr>
                                                                    <td><?php echo $i; ?></td>
                                                                    <td><?php 
                                                                    if($data1->created_date != '0000-00-00 00:00:00' || $data1->created_date ==''){
                                                                        $file_date = DateTime::createFromFormat("Y-m-d H:i:s" , $data1->created_date);                
                                                                        echo $file_date->format('m-d-Y H:i:s'); 
                                                                    }else{
                                                                        echo '';
                                                                    }
                                                                    ?></td>
                                                                    <td><?php echo $data1->file_name; ?></td>
                                                                    <td><a class="btn btn-success btn-sm" target="_blank" href="<?php echo url('public/uploads/goaltask').'/'.$data1->file_name; ?>" >View</a></td>
                                                                </tr>
                                                            <?php $i++; } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    

                                <?php } ?>

                                <!-- Button to Open the Modal -->

                                
                            </td>
                            <td>
                                <?php $gid = \Crypt::encrypt($data->id);?>
                                <?php $vid = \Crypt::encrypt($data->victims_id);?>
                                <a class="btn btn-success btn-sm" href="<?php echo url('admin/viewnote/'.$gid.'/'.$vid); ?>">View Notes</a>
                            </td>
                            <td><?php if(Auth::user()->type == 2){ ?>
                                @if($data->datastatus == 2)
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_point" onclick="getpoint({{$data->victims_id}},{{$data->points}},{{$data->id}});">Point: {{$data->points}}</button>
                                @endif
                                <?php } ?>
                            </td>
                        </tr>
                    @endforeach
                    
                     <!-- The Modal -->
                    <div class="modal" id="modal_point" style="width: 600px; margin-left: 400px;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" style="color:black;">Update Point</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <form action="{{route('admin.taskpoint')}}" method="post" id="taskpoint">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="staff_id" id="t_id">
                                    <input type="hidden" name="tgc_id" id="tgc_id">
                                    <input type="hidden" name="t_type" id="t_type" value="{{$type}}">
                                <div class="modal-body">
                                    <span id="point_msg" style="color: red;"></span>
                                    <input class="form-control" type="text" onkeyup="this.value = this.value.replace(/\D/g,'');" onkeydown="this.value = this.value.replace(/\D/g,'');" maxlength="2" name="point" id="point">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-info" onclick="getmaxpoint()">Submit</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    
                    
                    </tbody>
                </table>
                {{ $data_list->links() }}
            </div>
        </div>
    </div>
</div>
</div>
@endsection
