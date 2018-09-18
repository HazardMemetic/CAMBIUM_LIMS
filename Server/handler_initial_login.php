<?php 
header("Access-Control-Allow-Origin: *");
//$content = file_get_contents("php://input");
//connectToDatabase($content);
$ip = get_ip_address();
writeAddress($ip);
//$parjson = json_decode($content,true);
//echo $parjson["openplease"];

//----------------------------------------------------------------//
//----------------Recieve Post from Cylinder Form-----------------//
//----------------------------------------------------------------//
$setID = $_POST["setNumber"];

$projectNumber = $_POST["projectNumber"]; 
$projectName = $_POST["projectName"];
$client = $_POST["client"];
$contractor = $_POST["contractor"];
$locationOnStructure = $_POST["locationOnStructure"];
$concreteSupplier = $_POST["concreteSupplier"];
$castBy = $_POST["castBy"];
$timeMixerCharged = $_POST["timeMixerCharged"];
$specifiedSlump = $_POST["specifiedSlump"];
$airTemp= $_POST["airTemp"];
$specifiedAirContent = $_POST["specifiedAirContent"];
$waterAdded = $_POST["waterAdded"];
$truckNumber = $_POST["truckNumber"];
$nominalAggSize = $_POST["nominalAggSize"];
$admixtures = $_POST["admixtures"];
$minCuringTemp = $_POST["minCuringTemp"];
$reportDate = $_POST["reportDate"];
$dateCast = $_POST["dateCast"];
$receivedSample = $_POST["receivedSample"];
$pickupDateTime = $_POST["pickupDateTime"];
$measuredSlump = $_POST["measuredSlump"];
$concreteTemp = $_POST["concreteTemp"];
$measuredAir = $_POST["measuredAir"];
$authorizedBy = $_POST["authorizedBy"];
$quantity = $_POST["quantity"];
$accQuantity = $_POST["accQuantity"];
$typeOfMold = $_POST["typeOfMold"];
$ticketNumber = $_POST["ticketNumber"];
$maxCuringTemp = $_POST["maxCuringTemp"];

//recieve cylinder arrays
$cylID = $_POST["CylID"];
$curType = $_POST["CurType"];
$diam = $_POST["Diam"];
$diamTwo = $_POST["DiamTwo"];
$weight = $_POST["Weight"];
$length = $_POST["Length"];
$lengthTwo = $_POST["LengthTwo"];
$breakDays = $_POST["BreakDays"];


//----------------------------------------------------------------//
//----------------Setup Data to Be Sent to Server-----------------//
//----------------------------------------------------------------//


$setInfo = array($projectNumber,$projectName,$client,$contractor,$locationOnStructure,$concreteSupplier,$castBy,$timeMixerCharged,$specifiedSlump,$airTemp,$specifiedAirContent,$waterAdded,$truckNumber,$nominalAggSize,$admixtures,$minCuringTemp,$reportDate,$dateCast,$receivedSample,$pickupDateTime,$measuredSlump,$concreteTemp,$measuredAir,$authorizedBy,$quantity,$accQuantity,$typeOfMold,$ticketNumber,$maxCuringTemp);

//--------------------Cylinder Data--------------------//
//to store each new array into 2D; might end up serializing and storing with the set details as a backup.
$cylinders = [];

//loop through and create each array, then append it to the cylinders array
for($i=0; $i < count($cylID); $i++){
    
    //Cylinder property calculations
    $averageDiam = ($diam[$i] + $diamTwo[$i])/2;
    $averageLength = ($length[$i] + $lengthTwo[$i])/2;
    
    $area = round(($averageDiam / 2) * ($averageDiam / 2) * 3.14159,2);
    $volume = round($averageLength * $area,2);
    $density = round($weight[$i] / $volume,4);

    
    //create the date that the cylinder should be broken. Based on the cast date and the $breakDays variable (i.e. 7 day, 28 day etc.)
    $daysToString = (string) $breakDays[$i];
    $formatString = $daysToString . " days";
    $startDate = date_create_from_format('Y-m-d', $dateCast);
    $breakDate = date_format(date_add($startDate, date_interval_create_from_date_string($formatString)), 'Y-m-d');
    
    $cyl = array($cylID[$i],$curType[$i],$diam[$i],$diamTwo[$i],$averageDiam,$weight[$i],$length[$i],$lengthTwo[$i],$averageLength,$area,$volume,$density,$breakDays[$i],$breakDate);
    $cylinder[$i] = $cyl;
    
}

storeSetDetails($setInfo, $setID);
storeCylinderDetails($cylinder, count($cylID), $setID, $dateCast, $receivedSample);

//for debugging data sent to the database.
/*
$stringify = json_encode($cylinder);
echo $stringify;
*/

//----------------------------------------------------------------------//
//-------------------------Store data in database-----------------------//
//----------------------------------------------------------------------//

function storeSetDetails($setInfo, $setID){
    $server = "localhost";
    $db = "CAMBIUM_LIMS";
    $user = "chris_beetham";
    $pass = "Cambium2018";
    $connect = mysqli_connect($server,$user,$pass,$db);
    if(!$connect){
        echo "Error: " . mysqli_connect_error() . "<br><br>";
        exit;
    }
    echo "LIMS connection successful" . "<br><br>";
    echo "Host: " . mysqli_get_host_info($connect) . "<br>" . "Info: " . mysqli_info($connect) . "<br>" . "Status: " . mysqli_stat($connect) . "<br><br>";
    
    //was having trouble with arrays in SQL statements. Expanding it out like this for now.
    $projectNumber = $setInfo[0];
    $projectName = $setInfo[1];
    $client = $setInfo[2];
    $contractor = $setInfo[3];
    $locationOnStructure = $setInfo[4];
    $concreteSupplier = $setInfo[5];
    $castBy = $setInfo[6];
    $timeMixerCharged = $setInfo[7];
    $specifiedSlump = $setInfo[8];
    $airTemp = $setInfo[9];
    $specifiedAirContent = $setInfo[10];
    $waterAdded = $setInfo[11];
    $truckNumber = $setInfo[12];
    $nominalAggSize = $setInfo[13];
    $admixtures = $setInfo[14];
    $minCuringTemp = $setInfo[15];
    $reportDate = $setInfo[16];
    $dateCast = $setInfo[17];
    $receivedSample = $setInfo[18];
    $pickupDateTime = $setInfo[19];
    $measuredSlump = $setInfo[20];
    $concreteTemp = $setInfo[21];
    $measuredAir = $setInfo[22];
    $authorizedBy =  $setInfo[23];
    $quantity = $setInfo[24];
    $accQuantity = $setInfo[25];
    $typeOfMold = $setInfo[26];
    $ticketNumber = $setInfo[27];
    $maxCuringTemp = $setInfo[28];
    
    $sql = "INSERT INTO Sets (SetID, ProjectNumber, ProjectName, Client, Contractor, LocationOnStructure, ConcreteSupplier, CylindersCastBy, TimeMixerCharged, SpecifiedSlump, AirTemperature, SpecifiedAirContent, WaterAddedOnJob, TruckNumber, NominalAggregateSize, TypeOfAdmixtures, MinimumCuringTemperature, ReportDate, DateCast, TimeLabReceivedSample, PickupDateTime, MeasuredSlump, ConcreteTemperature, MeasuredAirPercentage, AuthorizedBy, Quantity,AccumulatedQuantity, TypeOFMoldAndSize, TicketNumber, MaximumCuringTemperature) VALUES ($setID,'$projectNumber','$projectName','$client','$contractor','$locationOnStructure','$concreteSupplier','$castBy','$timeMixerCharged',$specifiedSlump,$airTemp,$specifiedAirContent,'$waterAdded',$truckNumber,$nominalAggSize,'$admixtures',$minCuringTemp,'$reportDate','$dateCast','$receivedSample','$pickupDateTime',$measuredSlump,$concreteTemp,$measuredAir,'$authorizedBy',$quantity,$accQuantity,'$typeOfMold',$ticketNumber,$maxCuringTemp)";
    
    if(mysqli_query($connect, $sql)){
        echo "Sets database updated" . "<br><br>";
    }
    else{
        echo PHP_EOL . "Error: " . "<br><br>" . $sql . "<br><br>" . mysqli_error($connect) . "<br><br>";
    }
    mysqli_close($connect);
}
//cylinder data passed in an array to avoid a million arguments - also as it will be stored as a JSON obj to the set db entry.
function storeCylinderDetails($samples, $number, $set, $dateCast, $receivedSample){ 
    $server = "localhost";
    $db = "CAMBIUM_LIMS";
    $user = "chris_beetham";
    $pass = "Cambium2018";
    $connect = mysqli_connect($server,$user,$pass,$db);
    if(!$connect){
        echo "Error: " . mysqli_connect_error();
        exit;
    }
    echo "LIMS connection successful" . "<br>";
    echo "Host: " . mysqli_get_host_info($connect) . "<br>" . "Info: " . mysqli_info($connect) . "<br>" . "Status: " . mysqli_stat($connect) . "<br><br>";
    
    for ($i = 0; $i < $number; $i++){
        //I know this is a silly means of expanding out the array; I didn't want to use 'Implode' as I have on idea how many total arguments there are going to be and how they will line up with the coloumns in the database.
        $sampleID = $samples[$i][0];
        $curingType = $samples[$i][1];
        $firstDiam = $samples[$i][2];
        $secondDiam = $samples[$i][3];
        $averageDiam = $samples[$i][4];
        $weight = $samples[$i][5];
        $firstLength = $samples[$i][6];
        $secondLength = $samples[$i][7];
        $averageLength = $samples[$i][8];
        $area = $samples[$i][9];
        $volume =$samples[$i][10];
        $density = $samples[$i][11];
        $breakDays = $samples[$i][12];
        $breakDate = $samples[$i][13];
        
        $ID = $set . $sampleID;
        
        $sql = "INSERT INTO  Samples (ID, SampleID, SetID, SampleType, CuringType, FirstDiameter, SecondDiameter, AverageDiameter, Weight, FirstLength, SecondLength, AverageLength, Area, Volume, Density, BreakDays, BreakDate, DateCast, TimeReceived) VALUES ('$ID','$sampleID', $set, 'Cylinder', '$curingType', $firstDiam, $secondDiam,$averageDiam, $weight,  $firstLength, $secondLength, $averageLength,  $area, $volume, $density,  $breakDays, '$breakDate', '$dateCast', '$receivedSample')";
    }
    if(mysqli_query($connect, $sql)){
        echo "Samples database updated" . "<br><br>";
    }
    else{
        echo "Error: " . "<br><br>" . $sql . "<br><br>" . mysqli_error($connect) . "<br><br>";
    }
    mysqli_close($connect);
}

//connect to database if validation is okay and perform command
function connectToDatabase($json,$command,$query){
    $server = "localhost";
    $db = "ConcreteCards";
    $user = "chris_beetham";
    $pass = "Cambium2018";
    $connect = mysqli_connect($server,$user,$pass,$db);
    if(!$connect){
        echo "Error" . mysqli_connect_error();
        exit;
    }
    echo "Connection successful" . PHP_EOL;
    echo "Host details: " . mysqli_get_host_info($connect) . PHP_EOL;
    
    //check and perform commands
    if($command = "ID_Retrieve"){
        
    }
    else if($command = "Add_Entry"){
        
    }
    else if($command = "")
    $query =  "INSERT INTO experimental (payload) VALUES ('$json')";
    
    if(mysqli_query($connect,$query)){
        echo "New entry created successfully";
    }
    else{
        echo "Error: ". mysqli_error($connect) . PHP_EOL;
    }
}

//----------------------------------------------------------------------------//
//------------------------------IP Tracking System----------------------------//
//----------------------------------------------------------------------------//
//get the users IP address for tracking. Apparently a pretty accurate method as it can retrieve adressed from behind most simple proxys. This is a stack solution lol.
function get_ip_address(){
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
        if (array_key_exists($key, $_SERVER) === true){
            foreach (explode(',', $_SERVER[$key]) as $ip){
                $ip = trim($ip); // just to be safe

                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                    return $ip;
                }
            }
        }
    }
}
//Put it in a text file
function writeAddress($ip){
    $toWrite = $ip . PHP_EOL; 
    $addressFile = fopen("Visitor List","w") or die ("Can not open Visitor List file");
    fwrite($addressFile, $toWrite);
}

?>