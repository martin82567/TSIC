@extends('layouts.admin')
@section('content')
<div class="db-inner-content">
    <div class="db-box">
        <div class="heading-sec">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h3>Announcements</h3>
                </div>

                <div class="col-md-6">
                    <div class="search-sec">
                        <form action="{{ url('/admin/message-center/list') }}" id="search_form">
                            <input type="text" placeholder="Search" id="search" name="search" value="<?php echo $search; ?>">

                            <?php if(empty($search)){ ?>

                            <?php }else{ ?>
                            <button type="button" onclick="remove_search();">
                                <i class="fa fa-close" style="color:red"></i>
                            </button>
                            <?php } ?>
                        </form>
                    </div>

                    <div class="text-right">
                        <?php $a = \Crypt::encrypt(0);?>
                        <a class="btn btn-success btn-sm" href="{{url('/admin/message-center/add')}}">Add Message</a>
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
                                <th>Message</th>
                                <?php if(Auth::user()->type == 1){?>
                                <th>Created By</th>
                                <?php }?>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
			                    if(!empty($data)){
                                    foreach ($data as $d) {

                    		?>
                            <tr>
                                <td>
                                    <?php echo $d->message; ?>
                                    <?php echo $d->hidden ? "<br/><span class='bg-danger text-white px-2'>Message hidden from users</span" : "" ?>
                                </td>
                                <?php if(Auth::user()->type == 1){?>
                                <td><?php echo $d->name; ?></td>
                                <?php }?>
                                <td><?php echo date('m-d-y H:i a', strtotime($d->created_at)); ?></td>
                                <td>
                                    <?php if ($d->hidden) { ?>
                                        <a href="{{url('/admin/message-center/unhide')}}?id={{Crypt::encrypt($d->id)}}" onclick="return confirm('Are you sure you want to un-hide this message from users? Once un-hidden, it will be visible to the users.');" title="Un-hide"><i class="fa fa-eye"></i></a>
                                    <?php } else { ?>
                                        <a href="{{url('/admin/message-center/hide')}}?id={{Crypt::encrypt($d->id)}}" onclick="return confirm('Are you sure you want to hide this message from users? Once hidden, it won\'t be visible to the users.');" title="hide"><i class="fa fa-eye-slash"></i></a>
                                    <?php } ?>
                                    <a href="javascript:void(0);" onclick="return view_users('<?php echo $d->id; ?>');" title="View Users"><i class="fa fa-info-circle"></i></a>
                                </td>
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
<div class="modal" id="modal_users">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header" style="color:black;">
                <h4 class="modal-title">View Users</h4>
                <button type="button" class="close" data-dismiss="modal"><img src="<?php echo url('/assets/images/close.png');?>"></button>
            </div>

            <!-- Modal body -->
            <div class="modal-body" style="color:black;">
                <div class="listing-table">
                    <div class="table-responsive text-center">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">Mentors</th>
                                    <th scope="col">Mentees</th>
                                </tr>
                            </thead>
                            <?php ?>
                            <tbody>
                                <tr>
                                    <td id="mentor_names">
                                        <ul>

                                        </ul>
                                    </td>
                                    <td id="mentee_names">
                                        <ul>

                                        </ul>
                                    </td>
                                </tr>
                            </tbody>
                            <?php ?>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
<script>

    function remove_search() {
        $('#search').val('');
        $('#search_form').submit();
    }

    function view_users(i)
    {
        // alert(i);
        $('#modal_users').modal();
        $("#mentor_names ul").html('');
        $("#mentee_names ul").html('');

        $.ajax({
            url: "{{ url('/admin/message-center/message_users') }}",
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                "id": i
            },
            dataType: "json",
            success: function(data) {
                console.log(data);
                var is_mentor = data.check_message.is_mentor;
                var is_mentee = data.check_message.is_mentee;
                var message_mentors = data.message_mentors;
                var message_mentees = data.message_mentees;

                console.log(is_mentor);
                console.log(is_mentee);
                console.log(message_mentors);
                console.log(message_mentees);

                var mentor_names = "";
                if(is_mentor == 1){
                    mentor_names += `<li>All Mentors</li>`;;
                }else{
                    if(message_mentors.length == 0){
                        mentor_names += `<li>No Mentors</li>`;;
                    }else{
                        for(var i = 0; i < message_mentors.length; i++){
                            mentor_names += `<li>` + message_mentors[i].firstname + ` ` + message_mentors[i].lastname + `</li>`;
                        }
                    }
                }

                var mentee_names = "";
                if(is_mentee == 1){
                    mentee_names += `<li>All Mentees</li>`;;
                }else{
                    if(message_mentees.length == 0){
                        mentee_names += `<li>No Mentees</li>`;;
                    }else{
                        for(var i = 0; i < message_mentees.length; i++){
                            mentee_names += `<li>` + message_mentees[i].firstname + ` ` + message_mentees[i].lastname + `</li>`;
                        }
                    }
                }

                $("#mentor_names ul").html(mentor_names);
                $("#mentee_names ul").html(mentee_names);

            }
        });


    }
</script>
@endsection
