@extends('layouts.admin')

@section('content')
<div class="db-inner-content">
<div class="db-box">
    <div class="heading-sec">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3><?php echo !empty($goaltask_details->name)?$goaltask_details->name:''; ?></h3>
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
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Goal / Task / Challenge Name</th>
                            <th>Note</th>
                            <th>Completed Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($victim_note_arr as $data)
                    
                        <tr>
                            <td>{{ $data->type }}</td>
                            <td>{{ $data->note }}</td>
                            <td>{{ $data->created_date }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
