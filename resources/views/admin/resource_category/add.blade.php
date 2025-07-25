@extends('layouts.admin')

@section('content')


                    <div class="db-inner-content">
                    <form role="form" action="{{ route('admin.resource_category.save')}}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" id="id" name="id" value="<?php echo !empty($user_details->id)?$user_details->id:0;?>">
                        <div class="db-box">
                            <div class="heading-sec">
                                <div class="row align-items-center">
                                <div class="col-lg-12">
                                    <h3>Resource Category</h3>
                                    
                                    <a href="{{ url('/admin/resource_category') }}" class="back-btn" style="display: inline;float: right;"><i class="fa fa-arrow-left"></i></a>
                                </div>
                                    
                                </div>
                            </div>
                            
                            @if ($errors->any()) 
                            <div class="alert alert-danger alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
                                <!--{{ implode('', $errors->all(':message')) }}-->
                                @foreach ($errors->all() as $error)
                                <h4>
                                    {{ $error }}</h4>
                            @endforeach
                        </div>
                        @endif

                            <div class="box-inner">
                                <div class="form-section">
                                   <div class="row">
                                   <div class="col-sm-8">
                                    <h3>Create Resource Category</h3>
                                       </div>
                                       <div class="col-sm-4 text-right">
                                             <div class="check active-toggle">
                                                    <label>
                                                    <input type="checkbox" name="is_active" id="is_active" value="1"  <?php if((isset($user_details->is_active) && ($user_details->is_active == 1)) || !isset($user_details->is_active)){ ?>checked=""<?php } ?>> <span id="check_title"> <?php if((isset($user_details->is_active) && ($user_details->is_active == 1)) || !isset($user_details->is_active)){ ?>Active <?php }else{ ?>Inactive <?php } ?></span>
                                                    </label>
                                                </div>
                                       </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="single-inp">
                                                <label>Name <sup>*</sup></label>
                                                <div class="inp">
                                                    <input type="text" id="name" name="name" value="<?php echo !empty($user_details->name)?$user_details->name:'';?>" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <button type="submit" class="btn btn-success">Submit</button>
                                        <a href="{{ url('/admin/resource_category') }}" class="btn btn-danger" >Cancel</a>
                                       
                                    </div>
                                </div>                                
                            </div>

                            
</form>
                        </div>
                    </div>
@endsection
