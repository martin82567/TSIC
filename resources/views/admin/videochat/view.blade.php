@extends('layouts.admin') @section('content')
    <div class="db-inner-content">
        <div class="db-box">
            <div class="heading-sec">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h3>Video Room Details</h3>
                    </div>
                    <div class="col-lg-6">
                        <a href="{{url('/admin/videochat/list')}}" class="back-btn"><i class="fa fa-arrow-left"></i></a>
                    </div>
                </div>
            </div>

            <div class="box-inner">
                <div class="form_block_main form_block_conversation">
                    <div class="form_block">
                        <label>Room duration (in minute)</label>
                        <p><?php echo !empty($exist_chat->duration) ? convert_sec_to_min($exist_chat->duration) : ' - '; ?></p>
                    </div>
                    <div class="form_block">
                        <label>Remaining over conversation (in minute)</label>
                        <p><?php echo !empty($video_chat_user->remaining_time) ? convert_sec_to_min($video_chat_user->remaining_time) : ' - '; ?></p>
                    </div>
                    <div class="form_block">
                        <label>Recordings</label>
                        <div class="recording_btns">
                            @if($type == 'twilio')
                                    <?php if (!empty($exist_chat->duration)){ ?>
                                    <?php
                                    $i = 1; foreach ($recordings as $r){
                                    $vid = strstr($r, '.mkv');
                                    if (!empty($vid)) {
                                        $rec_string = "Recording " . $i . " (Video)";
                                    } else {
                                        $rec_string = "Recording " . $i . " (Audio)";
                                    }

                                    ?>
                                <a href="<?php echo $r;?>" class="btn btn-success btn-sm"
                                   target="_blank"> <?php echo $rec_string; ?></a>



                                    <?php $i++;
                                } ?>
                                <?php } ?>

                                <div class="recording_video">
                                        <?php if (!empty($exist_chat->duration)){ ?>
                                        <?php
                                        $i = 1; foreach ($recordings as $r){
                                        $vid = strstr($r, '.mkv');
                                        if (!empty($vid)) {
                                            $rec_string = "Recording " . $i . " (Video)";
                                        } else {
                                            $rec_string = "Recording " . $i . " (Audio)";
                                        }

                                        ?>

                                        <?php if (!empty($vid)){ ?>
                                    <video width="320" height="240" controls>
                                        <source src="<?php echo $r;?>">
                                    </video>

                                    <?php } ?>

                                        <?php $i++;
                                    } ?>
                                    <?php } ?>
                                </div>
                                <div class="recording_audio">
                                        <?php if (!empty($exist_chat->duration)){ ?>
                                        <?php
                                        $i = 1; foreach ($recordings as $r){
                                        $aud = strstr($r, '.mka');


                                        ?>

                                        <?php if (!empty($aud)){ ?>
                                    <audio controls>
                                        <source src="<?php echo $r;?>">
                                    </audio>
                                    <?php } ?>

                                        <?php $i++;
                                    } ?>
                                    <?php } ?>
                                </div>
                        </div>
                        @else
                            <div class="recording_video">
                                @if(!empty($recordings))
                                    <video width="320" height="240" controls>
                                        <source src="{{$recordings}}">
                                    </video>
                                @endif
                            </div>
                        @endif

                    </div>
                    <div class="form_block">
                        <label>Status</label>
                        <p><?php
                           if ($exist_chat->duration == '') {
                               $status = 'In-progress';
                           } else {
                               $status = $exist_chat->status;
                           }
                           echo $status;
                           ?>

                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">

    </script>
@endsection
