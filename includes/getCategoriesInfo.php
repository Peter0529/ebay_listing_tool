<?php
require_once('ebay_keys.php');
require_once('ebayFunctions.php');
global $eBayAPIURL_shopping,$responseEncoding,$APPNAME,$AUTH_TOKEN;
$categoryID = $_GET['catId'];
$siteID = get_siteID_Num($_GET['siteID']);

// Construct the FindItems call
$apicall = "$eBayAPIURL_shopping?callname=GetCategoryInfo"
     . "&appid=$APPNAME"
     . "&siteid=$siteID"
     . "&CategoryID=$categoryID"
     . "&version=967"
     . "&responseencoding=$responseEncoding"
     . "&IncludeSelector=ChildCategories";

// Load the call and capture the document returned by the GetCategoryInfo API

$xml = simplexml_load_file($apicall);

$errors = $xml->Errors;
$browse = "";
$i = isset($_GET['counter']) ? $_GET['counter'] + 1 : 0;
//echo $i;

//if there are error nodes
if($errors->count() > 0)
{
    echo '<p><b>eBay returned the following error(s):</b>';
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
    //if sub-categories found
    if($xml->CategoryArray->Category->LeafCategory=='false'):

        foreach($xml->CategoryArray->Category as $cat){
            if($cat->CategoryID!=$categoryID):
                if($cat->CategoryLevel!=0):
                    $browse.='<option value="'.$cat->CategoryID.'">'.$cat->CategoryName.'</option>';
                endif;
            endif;
        }

        echo '<select size="15" class="columns" id="subcat_'.$i.'">'.$browse.'</select><span class="subcat_'.$i.'"></span>';
        echo '<script>$("#continue").attr("disabled","disabled"); </script>';
    else: // if no sub-categories found
        $categorypath = str_replace(':', ' > ', $xml->CategoryArray->Category->CategoryNamePath);
        $name =  $xml->CategoryArray->Category->CategoryName;
        $id   = $xml->CategoryArray->Category->CategoryID;
        ?>
        <input type="hidden" name="category" value="<?php echo $id; ?>" />
        <span class="nocategories"><img src="http://pics.ebaystatic.com/aw/pics/icon/iconSuccess_32x32.gif" alt=" ">
              You have selected a category</span>
        <script>
            $("#continue").removeAttr("disabled","disabled"); $(".ionise").html("<b>Category you have selected:</b><ul><li><?php echo $categorypath ?></li></ul><input type='button' id='remove' value='Refresh' />")
            var html = '';
            var variant_html='';
            <?php

                $variant_mandatory = get_category_specifics($siteID,$categoryID,$AUTH_TOKEN);
                foreach($variant_mandatory as $item){
                ?>
                    variant_html = variant_html + "<div class=\"col-xl-3 col-lg-6 col-md-12\"><fieldset class=\"form-group\"><label><?php echo $item?></label><input type=\"text\" class=\"form-control\" name=\"variant_<?php echo $item?>\" id=\"variant_<?php echo $item?>\" required></fieldset></div>";
                <?php
                }
                ?>
                $("#div_variants").html(variant_html);
                <?php
                $res = get_category_features($id);
                if(isset($res['UPCEnabled'])){
                    if($res['UPCEnabled'] == 'Required'){
                        ?>
                       document.getElementById("upc").required = true;
                        <?php
                    }
                    else{
                        ?>
                        document.getElementById("upc").required = false;
                        <?php
                    }
                }
                if(isset($res['ConditionValues'])){
                    foreach(array_keys($res['ConditionValues']) as $cid){
                        ?>
                        html = html + '<option value="<?php echo $cid ?>"><?php echo $res['ConditionValues'][$cid] ?></option>';
                        <?php
                    }
                    ?>
                    $("#item_condition").html(html);
                    <?php
                }
            ?>
        </script>
    <?php
    endif;
}

?>
<script>
$(document).ready(function(){
    <?php $i = isset($_GET['counter']) ? $_GET['counter'] + 1 : 0; ?>
    var counter = <?php echo $i; ?>;
    $('#subcat_'+counter).change(function(){
        var catId = $('#subcat_'+counter).val();
        var siteId = $('#site').val();
        $.get('includes/getCategoriesInfo.php?counter='+counter+'&catId='+catId+'&siteID='+siteId, function(response,status){
            if(status =='success'){
                //alert(response);
                $('span.subcat_'+counter).html(response);
            }
        });
    }); //select onchange
    $('#remove').click(function(){ //alert('response cleared');
        $('.div-scrollbar > span,.ionise').html('');
    })
});
</script>