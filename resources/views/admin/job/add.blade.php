@extends('layouts.admin') @section('content')


<div class="db-inner-content">
    <form role="form" action="{{ route('admin.job.save')}}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="hidden" id="id" name="id" value="<?php echo !empty($user_details->id)?$user_details->id:0;?>">
        <div class="db-box">
            <div class="heading-sec">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h3>Job</h3>
                    </div>
                    <div class="col-lg-6">
                        <a href="{{ url('/admin/job') }}" class="back-btn"><i class="fa fa-arrow-left"></i></a>
                    </div>
                </div>
            </div>
            <?php if(!empty(session('success_message'))){ ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
                <h4 style="margin-bottom: 0"><i class="icon fa fa-check"></i>
                </h4>
            </div>
            <?php } ?> @if ($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
                <!--{{ implode('', $errors->all(':message')) }}-->
                @foreach ($errors->all() as $error)
                <h4>
                    {{ $error }}</h4>
                @endforeach
            </div>
            @endif

            <div class="box-inner">
                <div class="form-section">
                    <div class="row">
                        <div class="col-lg-8">
                            <h3>@if(empty($user_details->status)) Create @else Edit @endif job</h3>
                        </div>

                        <div class="col-lg-4 text-right">
                            <div class="check active-toggle">
                                <label>
                                    <input type="checkbox" name="status" id="" value="1"  <?php if((isset($user_details->status) && ($user_details->status == 1)) || !isset($user_details->status)){ ?>checked=""<?php } ?>> <span id="check_title"> <?php if((isset($user_details->status) && ($user_details->status == 1)) || !isset($user_details->status)){ ?>Active <?php }else{ ?>Inactive <?php } ?></span>
                                    </label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-xl-12 col-md-6">
                            <div class="single-inp">
                                <label>Job Title <sup>*</sup></label>
                                <div class="inp">
                                    <input type="text" id="job_title" name="job_title" value="<?php echo !empty($user_details->job_title)?$user_details->job_title:'';?>" required>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-xl-12 col-md-6">
                            <div class="single-inp">
                                <label>Application URL <sup>*</sup></label>
                                <div class="inp">
                                    <input type="url" id="application_url" name="application_url" value="<?php echo !empty($user_details->application_url)?$user_details->application_url:'';?>" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="manual_desc">
                        <div class="row mb-3">
                            <div class="col-xl-12 col-md-6">
                                <div class="single-inp">
                                    <label>Job Summary</label><br>
                                    <div class="inp">
                                        <textarea class="form-control" rows="2" name="summary" placeholder="Enter Job Summary" id="summary" required><?php echo !empty($user_details->summary)?$user_details->summary:""; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            
                        </div>

                        
                    </div>
                    <div class="box-footer">
                        <button id="submit_id" type="submit" class="btn btn-success">Save</button>
                        <a href="{{ url('/admin/job') }}" class="btn btn-danger">Cancel</a>
                        <!-- <button id="btn_id" type="button" class="btn btn-success pull-right" onclick="next_fields();">Next</button> -->
                    </div>
                </div>

            </div>



        </div>
    </form>
</div>

<script>
    $(function() {
        $(".datepicker").datepicker({
            dateFormat: 'mm-dd-yy'

        });
    });

    $(document).ready(function() {
        CKEDITOR.replace('summary');
        
    });

</script>
@endsection
