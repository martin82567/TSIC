@extends('layouts.admin')
@section('content')
<div class="db-inner-content">
    <div class="db-box">
        <div class="heading-sec">
            <div class="row align-items-center">
                <div class="col-lg-8">                
                    <h3>Switch To {{($type)}}</h3>                
                </div>            
            </div>
        </div>    
        <?php if(!empty(session('msg'))){ ?>
        <div class="alert alert-<?php  echo session('msg_class'); ?> alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
            <h4 style="margin-bottom: 0"><i class="icon fa fa-check"></i>
                <?php  echo session('msg'); Session::forget('msg');?>
            </h4>
        </div>
        <?php } ?>    
        <div class="box-inner">
            <div class="form-section"> 
                <form role="form" method="post" action="{{url('/admin/switch-acc/save_post')}}">
                    {{csrf_field()}}
                    <input type="hidden" name="type" value="{{$type}}">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <!-- <div class="select"> -->
                                <select name="id" id="id" class="form-control" required="required">
                                    <option value="">Choose a {{$type}}</option>
                                    @if(!empty($data))
                                    @foreach($data as $a)
                                    <option value="{{$a->id}}">{{$a->name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            <!-- </div> -->
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Go</button>
                        </div>
                    </div>                    
                </div>    
                </form>               
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    
</script>
@endsection
