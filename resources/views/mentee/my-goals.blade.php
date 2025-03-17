@extends('layouts.apps')
@section('content')
    <!-- <p>Mentee</p> -->
    <div class="db-inner-content">
        <div class="db-box">
            <div class="heading-sec">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                    </div>

                    <div class="col-lg-6">
                        <div class="search-sec" >

                        </div>



                    </div>
                </div>
            </div>
            <div class="box-inner">
                <div class="listing-table">
                    <div class="table-responsive text-center">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <?php if($type == 'pending'){?>
                                <th>Action</th>
                                <?php }?>
                                <?php if($type == 'completed'){?>
                                <th></th>
                                <?php }?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if(!empty($data)){
                            foreach($data as $d){
                           /* $is_datetime_valid = true;
                            if($d->end_date < date('Y-m-d H:i:s')){
                                $is_datetime_valid = false;
                            }*/

                            ?>
                            <tr>
                                <td><?php echo (strlen($d->name)>20)?substr($d->name,0,20).'...':$d->name; ?></td>
                                <td><?php echo (strlen($d->description)>50)?substr($d->description,0,50).'...':$d->description; ?></td>
                                <td><?php echo get_standard_datetime($d->start_date); ?></td>
                                <td><?php echo get_standard_datetime($d->end_date); ?></td>
                                <?php $mid = \Crypt::encrypt($d->id);?>
                                <?php if($type == 'pending'){?>
                                <td>
                                    <a href="{{url('mentee/my_goals/detail')}}?type={{$type == 'pending'}}?id={{$mid}}" class="btn btn-success"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                </td>
                                <?php }?>
                                <td>
                                    <a href="{{url('mentee/my_goals/detail')}}?id={{$mid}}" class="btn btn-success"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                </td>

                            </tr>
                            <?php }}else{?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No session found</td>
                            </tr>
                            <?php }?>
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
