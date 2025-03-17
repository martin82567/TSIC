@extends('layouts.admin')

@section('content')
<div class="db-inner-content">
<div class="db-box">

    
    <?php if(!empty(session('success_message'))){ ?>
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
            <div class="chat">
                    <div class="chat-header clearfix">
                      <!--<img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_01_green.jpg" alt="avatar">-->
                      
                      <div class="chat-about">
                        <div class="chat-with"><h3>NOTE on <span style="color:#00a7a8;"><b><?php echo $victim_details->firstname.' '.$victim_details->middlename.' '.$victim_details->lastname; ?></b></span></h3>
                        </div>
                      </div>
                      <a href="{{ url('/admin/mentee') }}" class="back-btn" style="display: inline;float: right;"><i class="fa fa-arrow-left"></i></a>
                    </div> <!-- end chat-header -->
                    
                    <div class="chat-history chat-box">
                      <ul>
                            <?php if(!empty($note_list)){ ?>
                                <?php $i = 1; foreach($note_list as $note){ ?>
                                    <?php if($i%2 == 0){ ?>
                                        <li class="clearfix">
                                            <div class="message-data align-right">
                                                <span class="message-data-time"><?php echo $note->admins_name; ?></span> &nbsp; &nbsp;
                                                <span class="message-data-name"><?php echo date('m-d-Y H:i:s' , strtotime($note->created_date)); ?></span> <i class="fa fa-circle me"></i>
                                            </div>
                                            <div class="message other-message float-right">
                                                <?php if($note->added_by == Auth::user()->id){ ?>
                                                    <a href="<?php echo url('admin/mentee/notefiledelete/'.$note->id); ?>" ><i class="fa fa-close" style="color:red;font-size:20px;float:right;"></i></a>
                                                <?php } ?>
                                                <?php echo $note->note; ?>
                                                <?php if(!empty($note->file)){ ?>
                                                    <br>
                                                    <a href="<?php echo url('admin/mentee/notefiledownload/'.$note->id); ?>" target="_blank" ><i class="fa fa-download" aria-hidden="true"></i><?php echo $note->file; ?></a>
                                                <?php } ?>
                                            </div>
                                        </li>
                                    <?php }else{ ?> 
                                        <li>
                                            <div class="message-data">
                                                <span class="message-data-name"><i class="fa fa-circle online"></i> <?php echo $note->admins_name; ?></span>
                                                <span class="message-data-time"><?php echo $note->created_date; ?></span>
                                            </div>
                                            <div class="message my-message">
                                                <?php if($note->added_by == Auth::user()->id){ ?>
                                                    <a href="<?php echo url('admin/mentee/notefiledelete/'.$note->id); ?>" ><i class="fa fa-close" style="color:red;font-size:20px;float:right;"></i></a>
                                                <?php } ?>
                                                <?php echo $note->note; ?>
                                                <?php if(!empty($note->file)){ ?>
                                                    <br>
                                                    <a href="<?php echo url('admin/mentee/notefiledownload/'.$note->id); ?>" target="_blank" ><i class="fa fa-download" aria-hidden="true"></i><?php echo $note->file; ?></a>
                                                <?php } ?>
                                            </div>
                                        </li>
                                    <?php } ?>
                                <?php $i++; } ?>
                            <?php } ?>
                        
                        
                        
                      </ul>
                      
                    </div> <!-- end chat-history -->
                    <div class="box-inner">
                        <form role="form" action="<?php echo route('admin.mentee.notesave'); ?>" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" id="victim_id" name="victim_id" value="<?php echo $victim_details->id; ?>" >
                            <div class="form-section">
                                <div class="chat-message clearfix">
                                        <div class="row mb-3">
                                                <div class="col-xl-12 col-md-12">
                                                    <div class="form-group">
                                                        <label>Note <sup>*</sup></label>
                                                        <textarea name="note" id="note" placeholder="Note" rows="3" required=""></textarea>
                                                    </div>
                                                </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="single-inp">
                                                    <label>File <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" title="Allowed file type are .png OR .jpg OR .jpeg OR .mp4 OR .3gp OR .docx OR .doc OR .pdf."></i></label>
                                                    <input type="file" name="image" id="image" class="form-control"  accept=".mp4,.3gp,.png, .jpg, .jpeg, .docx, .doc, .pdf" >
                                                    <br>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="box-footer">
                                           <button type="submit" class="btn btn-success">Submit</button>
                                            <a href="<?php echo route('admin.mentee'); ?>" class="btn btn-danger">Cancel</a>
                                         </div>
                                </div> <!-- end chat-message -->
                            </div>
                        </form>
                    </div>
                  </div>
                  
    </div>
</div>
</div>
<script>
    $(document).ready(function(){
        CKEDITOR.replace('note');
    });

    $('#image').inputFileText({
        text: 'Select File'
    });
</script>
@endsection
