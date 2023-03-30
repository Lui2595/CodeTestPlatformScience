<?php
// variables for quicktext
// $addresses="addresses.txt";
// $drivers="drivers.txt";
// goto open;

//read files 
checkAddresses:
$addresses =readline("Input Adresses file path (Example:../streetAddresses.txt) \n");
//Validate input is not empty
if ($addresses=="") goto checkAddresses;
//Validate file exist 
if (!file_exists($addresses)) {echo "File does not exist! \n";  goto checkAddresses;}

checkDrivers:
$drivers =readline("Input Drivers file path (Example:../drivers.txt) Remeber need to be sema drivers amount as street addresses \n");
//Validate input is not empty
if ($drivers=="") goto checkDrivers;
//Validate file exist 
if (!file_exists($drivers)) {echo "File does not exist! \n";  goto checkDrivers;}

open:
//Get files contents and make them an array
$addresses= explode("\n",file_get_contents($addresses));
$drivers= explode("\n",file_get_contents($drivers));

//Validate we have the same amount of addresses and drivers 
if(count($addresses)!=count($drivers)) {echo "Drivers and Addresses count does not match! \n"; goto checkAddresses;}

//remove white spaces at the start and the end 
$addresses= array_map("mapAddresses",$addresses); //get if is even or odd
$drivers= array_map("mapDrivers",$drivers); // get vowels and consonants

//find all possibilities
$possibilities=allpossibilities($addresses,$drivers);

// Sort the posibilities using function
usort($possibilities, 'sortBySS');

//best Assigments, combaning all posbilities and getting the highest SS
$assignments=bestAssingment($possibilities);

//print total SS
echo "Total SS: ".$assignments[0] ."\n";

//Replace Index with data
$assignments= array_map(function ($e) use ($addresses,$drivers){
    $e[1]=$addresses[$e[1]][0];
    $e[2]=$drivers[$e[2]][0];
    return [$e[1],$e[2]];
}, $assignments[1]);

//print assigments
foreach ($assignments as $a ){
    echo "Address: ".$a[0].", Driver: ".$a[1]."\n";
}

function allpossibilities($addresses, $drivers){
    $result=[];
    foreach ($addresses as $i => $a){
        foreach ($drivers as $i2 => $d){
            if($a[1]=="even"){
                $result[]=gcd($a[2],$d[3])>1?[(1.5*1.5*$d[1]),$i,$i2]:[(1.5*$d[1]),$i,$i2];
            }else{
                $result[]=gcd($a[2],$d[3])>1?[(1.5*$d[2]),$i,$i2]:[$d[2],$i,$i2];
            }
        }
    }
    return $result;
}

function mapAddresses($e){
    $value=trim($e);
    $len=strlen($value);
    $type = $len % 2 == 0 ?"even":"odd";
    return [$value,$type,$len];
}

function mapDrivers($e){
    $value=trim($e);
    $vowels = implode("",preg_split('/[^aeiou]/i', str_replace(" ","",$value), -1, PREG_SPLIT_NO_EMPTY));
    $consonants = implode("",preg_split('/[aeiou]/i', str_replace(" ","",$value), -1, PREG_SPLIT_NO_EMPTY));
    return [$value,strlen($vowels),strlen($consonants),strlen($value)];
}

function sortBySS($a, $b) {
    return $b[0] - $a[0];
}
function gcd($a, $b) {
    while ($b != 0) {
      $t = $b;
      $b = $a % $b;
      $a = $t;
    }
    return $a;
}

function bestAssingment($arr){
    $combinations=[];
    for ($i=0; $i < count($arr)-1; $i++) { 
        $assignments=[$arr[$i]];
        foreach ($arr as $i2=> $p){
            if(!in_array($p[1],array_column($assignments,1)) && !in_array($p[2],array_column($assignments,2)))
                $assignments[]=$p;
        }
        $total_ss = array_sum(array_column($assignments,0));
        $combinations[]=[$total_ss,$assignments];
    }
    usort($combinations, 'sortBySS');
    return $combinations[0];
    
}

?>

