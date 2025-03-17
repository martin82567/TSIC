@extends('layouts.admin') @section('content')
<div class="db-inner-content">
    <div class="db-box">
        <div class="heading-sec">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h3>Resource Category</h3>
                </div>

                <div class="col-md-6">
                    <div class="search-sec">
                        <form action="{{ route('admin.resource_category') }}">
                            <input type="text" placeholder="Search" id="search" name="search">
                        </form>
                    </div>
                    <div class="text-right">
                        <?php $a = \Crypt::encrypt(0);?>
                        <a class="btn btn-success btn-sm" href="<?php echo url('admin/resource_category/add/'.$a); ?>">Add Resource Category</a>
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
                            ?>
            </h4>
        </div>
        <?php } ?>
        <div class="box-inner">

            <div class="listing-table">
                <div class="table-responsive text-center">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($resource_category_arr as $data)
                            <tr>
                                <td>{{ $data->name }}</td>
                                <td>
                                    <?php echo !empty($data->is_active)?"Active":"Inactive"; ?>
                                </td>
                                <?php $cid = \Crypt::encrypt($data->id);?>
                                <td><a title="Edit" class="btn btn-success btn-sm" href="<?php echo url('admin/resource_category/add/'.$cid); ?>"><i class="fa fa-pencil"></i></a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $resource_category_arr->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
