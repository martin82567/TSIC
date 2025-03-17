@extends('layouts.admin') @section('content')


<div class="db-inner-content">
    <form role="form" action="{{ route('admin.offer_type.save')}}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}

        <input type="hidden" id="id" name="id" value="<?php echo !empty($offer_type->id)?$offer_type->id:0;?>">
        <div class="db-box">

            <div class="heading-sec">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <h3>Offer Type</h3>
                       
                        <a href="{{ url('/admin/active_offertypes') }}" class="back-btn" style="display: inline;float: right;"><i class="fa fa-arrow-left"></i></a>
                    </div>
                </div>
            </div>
            <div class="box-inner">
                <div class="form-section">
                    <h3>
                        <?php if(!empty($offer_type->id)){?>Edit
                        <?php }else{?>Create
                        <?php }?> offer type</h3>

                    <div class="card">

                        <div class="card-header" id="headingTwo">

                            <a class="card-link collapsed btn-link" data-toggle="collapse" href="#collapseTwo" <?php if(!empty($offer_type->id)){?> aria-expanded="false" <?php }else{?> aria-expanded="true" <?php }?> aria-controls="collapseTwo">
        <h5 class="">Offer Type</h5> 
       </a>

                        </div>


                        <div id="collapseTwo" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordionExample">
                            <div class="card-body">
                                <div id="nextdiv">

                                    @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button> {{ implode('', $errors->all(':message')) }} @foreach ($errors->all() as $error)
                                        <h4>{{ $error }}</h4>
                                        @endforeach
                                    </div>
                                    @endif
                                    <div class="">

                                        <div class="row mb-3">
                                            <div class="col-lg-12">

                                                <div class="form-group">
                                                    <label>Offer Type </label>
                                                    <input class="form-control" name="offer_type" type="text" value="<?php echo !empty($offer_type->name)?$offer_type->name:" "; ?>" required>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <br/>
                <div class="box-footer">
                    <button id="submit_id" type="submit" onclick="return formsubmit();" class="btn btn-success">Save</button>
                    <a href="{{ url('/admin/active_offertypes') }}" class="btn btn-danger">Cancel</a>

                    <!-- <button id="btn_id" type="button" class="btn btn-success pull-right" onclick="next_fields();">Next</button> -->
                </div>

            </div>

        </div>
</div>
</form>
</div>


<script>
    function formsubmit() {
        var name = $('#name').val();
        var status = $('#status').val();

        if (name == '') {
            swal('Please give name.');
            return false;
        }

        if (status == '') {
            swal('Please choose a status');
            return false;
        }
        // return true;
    }

    $('#is_active').click(function() {
        if ($(this).is(':checked'))
            $('#check_title').text('Active');
        else
            $('#check_title').text('Inactive');
    });

</script>


@endsection
