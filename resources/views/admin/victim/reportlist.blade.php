@extends('layouts.admin')
@section('content')
<div class="db-inner-content">
    <div class="db-box">
        <div class="heading-sec">
            <div class="row align-items-center">
                <div class="col-md-6"><h3>Documents</h3></div>               
                <div class="col-md-6">
                    <a href="{{url('/admin/mentee')}}" class="back-btn"><i class="fa fa-arrow-left"></i></a>
                </div>
            </div>
        </div>        
        <div class="box-inner">           
            <div class="listing-table">
                <div class="table-responsive text-center">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Image</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $d)
                        <tr>                           
                            <td>{{ $d->name }}</td>                            
                            <td><a href="{{url('/public/uploads/report')}}/{{$d->image}}" target="_blank">{{ $d->image}}</a></td>
                        </tr>
                        @endforeach                        
                        </tbody>
                    </table>
                    {{ $data->links() }}
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
