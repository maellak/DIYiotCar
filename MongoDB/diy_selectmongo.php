<?php

header("Content-Type: text/html; charset=utf-8");

/**
*
* @SWG\Resource(
*   apiVersion="0.1",
*   swaggerVersion="2.0",
*   basePath="https://arduino.os.cs.teiath.gr/api",
*   resourcePath="/mongodb",
*   description="Get list of devices",
*   produces="['application/json']"
* )
*/

/**
 * @SWG\Api(
 *   path="/mongodb",
 *   @SWG\Operation(
 *     method="GET",
 *     summary="Get list of devices (pou o user echei ta schetika dikaiomata)",
 *     notes="epistrefei ta devices pou o user echei ta schetika dikaiomata",
 *     type="devices",
 *     nickname="get_select",
 *     @SWG\Parameter(
 *       name="access_token",
 *       description="access_token",
 *       required=true,
 *       type="text",
 *       paramType="query"
 *     ),
 *     @SWG\ResponseMessage(code=200, message="Επιτυχία", responseModel="Success"),
 *     @SWG\ResponseMessage(code=500, message="Αποτυχία", responseModel="Failure")
 *   )
 * )
 *
     */

 /**
 *
 * @SWG\Model(
 *              id="devices",
 *                  @SWG\Property(name="error",type="text",description="error")
 * )
 *                  @SWG\Property(name="status",type="integer",description="status code")
 *                  @SWG\Property(name="message",type="string",description="status message")
 *                  @SWG\Property(name="org",type="string",description="organisation pou aniki to device")
 *                  @SWG\Property(name="device",type="string",description="device name")
 *                  @SWG\Property(name="device_desc",type="string",description="device desc")
 *                  @SWG\Property(name="status",type="string",description="status of device private/org/public")
 *                  @SWG\Property(name="mode",type="string",description="mode of device devel/production")
 */
//$mongoResult = "maellakia";

//api/get/diy_getdevices.php
$app->get('/mongodb', function () use ($authenticateForRole, $diy_storage)  {
        global $app;
        $params = loadParameters();
        $server = $authenticateForRole();
        $dbstorage = $diy_storage();
        if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
                echo 'Unable to verify access token: '."\n";
                $server->getResponse()->send();
                die;
        }else{
                $crypto_token = OAuth2\Request::createFromGlobals()->query["access_token"];
                $separator = '.';
                list($header, $payload, $signature) = explode($separator, $crypto_token);
                //echo base64_decode($payload);
                $params["payload"] = $payload;
                $params["storage"] = $dbstorage;
                $result = diy_select(
                        $params["payload"],
                        $params["storage"],
                        $params["test"]
                );
                PrepareResponse();
                $app->response()->setBody( toGreek( json_encode( $result ) ) );
        }
});

function diy_seletc($payload,$storage){
    global $app;
    $result["controller"] = __FUNCTION__;
    $result["function"] = substr($app->request()->getPathInfo(),1);
    $result["method"] = $app->request()->getMethod();
    $params = loadParameters();
    $result->function = substr($app->request()->getPathInfo(),1);
    $result->method = $app->request()->getMethod();
    $params = loadParameters();
    $up=json_decode(base64_decode($payload));
    $client_id=$up->client_id;


    try {
        
        $m = new MongoClient("mongodb://localhost:27017");
        $db = $m->selectDB("diyiot_sensorsData");
        $collection = $db->mycol;
        switch ((int)$params["operation_id"]) {

            case 1:$mongoResult = iterator_to_array($collection->find(array("Map.mapName" => $params["map_name"])));  break;
            case 2:$mongoResult = iterator_to_array($collection->find(array("Date&Time.year" => $params["date_year"],"Date&Time.month" => $params["date_month"],
"Date&Time.day" => $params["date_day"])));  break;
            case 3:$mongoResult = iterator_to_array($collection->find(array("Date&Time.year" => $params["date_year"],"Date&Time.month" => $params["date_month"],
"Date&Time.day" => $params["date_day"],"Map.mapName" => $params["map_name"]))); break;
        default:
            $mongoResult = "Wrong number of arguments";
            
        }
        
        
        $result["message"] = "[".$result["method"]."][".$result["function"]."]: NoErrors";
        $result["status"] = "200";
        $result["result"]=  $mongoResult;

    } catch (Exception $e) {
	$diy_error["db"] = $e->getCode();
	$result["status"] = $e->getCode();
	$result["message"] = "[".$result["method"]."][".$result["function"]."]:".$e->getMessage();
    }

        if(diyConfig::read('debug') == 1){
                $result["debug"]=$diy_error;
        }

    return $result;
    
}

?>
