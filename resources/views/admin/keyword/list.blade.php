@extends('layouts.admin')
@section('content')
<div class="db-inner-content">
<div class="db-box">
    <div class="heading-sec">
        <div class="row align-items-center">
            <div class="col-md-6">
               <h3>Keywords</h3>
            </div>
            <div class="col-md-6">
                <div class="search-sec">
                    <form action="{{url('/admin/keyword/list')}}">
                        <input type="text" placeholder="Search" id="search" name="search" value="<?php echo !empty($search)?$search:'';?>">
                    </form>
                </div>
                <div class="text-right">
                  <a class="btn btn-success btn-sm" href="{{url('/admin/keyword/add')}}" >Add</a>
                </div>
            </div>
        </div>
    </div>
    <?php if(!empty(session('success_message'))){  ?>
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
                            <th>Title</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if(!empty($data))
                    @foreach ($data as $d)
                    <?php 
                    if(empty($d->status)){
                        $ht = "fa fa-check";
                    }else{
                        $ht = "fa fa-times";
                    }
                    ?>

                    <tr>                       
                        <td>{{ $d->title }}</td>                          
                        <td>
                            <?php if(empty($d->status)){?>
                            <span id="span_<?php echo $d->id; ?>">Inactive</span>
                            <?php }else{?>
                            <span id="span_<?php echo $d->id; ?>">Active</span>
                            <?php }?>
                        </td>
                        <td> <?php $lid = \Crypt::encrypt($d->id);?>
                            
                            <a title="Edit" class="btn btn-success btn-sm" href="{{url('/admin/keyword/change_status')}}?id={{$lid}}"><i class="{{$ht}}"></i></a>
                            
                           
                        </td>
                    </tr>
                    @endforeach
                    @endif
                    </tbody>
                </table>
                {{$data->links()}}
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
    
</script>
</div>
</div>
@endsection
