@extends('layouts.admin')

@section('content')
<div class="db-inner-content">
<div class="db-box">
    <div class="heading-sec">
        <div class="row align-items-center">
            <div class="col-md-6">
               <h3>Offer Types</h3>

            </div>
           
            <div class="col-md-6">
                <div class="search-sec"></div>
                 <div class="text-right">
            <?php $a = \Crypt::encrypt(0);?>
           <a class="btn btn-success btn-sm" href="<?php echo url('admin/offer_type/add/'.$a); ?>" >Add Offer Type</a>
        </div>
            </div>
        </div>
    </div>
    <?php if(!empty(session('success_message'))){ 

        
        
        ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&Cross;</button>
            <h4 style="margin-bottom: 0"><i class="icon fa fa-check"></i>
        <?php 
            echo session('success_message'); 
            Session::forget('success_message');
        ?></h4>
        </div>
    <?php } ?>
    <div class="box-inner">
       
        <div class="listing-table">
            <div class="table-responsive text-center">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>                            
                            <th>Offer Type Name</th>
                            @if(Auth::user()->type == 1)
                            <th>Created By</th>
                            @endif
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($offer_types as $ot)
                    <tr>
                       
                        <td>{{ $ot->name }}</td>
                        @if(Auth::user()->type == 1)
                        @if($ot->created_by==1)
                        <td>Super Admin</td>
                        @else
                        <td>{{ $ot->creator }}</td>
                        @endif
                        @endif
                        <td>
                            <?php if(empty($ot->status)){?>
                            <span id="span_<?php echo $ot->id; ?>">Inactive</span>
                            <?php }else{?>
                            <span id="span_<?php echo $ot->id; ?>">Active</span>
                            <?php }?>
                        </td>
                        <td> <?php $oid = \Crypt::encrypt($ot->id);?>
                            <a title="Edit" class="btn btn-success btn-sm" href="<?php echo url('admin/offer_type/add/'.$oid); ?>"><i class="fa fa-pencil"></i></a>
                           
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                {{ $offer_types->links() }}
            </div>
        </div>
    </div>

</div>
</div>
@endsection
