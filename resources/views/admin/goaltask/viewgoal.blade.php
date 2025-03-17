@extends('layouts.admin')

@section('content')
<div class="db-inner-content">
<div class="db-box">
    <div class="heading-sec">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3>Goal / Task / Challenge</h3>
            </div>
            <div class="col-lg-4">
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
        <div class="text-right mb-3">
            <a class="btn btn-success btn-sm" href="<?php echo url('admin/goaltask/add/0'); ?>" >Add Goal / Task / Challenge</a>
        </div>
        <div class="listing-table">
            <div class="table-responsive text-center">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($goaltask_arr as $data)
                        <tr>
                            <td>{{ $data->name }}</td>
                            <td>{{ $data->type }}</td>
                            <td><?php if(empty($data->status)){ echo "Inactive";}else if($data->status == 1){ echo "Active";}else if($data->status == 2){ echo "Begin";}else{ echo "Completed"; }  ?></td>
                            
                            <td><a class="btn btn-success btn-sm" href="<?php echo url('admin/goaltask/add/'.$data->id); ?>">Edit</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{ $goaltask_arr->links() }}
            </div>
        </div>
    </div>
</div>
</div>
@endsection
