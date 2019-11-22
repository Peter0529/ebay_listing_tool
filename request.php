  <?php

include_once 'includes/eBayFunctions.php';
 include_once 'includes/ebay_keys.php';
if(isset($_GET['siteID']))
{
  $siteID = $_GET['siteID'];
  echo get_seller_profiles($siteID,$AUTH_TOKEN);
}


?>