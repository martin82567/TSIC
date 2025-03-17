@extends('layouts.apps')
@section('content')
    <div class="db-inner-content">
        <div class="db-box">
            <div class="heading-sec">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h3>Resources</h3>
                    </div>

                    <div class="col-lg-6">
                        <div class="search-sec" >
                            <form action="{{ url('/mentee/resources') }}" id="search_form">
                                <input type="text" placeholder="Search" id="search" name="search" value="<?php echo $search; ?>">
                                <?php if(empty($search)){ ?>

                                <?php }else{ ?>
                                <button type="button" onclick="remove_search();">
                                    <i class="fa fa-close" style="color:red"></i>
                                </button>
                                <?php } ?>
                            </form>
                        </div>

                        <div class="text-right">

                        </div>

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
            <?php } ?>
            <div class="box-inner">

                <div class="listing-table">
                    <div class="table-responsive text-center">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($data as $d)
                                <tr id="tr_<?php echo $d->id; ?>" <?php if(!empty($d->is_active)){ ?> class="table-active" <?php } ?>>
                                    <td><?php echo (strlen($d->name) > 75) ? substr($d->name,0,75).'...' : $d->name ;?>
                                        <?php //echo (strlen($d->title) > 20)?substr($d->title,0,20).'...':$d->title;?>
                                    </td>
                                    <td>
                                        <p>{!! $d->description !!}</p>
                                    </td>
                                    <td>
                                        <?php if($d->type == 'pdf' || $d->type == 'url'){ ?>
                                            <a class="btn btn-success" href="{{ $d->type == 'pdf' ? $base_path . '/' . $d->file : $d->url }}" target="_blank">
                                                View
                                            </a>
                                        <?php }else { ?>
                                            <a class="btn btn-success" data-toggle="modal" data-target="#viewFile{{$d->id}}">
                                                View
                                            </a>
                                        <?php }?>
                                    </td>
                                </tr>
                                <div class="modal fade" id="viewFile{{ $d->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h6 class="modal-title" id="exampleModalLabel">{{ $d->name }}</h6>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <?php if($d->type == "image"){ ?>
                                                <img style="width: 100%" src="{{ $base_path . '/' . $d->file }}">
                                                <?php }else if($d->type == "video"){ ?>
                                                    <video width="320" height="240" controls>
                                                        <source src="{{ $d->file }}" type="video">
                                                    </video>
                                                <?php }?>
                                            </div>
                                            <div class="modal-footer">
                                                <a class="btn btn-success" data-dismiss="modal">Close</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>

        function remove_search() {
            $('#search').val('');
            $('#search_form').submit();
        }
    </script>
@endsection
