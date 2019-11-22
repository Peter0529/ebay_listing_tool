<?php

include_once 'eBayFunctions.php';
include_once 'ebay_keys.php';
function upload_image($img_files)
{
    global $APPNAME;
	global $responseEncoding;
    global $COMPATIBILITYLEVEL;
    global $DEVNAME;
    global $APPNAME;
    global $CERTNAME;
    global $AUTH_TOKEN;
    global $eBayAPIURL;

    $error_msg = [];
    $res_msg = [];
    $html_request_head = array("X-EBAY-API-SITEID:0",
                    "X-EBAY-API-COMPATIBILITY-LEVEL:967",
                    "X-EBAY-API-CALL-NAME:" . "UploadSiteHostedPictures",
                    "X-EBAY-API-APP-NAME:" . $APPNAME,
                    "X-EBAY-API-DEV-NAME:" . $DEVNAME,
                    "X-EBAY-API-CERT-NAME:" . $CERTNAME);
    
    $img_files = ['http://www.mysavings.com/img/link/large/77662.jpg','http://www.mysavings.com/img/link/large/77661.jpg','http://www.mysavings.com/img/link/large/77660.jpg'];//This is for test
    foreach ($img_files as $file => $img_file) {
        $html_request_body = '<?xml version="1.0" encoding="utf-8"?>
        <UploadSiteHostedPicturesRequest xmlns="urn:ebay:apis:eBLBaseComponents">
            <RequesterCredentials>
                <eBayAuthToken>' . $AUTH_TOKEN . '</eBayAuthToken>
            </RequesterCredentials>
            <ErrorLanguage>en_US</ErrorLanguage>
            <WarningLevel>High</WarningLevel>
            <ExternalPictureURL>'.$img_file.'</ExternalPictureURL>
            <!--<PictureName>Developer Page Banner</PictureName>-->
        </UploadSiteHostedPicturesRequest>';
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
            if (isset($item_trans_xml->SiteHostedPictureDetails)) {
                $res_msg[$img_file] = (string)$item_trans_xml->SiteHostedPictureDetails[0]->FullURL;
            }
            if (!isset($item_trans_xml->Errors) || empty(($item_trans_xml->Errors))){}
            else
            {
                foreach ($item_trans_xml->Errors as $error){
                    $error_msg[$img_file][]=(string)$error->LongMessage;
                }
            }
        }
        curl_close($curl);
    }
    return ['hosted_url'=>$res_msg,'error_msg'=>$error_msg];
}
?>