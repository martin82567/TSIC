@extends('layouts.admin')

@section('content')
<div class="db-inner-content">
<div class="db-box">
    <div class="heading-sec">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3>Job Applications</h3>
            </div>
           
            {{-- <div class="col-lg-4">
                <div class="row">
                    <div class="col-xl-12 col-sm-12">
                        <div class="search-sec">
                            <form action="{{ route('admin.job') }}">
                                <input type="text" placeholder="Search" id="search" name="search">
                                <button type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div> --}}
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
                            <th>#</th>
                            <th>User Name</th>
                            <th>Job Name</th>
                            <th>Applicant Name</th>
                            <th>Applicant Email</th>
                            <th>Applicant Experience</th>
                            <th>Cover Letter</th>
                            <th>Resume</th>
                            <th>Created Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $i = 1;
                        ?>
                    @foreach ($job_application_arr as $jobs)
                    
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $jobs->user_id }}</td>
                            <td>{{ $jobs->job_id }}</td>
                            <td>{{ $jobs->name }}</td>
                            <td>{{ $jobs->email }}</td>
                            <td>{{ $jobs->experience }}</td>
                            <td>{{ $jobs->cover_letter }}</td>
                            <td>
                                <?php if(!empty($jobs->resume)){ ?>
                                    <a target="_blank" class="btn btn-success btn-sm" href="<?php echo url('public/uploads/jobapplication/'.$jobs->resume); ?>">View Resume</a>
                                <?php } ?>
                            </td>
                            <td>{{ $jobs->created_date }}</td>
                        </tr>
                    <?php
                        $i++;
                    ?>
                    @endforeach
                    </tbody>
                </table>
               
            </div>
        </div>
    </div>
</div>
</div>
@endsection
