<?php
	include_once 'upload_image.php';
	include_once 'eBayFunctions.php';
	include_once 'ebay_keys.php';
	require_once('eBaySession.php');

    if(isset($_POST['Put_Listing']))
	{
		if(!isset($_POST['category'])){
			echo "please select the category first";
			exit;
		}
		$images = $_FILES['image_file'];
        $img_name = array();
		//Image Upload
		if(count($images['name']) == 0){
			echo "please select at least one image";
			exit;
		}
      	

		if(count($images['name']) > 12){
			echo '<span style="color:red;">Please choose 12 photos maximum or less</span>';
		}
		else{
			for($i=0;$i<count($images['name']);$i++):
				//check image type
				if($images['type'][$i]!='image/png'&& $images['type'][$i]!='image/jpeg' && $images['type'][$i]!='image/jpg' && $images['type'][$i]!='image/gif'){
					echo 'Please upload a valid Image';
					break;
				}
				else{
					$name = time();
					$ext = pathinfo($images['name'][$i],PATHINFO_EXTENSION);
					$name .=$i.'.'.$ext;
					move_uploaded_file($images['tmp_name'][$i], 'uploads/'.$name);
					$img_name[]=$name;
				}
				//check image type
			endfor;

		}
		
        //Image Upload
		// $res = upload_image($img_name);
		// if(count($res['hosted_url']) < 1){
		// 	echo 'There are not valid Images';
		// }
		//check
        ini_set('magic_quotes_gpc', false);    // magic quotes will only confuse things like escaping apostrophe
		//Get the item entered
		$listingType     = $_POST['item_type'];
		
		$primaryCategory = $_POST['category'];
		$item_location   = $_POST['location'];
		
		//$itemCondition   = $_POST['item_condition'];
		
		$itemTitle       = $_POST['listing_title'];
		if(isset($_POST['price'])){
		$startPrice      = $_POST['price'];
		}
        //$buyItNowPrice   = $_POST['buyItNowPrice'];
		$listingDuration = $_POST['listing_duration'];
		$quantity 		 = $_POST['quantity'];
		$payment_policy_id  =$_POST['payment_policy'];
		$shipping_policy_id  =$_POST['shipping_policy'];
		$return_policy_id  =$_POST['return_policy'];
		$country 		 = get_user_country($AUTH_TOKEN);
        // $safequery       = $_POST['searched_keyword'];

        if(get_magic_quotes_gpc()) {
            $itemDescription = stripslashes($_POST['listing_desc']);
        } else {
            $itemDescription = $_POST['listing_desc'];
        }
        $itemDescription = $_POST['listing_desc'];
		$siteID = get_siteID_Num($_POST['site']);
		//the call being made:
		$verb = 'VerifyAddItem';

		$upc = $_POST['upc'];
		if($_POST['upc'] == ''){
			$upc = "Does not apply";
		}
		
		if ($listingType == 'Chinese') {
          $buyItNowPrice = $_POST['buy_it_now_price'];   // don't have BuyItNow for FixedPriceItem
		}
		else{
			
		}

		///Build the request Xml string
		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<VerifyAddItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$AUTH_TOKEN</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<DetailLevel>ReturnAll</DetailLevel>';
		$requestXmlBody .= '<ErrorLanguage>en_GB</ErrorLanguage>';
		$requestXmlBody .= "<Version>$COMPATIBILITYLEVEL</Version>";
		$requestXmlBody .= '<Item>';
		if (isset($_POST['item_condition'])){
			if (($_POST['item_condition'] != '')){
		$requestXmlBody .= '<ConditionID>'.$_POST['item_condition'].'</ConditionID>';}}

		$requestXmlBody .= '<Site>'.get_siteID_Name($_POST['site']).'</Site>';
		$requestXmlBody .= '<PrimaryCategory>';
		$requestXmlBody .= "<CategoryID>$primaryCategory</CategoryID>";
		$requestXmlBody .= '</PrimaryCategory>';
		// $requestXmlBody .= '<BestOfferDetails>';y
		// $requestXmlBody .= '<BestOfferEnabled>1</BestOfferEnabled>';
		// $requestXmlBody .= '</BestOfferDetails>';
		$requestXmlBody .= '<PictureDetails>';

		$img_name = ['https://i.ebayimg.sandbox.ebay.com/00/s/MjUwWDMwMA==/z/ZkQAAOSwBW1dziRu/$_1.JPG?set_id=2',];

        for($j=0;$j<count($img_name);$j++):
			$requestXmlBody .= '<PictureURL>'.$img_name[$j].'</PictureURL>';
		endfor;
		$requestXmlBody .= '</PictureDetails>';
		if (($listingType == 'Chinese' && $_POST['buy_it_now_price'] != '')) {
			$requestXmlBody .= "<BuyItNowPrice currencyID=\"USD\">".$_POST['buy_it_now_price']."</BuyItNowPrice>";
		}
		$requestXmlBody .= '<Country>'.$country.'</Country>';
		$requestXmlBody .= '<Currency>USD</Currency>';
		// $requestXmlBody .= '<DispatchTimeMax>1</DispatchTimeMax>';
		$requestXmlBody .= "<ListingDuration>$listingDuration</ListingDuration>";
        $requestXmlBody .= '<ListingType>'.$listingType.'</ListingType>';
		$requestXmlBody .= '<Location>'.$item_location.'</Location>';
		// $requestXmlBody .= '<PaymentMethods>PayPal</PaymentMethods>';
		// $requestXmlBody .= "<PayPalEmailAddress>$paypalEmailAddress</PayPalEmailAddress>";
		$requestXmlBody .= "<Quantity>$quantity</Quantity>";
		// $requestXmlBody .= '<RegionID>77</RegionID>';
		if(isset($startPrice)){
		$requestXmlBody .= "<StartPrice>$startPrice</StartPrice>";}
		// $requestXmlBody .= '<ShippingTermsInDescription>True</ShippingTermsInDescription>';
		$requestXmlBody .= "<Title><![CDATA[$itemTitle]]></Title>";
		$requestXmlBody .= "<Description><![CDATA[$itemDescription]]></Description>";
		$requestXmlBody .= '<SellerProfiles>';
		$requestXmlBody .= '<SellerPaymentProfile>';
		$requestXmlBody .= '<PaymentProfileID>'.$payment_policy_id.'</PaymentProfileID>';
		$requestXmlBody .= '</SellerPaymentProfile>';
		$requestXmlBody .= '<SellerReturnProfile>';
		$requestXmlBody .= '<ReturnProfileID>'.$return_policy_id.'</ReturnProfileID>';
		$requestXmlBody .= '</SellerReturnProfile>';
		$requestXmlBody .= '<SellerShippingProfile>';
		$requestXmlBody .= '<ShippingProfileID>'.$shipping_policy_id.'</ShippingProfileID>';
		$requestXmlBody .= '</SellerShippingProfile>';
		$requestXmlBody .= '</SellerProfiles>';
		//get variant values
		$requestXmlBody .= '<ItemSpecifics>';
		foreach(array_keys($_POST) as $key){
			if (strpos($key, 'variant_') !== false){
				$requestXmlBody .= '<NameValueList>';
				$requestXmlBody .= '<Name>'.str_replace("_"," ",explode("variant_",$key)[1]).'</Name>';
				$requestXmlBody .= '<Value>'.$_POST[$key].'</Value>';
				$requestXmlBody .= '</NameValueList>';
			}
		}
		$requestXmlBody .= '</ItemSpecifics>';
		$requestXmlBody .= '<ProductListingDetails>';
		$requestXmlBody .= '<UPC>'.$upc.'</UPC>';
		$requestXmlBody .= '</ProductListingDetails>';
		/*$requestXmlBody .= '<ReturnPolicy>';
		$requestXmlBody .= '<ReturnsAcceptedOption>'.$returnsAccepted.'</ReturnsAcceptedOption>';
		$requestXmlBody .= '<ReturnsWithinOption>'.$returnWithin.'</ReturnsWithinOption>';
		$requestXmlBody .= '</ReturnPolicy>';*/
	    // $requestXmlBody .= '<ShippingDetails>';
	    // $requestXmlBody .= '<ShippingType>Flat</ShippingType>';
	    // $requestXmlBody .= '<ShippingServiceOptions>';
	    // $requestXmlBody .= '<ShippingServiceAdditionalCost currencyID="EUR">2.0</ShippingServiceAdditionalCost>';
		// $requestXmlBody .= '<ShippingServiceCost currencyID="EUR">7.50</ShippingServiceCost>';
        // $requestXmlBody .= '<ShippingServicePriority>1</ShippingServicePriority>';
        // $requestXmlBody .= '<ShippingService>DE_Express</ShippingService>';
        // $requestXmlBody .= '</ShippingServiceOptions>';
        // $requestXmlBody .= '</ShippingDetails>';
		$requestXmlBody .= '</Item>';
		$requestXmlBody .= '</VerifyAddItemRequest>';

		//echo $requestXmlBody;

        //Create a new eBay session with all details pulled in from included keys.php
        $session = new eBaySession($AUTH_TOKEN, $DEVNAME, $APPNAME, $CERTNAME, $eBayAPIURL, $COMPATIBILITYLEVEL, $siteID, $verb);

		//send the request and get response
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
			die('<P>Error sending request');

		//Xml string is parsed and creates a DOM Document object
		$responseDoc = new DomDocument();
		$responseDoc->loadXML($responseXml);

		//get any error nodes
		$errors = $responseDoc->getElementsByTagName('Errors');

		//if there are error nodes
		if($errors->length > 0)
		{
			//echo 'item updated';
			echo '<P><B>eBay returned the following error(s):</B>';
			//display each error
			//Get error code, ShortMesaage and LongMessage
			foreach($errors as $error){
				$code     = $error->getElementsByTagName('ErrorCode');
				$shortMsg = $error->getElementsByTagName('ShortMessage');
				$longMsg  = $error->getElementsByTagName('LongMessage');
				//Display code and shortmessage
				echo '<P>', $code->item(0)->nodeValue, ' : ', str_replace(">", "&gt;", str_replace("<", "&lt;", $shortMsg->item(0)->nodeValue));
				//if there is a long message (ie ErrorLevel=1), display it
				if(count($longMsg) > 0)
					echo '<BR>', str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg->item(0)->nodeValue));
			}

		} else { //no errors


			//get results nodes
			$responses = $responseDoc->getElementsByTagName("AddItemResponse");
            $itemID = "";
            foreach ($responses as $response) {
              $acks = $response->getElementsByTagName("Ack");
              $ack   = $acks->item(0)->nodeValue;
              echo "Ack = $ack <BR />\n";   // Success if successful

              $endTimes  = $response->getElementsByTagName("EndTime");
              $endTime   = $endTimes->item(0)->nodeValue;
              echo "endTime = $endTime <BR />\n";

              $itemIDs  = $response->getElementsByTagName("ItemID");
              $itemID   = $itemIDs->item(0)->nodeValue;
              echo "itemID = $itemID <BR />\n";

              $linkBase = "http://cgi.sandbox.ebay.com/ws/eBayISAPI.dll?ViewItem&item=";
              echo "<a href=$linkBase" . $itemID . ">$itemTitle</a> <BR />";

              $feeNodes = $responseDoc->getElementsByTagName('Fee');
              foreach($feeNodes as $feeNode) {
                $feeNames = $feeNode->getElementsByTagName("Name");
                if ($feeNames->item(0)) {
                    $feeName = $feeNames->item(0)->nodeValue;
                    $fees = $feeNode->getElementsByTagName('Fee');  // get Fee amount nested in Fee
                    $fee = $fees->item(0)->nodeValue;
                    if ($fee > 0.0) {
                        if ($feeName == 'ListingFee') {
                          printf("<B>$feeName : %.2f </B><BR>\n", $fee);
                        } else {
                          printf("$feeName : %.2f <BR>\n", $fee);
                        }
                    }  // if $fee > 0
                } // if feeName
              } // foreach $feeNode

            } // foreach response

            //Insert into Database
            // $xml = simplexml_load_string($responseXml);
			// $total_images = implode(',',$img_name);
			// $conn = mysqli_connect('localhost','root','root','bay') or mysqli_connect_error();
			// mysqli_set_charset($conn,'utf8');
			// $query = mysqli_query($conn,'INSERT INTO `ebay_items` (`search_keyword`,`categoryID`,`itemID`,`title`,`description`,`startprice`,`condition`,`listingDuration`,`image`,`listingtype`)
			// 	VALUES ("'.$safequery.'","'.$primaryCategory.'","'.$xml->ItemID.'","'.mysqli_real_escape_string($conn,$itemTitle).'","'.mysqli_real_escape_string($conn,$itemDescription).'","'.$startPrice.'","'.$itemCondition.'","'.$listingDuration.'","'.$total_images.'","'.$listingType.'")');
			// if(!$query):
			// 	echo 'There was an Error while inserting data<br/>';
			// endif;
			//Insert into Database

		} // if $errors->length > 0
	}
?>
</div>
</body>
</html>
