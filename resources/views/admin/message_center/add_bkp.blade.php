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

                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="check">
                                <label>
                                    <input type="checkbox" name="is_mentor" class="" value="1">Mentor
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="check">
                                <label>
                                    <input type="checkbox" name="is_mentee" class="" value="1">Mentee
                                </label>
                            </div>
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
        var apps = $('#submit_form input:checked').length;

        console.log(apps);


        if(message == ''){
            swal('Message is required');
            return false;
        }else if (apps == 0) {
            swal('Please add at least one app');
            return false;
        }

        // return true;

        $('#submit_btn').prop('disabled', true);
        $('#submit_form').submit();
    }

</script>
@endsection
