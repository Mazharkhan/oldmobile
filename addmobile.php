<?php
require 'class.dbhelper.inc';
// Object Created of Class clsdbhelper
$objbrand = new clsdbhelper();
// Call Function to Get Data to fill mobile brands
$resultbrand = $objbrand->fillmobilebrand();

// Object Created of Class clsdbhelper
$objstate = new clsdbhelper();
// Call Fuction to Fill State Dropdown
$resultstate = $objstate->fillstate();
?>

<?php

if (isset($_POST) and filter_input(INPUT_SERVER, 'REQUEST_METHOD') == "POST") {
    // Add Data into Database
    try {

        if (!empty($_POST)) {
            $username = $_POST['sellername'];
            $useremail = $_POST['emailaddr'];
            $userphone = $_POST['mobnumber'];

            $objadduser = new clsdbhelper();
            $adduserresult = $objadduser->adduser($username, $useremail, $userphone);

            $posttitle = $_POST['adtitle'];
            $radioVal = $_POST['newused'];
            if ($radioVal == "0") {
                $condition=0;
            } else if ($radioVal == "1") {
                $condition=1;
            }
            $mprice = $_POST['adprice'];
            $pbrand = $_POST['brands'];
            $pmodel = $_POST['models'];
            $pnumsim = $_POST['numsim'];
            $pstate = $_POST['states'];
            $pcitiy = $_POST['cities'];
            $pLocID = 1;
            $pdescription = $_POST['description'];
            $pios = 'none';
            $pPostdate = date("m/d/Y h:i:s a", time());
            
            $objaddpost = new clsdbhelper();
            $addpostresult = $objaddpost->addnewpost($posttitle, $pdescription, $pbrand, $pmodel, $pios, $pnumsim, $pstate, $pcitiy, $pLocID, $adduserresult, $pPostdate, $condition, $mprice);
            
            if($addpostresult >0){
               echo "<script type=\"text/javascript\">alert('bleh');</script>";
            }  else {
            echo "<script type=\"text/javascript\">alert('ohhh no..');</script>";    
            }
        }
    } catch (Exception $ex) {
        
    }



    /* Below code is for image upload */

    $valid_formats = array("jpg", "png", "gif");
    $max_file_size = 1024 * 100; //100 kb
    $mobadsID = $addpostresult;
    $path = "uploads/$mobadsID/"; // Upload directory
    $count = 0;
    //Check if Directory Exists 
    $exist = is_dir($path);
    // If directory doesn't exist, create directory
    if (!$exist) {
        mkdir("$path");
        chmod("$path", 0755);
    } else {
        echo "Folder already exists";
    }


    // Loop $_FILES to execute all files
    foreach ($_FILES['files']['name'] as $f => $name) {
        if ($_FILES['files']['error'][$f] == 4) {
            continue; // Skip file if any error found
        }
        if ($_FILES['files']['error'][$f] == 0) {
            if ($_FILES['files']['size'][$f] > $max_file_size) {
                $message[] = "$name is too large!.";
                continue; // Skip large files
            } elseif (!in_array(pathinfo($name, PATHINFO_EXTENSION), $valid_formats)) {
                $message[] = "$name is not a valid format";
                continue; // Skip invalid file formats
            } else { // No error found! Move uploaded files 
                if (move_uploaded_file($_FILES["files"]["tmp_name"][$f], $path . $name)) {
                    $count++; // Number of successfully uploaded files
                }
            }
        }
    }
}
?>
<?php

//Check Which Dropdown is Selected
$passkey = explode('=', $_SERVER['QUERY_STRING']) ;
if ($passkey[0] === 'brandID'){
    // Object Created of Class clsdbhelper
    $objmodels= new clsdbhelper();
    // Variable to get brand ID
$brandID = $_GET['brandID'];
// Call Function to Get Data to fill mobile brands
$resultmodel = $objmodels->fillmobilemodel($brandID);

 while ($row = $resultmodel->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='" . $row['modelID'] . "'>" . $row['modelname'] . "</option>";
                        }
}elseif ($passkey[0] === 'stateID') {
    // Object Created of Class clsdbhelper
    $objcities= new clsdbhelper();
    // Variable to get brand ID
    $stateID = $_GET['stateID'];

    $resultcity =  $objcities->fillcity($stateID);
            
   while ($row = $resultcity->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='" . $row['cityid'] . "'>" . $row['cityname'] . "</option>";
                        }
}
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Add Mobile Ads</title>
        <link rel="StyleSheet" href="css/style.css">
        <link rel="StyleSheet" href="css/bootstrap.min.css">
        <script src="js/jquery-1.7.1.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function () {

                $("#brandID").change(function () {
                    $(this).after('<div id="loader"><img src="img/loading.gif" alt="loading subcategory" /></div>');
                    $.post('addmobile.php?brandID=' + $(this).val(), function (data) {
                        $("#modelID").html(data);
                        $('#loader').slideUp(200, function () {
                            $(this).remove();
                        });
                    });
                });
                $("#stateID").change(function () {
                    $(this).after('<div id="loader"><img src="img/loading.gif" alt="loading subcategory" /></div>');
                    $.post('addmobile.php?stateID=' + $(this).val(), function (data) {
                        $("#cityID").html(data);
                        $('#loader').slideUp(200, function () {
                            $(this).remove();
                        });
                    });
                });

            });
        </script>
    </head>
    <body>
        <form method="post" action="addmobile.php">
            <div class="wrap">
                <h2>Post an Ad to sell your mobile</h2>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <h4>Title of your Ad*</h4>
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                        <input type="text" name="adtitle"  >
                    </div>
                </div>
                <div class="row">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12"> 
                            <h4>Add Photo</h4>
                        </div>
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                            <input type="file" name="files[]" multiple="multiple" accept="image/*">

                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <h4>Condition</h4>
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                        <input type="radio" name="newused" value="0"> New
                        <input type="radio" name="newused" value="1" checked> Used
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <h4>Price in Rs.</h4>
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                        <input type="text" name="adprice"  >
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <h4>Brand Name</h4>
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                        <select name="brands" id="brandID">
<?php
// List fetched data from function
while ($row = $resultbrand->fetch(PDO::FETCH_ASSOC)) {
    echo "<option value='" . $row['brandID'] . "'>" . $row['brandname'] . "</option>";
}
?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <h4>Model Name</h4>
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                        <form method="get">
                            <select name="models" id="modelID">
                                
                            </select>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <h4>Number of SIM</h4>
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                        <select name="numsim" id="numSim">
                            <option value="1">1</option>
                            <option value="2">2</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <h4>State</h4>
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                        <select name="states" id="stateID">
<?php
// List fetched data from function
while ($rowstate = $resultstate->fetch(PDO::FETCH_ASSOC)) {
    echo "<option value='" . $rowstate['stateid'] . "'>" . $rowstate['statename'] . "</option>";
}
?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <h4>City</h4>
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                        <form method="get">
                            <select name="cities" id="cityID">
                                
                            </select>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <h4>Description</h4>
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                        <textarea name="description" form="usrform"></textarea>
                    </div>
                </div>
                <div class="row">
                    <h3>Seller Information</h3>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <h4>Name</h4>
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                        <input type="text" name="sellername"  >
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <h4>Mobile Number</h4>
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                        <input type="text" name="mobnumber"  >
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <h4>Email</h4>
                    </div>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
                        <input type="text" name="emailaddr"  >
                    </div>
                </div>
                <div class="row">
                    <input type="checkbox" title="">Send me abc Email/SMS Alerts for people looking to buy mobile handsets in  Www
                    By clicking "Post", you agree to our Terms of Use & Privacy Policy. 
                </div>
                <div class="row">
                    <input type="submit" value="Post">

                </div>
            </div>
        </form>
    </body>
</html>

