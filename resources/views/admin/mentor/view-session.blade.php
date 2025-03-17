@extends('layouts.admin') @section('content')
<div class="db-inner-content">
    <div class="db-box">
        <div class="heading-sec">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h3>Session</h3>
                </div>
                <div class="col-lg-6">
                    <a href="{{url('/admin/mentor')}}" class="back-btn"><i class="fa fa-arrow-left"></i></a>
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
        <?php if(!empty(session('error_message'))){ ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
            <h4 style="margin-bottom: 0"><i class="icon fa fa-warning"></i>
                <?php
                    echo session('error_message');
                    Session::forget('error_message');
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
                                <th>Mentee</th>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Time Duration</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($session as $s)

                            <?php

                    		?>
                            <tr>
                                <td>
                                    {{ $s->firstname}} {{ $s->middlename }} {{ $s->lastname }}
                                    <?php echo $s->no_show ? "<span class='bg-danger text-white px-2 ml-2'>No Show</span" : "" ?>
                                </td>
                                <td><?php echo (strlen($s->name)>25)?substr($s->name,0,25).'...':$s->name;?></td>
                                <td>{{ date('m-d-Y', strtotime($s->schedule_date)) }}</td>
                                <td>{{ $s->time_duration }}</td>
                                <td><?php echo !empty($s->status)?"Active":"Inactive"; ?></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $session->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
<script>


    function sort_data(sort_val) {
        $('#sort').val(sort_val);
        $('#search_form').submit();
    }

    function remove_search() {
        $('#search').val('');
        $('#search_form').submit();
    }

</script>
@endsection
