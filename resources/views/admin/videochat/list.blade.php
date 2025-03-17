@extends('layouts.admin') @section('content')
    <?php
    $view_access = 0;
    if (Auth::user()->type == 1) {
        $view_access = 1;
    } else if (Auth::user()->type == 3 && Auth::user()->parent_id == 1) {
        $view_access = 1;
    }
    ?>
    <div class="db-inner-content">
        <div class="db-box">
            <div class="heading-sec">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3>Video Chats</h3>
                    </div>

                    <div class="col-md-6">
                        <div class="search-sec">
                            <form action="{{ url('/admin/videochat/list') }}" id="search_form">
                                <input type="text" placeholder="Search" id="search" name="search"
                                       value="<?php echo $search; ?>">
                                <input type="hidden" name="sort" id="sort"
                                       value="<?php if(!empty($sort_needed)){ echo $sort;  } ?>">
                                <input type="hidden" name="column" id="column"
                                       value="<?php if(!empty($sort_needed)){ echo $column;  } ?>">
                                <?php if (empty($search)){ ?>

                                <?php }else{ ?>
                                <button type="button" onclick="remove_search();">
                                    <i class="fa fa-close" style="color:red"></i>
                                </button>
                                <?php } ?>
                            </form>
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
                                <?php if (Auth::user()->type != 2){ ?>
                                <th>Affiliate</th>
                                <?php } ?>
                                <th>Sender</th>
                                <th>Sender Name
                                    <span>
                                        <?php if (!empty($column) && $column != 'sender' || empty($sort_needed)){ ?>
                                            <a href="javascript:void(0);"
                                               onclick="sort_data('<?php echo $sort; ?>','sender');">
                                                <i class="fa fa-sort"></i>
                                            </a>
                                        <?php }else{ ?>
                                            <?php if ($sort == 'asc'){ ?>
                                                <a href="javascript:void(0);" onclick="sort_data('desc','sender');">
                                                    <i class="fa fa-sort-asc"></i>
                                                </a>
                                            <?php }else{ ?>
                                                <a href="javascript:void(0);" onclick="sort_data('asc','sender');">
                                                    <i class="fa fa-sort-desc"></i>
                                                </a>
                                            <?php } ?>
                                            <?php } ?>
                                    </span>
                                </th>
                                <th>Receiver</th>
                                <th>Receiver Name
                                    <span>
                                        <?php if (!empty($column) && $column != 'receiver' || empty($sort_needed)){ ?>
                                            <a href="javascript:void(0);"
                                               onclick="sort_data('<?php echo $sort; ?>','receiver');">
                                                <i class="fa fa-sort"></i>
                                            </a>
                                        <?php }else{ ?>
                                            <?php if ($sort == 'asc'){ ?>
                                                <a href="javascript:void(0);" onclick="sort_data('desc','receiver');">
                                                    <i class="fa fa-sort-asc"></i>
                                                </a>
                                            <?php }else{ ?>
                                                <a href="javascript:void(0);" onclick="sort_data('asc','receiver');">
                                                    <i class="fa fa-sort-desc"></i>
                                                </a>
                                            <?php } ?>
                                            <?php } ?>
                                    </span>
                                </th>
                                <th>Room Duration</th>
                                <th>Created From</th>
                                <th>Status
                                    <span>
                                        <?php if (!empty($column) && $column != 'status' || empty($sort_needed)){ ?>
                                            <a href="javascript:void(0);"
                                               onclick="sort_data('<?php echo $sort; ?>','status');">
                                                <i class="fa fa-sort"></i>
                                            </a>
                                        <?php }else{ ?>
                                            <?php if ($sort == 'asc'){ ?>
                                                <a href="javascript:void(0);" onclick="sort_data('desc','status');">
                                                    <i class="fa fa-sort-asc"></i>
                                                </a>
                                            <?php }else{ ?>
                                                <a href="javascript:void(0);" onclick="sort_data('asc','status');">
                                                    <i class="fa fa-sort-desc"></i>
                                                </a>
                                            <?php } ?>
                                            <?php } ?>
                                    </span>
                                </th>
                                <th>Date
                                    <span>
                                        <?php if (!empty($column) && $column != 'date' || empty($sort_needed)){ ?>
                                            <a href="javascript:void(0);"
                                               onclick="sort_data('<?php echo $sort; ?>','date');">
                                                <i class="fa fa-sort"></i>
                                            </a>
                                        <?php }else{ ?>
                                            <?php if ($sort == 'asc'){ ?>
                                                <a href="javascript:void(0);" onclick="sort_data('desc','date');">
                                                    <i class="fa fa-sort-asc"></i>
                                                </a>
                                            <?php }else{ ?>
                                                <a href="javascript:void(0);" onclick="sort_data('asc','date');">
                                                    <i class="fa fa-sort-desc"></i>
                                                </a>
                                            <?php } ?>
                                            <?php } ?>
                                    </span>
                                </th>
                                <?php if (!empty($view_access)){ ?>
                                <th>Action</th>
                                <?php } ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (!empty($data)){
                            foreach ($data as $d) {

                                $sender_name = '';
                                $receiver_name = '';

                                $unique_name = $d->unique_name;

                                if ($d->sender_type == 'mentor') {
                                    $sender_name = $d->sender_mentor_firstname . ' ' . $d->sender_mentor_lastname;
                                } else if ($d->sender_type == 'mentee') {
                                    $sender_name = $d->sender_mentee_firstname . ' ' . $d->sender_mentee_lastname;
                                }

                                if ($d->receiver_type == 'mentee') {
                                    $receiver_name = $d->receiver_mentee_firstname . ' ' . $d->receiver_mentee_lastname;
                                } else if ($d->receiver_type == 'mentor') {
                                    $receiver_name = $d->receiver_mentor_firstname . ' ' . $d->receiver_mentor_lastname;
                                }


                                if ($d->status == '') {
                                    $status = 'In-progress';
                                } else {
                                    $status = $d->status;
                                }

                                // $participants = DB::table('video_chat_participants')->where('room_sid',$d->room_sid)->get()->toarray();

                                // $i = 0;
                                // $len = count($participants);

//                                $sender_duration = $d->sender_duration;
//                                $receiver_duration = $d->receiver_duration;

                                // if(!empty($participants)){
                                //     foreach($participants as $p){
                                //         if ($len > 1) {
                                //             if ($i == 0) {
                                //                 $receiver_duration = $p->duration;
                                //             } else if ($i == $len - 1) {
                                //                 $sender_duration = $p->duration;
                                //             }
                                //         }else{
                                //             $sender_duration = $p->duration;
                                //             $receiver_duration = '0';
                                //         }
                                //         $i++;
                                //     }
                                // }


                                ?>
                            <tr>
                                    <?php if (Auth::user()->type != 2){ ?>
                                <td><?php echo $d->affiliate_name; ?></td>
                                <?php } ?>
                                <td><?php echo ucwords($d->sender_type); ?></td>
                                <td><?php echo $sender_name; ?></td>
                                <td><?php echo ucwords($d->receiver_type); ?></td>
                                <td><?php echo $receiver_name; ?></td>
                                <td><?php echo !empty($d->duration) ? convert_sec_to_min($d->duration) : ' 0 '; ?></td>
                                <td><?php if ($d->created_from == 'app') {
                                        echo '<i class="fa fa-mobile" title="app"></i>';
                                    } else {
                                        echo '<i class="fa fa-globe" title="web"></i>';
                                    } ?></td>
                                <td><?php echo $status; ?></td>
                                <td><?php echo get_standard_datetime($d->created_at); ?></td>
                                    <?php if (!empty($view_access)){ ?>
                                <td>
                                    <a href="{{url('/admin/videochat/details')}}/{{$d->room_sid ? $d->room_sid : $d->unique_name}}"
                                       title="View Details"><i class="fa fa-eye"></i></a></td>
                                <?php } ?>
                            </tr>
                                <?php
                            }
                            }
                            ?>
                            </tbody>
                        </table>
                        {{ $data->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function sort_data(sort_val, column) {
            $('#sort').val(sort_val);
            $('#column').val(column);
            $('#search_form').submit();
        }

        function remove_search() {
            $('#search').val('');
            $('#search_form').submit();
        }
    </script>
@endsection
