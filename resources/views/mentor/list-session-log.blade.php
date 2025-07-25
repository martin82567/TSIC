@extends('layouts.apps')
@section('content')
    <div class="db-inner-content">
        <div class="db-box">
            <div class="heading-sec">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h3>Logged Sessions</h3>
                    </div>
                    <div class="col-lg-6">
                        <div class="search-sec">

                        </div>
                        <div class="text-right">
                            <a class="btn btn-success btn-sm" href="{{ url('/mentor/sessionlog/add') }}">Log A
                                Session</a>
                        </div>

                    </div>
                </div>
            </div>
            <?php if (!empty(session('success_message'))){ ?>
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
            <?php if (!empty(session('error_message'))){ ?>
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
                                <th>Time Duration (minutes)</th>
                                <th>Method/Location</th>
                                <th>Type</th>

                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (!empty($data)){
                            foreach ($data as $d){

                                ?>
                            <tr>
                                <td>
                                        <?php echo $d->firstname . ' ' . $d->middlename . ' ' . $d->lastname; ?>
                                        <?php echo isset($d->no_show) && $d->no_show == 1 ? "<span class='bg-danger text-white px-2 ml-2'>No Show</span" : "" ?>
                                </td>
                                <td><?php echo (strlen($d->name) > 20) ? substr($d->name, 0, 20) . '...' : $d->name; ?></td>
                                <td><?php echo date('m-d-Y', strtotime($d->schedule_date)); ?></td>
                                <td><?php echo $d->time_duration; ?></td>
                                <td><?php echo !empty($d->method_value) ? $d->method_value : ''; ?></td>
                                <td>
                                        <?php if (!empty($d->type)) {
                                        if ($d->type == 1) {
                                            echo 'Group';
                                        } else {
                                            echo 'Individual';
                                        }
                                    }
                                        ?>
                                </td>
                            </tr>
                            <?php }
                            }else{
                                ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No log found</td>
                            </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                        {{ $data->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
<script>


</script>
@endsection
