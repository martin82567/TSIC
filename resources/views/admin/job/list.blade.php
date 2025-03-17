@extends('layouts.admin')

@section('content')
<div class="db-inner-content">
<div class="db-box">
    <div class="heading-sec">
        <div class="row align-items-center">
            <div class="col-md-6">
                <?php if(Request::segment(2) == 'job'){?>
                <h3>Active Jobs</h3>
                <?php }else{?>
                <h3>Inactive Jobs</h3>
                <?php }?>
            </div>
           
            <div class="col-md-6">
                        <div class="search-sec">
                            <form action="{{ route('admin.job') }}">
                                <input type="text" placeholder="Search" id="search" name="search">
                               
                            </form>
                        </div>
                   <div class="text-right">
        <?php $a = \Crypt::encrypt(0);?>
        <a class="btn btn-success btn-sm" href="<?php echo url('admin/job/add/'.$a); ?>" >Add Job</a>
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
                            <th>Affiliate / Staff</th>
                            <?php }?>
                            <th>Job Title</th>
                            <th>Status</th>
                            <th style="width: 150px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($job_arr as $jobs)

                        <?php 
                            if(Auth::user()->type == 1){                            
                                if(!empty($jobs->created_by)){
                                    $agency_name_val = DB::table('admins')->select('name')->where('id',$jobs->created_by)->first();
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
                            <td>{{ $jobs->job_title }}</td>                      
                            <td>
                                <?php if(empty($jobs->status)){ ?>
                                <span id="span_<?php echo $jobs->id; ?>">Inactive</span>
                                <?php }else if($jobs->status == 1){?>
                                <span id="span_<?php echo $jobs->id; ?>">Active</span>
                                <?php }?>                                
                            </td>
                            <td><?php $jid = \Crypt::encrypt($jobs->id);?>
                                <a class="btn btn-success btn-sm" href="<?php echo url('admin/job/add/'.$jid); ?>" title="Edit Job"><i class="fa fa-edit"></i></a>
                                <?php if(!empty($jobs->status)){  ?>
                                <a href="<?php echo url('admin/job/changestatus/'.$jobs->id.'/'.Request::segment(2)); ?>" id="a_<?php echo $jobs->id; ?>" class="btn btn-danger btn-sm"><i class="fa fa-times"></i></a>
                                <?php }else{ ?>
                                <a href="<?php echo url('admin/job/changestatus/'.$jobs->id.'/'.Request::segment(2)); ?>" id="a_<?php echo $jobs->id; ?>" class="btn btn-success btn-sm"><i class="fa fa-check"></i></a>
                                <?php } ?>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{ $job_arr->links() }}
            </div>
        </div>
    </div>
<script type="text/javascript">
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

    function statuschange(id){ 
      $('#box_overlay').show();
    $.ajax({
      url: '<?php echo url('/'); ?>/admin/job/changestatusajax',
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
</div>
</div>
@endsection
