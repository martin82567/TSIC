@extends('layouts.apps')
@section('content')
    @include('message')
    <div class="db-inner-content">
        <div class="db-box">
            <div class="heading-sec">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h3>Upload Report</h3>
                    </div>
                </div>
            </div>


            <div style="margin-top: 10px; margin-left: 10px;" class="form-group">

                <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                        data-target="#createModal1">
                    Add Report
                </button>


                <div class="modal fade" id="createModal1" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                Create Report
                                <button type="button" class="close" data-dismiss="modal"
                                        aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="post" action="{{ url('/mentee/upload_report') }}"
                                      enctype="multipart/form-data">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                                    <div class="form-group">
                                        <div class="single-inp">
                                            <label>Name <sup>*</sup></label>
                                            <div class="inp">
                                                <input type="text" id="name" name="name" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>File Upload<sup>*</sup></label>
                                        <div class="single-inp">
                                            <div id="img">
                                                <input type="file" name="image" id="image" class="form-control pt-1"
                                                       accept="image/jpg, image/jpeg, image/png">
                                            </div>
                                            <br>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-danger" type="submit">
                                            Save
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{--        Report List--}}
            <div class="box-inner">
                <div class="row">
                    @foreach($reports as $report)
                        <div class="col-md-4" style="text-align: center">
                            <a href="{{ $base_path . '/' . $report->image }}" target="_blank"
                               style="display: inline-block">
                                <img src="{{ $base_path . '/' . $report->image }}" style="width: 50%; height: auto">
                            </a>
                            <br>
                            <b> {{ $report->name }}</b>
                            <br>
                            <b>{{$report->created_date}}</b>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection