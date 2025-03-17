@extends('layouts.apps')
@section('content')
<!-- <p>Mentee</p> -->

<div class="db-inner-content">
    <div class="db-box">
        <div class="heading-sec">
            <div class="row align-items-center">
                <div class="col-lg-8">

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

            <div id="remote-media"></div>

            <div id="preview">
                <div id="local-media"></div>
            </div>

            <div>
                <input id="selfType" type="hidden" value="mentee">
                <input id="selfId" type="hidden" value="{{ Auth::user()->id }}">
                <input id="otherType" type="hidden" value="mentor">
                <input id="otherId" type="hidden" value="15">
            </div>

            <button class="btn btn-success" id="roomJoinBtn">Join Room</button>
            <button class="btn btn-danger" id="roomLeftBtn" style="display: none">Leave Room</button>

        </div>

    </div>
</div>

<style></style>

<script type="text/javascript" src="https://mentorappdev.tsic.org:3000/quickstart/index.js"></script>

@endsection
