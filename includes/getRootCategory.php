<?php
require_once('ebay_keys.php');
require_once('ebayFunctions.php');
$siteId = get_siteID_Num($_GET['siteID']);
global $eBayAPIURL_shopping,$APPNAME;
// Construct the FindItems call
$browse = '';
$apicall = "$eBayAPIURL_shopping?callname=GetCategoryInfo"
    . "&appid=$APPNAME"
    . "&siteid=$siteId"
    . "&CategoryID=-1"
    . "&version=967"
    . "&IncludeSelector=ChildCategories";

// Load the call and capture the document returned by the GetCategoryInfo API
$xml = simplexml_load_file($apicall);

$errors = $xml->Errors;

//if there are error nodes
if($errors->count() > 0)
{
    echo '<p><b>eBay returned the following error(s):</b></p>';
    //display each error
    //Get error code, ShortMesaage and LongMessage
    $code = $errors->ErrorCode;
    $shortMsg = $errors->ShortMessage;
    $longMsg = $errors->LongMessage;
    //Display code and shortmessage
    echo '<p>', $code, ' : ', str_replace(">", "&gt;", str_replace("<", "&lt;", $shortMsg));
    //if there is a long message (ie ErrorLevel=1), display it
    if(count($longMsg) > 0)
        echo '<br>', str_replace(">", "&gt;", str_replace("<", "&lt;", $longMsg));

}
else //no errors
{
    foreach($xml->CategoryArray->Category as $cat){
    if($cat->CategoryLevel!=0):
        $browse.='<option value="'.$cat->CategoryID.'">'.$cat->CategoryName.'</option>';
    endif;
    }

}
echo $browse;
?>