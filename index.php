<?php
header("Content-type:application/json");
/*---------------- Use Buzz HTTPClient -------------------*/
require_once 'vendor/autoload.php';
/*--------------------------------------------------------*/
$client = new \SonarQube\Client('https://sonar.reisys.com/api/', 'username', 'password');
$authentication = $client->api('authentication')->validate();
$projects = $client->projects->search();
$array = array();
foreach ($projects as $project)
{
    $arrayParent = array();
    $arrayChild = array();
    $arrayParent['project'] = $project["k"];
    $measures = $client->measures->component(['componentKey'=>$project["k"],'metricKeys'=>'bugs,reliability_rating,vulnerabilities,security_rating,code_smells,sqale_rating,coverage']);
    $measuresVal = $measures['component']['measures'];
    foreach ($measuresVal as $measure){
      if($measure['metric']=="reliability_rating" || $measure['metric']=="security_rating" || $measure['metric']=="sqale_rating"){
        $arrayChild[$measure['metric']] = ratingChecker($measure['value']);
      }else{
        $arrayChild[$measure['metric']] = $measure['value'];
      }
    }
    $arrayParent['metrics'] = $arrayChild;
    $array[] = $arrayParent;
}
$arrayServer = array();
$arrayServer['sonar.reisys.com'] = $array;
echo json_encode($arrayServer);

function ratingChecker($rscale){
  if($rscale==1.0){
    return "A";
  }else if($rscale==2.0){
    return "B";
  }else if($rscale==3.0){
    return "C";
  }else if($rscale==4.0){
    return "D";
  }else if($rscale==5.0){
    return "E";
  }else{
    return "F";
  }
}
?>
