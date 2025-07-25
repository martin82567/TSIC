
@extends('layouts.apps')
@section('content')
    <div class="db-inner-content">
        <div class="db-box">
            <div class="heading-sec">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h3>Announcements</h3>
                    </div>

                    <div class="col-lg-6">
                        <div class="search-sec" >

                        </div>
                    </div>
                </div>
            </div>

            <div class="box-inner">
                <div class="form-section" style="max-height: 700px; overflow-y: scroll; ">
                    <div class="chat-message clearfix">
                        <div class="row mb-3">
                            <div class="col-xl-12 col-md-12 ">
                               @foreach($data as $d)
                                   <p style="margin-bottom: 0 !important; color: lightgrey">System Admin <span style="float: right !important;">{{ $d->created_at }}</span></p>
                                <div class="card shadow mb-4">
                                    <div class="card-body">
                                   <p style="color: black">{{$d->message}}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div> <!-- end chat-message -->
                </div>
            </div>
        </div>
    </div>
    <script>


    </script>
@endsection
