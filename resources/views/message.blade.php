<?php

if(!empty(session('success_message'))){ ?>
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

<?php

if(!empty(session('error_message'))){ ?>
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