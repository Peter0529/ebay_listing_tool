<?php	

require_once('ebay_keys.php');

if(session_id() == '' || !isset($_SESSION)) {
    // session isn't started
   // session_start();
}

function get_categories() {
    global $token;

    $post_data = '<?xml version="1.0" encoding="utf-8"?>
<GetCategoriesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
  <RequesterCredentials>
    <eBayAuthToken>' . $token . '</eBayAuthToken>
  </RequesterCredentials>
  <CategorySiteID>0</CategorySiteID>
  <DetailLevel>ReturnAll</DetailLevel>
  <LevelLimit>1</LevelLimit>
  <ViewAllNodes>true</ViewAllNodes>
</GetCategoriesRequest>';
    $body = callapi($post_data, "GetCategories");
	echo $body;
    return $body;
}

function get_siteID_Num($siteID)
{
	$num=array(
				"EBAY-US"=>"0",
				"EBAY-ENCA"=>"2",
				"EBAY-GB"=>"3",
				"EBAY-AU"=>"15",
				"EBAY-AT"=>"16",
				"EBAY-FRBE"=>"23",
				"EBAY-FR"=>"71",
				"EBAY-DE"=>"77",
				"EBAY-MOTOR"=>"100",
				"EBAY-IT"=>"101",
				"EBAY-NLBE"=>"123",
				"EBAY-NL"=>"146",
				"EBAY-ES"=>"186",
				"EBAY-CH"=>"193",
				"EBAY-HK"=>"201",
				"EBAY-IN"=>"203",
				"EBAY-IE"=>"205",
				"EBAY-MY"=>"207",
				"EBAY-FRCA"=>"210",
				"EBAY-PH"=>"211",
				"EBAY-PL"=>"212",
				"EBAY-SG"=>"216"
				);
		
	return $num[$siteID];
	
}
function get_siteID_Name($siteID)
{
	$num=array(
				"EBAY-US"=>"US",
				"EBAY-ENCA"=>"Canada",
				"EBAY-GB"=>"UK",
				"EBAY-AU"=>"Australia",
				"EBAY-AT"=>"Austria",
				"EBAY-FRBE"=>"Belgium_French",
				"EBAY-FR"=>"France",
				"EBAY-DE"=>"Germany",
				"EBAY-MOTOR"=>"eBayMotors",
				"EBAY-IT"=>"Italy",
				"EBAY-NLBE"=>"Belgium_Dutch",
				"EBAY-NL"=>"Netherlands",
				"EBAY-ES"=>"Spain",
				"EBAY-CH"=>"Switzerland",
				"EBAY-HK"=>"HongKong",
				"EBAY-IN"=>"India",
				"EBAY-IE"=>"Ireland",
				"EBAY-MY"=>"Malaysia",
				"EBAY-FRCA"=>"CanadaFrench",
				"EBAY-PH"=>"Philippines",
				"EBAY-PL"=>"Poland",
				"EBAY-SG"=>"Singapore"
				);
		
	return $num[$siteID];
	
}
function get_user_country($AUTH_TOKEN){
    global $APPNAME;
	global $responseEncoding;
    global $COMPATIBILITYLEVEL;
    global $DEVNAME;
    global $APPNAME;
    global $CERTNAME;
    //global $AUTH_TOKEN;
    global $eBayAPIURL;
    $html_request_head = array("X-EBAY-API-SITEID:0",
                    "X-EBAY-API-COMPATIBILITY-LEVEL:967",
                    "X-EBAY-API-CALL-NAME:" . "GetUser",
                    "X-EBAY-API-APP-NAME:" . $APPNAME,
                    "X-EBAY-API-DEV-NAME:" . $DEVNAME,
                    "X-EBAY-API-CERT-NAME:" . $CERTNAME);
    
    $html_request_body = '<?xml version="1.0" encoding="utf-8"?> 
    <GetUserRequest xmlns="urn:ebay:apis:eBLBaseComponents"> 
      <RequesterCredentials>
        <eBayAuthToken>'.$AUTH_TOKEN.'</eBayAuthToken>
      </RequesterCredentials>
        <ErrorLanguage>en_US</ErrorLanguage>
        <WarningLevel>High</WarningLevel>
        <DetailLevel>ReturnAll</DetailLevel>
    </GetUserRequest> ';
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $eBayAPIURL);
    curl_setopt($curl, CURLOPT_HEADER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $html_request_head);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $html_request_body);
    curl_setopt($curl, CURLOPT_TIMEOUT, 120);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    $item_trans_data = curl_exec($curl);
    if (strpos($item_trans_data, "<ShortMessage>Call usage limit has been reached.</ShortMessage>")) {
        return "Call API Limited";
    }
    $res = 'US';
    $item_trans_infos = array();
    $item_trans_xml = simplexml_load_string(strstr($item_trans_data, '<?xml'));
    if (gettype($item_trans_xml) == 'object') {
        if (!isset($item_trans_xml->Errors) || empty(($item_trans_xml->Errors))) {
            if (isset($item_trans_xml->User->RegistrationAddress)) {
                    $res = (string)$item_trans_xml->User->RegistrationAddress->Country;
                }
            }
        }
    curl_close($curl);
    return $res;
}
function get_category_specifics($siteID,$catID,$AUTH_TOKEN){
    global $eBayAPIURL;
    global $APPNAME;
    global $responseEncoding;
    global $COMPATIBILITYLEVEL;
    global $DEVNAME;
    global $APPNAME;
    global $CERTNAME;
    //global $AUTH_TOKEN;
    $html_request_head = array("X-EBAY-API-SITEID:".$siteID,
                    "X-EBAY-API-COMPATIBILITY-LEVEL:" . $COMPATIBILITYLEVEL,
                    "X-EBAY-API-CALL-NAME:" . "GetCategorySpecifics",
                    "X-EBAY-API-APP-NAME:" . $APPNAME,
                    "X-EBAY-API-DEV-NAME:" . $DEVNAME,
                    "X-EBAY-API-CERT-NAME:" . $CERTNAME);
    $html_request_body = '<?xml version="1.0" encoding="utf-8"?>
                            <GetCategorySpecificsRequest xmlns="urn:ebay:apis:eBLBaseComponents">
                            <RequesterCredentials>
                                <eBayAuthToken>'.$AUTH_TOKEN.'</eBayAuthToken>
                            </RequesterCredentials>
                            <WarningLevel>High</WarningLevel>
                            <CategorySpecific>
                                <!--Enter the CategoryID for which you want the Specifics-->
                                <CategoryID>'.$catID.'</CategoryID>
                            </CategorySpecific>
                            </GetCategorySpecificsRequest>';
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $eBayAPIURL);
    curl_setopt($curl, CURLOPT_HEADER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $html_request_head);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $html_request_body);
    curl_setopt($curl, CURLOPT_TIMEOUT, 120);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    $item_trans_data = curl_exec($curl);
    if (strpos($item_trans_data, "<ShortMessage>Call usage limit has been reached.</ShortMessage>")) {
        return "Call API Limited";
    }
    $mandatory = [];
    $item_trans_infos = array();
    $item_trans_xml = simplexml_load_string(strstr($item_trans_data, '<?xml'));
    if (gettype($item_trans_xml) == 'object') {
        if (!isset($item_trans_xml->Errors) || empty(($item_trans_xml->Errors))) {
            
            if (isset($item_trans_xml->Recommendations)){
                foreach ($item_trans_xml->Recommendations->NameRecommendation as $item) {
                    if(isset($item->ValidationRules) & isset($item->ValidationRules->UsageConstraint)){
                        if((string)$item->ValidationRules->UsageConstraint == 'Required'){//mandatory field
                            $mandatory[]=(string)$item->Name;
                        }
                        if((string)$item->ValidationRules->UsageConstraint == 'Recommended'){//recommended field
                            $mandatory[]=(string)$item->Name;
                        }
                    }
                }
            }
        }
    }
    return $mandatory;
}
function get_seller_profiles($SITE_GLOBAL_ID,$AUTH_TOKEN){
    global $eBayBusinessPolicy_api;
    global $APPNAME;
    global $responseEncoding;
    global $COMPATIBILITYLEVEL;
    global $DEVNAME;
    global $APPNAME;
    global $CERTNAME;
    //global $AUTH_TOKEN;
    global $eBayAPIURL_trading;
    $html_request_head = array("X-EBAY-SOA-SERVICE-NAME: SellerProfilesManagementService",
                    "X-EBAY-SOA-OPERATION-NAME: getSellerProfiles",
                    "X-EBAY-SOA-GLOBAL-ID:".$SITE_GLOBAL_ID,
                    "X-EBAY-SOA-SERVICE-VERSION: 1.0.0",
                    "X-EBAY-SOA-SECURITY-TOKEN:".$AUTH_TOKEN,
                    "X-EBAY-SOA-REQUEST-DATA-FORMAT: XML");
    $html_request_body = '<?xml version="1.0" encoding="utf-8"?>
                    <getSellerProfilesRequest xmlns="http://www.ebay.com/marketplace/selling">
                      <includeDetails> true </includeDetails>
                      <profileType> PAYMENT </profileType>
                      <profileType> RETURN_POLICY </profileType>
                      <profileType> SHIPPING </profileType>
                    </getSellerProfilesRequest>';
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $eBayBusinessPolicy_api);
    curl_setopt($curl, CURLOPT_HEADER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $html_request_head);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $html_request_body);
    curl_setopt($curl, CURLOPT_TIMEOUT, 120);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    $item_trans_data = curl_exec($curl);
    if (strpos($item_trans_data, "<ShortMessage>Call usage limit has been reached.</ShortMessage>")) {
        return "Call API Limited";
    }
    $res = '
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-12">
            <label for="site">Shipping Policy</label>
            <select class="form-control" id="shipping_policy" name="shipping_policy">';
    $item_trans_infos = array();
    $paymentProfile = array();
    $returnProfile = array();
    $shippingProfile = array();
    $item_trans_xml = simplexml_load_string(strstr($item_trans_data, '<?xml'));
    if (gettype($item_trans_xml) == 'object') {
        if (!isset($item_trans_xml->Errors) || empty(($item_trans_xml->Errors))) {
            
            if (isset($item_trans_xml->shippingPolicyProfile)) {
                foreach ($item_trans_xml->shippingPolicyProfile->ShippingPolicyProfile as $item) {
                    //$shippingProfile[] = ['Id' => (string)$item->profileId,'Name'=>(string)$item->profileName];
                    $res = $res.'<option value='.(string)$item->profileId.'>'.(string)$item->profileName.'</option>';
                }
            }
            $res.='</select></div>
            <div class="col-xl-3 col-lg-6 col-md-12">
                <label for="site">Payment Policy</label>
                <select class="form-control" id="payment_policy" name="payment_policy">';
            
            if (isset($item_trans_xml->paymentProfileList)) {
                foreach ($item_trans_xml->paymentProfileList->PaymentProfile as $item) {
                    //$paymentProfile[] = ['Id' => (string)$item->profileId,'Name'=>(string)$item->profileName];
                    $res = $res.'<option value='.(string)$item->profileId.'>'.(string)$item[0]->profileName.'</option>';
                }
            }
            $res.='</select></div></div>
            <div class="row">
                <div class="col-xl-3 col-lg-6 col-md-12">
                    <label for="site">Return Policy</label>
                    <select class="form-control" id="return_policy" name="return_policy">';
            
            if (isset($item_trans_xml->returnPolicyProfileList)) {
                foreach ($item_trans_xml->returnPolicyProfileList->ReturnPolicyProfile as $item) {
                    //$returnProfile[] = ['Id' => (string)$item->profileId,'Name'=>(string)$item->profileName];
                    $res = $res.'<option value='.(string)$item->profileId.'>'.(string)$item->profileName.'</option>';
                }
            }
            $res.='</select>
            </div>
            </div>
            </div>';
        }
    }
    //return ['payment'=>$paymentProfile,'return'=>$returnProfile,'shipping'=>$shippingProfile];
    return $res;
}
function get_category_features($catID){
    global $APPNAME;
	global $responseEncoding;
    global $COMPATIBILITYLEVEL;
    global $DEVNAME;
    global $APPNAME;
    global $CERTNAME;
    global $AUTH_TOKEN;
    global $eBayAPIURL;
    $html_request_head = array("X-EBAY-API-SITEID:0",
                    "X-EBAY-API-COMPATIBILITY-LEVEL:" . $COMPATIBILITYLEVEL,
                    "X-EBAY-API-CALL-NAME:" . "GetCategoryFeatures",
                    "X-EBAY-API-APP-NAME:" . $APPNAME,
                    "X-EBAY-API-DEV-NAME:" . $DEVNAME,
                    "X-EBAY-API-CERT-NAME:" . $CERTNAME);
    
    $html_request_body = '<?xml version="1.0" encoding="utf-8"?>
    <GetCategoryFeaturesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
        <RequesterCredentials>
            <eBayAuthToken>' . $AUTH_TOKEN . '</eBayAuthToken>
        </RequesterCredentials>
        <ErrorLanguage>en_US</ErrorLanguage>
        <WarningLevel>High</WarningLevel>
        <CategoryID>'.$catID.'</CategoryID>
        <DetailLevel>ReturnAll</DetailLevel>
        <AllFeaturesForCategory>true</AllFeaturesForCategory>
        
    </GetCategoryFeaturesRequest>';
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $eBayAPIURL);
    curl_setopt($curl, CURLOPT_HEADER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $html_request_head);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $html_request_body);
    curl_setopt($curl, CURLOPT_TIMEOUT, 120);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    $item_trans_data = curl_exec($curl);
    if (strpos($item_trans_data, "<ShortMessage>Call usage limit has been reached.</ShortMessage>")) {
        return "Call API Limited";
    }

    $res = [];
    $item_trans_infos = array();
    $item_trans_xml = simplexml_load_string(strstr($item_trans_data, '<?xml'));
    if (gettype($item_trans_xml) == 'object') {
        if (!isset($item_trans_xml->Errors) || empty(($item_trans_xml->Errors))) {
            if (isset($item_trans_xml->Category)) {
                if (isset($item_trans_xml->Category->BestOfferEnabled)) {
                    $res['BestOfferEnabled'] = (string)$item_trans_xml->Category->BestOfferEnabled;
                }
                if (isset($item_trans_xml->Category->ConditionValues)) {
                    $res['ConditionValues'] = [];
                    foreach($item_trans_xml->Category->ConditionValues->children() as $cond){
                        $res['ConditionValues'][(string)$cond->ID] = (string)$cond->DisplayName;
                    }
                }
                if (isset($item_trans_xml->Category->UPCEnabled)) {
                    $res['UPCEnabled'] = (string)$item_trans_xml->Category->UPCEnabled;
                }
            }
        }
    }
    return $res;
    // $item_trans_infos = array();
    // $item_trans_xml = simplexml_load_string(strstr($item_trans_data, '<?xml'));
    // if (gettype($item_trans_xml) == 'object') {
    //     if (!isset($item_trans_xml->Errors) || empty(($item_trans_xml->Errors))) {
    //         $category_count = $item_trans_xml->CategoryCount;
    //         echo ('<select class="form-control" name="catid" id="catid">');
    //         if (isset($item_trans_xml->CategoryArray)) {
    //             foreach ($item_trans_xml->CategoryArray[0]->children() as $cat) {
    //                 $cat_id = $cat->CategoryID;
    //                 $cat_name = $cat->CategoryName;
    //                 $selected = '';
    //                 if(isset($_SESSION["cat"]) and $_SESSION['cat'] == $cat->CategoryID) {
    //                     $selected = ' selected="selected"'; 
    //                 }
    //                 echo '<option value="' . $cat->CategoryID . '"' . $selected . '>';
    //                 if($cat->CategoryName == 'Root')
    //                 {
    //                     echo ' ' . '</option>';
    //                 }
    //                 else{
    //                     echo $cat->CategoryName . '</option>';
    //                 }
    //             }
    //             echo "</select>";
    //         }
    //     }
    // }
    curl_close($curl);
}
function get_first_category_level($siteID){
    global $APPNAME;
	global $responseEncoding;
    global $COMPATIBILITYLEVEL;
    global $DEVNAME;
    global $APPNAME;
    global $CERTNAME;
    global $AUTH_TOKEN;
    global $eBayAPIURL;
    $html_request_head = array("X-EBAY-API-SITEID:0",
                    "X-EBAY-API-COMPATIBILITY-LEVEL:" . $COMPATIBILITYLEVEL,
                    "X-EBAY-API-CALL-NAME:" . "GetCategories",
                    "X-EBAY-API-APP-NAME:" . $APPNAME,
                    "X-EBAY-API-DEV-NAME:" . $DEVNAME,
                    "X-EBAY-API-CERT-NAME:" . $CERTNAME);
    $html_request_body = '<?xml version="1.0" encoding="utf-8"?>
    <GetCategoriesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
        <RequesterCredentials>
            <eBayAuthToken>' . $AUTH_TOKEN . '</eBayAuthToken>
        </RequesterCredentials>
        <ErrorLanguage>en_US</ErrorLanguage>
        <WarningLevel>High</WarningLevel>
        <CategorySiteID>'.get_siteID_Num($siteID).'</CategorySiteID>
        <DetailLevel>ReturnAll</DetailLevel>
        <LevelLimit>1</LevelLimit>
        <ViewAllNodes>true</ViewAllNodes>
    </GetCategoriesRequest>';
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $eBayAPIURL);
    curl_setopt($curl, CURLOPT_HEADER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $html_request_head);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $html_request_body);
    curl_setopt($curl, CURLOPT_TIMEOUT, 120);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    $item_trans_data = curl_exec($curl);
    if (strpos($item_trans_data, "<ShortMessage>Call usage limit has been reached.</ShortMessage>")) {
        return "Call API Limited";
    }
    $item_trans_infos = array();
    $item_trans_xml = simplexml_load_string(strstr($item_trans_data, '<?xml'));
    if (gettype($item_trans_xml) == 'object') {
        if (!isset($item_trans_xml->Errors) || empty(($item_trans_xml->Errors))) {
            //$page_count = $item_trans_xml->PaginationResult[0]->TotalNumberOfPages;
            $category_count = $item_trans_xml->CategoryCount;
            echo ('<select class="form-control" name="catid" id="catid">');
            if (isset($item_trans_xml->CategoryArray)) {
                foreach ($item_trans_xml->CategoryArray[0]->children() as $cat) {
                    $cat_id = $cat->CategoryID;
                    $cat_name = $cat->CategoryName;
                    $selected = '';
                    if(isset($_SESSION["cat"]) and $_SESSION['cat'] == $cat->CategoryID) {
                        $selected = ' selected="selected"'; 
                    }
                    echo '<option value="' . $cat->CategoryID . '"' . $selected . '>';
                    if($cat->CategoryName == 'Root')
                    {
                        echo ' ' . '</option>';
                    }
                    else{
                        echo $cat->CategoryName . '</option>';
                    }
                }
                echo "</select>";
            }
        }
    }
    curl_close($curl);
}
// function get_categories_new($siteID) {
    
// 	global $APPNAME;
// 	global $responseEncoding;
// 	global $eBayAPIURL_shopping;


// 	$apicalla  = "$eBayAPIURL_shopping";
//     $apicalla .= "callname=GetCategoryInfo";
//     $apicalla .= "&appid=$APPNAME";
// 	$apicalla .= "&version=967";
// 	$apicalla .= "&siteid=" . get_siteID_Num($siteID) ;
//     //$apicalla .= "&siteid=0";
//     $apicalla .= "&CategoryID=-1"; 
// 	$apicalla .= "&IncludeSelector=ChildCategories";
    
//     // Load the call and capture the document returned by eBay API
//     $resp = simplexml_load_file($apicalla);
	
// 	 if ($resp) {
//       // Set return value for the function to null
//       $retna = '';
// 	  //$result = $xml->xpath("//CategoryArray/Category");
// 	   if ($resp->Ack == "Success") {
			
// 			echo ('<select class="form-control" name="catid" id="catid">');
// 			foreach ($resp->CategoryArray->Category as $cat) {
				
// 				$selected = '';
// 			if(isset($_SESSION["cat"]) and $_SESSION['cat'] == $cat->CategoryID) {
// 					$selected = ' selected="selected"'; 
// 				}
//                 echo '<option value="' . $cat->CategoryID . '"' . $selected . '>';
//                 if($cat->CategoryName == 'Root')
//                 {
//                     echo ' ' . '</option>';
//                 }
//                 else{
//                     echo $cat->CategoryName . '</option>';
//                 }
				
// 			}
// 			echo "</select>";
// 	   }
 
//     } else {
      
//     } 

//     // Return the function's value
//     return $retna;
   
// }

// function get_mostwatched($catID, $apiEndPoint) {
//     global $token;
// 	global $APPNAME;
// 	global $responseEncoding;
// 	global $eBayAPIURL;


// 	$apicalla  = "$apiEndPoint";
//     $apicalla .= "OPERATION-NAME=getMostWatchedItems";
//     $apicalla .= "&SERVICE-VERSION=1.0.0";
//     $apicalla .= "&CONSUMER-ID=$APPNAME";
//     $apicalla .= "&RESPONSE-DATA-FORMAT=$responseEncoding";
//     $apicalla .= "&maxResults=10";
//     //$apicalla .= "&categoryId=$catID"; 
// 	$apicalla .="&categoryId=1217";
    
//     // Load the call and capture the document returned by eBay API
//     $resp = simplexml_load_file($apicalla);
//    if ($resp) {
//       // Set return value for the function to null
//       $retna = '';

//     // Verify whether call was successful
//     if ($resp->ack == "Success") {

//       // If there were no errors, build the return response for the function
//       $retna .= "<h1>Top 3 Most Watched Items in the ";
//       $retna .=  $resp->itemRecommendations->item->primaryCategoryName; 
//       $retna .= " Category</h1> \n";

//       // Build a table for the 3 most watched items
//       $retna .= "<!-- start table in getMostWatchedItemsResults --> \n";
//       $retna .= "<table width=\"100%\" cellpadding=\"5\" border=\"0\"><tr> \n";

//       // For each item node, build a table cell and append it to $retna 
//       foreach($resp->itemRecommendations->item as $item) {

//         /* Set the cell color blue for the selected most watched item
//         if ($selectedItemID == $item->itemId) {
//           $thisCellColor = $cellColor;
//         } else {
//           $thisCellColor = '';
//         }
// 		*/
//         // Determine which price to display
//         if ($item->currentPrice) {
//         $price = $item->currentPrice;
//         } else {
//         $price = $item->buyItNowPrice;
//         }

//         // For each item, create a cell with imageURL, viewItemURL, watchCount, currentPrice
//        // $retna .= "<td $thisCellColor valign=\"bottom\"> \n";
//         $retna .= "<td><img src=\"$item->imageURL\"> \n";
//         $retna .= "<p><a href=\"" . $item->viewItemURL . "\">" . $item->title . "</a></p>\n";
//         $retna .= 'Watch count: <b>' . $item->watchCount . "</b><br> \n";
//         $retna .= 'Current price: <b>$' . $price . "</b><br><br> \n";
//         $retna .= "<FORM ACTION=\"" . $_SERVER['PHP_SELF'] . "\" METHOD=\"POST\"> \n";
//         $retna .= "<INPUT TYPE=\"hidden\" NAME=\"Selection\" VALUE=\"$item->itemId\"> \n";
//         $retna .= "<INPUT TYPE=\"submit\" NAME=\"$item->itemId\" ";
//         $retna .= "VALUE=\"Get Details and Related Category Items\"> \n";
//         $retna .= "</FORM> \n";
//         $retna .= "</td> \n";
//       }
//       $retna .= "</tr></table> \n<!-- finish table in getMostWatchedItemsResults --> \n";
      
//       } else {
//           // If there were errors, print an error
//           $retna = "The response contains errors<br>";
//           $retna .= "Call used was: $apicalla";

//     }  // if errors

//     } else {
//       // If there was no response, print an error
//       $retna = "Dang! Must not have got the getMostWatchedItems response!<br>";
//       $retna .= "Call used was: $apicalla";
//     }  // End if response exists

//     // Return the function's value
//     return $retna;
   
// 	}
	
 
// function get_mostwatched_keywords($catID, $apiEndPoint, $kw, $countryID) {
 
// 	global $APPNAME;
// 	global $responseEncoding;
// 	global $eBayAPIURL;
//    $apiEndPoint = '';
//     $apicalla  = "https://svcs.ebay.com/services/search/FindingService/v1?";
//     $apicalla .= "OPERATION-NAME=findItemsAdvanced";
//     $apicalla .= "&SERVICE-VERSION=1.0.0";
// 	$apicalla .= "&GLOBAL-ID=$countryID";
//     $apicalla .= "&SECURITY-APPNAME=$APPNAME";
//     $apicalla .= "&RESPONSE-DATA-FORMAT=$responseEncoding";
//     $apicalla .= "&REST-PAYLOAD";
//     $apicalla .= "&paginationInput.entriesPerPage=50";
//     if(strlen($kw) > 0){
// 	$apicalla .= "&keywords=$kw";
// 	}
// 	if($catID > 0){
//     $apicalla .= "&categoryId=$catID";
// 	} 
	
// 	$apicalla .= "&sortOrder=WatchCountDecreaseSort";
// 	$apicalla .= "&outputSelector=PictureURLLarge";
// 	//$apicalla .="&categoryId=1217";
//    //echo ($apicalla);
	
//     // Load the call and capture the document returned by eBay API
//     $resp = simplexml_load_file($apicalla);
//    if ($resp) {
//       // Set return value for the function to null
//       $retna = '';
//     $i =0; 
//     $itemIDs = array();
//     foreach($resp->searchResult->item as $item) {
//         $itemIDs[$i++] = $item->itemId;
//     }

//     $sold_counts = get_items_sold_count($catID, "http://open.api.ebay.com/Shopping?", $kw, $countryID,$itemIDs);
//     // Verify whether call was successful
//     if ($resp->ack == "Success") {

      
//       // For each item node, build a table cell and append it to $retna 
//       foreach($resp->searchResult->item as $item) {
		
// 		$hot = '';
//         // Determine which price to display
//         if ($item->sellingStatus->currentPrice) {
// 			$price = $item->sellingStatus->currentPrice;
//         } else {
// 			$price = $item->sellingStatus->convertedCurrentPrice;
//         }
		
// 		$img = '';
// 		$str_count = isset($sold_counts[(string)$item->itemId])? $sold_counts[(string)$item->itemId]:' ';
// 		if ($item->pictureURLLarge) {
// 			$img = $item->pictureURLLarge;
//         } else {
// 			$img = $item->galleryURL;
//         }
		
// 		if($item->listingInfo->watchCount > 999){
// 			$hot = '<i style="margin-left:10px; font-size:40px; color:red;" class="fab fa-gripfire"></i>';

// 			//$hot = ' <span style="color:yellow;font-weight:bold;background-color:red">(&#10067;)</span>';
// 		}
        
		
// 		$retna .= '<div class="row">
// 				<div class="col-md-12">
// 					<div class="card">
// 					<div class="card-header">
//                         <div class="card-title-wrap bar-success">
// 							<h4 class="card-title"><a href="' . $item->viewItemURL . '">' . $item->title . '</a></h4>
// 						</div>
// 					</div>
// 					<div class="card-body">
// 						<div class="row">
// 						<div class="col-md-1">&nbsp;</div>
// 						<div class="col-md-7">
// 							<div class="card-text"><span style="font-size:20px; font-weight:bold;">Price: $' . $price . '</span></div>
// 							<div class="card-text"><span style="font-size:20px; font-weight:bold;">Watch Count : ' . $item->listingInfo->watchCount .'</span>' . $hot . '</div>
//                             <div class="card-text"><span style="font-size:20px; font-weight:bold;">Sold Count: ' . $str_count . '</span></div>
// 						</div>
						
// 						<div class="col-md-4">
							 
// 								<a href="'.$item->viewItemURL.'"><img class="img-responsive card-img img-fluid p-4" style="max-height:250px; max-width:250px;" src=' . $img . '></a>
// 						</div>
						 
							
// 						</div>			
// 					</div>
// 					</div>
			 
// 			</div>
// 		</div>';
		
		
		
//       }
      
//       } else {
//           // If there were errors, print an error
//           $retna = "No Search Result found.";
          
//     }  // if errors

//     } else {
//       // If there was no response, print an error
//       $retna = "No Search Result found.";
     
//     }  // End if response exists

//     // Return the function's value
//     return $retna;
   
// 	}
	
 
	
// function callapi($post_data, $call_name) {
	
//     global $COMPATIBILITYLEVEL, $DEVNAME, $APPNAME, $CERTNAME, $SiteId, $eBayAPIURL;
    
// 	//Merchandising API
// 	$ebayapiheader = array(
// 		"EBAY-SOA-CONSUMER-ID: $APPNAME",
// 		"X-EBAY-SOA-OPERATION-NAME: getMostWatchedItems",
//         "X-EBAY-SOA-REQUEST-DATA-FORMAT: XML",
// 		"X-EBAY-SOA-GLOBAL-ID: EBAY-US",
//         "X-EBAY-SOA-SERVICE-NAME: 1.0.0");
	

//     $ch = curl_init();
//     $res = curl_setopt($ch, CURLOPT_URL, $eBayAPIURL);

//     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

//     curl_setopt($ch, CURLOPT_HEADER, 0); // 0 = Don't give me the return header 
//     curl_setopt($ch, CURLOPT_HTTPHEADER, $ebayapiheader); // Set this for eBayAPI 
//     curl_setopt($ch, CURLOPT_POST, 1); // POST Method 
//     curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data); //My XML Request 
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// 	curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1:8888');
// 	    $body = curl_exec($ch); //Send the request 
//     curl_close($ch); // Close the connection
//     return $body;
// }

// 
// function get_items_sold_count($catID, $apiEndPoint, $kw, $countryID,$itemIDs) {
//     global $APPNAME;
// 	global $responseEncoding;
// 	global $eBayAPIURL;
   
//     $apicalla  = "$apiEndPoint";
//     $apicalla .= "callname=GetMultipleItems";
//     $apicalla .= "&version=967";
// 	$apicalla .= "&siteid=$countryID";
//     $apicalla .= "&appid=$APPNAME";
//     $apicalla .= "&responseencoding=$responseEncoding";
//     $apicalla .= "&includeSelector=Details";
//     $apicalla .= "&ItemID=";

//     $itemids_str = '';
//     $i = 0;
//     $ret = array();
//     foreach($itemIDs as $item) {
//         $i++;
//         if($i % 20 == 0 || sizeof($itemIDs) == $i)
//         {
            
//             $request_str = $apicalla;
//             $itemids_str .= $item;
//             //$itemids_str = substr($itemids_str,0,-1);
            
//             $request_str.=$itemids_str;
            
            
//             $resp = simplexml_load_file($request_str);
//             if ($resp) {
//                 // Set return value for the function to null
//                 $retna = '';
                
//               // Verify whether call was successful 201028256493
//               if ($resp->Ack == "Success") {
                
//                 foreach($resp->Item as $item){
//                     #array_push($ret,$item->QuantitySold);
//                     $ret[(string)$item->ItemID] = $item->QuantitySold;
//                 }
//               }
//             }
            
//             $itemids_str = '';
//             //$i = 0;
//             //QuantitySold
//         }
//         else
//         {
//             $itemids_str .= $item.',';
//         }
//     }
//     return $ret;
?>
