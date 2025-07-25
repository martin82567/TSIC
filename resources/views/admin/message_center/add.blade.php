@extends('layouts.admin')
@section('content')
<style type="text/css">
    .card-body {
        background: #27528c;
    }

    .card-header {
        background: #00a7a8;
    }

    .db-container .form-section .btn-link {
        color: #fff;
        text-decoration: none;
    }

    .db-container .form-section .accordion .btn-link:hover {
        color: #fff;

    }


    p.arrow {
        float: right;
        margin-top: 8px;
    }

    p.arrow {
        position: absolute;
        top: 5px;
        right: 20px;
        font-size: 20px;
        color: white;
        -webkit-animation: minus 0.5s;
    }

    @keyframes minus {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    [aria-expanded="false"]>span.expanded,
    [aria-expanded="true"]>span.collapsed {
        display: none;
    }

</style>
<div class="db-inner-content">
    <form role="form" action="{{url('/admin/message-center/save')}}" method="post" enctype="multipart/form-data" id="submit_form">
        {{ csrf_field() }}
        <input type="hidden" id="id" name="id" value="">
        <input type="hidden" id="affiliate_id" name="affiliate_id" value="{{ $affiliate_id }}">
        <div class="db-box">
            <div class="heading-sec">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h3>Announcements</h3>
                    </div>
                    <div class="col-lg-6">
                        <a href="{{ url('/admin/message-center/list') }}" class="back-btn"><i class="fa fa-arrow-left"></i></a>
                    </div>
                </div>
            </div>
            <div class="box-inner">
                <div class="form-section">
                    <div class="row">
                        <div class="col-sm-8">
                            <h3>Create</h3>
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
                    <?php } ?> @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
                        <!--{{ implode('', $errors->all(':message')) }}-->
                        @foreach ($errors->all() as $error)
                        <h4>{{ $error }}</h4>
                        @endforeach
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Message <sup style="color: #ff6c6c;">*</sup></label>
                                <textarea class="form-control" name="message"  id="message"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="select">
                                <select class="form-control" name="is_mentor" id="is_mentor">
                                    <option value="">Choose a mentor option</option>
                                    <option value="1">All Mentor</option>
                                    <option value="0">Custom Mentor</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-9" id="div_mentors">
                            <div class="form-group">
                                <input type="hidden" id="mentor_id_hidden" name="mentor_id_val" value="">
                                <div class="mentor-box">
                                    <input type="text" class="form-control" placeholder="Please search mentor at least two letters" name="search_mentor" id="search_mentor" required="required" onkeyup="search(this.value,'mentor');">
                                </div>
                                <div class="mentorList" id="mentorList">
                                    <div class="mentor_inr" id="mentor_inr">
                                        <ul>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="select">
                                <select class="form-control" name="is_mentee" id="is_mentee">
                                    <option value="">Choose a mentee option</option>
                                    <option value="1">All Mentee</option>
                                    <option value="0">Custom Mentee</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-9" id="div_mentees">
                            <div class="form-group">
                                <input type="hidden" id="mentee_id_hidden" name="mentee_id_val" value="">
                                <div class="mentor-box">
                                    <input type="text" class="form-control" placeholder="Please search mentee at least two letters" name="search_mentee" id="search_mentee" required="required" onkeyup="search(this.value,'mentee');">
                                </div>
                                <div class="menteeList" id="menteeList">
                                    <div class="mentee_inr" id="mentee_inr">
                                        <ul>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row m-0" id="mentor_names_div">
                        <div id="mentor_names" class="mentor_names">
                            <h5>Mentors</h5>
                            <ul></ul>
                        </div>
                    </div>
                    <div class="row m-0" id="mentee_names_div">
                        <div id="mentee_names" class="mentor_names">
                            <h5>Mentees</h5>
                            <ul></ul>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button  type="submit" id="submit_btn" onclick="return formsubmit();" class="btn btn-success">Save</button>
                        <a href="{{ url('/admin/message-center/list') }}" class="btn btn-danger">Cancel</a>
                    </div>

                </div>

            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    function formsubmit() {

        var message = $('#message').val();
        var is_mentor = $('#is_mentor').val();
        var is_mentee = $('#is_mentee').val();
        var mentor_id_hidden = $('#mentor_id_hidden').val();
        var mentee_id_hidden = $('#mentee_id_hidden').val();

        if(message == ''){
            swal('Message is required');
            return false;
        }else if(is_mentor == '' && is_mentee == ''){
            swal('Please choose at least any option for mentor or mentee');
            return false;
        }else if(is_mentor == '0' ){
            if(mentor_id_hidden == '' ){
                swal('Please choose any mentor');
                return false;
            }
        }else if(is_mentee == '0'){
            if(mentee_id_hidden == ''){
                swal('Please choose any mentee');
                return false;
            }
        }


        // return true;

        $('#submit_btn').prop('disabled', true);
        $('#submit_form').submit();
    }

    $( document ).ready(function() {
        $('#div_mentors').hide();
        $('#div_mentees').hide();
        $('#mentorList').hide();
        $('#menteeList').hide();
        $('#mentor_names').hide();
        $('#mentee_names').hide();

        $('#is_mentor').on('change', function(){
            if(this.value == '0'){
                $('#div_mentors').show();
                $('#mentor_names').show();
            }else{
                $('#div_mentors').hide();
                $('#mentor_id_hidden').val('');
                $('#mentor_names').hide();
            }
        });
        $('#is_mentee').on('change', function(){
            if(this.value == '0'){
                $('#div_mentees').show();
                $('#mentee_names').show();
            }else{
                $('#div_mentees').hide();
                $('#mentee_id_hidden').val('');
                $('#mentee_names').hide();
            }
        });
    });

    var mentor_id_arr = [];
    var mentor_name_arr = [];
    var mentee_id_arr = [];
    var mentee_name_arr = [];

    $(document).on('change', '.mentor_ids', function() {
        if(this.checked) {
            // checkbox is checked
            mentor_id_arr.push(this.value);
            mentor_name_arr.push($(this).attr("data-name"));

        }else{
            const index = mentor_id_arr.indexOf(this.value);
            const index_name = mentor_id_arr.indexOf($(this).attr("data-name"));
            if (index > -1) {
              mentor_id_arr.splice(index, 1);
              mentor_name_arr.splice(index_name, 1);
            }
        }
        $('#mentor_id_hidden').val(mentor_id_arr);

        var mentornameData = "";

        if(mentor_name_arr.length > 0){
            $('#mentor_names').show();
            for(var i = 0; i < mentor_name_arr.length; i++){
                mentornameData += `<li>`+mentor_name_arr[i]+`<a href="javascript:void(0);" onclick="return remove('mentor','`+mentor_name_arr[i]+`','`+mentor_id_arr[i]+`');" class="close mentor_close">x</a></li>`;
            }
        }

        $('#mentor_names ul').html(mentornameData);
    });

    $(document).on('change', '.mentee_ids', function() {
        if(this.checked) {
            // checkbox is checked
            // console.log('Hi');
            mentee_id_arr.push(this.value);
            mentee_name_arr.push($(this).attr("data-name"));
        }else{
            const index = mentee_id_arr.indexOf(this.value);
            const index_name = mentee_name_arr.indexOf($(this).attr("data-name"));
            if (index > -1) {
              mentee_id_arr.splice(index, 1);
              mentee_name_arr.splice(index_name, 1);
            }
        }
        $('#mentee_id_hidden').val(mentee_id_arr);
        // console.log(mentee_id_arr);

        var menteenameData = "";

        if(mentee_name_arr.length > 0){
            $('#mentee_names').show();
            for(var i = 0; i < mentee_name_arr.length; i++){
                menteenameData += `<li>`+mentee_name_arr[i]+`<a href="javascript:void(0);" onclick="return remove('mentee','`+mentee_name_arr[i]+`','`+mentee_id_arr[i]+`');" class="close mentee_close">x</a></li>`;
            }
        }

        $('#mentee_names ul').html(menteenameData);
    });


    function remove(type,name,id)
    {

        var mentor_id_hidden = $('#mentor_id_hidden').val();
        var mentee_id_hidden = $('#mentee_id_hidden').val();

        var mentor_id_arr = mentor_id_hidden.split(',');
        var mentee_id_arr = mentee_id_hidden.split(',');

        console.log(mentor_id_arr);
        console.log(mentee_id_arr);

        if(type == 'mentor'){
            const mentor_index = mentor_id_arr.indexOf(id);
            const mentor_index_name = mentor_id_arr.indexOf(name);
            if (mentor_index > -1) {
                mentor_id_arr.splice(mentor_index, 1);
                mentor_name_arr.splice(mentor_index_name, 1);
            }

            $('#mentor_id_hidden').val(mentor_id_arr);

            var mentornameData = "";

            if(mentor_name_arr.length > 0){
                $('#mentor_names').show();
                for(var i = 0; i < mentor_name_arr.length; i++){
                    mentornameData += `<li>`+mentor_name_arr[i]+`<a href="javascript:void(0);" onclick="return remove('mentor','`+mentor_name_arr[i]+`','`+mentor_id_arr[i]+`');" class="close mentor_close">x</a></li>`;
                }
            }

            $('#mentor_names ul').html(mentornameData);

        }else if(type == 'mentee'){
            const mentee_index = mentee_id_arr.indexOf(id);
            const mentee_index_name = mentee_id_arr.indexOf(name);
            if (mentee_index > -1) {
                mentee_id_arr.splice(mentee_index, 1);
                mentee_name_arr.splice(mentee_index_name, 1);
            }

            $('#mentee_id_hidden').val(mentee_id_arr);

            var menteenameData = "";

            if(mentee_name_arr.length > 0){
                $('#mentee_names').show();
                for(var i = 0; i < mentee_name_arr.length; i++){
                    menteenameData += `<li>`+mentee_name_arr[i]+`<a href="javascript:void(0);" onclick="return remove('mentee','`+mentee_name_arr[i]+`','`+mentee_id_arr[i]+`');" class="close mentee_close">x</a></li>`;
                }
            }

            $('#mentee_names ul').html(menteenameData);

        }

    }


    function search(v,t)
    {
        var minlength = 2;
        var value = v;
        var user_type = t;
        var affiliate_id = $('#affiliate_id').val();
        // console.log(affiliate_id);
        if (value.length >= minlength) {
            // alert(value.length);
            if(user_type == 'mentor'){
                $("#mentorList").show();
                $("#mentor_inr").show();
            }else if(user_type == 'mentee'){
                $("#menteeList").show();
                $("#mentee_inr").show();
            }

            $.ajax({
                url: "{{ url('/admin/message-center/get_user_search') }}",
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "search": value,
                    "user_type": user_type,
                    "affiliate_id": affiliate_id
                },
                dataType: "json",
                success: function(data) {
                    if(user_type == 'mentor'){
                        var listData = "";
                        var mentor_id_hidden = $('#mentor_id_hidden').val();
                        mentor_id_hidden = mentor_id_hidden.split(',');
                        console.log(mentor_id_hidden);

                        if(data.length > 0){
                            for (var i = 0; i < data.length; i++) {
                                var checked = '';
                                var data_id = data[i].id;
                                var id_string = data_id.toString();

                                if(jQuery.inArray(id_string, mentor_id_hidden) != -1){
                                    checked = ' checked ';
                                }

                                listData += `<li><input type="checkbox" `+checked+` class="mentor_ids" name="" value="` + data[i].id + `" data-name="` + data[i].firstname + ` ` + data[i].lastname + `"><label for="vehicle1">` + data[i].firstname + ` ` + data[i].lastname + `</label></li>`;

                            }
                            $("#mentor_inr ul").html(listData);

                        }else{
                            $("#mentorList").hide();
                            $("#mentor_inr").hide();
                            $("#mentor_inr ul").html('');
                        }



                    }else if(user_type == 'mentee'){
                        var listData1 = "";
                        var mentee_id_hidden = $('#mentee_id_hidden').val();
                        mentee_id_hidden = mentee_id_hidden.split(',');
                        console.log(mentee_id_hidden);

                        if(data.length > 0){
                            for (var i = 0; i < data.length; i++) {
                                var checked = '';
                                var data_id = data[i].id;
                                var id_string = data_id.toString();

                                if(jQuery.inArray(id_string, mentee_id_hidden) != -1){
                                    checked = ' checked ';
                                }

                                listData1 += `<li><input type="checkbox" `+checked+` class="mentee_ids" name="" value="` + data[i].id + `" data-name="` + data[i].firstname + ` ` + data[i].lastname + `"><label for="vehicle1">` + data[i].firstname + ` ` + data[i].lastname + `</label></li>`;

                            }
                            $("#mentee_inr ul").html(listData1);
                        }else{
                            $("#menteeList").hide();
                            $("#mentee_inr").hide();
                            $("#mentee_inr ul").html('');
                        }

                    }

                }
            });
        } else {
            if(user_type == 'mentor'){
                $("#mentorList").hide();
                $("#mentor_inr").hide();
                $("#mentor_inr ul").html('');
            }else if(user_type == 'mentee'){
                $("#menteeList").hide();
                $("#mentee_inr").hide();
                $("#mentee_inr ul").html('');
            }

        }

    }


</script>
@endsection
