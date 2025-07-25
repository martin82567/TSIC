@extends('layouts.admin') 
@section('content')
<div class="db-inner-content">
    <div class="db-box">
        <div class="heading-sec">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h3><?php echo $data->title; ?></h3>
                </div>
                <div class="col-lg-6">
                    <a href="{{url('/admin/agency/meeting')}}" class="back-btn"><i class="fa fa-arrow-left"></i></a>
                </div>                
            </div>
        </div> 
        <div class="box-inner">
            <div class="form_block_main form_block_top">
                <div class="form_block">
                    <label>Created From</label>
                    <p><?php echo ucwords(str_replace("_", " ", $data->created_from)); ?></p>
                </div>
                <div class="form_block">
                    <label>Mentor</label>
                    <p><?php echo $data->mentor_firstname.' '.$data->mentor_middlename.' '.$data->mentor_lastname; ?></p>
                </div>              
                <div class="form_block">
                    <label>Mentee</label>
                    <p><?php echo $data->mentee_firstname.' '.$data->mentee_middlename.' '.$data->mentee_lastname; ?></p>
                </div>                
                <div class="form_block">
                    <label>Title</label>
                    <p><?php echo $data->title; ?></p>
                </div>                
                <div class="form_block">
                    <label>Description</label>
                    <p><?php echo $data->description; ?></p>
                </div>               
                <div class="form_block">
                    <label>Session Location</label>
                    <p><?php echo !empty($data->address)?$data->address:$data->school_type; ?></p>
                </div>  
                <div class="form_block">
                    <label>Session Space</label>
                    <p><?php echo !empty($data->school_location)?$data->school_location:$data->school_location; ?></p>
                </div>               
                <div class="form_block">
                    <label>Date</label>
                    <p><?php echo $data->date; ?></p>
                </div>                
                <div class="form_block">
                    <label>Time</label>
                    <p><?php echo $data->time; ?></p>
                </div> 
                <div class="form_block">
                    <label>Method</label>
                    <p><?php echo $data->method_value; ?></p>
                </div>
                <div class="form_block">
                    <label>Logged</label>
                    <p><?php echo !empty($data->is_logged)?'Yes':'No'; ?></p>
                </div>                
                <div class="form_block">
                    <label>Status</label>
                    <p>
                    <?php 
                    if($data->status == 0){
                        echo "Pending";
                    }else if($data->status == 1){
                        echo "Accepted";
                    }else if($data->status == 3){
                        echo "Cancelled";
                    }
                    ?>
                    </p>
                </div>
            </div>            
        </div>
    </div>
</div>
<script>
   
</script>
@endsection
