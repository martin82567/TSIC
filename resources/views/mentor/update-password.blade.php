@extends('layouts.apps')
@section('content')
@include('message')

    <div class="db-inner-content">
<div class="db-box">
    <div class="heading-sec">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h3>Update Password</h3>
            </div>
        </div>
</div>


    <form method="POST" action="{{ route("mentor.password.update") }}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <div class="form-group">
            <label class="required" for="password">New Password</label>
            <input class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" type="password" name="password" id="password" required>
            @if($errors->has('password'))
                <span class="text-danger">{{ $errors->first('password') }}</span>
            @endif
        </div>
        <div class="form-group">
            <label class="required" for="password_confirmation">Repeat New Password</label>
            <input class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}" type="password" name="password_confirmation" id="password_confirmation" required>
        </div>
        <div class="form-group">
            <button class="btn btn-danger" type="submit">
                Save
            </button>
        </div>
    </form>

</div>
</div>

@endsection
