<?php
    include_once ('zoom_Api.php');
    include ('connection.php');


    $error=0;
   $msg=0;
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
            <form action="" method="post">
            <div class="card">
                <input type="datetime" name="time" id="time">
            </div>
            <div class="card">
                <input type="text" name="topic" id="topic">
            </div>
        <div class="card">


    <select name="hods" id="">

<option value=""><========select hods to attend meeting=====></option>
  
<?php

$send="select * from dep";
$result= mysqli_query($con,$send);
mysqli_num_rows($result);

while ($rows= mysqli_fetch_assoc($result)) { ?>

<option value="<?php echo $rows['id']; ?>"><?php echo $rows['name']."(".  $rows['abrviation'].")"; ?></option>
<?php  }  ?>

    </select>
<button type="submit" name="send"> Send key to a hods </button>
    </form>

        </div>
        <?php
    
    $zoom_meeting = new Zoom_Api();

    $data = array();
    $data['topic'] 		= 'department meeting';
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
                <?php echo (isset($departmentmeeting_status))? "<div class='card shadow p-2'> $departmentmeeting_status </div>" : ""; ?>

                <?php 
// msg and erroor
                
         if($error){?><div class="alert alert-danger"><strong>ERROR</strong>:<?php echo htmlentities($error); ?> </div><?php } 
         else if($msg){?><div class="alert alert-success"><strong>SUCCESS</strong> : <?php echo htmlentities($msg); ?> </div><?php }
 // end of msg
        
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
        // edited hear

        if (isset($_POST['send'])) {
    
            $dpi=$_POST['hods'];
            $query = " SELECT phon_no FROM users WHERE dep_id= $dpi AND  role= 'hod' ";
            $recive= mysqli_query($con,$query);
            while ($number =mysqli_fetch_assoc($recive) ) {
                $user_phon  = $number['phon_no'];
        
                
        // user api
        //.... replace <api_key> and <secret_key> with the valid keys obtained from the platform, under profile>authentication information
        $api_key='f8a54584fbdee92c';
        $secret_key = 'ODhhZTczZjNiNDJhYTRmMjdjNjIwMGU3YzQ1YWI1YjM2N2VmNmM4ZjY2MzE4YzJlYWExY2YzMzg0NmJmNmI5Mw==';
        // The data to send to t
        $postData = array(
            'source_addr' => 'INFO',
            'encoding'=>0,
            'schedule_time' => '',
            'message' => "hellow  meeting topic= $response->topic
              user id= $response->id
              user password= $response->password
              meeting url = $response->join_url
               use those details to join the department meeting" ,
            'recipients' => [array('recipient_id' => '1','dest_addr'=>'255'.$user_phon)]//,array('recipient_id' => '2','dest_addr'=>'255700000011')]
        );
        //.... Api url
        $Url ='https://apisms.beem.africa/v1/send';
        
        // Setup cURL
        $ch = curl_init($Url);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Authorization:Basic ' . base64_encode("$api_key:$secret_key"),
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => json_encode($postData)
        ));
        
        // Send the request
        $response = curl_exec($ch);
        
        // Check for errors
        if($response === FALSE){
                echo $response;
        
            die(curl_error($ch));
        }
        //var_dump($response);
        // END OF user  API KARYZ  
          }
            
            // for browser to be viewed 
        
            
        $for_user= "INSERT INTO `meeting` (`id`, `mettin_id`, `password`, `join_url`, `dpi_id`) VALUES (NULL, '$response->id', '$response->password', '$response->join_url', '$dpi') ";
            
        if (mysqli_query($con,$for_user)) {
        
            $msg="data have been added succes";
           
        }else {
            $error="data was not added succesfuly pleas try again";
        }
        
        
            }
            // upt heare

        ?>
                <div class="form-responsive">
                        <button onclick="openWin()" name="send_interview" class="btn btn-sm btn-outline-secondary mt-2 mb-2"><i
                                class="far fa-paper-plane"></i>&nbsp;&nbsp;Send key to a hods</button>
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