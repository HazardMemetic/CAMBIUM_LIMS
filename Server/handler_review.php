<?php
//------------------------------------------------------------------------------------------------------------------------------------------------------------//
//------------------This file is used to query the database and return data to the frontend. Review pages can use an HTTP POST to send a command--------------//
//------------------------------------------------------------------------------------------------------------------------------------------------------------//

//I realized a little too late that I could have put all this in the same file as the HTML (though I do prefer to keep client/server seperate) to avoid so many AJAX calls and SQL calls. I also realize that it doesn't matter very much because the volume is low - 3 calls is barely different than 1 in our case, because there will never be hundreds of people using it (i.e. we'll never have thousands of calls per minute etc. where optimizing this aspect matters). If we do ever get there, this can all be optimized without too much trouble.
header("Access-Control-Allow-Origin: *");

$dataRaw = file_get_contents("php://input");

$dataDecode = json_decode($dataRaw,true);

$function = $dataDecode['functionCall'];
$setID = $dataDecode['setNum'];

if($function == 'GetSet'){
    error_log("GetSetDetails() called",0);
    error_log($setID,0);
    GetSetDetails($setID);
}
else if($function == 'GetRows'){
    error_log("GetRows() called",0);
    GetRows($setID);
}
else if ($function == 'GetSamples'){
    error_log("GetSamples() called",0);
    GetSamples($setID);
}
else{
    error_log("Function value appears missing or incorrect");
}


function GetSetDetails($setID){
    $server = "localhost";
    $db = "CAMBIUM_LIMS";
    $user = "chris_beetham";
    $pass = "Cambium2018";
    $connect = mysqli_connect($server,$user,$pass,$db);
    if(!$connect){
        echo "Error: " . mysqli_connect_error() . "<br><br>";
        exit;
    }
    error_log("LIMS connection successful",0);
    error_log($setID,0);
    
    $sqlSet = "SELECT * FROM Sets WHERE SetID=$setID";
    $setResult = mysqli_query($connect, $sqlSet);
    
    
    if(mysqli_num_rows($setResult) > 0){
        $formatResult = mysqli_fetch_row($setResult);
        $jsonResult = json_encode($formatResult);
        echo $jsonResult;
    }
    else{
        error_log("failed to select any data");
    }
    mysqli_close($connect);
}

function GetRows($setID){ //How many rows will the system require
    $server = "localhost";
    $db = "CAMBIUM_LIMS";
    $user = "chris_beetham";
    $pass = "Cambium2018";
    $connect = mysqli_connect($server,$user,$pass,$db);
    if(!$connect){
        echo "Error: " . mysqli_connect_error() . "<br><br>";
        exit;
    }
    error_log("Return Rows",0);
    error_log($setID,0);
    
    $sqlSet = "SELECT * FROM Samples WHERE SetID=$setID";

    $sampleResult = mysqli_query($connect, $sqlSet);
    
    $rows =  mysqli_num_rows($sampleResult);
    
    echo $rows;
}

function GetSamples($setID){
    $server = "localhost";
    $db = "CAMBIUM_LIMS";
    $user = "chris_beetham";
    $pass = "Cambium2018";
    $connect = mysqli_connect($server,$user,$pass,$db);
    if(!$connect){
        echo "Error: " . mysqli_connect_error() . "<br><br>";
        exit;
    }
    error_log("Return Samples",0);
    error_log($setID,0);
    
    $sqlSample = "SELECT * FROM Samples WHERE SetID=$setID";

    $sampleResult = mysqli_query($connect, $sqlSample);
    
    if(mysqli_num_rows($sampleResult) > 0){
        $formatResult = mysqli_fetch_array($sampleResult);
        $jsonResult = json_encode($formatResult);
        echo $jsonResult;
    }
    else{
        error_log("failed to select any data");
    }
    mysqli_close($connect);
}
?>