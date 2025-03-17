<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TAKE STOCK IN CHILDREN</title>


    <style>
        body,
        html {
            width: 100%;
            height: 100%;
            padding: 0;
            margin: 0;
        }

        .error-main {
            background: url('<?php echo url('/assets/images/404_bg.png');?>')no-repeat center;
            background-size: cover;
            width: 100%;
            height: 100%;
        }

        .error-sec {
            text-align: center;
        }

        .error-sec h2 {
            font-weight: 800;
            font-size: 109px;
            color: #393939;
            margin: 0;
            padding-top: 20px;
        }

        .error-sec h6 {
            font-weight: 400;
            font-size: 20px;
            color: #393939;
            margin: 0;
        }

        .green-btn {
            display: inline-block;
            background: #f07622;
            box-shadow: 3px 3px 8px rgba(0, 0, 0, 0.4);
            color: #fff;
            font-size: 18px;
            text-decoration: none;
            padding: 10px;
            border-radius: 5px;
            width: 220px;
            position: absolute;
            bottom: 10%;
            left: 0;
            right: 0;
            margin: auto;
        }

    </style>

</head>

<body>
<div class="error-main">
    <div class="error-sec">
        <h2>404</h2>
        <h6>No Page Found </h6>

        <div class="content">
            <a href="{{url('/admin')}}" class="green-btn">Back to Home</a>
        </div>
    </div>

</div>

</body>
</html>