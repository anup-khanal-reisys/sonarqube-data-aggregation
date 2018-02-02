<?php
header("Content-type:application/json");
/*---------------- Use Buzz HTTPClient -------------------*/
require_once 'vendor/autoload.php';
/*--------------------------------------------------------*/
// ------------- Connecting with Username and Password -------------- //
//$client = new \SonarQube\Client('https://sonar.reisys.com/api/', 'username', 'password');

// ------------- Connecting with tokens -------------- //
//$client = new \SonarQube\Client('https://sonar.reisys.com/api/', 'token', '');

$arrayQuery = array();
$arrayA = queryMetrics('[HostnameA]/api', '[token]');
$arrayB = queryMetrics('[HostnameB]/api', '[token]');
$arrayC = queryMetrics('[HostnameC]/api', '[token]');
$arrayQuery[] = $arrayA;
$arrayQuery[] = $arrayB;
$arrayQuery[] = $arrayC;
echo json_encode($arrayQuery);

function queryMetrics($hostname, $token){
  $client = new \SonarQube\Client($hostname, $token, '');
  $authentication = $client->api('authentication')->validate();
  $projects = $client->projects->search();
  $array = array();
  foreach ($projects as $project)
  {
      $arrayParent = array();
      $arrayChild = array();
      $arrayParent['project'] = $project["k"];
      $measures = $client->measures->component(['componentKey'=>$project["k"],'additionalFields'=>'periods','metricKeys'=>'bugs,reliability_rating,vulnerabilities,security_rating,code_smells,sqale_rating,coverage,new_bugs,new_coverage,new_code_smells,new_vulnerabilities']);
      $measuresVal = $measures['component']['measures'];
      $periodsVal = $measures['periods'];
      foreach ($measuresVal as $measure){
        if($measure['metric']=="reliability_rating" || $measure['metric']=="security_rating" || $measure['metric']=="sqale_rating"){
          $arrayChild[$measure['metric']] = ratingChecker($measure['value']);
        }else if($measure['metric']=="new_coverage" || $measure['metric']=="new_bugs" || $measure['metric']=="new_vulnerabilities" || $measure['metric']=="new_code_smells"){
          foreach($measure['periods'] as $newMeasure){
            $arrayChild[$measure['metric']] = $newMeasure['value'];
          }
        }else{
          $arrayChild[$measure['metric']] = $measure['value'];
        }
      }
      foreach ($periodsVal as $period){
          $arrayChild['last_analyzed'] = $period['date'];
      }
      $arrayParent['metrics'] = $arrayChild;
      $array[] = $arrayParent;
  }
    $arrayServer = array();
    $arrayServer[$hostname] = $array;
    return $arrayServer;
}

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
