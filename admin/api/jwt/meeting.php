<?php
    include_once ('zoom_Api.php');
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>ZOOM | RT</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../font-awesome/css/all.css">
    <script src="../bootstrap/js/bootstrap.js"></script>
</head>

<body>
    <div class="container">
        <div class="card-body">
            <div class="card">
                <h1 class="m-2 p-2 text-center">START VIDEO MEETING</h1>
            </div>
        </div>
        <?php
    
    $zoom_meeting = new Zoom_Api();

    $data = array();
    $data['topic'] 		= 'Interview';
    $data['start_date'] = date("Y-m-d h:i:s", strtotime('tomorrow'));
    $data['duration'] 	= 30;
    $data['type'] 		= 2;
    $data['password'] 	= floor(rand());

    try {
        $response = $zoom_meeting->createMeeting($data);
        
        //echo "<pre>";
        //print_r($response);
        //echo "<pre>";
        ?>
        <div class="card-body col-sm-12 p-relative justify-content-around">
            <div class="card p-3 shadow">
                <?php echo (isset($interview_status))? "<div class='card shadow p-2'> $interview_status </div>" : ""; ?>

                <?php 
        
        echo "<h5 style='color: grey;'> Event: "	. "<span class='text-dark'> $response->topic</span></h5>";
        echo "<br>";
        echo "Meeting ID: ". "<span class='alert bg-dark col-sm-4 text-light text-center shadow' style='height: 25px; line-height: 5%;'>$response->id</span>";
        echo "<br>";
        echo "Meeting Password: "."<span class='alert col-sm-4 text-dark text-center shadow' style='height: 25px; line-height: 5%;'>$response->password</span>";
        echo "<br>";
        echo "<br>";
        if (isset($response->topic)) {
            $zoom_interview_topic = "$response->topic";
            if (isset($response->id)) {
                $zoom_interview_id = "$response->id";
                if (isset($response->password)) {
                    $zoom_interview_password = "$response->password";
                    if (isset($response->join_url)) {
                        $zoom_interview_url = "$response->join_url";
                    }
                }
            }
        }
        ?>
                <div class="form-responsive">
                        <button onclick="openWin()" name="send_interview" class="btn btn-sm btn-outline-secondary mt-2 mb-2"><i
                                class="far fa-paper-plane"></i>&nbsp;&nbsp;Send key to a candidate</button>
                </div>
                <?php
        echo "<div class='col-sm-3'><a class='btn btn-sm btn-primary' href='". $response->join_url ."' target='_blank'>Start meeting</a></div>";
        echo "<br> Or copy this link below" . "<span style='text-decoration: underline'>$response->join_url</span>";
        ?>

            </div>
        </div>
        <?php
        
    } catch (Exception $ex) {
        echo $ex;
    }
    
    ?>

    </div>
    <script>
        var myWindow;

        function openWin() {
        myWindow = window.open("./send.php?topic=<?php echo "$zoom_interview_topic"; ?>&id=<?php echo "$zoom_interview_id"; ?>&password=<?php echo "$zoom_interview_password"; ?>&url=<?php echo "$zoom_interview_url"; ?>", "", "width=500, height=500");
            }function closeWin() {
        myWindow.close();
            }
    </script>
</body>

</html>