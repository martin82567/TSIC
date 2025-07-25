@extends('layouts.admin') @section('content')


<div class="db-inner-content">
    <form role="form" action="{{ route('admin.keyword.save')}}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}

        <input type="hidden" id="id" name="id" value="<?php //echo !empty($mtl_status->id)?$mtl_status->id:0;?>">
        <div class="db-box">

            <div class="heading-sec">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <h3>Add Keyword</h3>
                        <a href="{{ url('/admin/keyword/list') }}" class="back-btn" style="display: inline;float: right;"><i class="fa fa-arrow-left"></i></a>
                    </div>
                </div>
            </div>
            <?php if(!empty(session('err_msg'))){?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                    echo session('err_msg'); 
                    Session::forget('err_msg');
                ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php } ?>
            <div class="box-inner">
                <div class="form-section">
                    <div class="row mb-3">
                        <div class="col-xl-6 col-md-6">
                            <div class="form-group">
                                <div class="single-inp">
                                    <label>Title</label>
                                    <div class="inp">
                                        <input type="text" id="title" name="title"  maxlength="100" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="box-footer">
                            <button id="" type="submit"  class="btn btn-success">Save</button>
                            <a href="{{ url('/admin/keyword/list') }}" class="btn btn-danger">Cancel</a>
                            
                        </div> 
                    </div>             
                </div>
                

            </div>

        </div>
</div>
</form>
</div>

<script type="text/javascript">
    jQuery(function($){
        $('#title').keyup(function(e){
            if (e.which === 32) {
                alert('No space are allowed in keyword');
                var str = $(this).val();
                str = str.replace(/\s/g,'');
                $(this).val(str);            
            }
        }).blur(function() {
            var str = $(this).val();
            str = str.replace(/\s/g,'');
            $(this).val(str);            
        });
    });
</script>

@endsection
