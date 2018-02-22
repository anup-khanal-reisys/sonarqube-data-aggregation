<?php
header("Content-type:application/json");
/*---------------- Use Buzz HTTPClient -------------------*/
require_once 'vendor/autoload.php';
/*--------------------------------------------------------*/
// ------------- Connecting with Username and Password -------------- //
//$client = new \SonarQube\Client('hostname', 'username', 'password');

// ------------- Connecting with tokens -------------- //
//$client = new \SonarQube\Client('hostname', 'token', '');

$cache_file = dirname(__FILE__) . '/api-cache.array';
$json_file = dirname(__FILE__) . '/api-cache.json';
$settings_file = dirname(__FILE__) . '/settings.array';
$cred_file = dirname(__FILE__) . '/settings.json';
$purgeCache = false;
$settings = unserialize(file_get_contents($settings_file));
$cred = json_decode(file_get_contents($cred_file));
$uriA = $cred->uriA;
$uriB = $cred->uriB;
$uriC = $cred->uriC;
$tokenA = $cred->tokenA;
$tokenB = $cred->tokenB;
$tokenC = $cred->tokenC;

if(empty($settings)){
  $expires = time() + 24*60*60;
  $setting = array();
  $setting['expires'] = $expires;
  file_put_contents($settings_file, serialize($setting));
}else{
  $expires = $settings['expires'];
}
if ( time() > $expires || empty(unserialize(file_get_contents($cache_file))) || $purgeCache) {
  $arrayQuery = array();
  $arrayA = queryMetrics($uriA, $tokenA);
  $arrayB = queryMetrics($uriB, $tokenB);
  $arrayC = queryMetrics($uriC, $tokenC);
  $arrayQuery[] = $arrayA;
  $arrayQuery[] = $arrayB;
  $arrayQuery[] = $arrayC;
  if ( $arrayQuery ){
    file_put_contents($cache_file, serialize($arrayQuery));
    file_put_contents($json_file, json_encode($arrayQuery));
  }else{
    unlink($cache_file);
  }
  $expires = time() + 24*60*60;
  $setting = array();
  $setting['expires'] = $expires;
  file_put_contents($settings_file, serialize($setting));
  echo json_encode($arrayQuery);
} else {
  echo json_encode(unserialize(file_get_contents($cache_file)));
}

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
      $measures = $client->measures->component(['componentKey'=>$project["k"],'additionalFields'=>'periods','metricKeys'=>'bugs,reliability_rating,new_reliability_rating,vulnerabilities,security_rating,new_security_rating,code_smells,sqale_rating,new_maintainability_rating,coverage,new_bugs,new_coverage,new_code_smells,new_vulnerabilities']);
      $measuresVal = $measures['component']['measures'];
      $periodsVal = $measures['periods'];
      foreach ($measuresVal as $measure){
        if($measure['metric']=="reliability_rating" || $measure['metric']=="security_rating" || $measure['metric']=="sqale_rating"){
          $arrayChild[$measure['metric']] = ratingChecker($measure['value']);
        }else if($measure['metric']=="new_coverage" || $measure['metric']=="new_bugs" || $measure['metric']=="new_vulnerabilities" || $measure['metric']=="new_code_smells"){
          foreach($measure['periods'] as $newMeasure){
            $arrayChild[$measure['metric']] = $newMeasure['value'];
          }
        }else if($measure['metric']=="new_reliability_rating" || $measure['metric']=="new_security_rating" || $measure['metric']=="new_maintainability_rating"){
            $arrayChild[$measure['metric']] = ratingChecker($measure['periods'][0]['value']);
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
