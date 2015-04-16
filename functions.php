<?php
/***
* @param [$var ....]
*/

function xmp($o, $add_inf = '')
{
    echo '|********|<xmp>';
    echo $add_inf; print_r($o);
    echo '</xmp>|********|<br>';
}
function stop($o)
{
    xmp($o);
    exit;
}

function print_vars(){
    if(IS_PROD_SITE === true) return ;
    $args = func_get_args();
    $output = call_user_func_array("print_vars2",$args);
    echo '<pre style="font-family: Courier New;font-size:12px;text-align:left">';
    echo htmlspecialchars($output);
    echo '</pre>';
}

function trace_log($text , $file = ''){
    if(IS_PROD_SITE === true ){
        return ;
    }

    if(!is_string($text)){
        $text = print_r($text,true);
    }

    if($file == ''){
        $file = 'trace.log';
    }
    $fp = fopen($file,'a');
    fwrite($fp , date('Y-m-d H:i:s ') . "\r\n" . $text);
    fclose($fp);

}
/**
 * translate China Standard Time (CST) to Pacific Daylight Time (PDT)
 */
function cst2pdt($time){
    $default = strtolower(date_default_timezone_get());

    if(!is_numeric($time)){
        $time = strtotime($time);
    }



    if($default == 'asia/shanghai'){
        $time = $time - 15*3600 ;
    }else if($default == 'etc/utc'){
        $time = $time - 7*3600 ; ;
    }

    return $time;
}
/**
 * print_r
 * @param [$var ....]
 */
function print_vars2(){
    $output = '';
    $args = func_get_args();
    foreach($args as $arg){
        $type = gettype($arg);
        if($type == 'boolean')$output .= $arg == true ? 'true':'false' ;
        else if(in_array($type,array('integer','double','string')))$output .= $arg ;
        else if(in_array($type,array('array','object')))$output .=print_r($arg,true);
        else if(in_array($type,array('NULL','unknown type')))$output .= $type;
        else if($type == 'resource')$output .= '['.get_resource_type($arg).']'.$arg;
        else $output .= 'unknow';
        $output .= "\n";
    }
    return $output;
}
function scs_cc_encrypt($text) {
    $key = CC_ENC_KEY_SECURE_KEY;
    $key = md5($key);
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $text, MCRYPT_MODE_ECB, $iv);

    if($text!=''){
        $return_enc_text = base64_encode($crypttext);
    }else{
        $return_enc_text = '';
    }

    return $return_enc_text;
}

function scs_cc_decrypt($enc) {
    $key = CC_ENC_KEY_SECURE_KEY;
    $enc =base64_decode($enc);
    $key = md5($key);
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $enc, MCRYPT_MODE_ECB, $iv);

    if($enc!=''){
        $return_dec_text = trim($decrypttext);
    }else{
        $return_dec_text = '';
    }
    return ($return_dec_text);
}

function enleve_accent($chaine, $language){
    if($language == 'spanish'){
        return $chaine;
    }else{
        return strtr($chaine, "", "");
    }
}
function tep_get_total_days_of_month($month, $year){
    $month = (int)$month;
    if($month == 1 || $month == 3 || $month == 5 || $month == 7 || $month == 8 || $month == 10 || $month == 12){
        $total_days = 31;
    }else if($month == 4 || $month == 6 || $month == 9 || $month == 11){
        $total_days = 30;
    }else if( ($year%4 == 0 && $year%100 != 0) || $year % 400 == 0){
        $total_days = 29;
    }else{
        $total_days = 28;
    }
    return $total_days;
}
function tep_get_date_db($date){
    if($date!='')
    {
        $date_disp = explode("/",$date);
        $date_return = $date_disp[2].'-'.$date_disp[0].'-'.$date_disp[1];
    }
    else
    {
        $date_return='';
    }

    return $date_return;
}
function tep_get_total_nos_of_rooms($roomsinfo_string){
    if(preg_match('/'.'Total # of Rooms'.': ([0-9]+)/', $roomsinfo_string, $m)) {
        $rtn_val = $m[1];
    }else if(preg_match('/'.'Total # of Rooms'.':([0-9]+)/', $roomsinfo_string, $m)) {
        $rtn_val = $m[1];
    }
    return (int)$rtn_val;
}
function tep_get_rooms_adults_childern($roomsinfo_string,$room,$customer_type){
    switch ($customer_type) {
        case 'adult' :
            if(preg_match('/<br>'.Yii::t('main', TEXT_SHOPPIFG_CART_ADULTS_IN_ROOMS).' '.$room.': ([0-9]+)/', $roomsinfo_string, $m)) {
                $rtn_val = $m[1];
            }else if(preg_match('/'.Yii::t('main', TEXT_SHOPPIFG_CART_ADULTS_IN_ROOMS_WO_SPACE).' '.$room.': ([0-9]+)/', $roomsinfo_string, $m)) {
                $rtn_val = $m[1];
            }else if(preg_match('/'.Yii::t('main', TEXT_SHOPPIFG_CART_ADULTS_IN_ROOMS_OLD).' '.$room.': ([0-9]+)/', $roomsinfo_string, $m)) {
                $rtn_val = $m[1];
            }
            break;
        case 'children' :
            if(preg_match('/<br>'.Yii::t('main', TEXT_SHOPPIFG_CART_CHILDREDN_IN_ROOMS).' '.$room.': ([0-9]+)/', $roomsinfo_string, $m)) {
                $rtn_val = $m[1];
            }else if(preg_match('/'.Yii::t('main',TEXT_SHOPPIFG_CART_CHILDREDN_IN_ROOMS_WO_SPACE).' '.$room.': ([0-9]+)/', $roomsinfo_string, $m)) {
                $rtn_val = $m[1];
            }else if(preg_match('/'.Yii::t('main', TEXT_SHOPPIFG_CART_CHILDREDN_IN_ROOMS_OLD).' '.$room.': ([0-9]+)/', $roomsinfo_string, $m)) {
                $rtn_val = $m[1];
            }
            break;
        case 'price' :
            if(preg_match('/<br>'.Yii::t('main',TEXT_SHOPPIFG_CART_TOTAL_DOLLOR_OF_ROOM).' '.$room.': ([$][0-9.,]+)/', $roomsinfo_string, $m)) {
                $get_room_info_price = explode(Yii::t('main',TEXT_SHOPPIFG_CART_TOTAL_DOLLOR_OF_ROOM).' '.$room.': ', $roomsinfo_string);
                $rtn_val = $m[1];
            }elseif(preg_match('/([$][0-9.,]+)/', $roomsinfo_string, $m)) {
                $get_room_info_price = explode(Yii::t('main', TEXT_SHOPPIFG_CART_TOTAL_DOLLOR_OF_ROOM).' '.$room.': ', $roomsinfo_string);
                $rtn_val = $m[1];
            }
            break;
    }
    if($customer_type == 'price'){
        return $rtn_val;
    }else{
        return (int)$rtn_val;
    }
}
function tep_get_no_adults_childern($roomsinfo_string,$customer_type){
    switch ($customer_type) {
        case 'adult' :
            if(preg_match('/'.Yii::t('main',TEXT_SHOPPIFG_CART_ADULTS_NO).' : ([0-9]+)/', $roomsinfo_string, $m)) {
                $rtn_val = $m[1];
            }else if(preg_match('/'.Yii::t('main', TEXT_SHOPPIFG_CART_ADULTS_NO_WO_SPACE).' : ([0-9]+)/', $roomsinfo_string, $m)) {
                $rtn_val = $m[1];
            }else if(preg_match('/'.Yii::t('main', TEXT_SHOPPIFG_CART_ADULTS_NO_OLD).' : ([0-9]+)/', $roomsinfo_string, $m)) {
                $rtn_val = $m[1];
            }
            break;
        case 'children' :
            if(preg_match('/'.Yii::t('main', TEXT_SHOPPIFG_CART_CHILDREDN_NO).' : ([0-9]+)/', $roomsinfo_string, $m)) {
                $rtn_val = $m[1];
            }else if(preg_match('/'.Yii::t('main', TEXT_SHOPPIFG_CART_CHILDREDN_NO_WO_SPACE).' : ([0-9]+)/', $roomsinfo_string, $m)) {
                $rtn_val = $m[1];
            }else if(preg_match('/'.Yii::t('main', TEXT_SHOPPIFG_CART_CHILDREDN_NO_OLD).' : ([0-9]+)/', $roomsinfo_string, $m)) {
                $rtn_val = $m[1];
            }
            break;
        case 'price' :
            if(preg_match('/'.Yii::t('main', TEXT_SHOPPIFG_CART_NO_ROOM_TOTAL).' : ([$][0-9.,]+)/', $roomsinfo_string, $m)) {
                $rtn_val = $m[1];
            }
            break;
    }

    if($customer_type == 'price'){
        return $rtn_val;
    }else{
        return (int)$rtn_val;
    }
}
function tep_get_room_adult_child_persion_on_room_str($str,$room_no){
    $get_room_no_array = explode('###',$str);
    $adu_and_chile_val_array = explode('!!',$get_room_no_array[$room_no]);
    $total_ad_ch_val_array[0] = $adu_and_chile_val_array[0];
    $total_ad_ch_val_array[1] = $adu_and_chile_val_array[1];
    return $total_ad_ch_val_array;
}
function tep_round($value, $precision) {
    if (PHP_VERSION < 4) {
        $exp = pow(10, $precision);
        return round($value * $exp) / $exp;
    } else {
        return round($value, $precision);
    }
}
function tep_datetime_short($raw_datetime) {
    if ( ($raw_datetime == '0000-00-00 00:00:00') || ($raw_datetime == '') ) return false;

    $year = (int)substr($raw_datetime, 0, 4);
    $month = (int)substr($raw_datetime, 5, 2);
    $day = (int)substr($raw_datetime, 8, 2);
    $hour = (int)substr($raw_datetime, 11, 2);
    $minute = (int)substr($raw_datetime, 14, 2);
    $second = (int)substr($raw_datetime, 17, 2);

    return strftime('%m/%d/%Y  %H:%M:%S', mktime($hour, $minute, $second, $month, $day, $year));
}
function tep_date_short($raw_date) {
    if ( ($raw_date == '0000-00-00 00:00:00') || $raw_date == '0000-00-00' || ($raw_date == '') ) return false;

    $year = substr($raw_date, 0, 4);
    $month = (int)substr($raw_date, 5, 2);
    $day = (int)substr($raw_date, 8, 2);
    $hour = (int)substr($raw_date, 11, 2);
    $minute = (int)substr($raw_date, 14, 2);
    $second = (int)substr($raw_date, 17, 2);

    if (@date('Y', mktime($hour, $minute, $second, $month, $day, $year)) == $year) {
        return date(Yii::t('main',DATE_FORMAT), mktime($hour, $minute, $second, $month, $day, $year));
    } else {
        return preg_replace('/2037' . '$/', $year, date(Yii::t('main',DATE_FORMAT), mktime($hour, $minute, $second, $month, $day, 2037)));
    }
}
function highlightWords($string, $words){
    if($words!=''){
        $string = preg_replace("/(".$words.")/i", "<span class=highlight_word>".$words."</span>", $string);
    }
    return $string;
}
function tep_get_spam_report_type($post_type_id){
    if($post_type_id == '1'){
        $spam_post_type = 'Spam or Scam';
    }elseif($post_type_id == '2'){
        $spam_post_type = 'Contains hate speech or attacks an individual';
    }elseif($post_type_id == '3'){
        $spam_post_type = 'Violence, crime, or self harm';
    }elseif($post_type_id == '4'){
        $spam_post_type = 'Nudity, pornography, or sexually explicit content';
    }
    return  $spam_post_type;
}
function tep_date_long_review($raw_date) {
    if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '') ) return false;

    $year = (int)substr($raw_date, 0, 4);
    $month = (int)substr($raw_date, 5, 2);
    $day = (int)substr($raw_date, 8, 2);
    $hour = (int)substr($raw_date, 11, 2);
    $minute = (int)substr($raw_date, 14, 2);
    $second = (int)substr($raw_date, 17, 2);

    return strftime('%a %b %d, %Y at %I.%M %p ', mktime($hour,$minute,$second,$month,$day,$year));
}
function tep_db_prepare_input($string) {
    if (is_string($string)) {
        return trim(tep_sanitize_string(stripslashes($string)));
    } elseif (is_array($string)) {
        reset($string);
        while (list($key, $value) = each($string)) {
            $string[$key] = tep_db_prepare_input($value);
        }
        return $string;
    } else {
        return $string;
    }
}

function tep_sanitize_string($string) {
    // Squeezes multiple spaces into a single one:
    $string = preg_replace('/\s+/', ' ', trim($string));

    $string = preg_replace("/[<>]/", '_', $string);

    return $string;
}

function tep_sort_created($a, $b) {
    if (strtotime($a['created']) < strtotime($b['created'])) {
        return 1;
    } else {
        return -1;
    }
}

function tep_substr($str, $max_len=100){
    if(strlen($str) > $max_len ){
        $ret_str = tep_db_prepare_input(substr($str, 0, $max_len-3)).'...';
    }else{
        $ret_str = tep_db_prepare_input($str);
    }
    return $ret_str;
}
function checkImage($path) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $path);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10); //follow up to 10 redirections - avoids loops
    $data = curl_exec($ch);
    curl_close($ch);
    if (!$data) {
        echo "image could not be found";
    }
    else {
        preg_match_all("/HTTP\/1\.[1|0]\s(\d{3})/",$data,$matches);
        $code = end($matches[1]);
        if ($code == 200) {
            //echo "Page Found";
            return true;
        }
        elseif ($code == 404) {
            //echo "Page Not Found";
            return false;
        }
    }
}
// Break a word in a string if it is longer than a specified length ($len)
function tep_break_string($string, $len, $break_char = '-') {
    $l = 0;
    $output = '';
    for ($i=0, $n=strlen($string); $i<$n; $i++) {
        $char = substr($string, $i, 1);
        if ($char != ' ') {
            $l++;
        } else {
            $l = 0;
        }
        if ($l > $len) {
            $l = 1;
            $output .= $break_char;
        }
        $output .= $char;
    }

    return $output;
}
function tep_parse_input_field_data($data, $parse) {
    return strtr(trim($data), $parse);
}
function tep_output_string($string, $translate = false, $protected = false) {
    if ($protected == true) {
        return htmlspecialchars($string);
    } else {
        if ($translate == false) {
            return tep_parse_input_field_data($string, array('"' => '&quot;'));
        } else {
            return tep_parse_input_field_data($string, $translate);
        }
    }
}
function tep_output_string_protected($string) {
    return tep_output_string($string, false, true);
}
function split_desk_numbers_display_in_two_parts($number_to_split){
    $number_in_two_parts_array = explode('-',$number_to_split);
    $n = sizeof($number_in_two_parts_array);
    if($n>1){
        for($t=1;$t<$n;$t++){
            $rest_number_in_two_parts_array .= $number_in_two_parts_array[$t].'-';
        }
    }
    $number_in_two_parts_array[1] = substr($rest_number_in_two_parts_array,0,-1);
    return $number_in_two_parts_array;
}

function RTESafe($strText) {
    //returns safe code for preloading in the RTE
    $tmpString = trim($strText);
    //convert all types of single quotes
    $tmpString = str_replace(chr(145), chr(39), $tmpString);
    $tmpString = str_replace(chr(146), chr(39), $tmpString);
    $tmpString = str_replace("'", "&#39;", $tmpString);
    //convert all types of double quotes
    $tmpString = str_replace(chr(147), chr(34), $tmpString);
    $tmpString = str_replace(chr(148), chr(34), $tmpString);
    //replace carriage returns & line feeds
    $tmpString = str_replace(chr(10), " ", $tmpString);
    $tmpString = str_replace(chr(13), "<br />", $tmpString);
    return $tmpString;
}

function tep_not_null($value) {
    if (is_array($value)) {
        if (sizeof($value) > 0) {
            return true;
        } else {
            return false;
        }
    } else {
        if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) {
            return true;
        } else {
            return false;
        }
    }
}
function get_total_room_from_str($str){
    $get_room_no_array = explode('###',$str);
    return $get_room_no_array[0];
}
function get_roominfo_formatted_string($new_roominfo_string, $old_roominfo_string){
    $total_rooms = get_total_room_from_str($new_roominfo_string);
    $return_str = '';
    $return_str .= '<table width="250" cellspacing="0" cellpadding="0" border="0">';
    if($total_rooms > 0){
        $return_str .= '<tr><td class="p_l1 tab_t_bg ">'.Yii::t('main',TXT_ROOMS).'</td><td class="tab_t_bg ">'.Yii::t('main',TXT_ADULTS).'</td><td class="tab_t_bg ">'.Yii::t('main',TXT_CHILDREN).'</td><td class="tab_t_bg ">'.Yii::t('main',TXT_PRICE).'</td></tr>';
        for($t=1;$t<=$total_rooms;$t++){
            $chaild_adult_no_arr = tep_get_room_adult_child_persion_on_room_str($new_roominfo_string, $t);
            $room_total_price = tep_get_rooms_adults_childern($old_roominfo_string, $t, 'price');
            $return_str .= '<tr><td class="p_l1 order_default"><span>'.$t.'</span></td><td class="order_default">'.$chaild_adult_no_arr[0].'</td><td class="order_default">'.$chaild_adult_no_arr[1].'</td><td class="order_default">'.$room_total_price.'</td></tr>';
        }
    }else{
        $chaild_adult_no_arr = tep_get_room_adult_child_persion_on_room_str($new_roominfo_string, 1);
        $total_adults = $chaild_adult_no_arr[0];
        $total_children = $chaild_adult_no_arr[1];
        $total_price = tep_get_no_adults_childern($old_roominfo_string, 'price');
        $return_str .= '<tr><td class="tab_t_bg ">'.Yii::t('main',TXT_ADULTS).'</td><td class="tab_t_bg ">'.Yii::t('main',TXT_CHILDREN).'</td><td class="tab_t_bg ">'.Yii::t('main',TXT_PRICE).'</td></tr><tr><td class="order_default">'.$total_adults.'</td><td class="order_default">'.$total_children.'</td><td class="order_default">'.$total_price.'</td></tr>';
    }
    if(strstr(strip_tags($old_roominfo_string), Yii::t('main',TEXT_SHOPPIFG_CART_TOTAL_FARES_TRANSACTION_FEE_NO_PERSENT))){
        $return_str .= '<tr><td colspan="4">'.Yii::t('main',TEXT_SHOPPIFG_CART_TOTAL_FARES_TRANSACTION_FEE_NO_PERSENT).'</td></tr>';
    }
    $return_str .= '</table>';
    return $return_str;
}
//get roominfo and traveler
function get_roominfo_formatted_string_phone($new_roominfo_string, $old_roominfo_string, $_version = 1){
    $total_rooms = get_total_room_from_str($new_roominfo_string);
    $room_info_array = array();
    if($total_rooms > 0){
        $room_info_array['title'] = array(Yii::t('main',TXT_ROOMS), Yii::t('main',TXT_ADULTS), Yii::t('main',TXT_CHILDREN), Yii::t('main',TXT_PRICE));


        // override for _version 2
        // @author Gihan S <gihanshp@gmail.com>
        if($_version == 2)
            $room_info_array['title'] = array('rooms' => Yii::t('main',TXT_ROOMS), 'adults' => Yii::t('main',TXT_ADULTS), 'children' => Yii::t('main',TXT_CHILDREN), 'price' => Yii::t('main',TXT_PRICE));

        for($t=1;$t<=$total_rooms;$t++){
            $chaild_adult_no_arr = tep_get_room_adult_child_persion_on_room_str($new_roominfo_string,$t);
            $room_total_price = tep_get_rooms_adults_childern($old_roominfo_string,$t,'price');
            $room_info_array['value'][$t-1] = array($t, $chaild_adult_no_arr[0], $chaild_adult_no_arr[1], $room_total_price);

            // override for _version 2
            // @author Gihan S <gihanshp@gmail.com>
            if($_version == 2)
                $room_info_array['value'][$t-1] = array('room'=>$t, 'adults'=>$chaild_adult_no_arr[0], 'children'=>$chaild_adult_no_arr[1], 'price'=>$room_total_price);
        }
    }
    else{
        $room_info_array['title'] = array(Yii::t('main',TXT_ADULTS), Yii::t('main',TXT_CHILDREN), Yii::t('main',TXT_PRICE));

        // override for version 2
        // @author Gihan S <gihanshp@gmail.com>
        if($_version == 2)
            $room_info_array['title'] = array('adults'=> Yii::t('main',TXT_ADULTS), 'children'=>Yii::t('main',TXT_CHILDREN), 'price'=>Yii::t('main',TXT_PRICE));

        $chaild_adult_no_arr = tep_get_room_adult_child_persion_on_room_str($new_roominfo_string, 1);
        $total_adults = $chaild_adult_no_arr[0];
        $total_children = $chaild_adult_no_arr[1];
        $total_price = tep_get_no_adults_childern($old_roominfo_string,'price');
        $room_info_array['value'][0] = array($total_adults, $total_children, $total_price);

        // override for version 2
        // @author Gihan S <gihanshp@gmail.com>
        if($_version == 2)
            $room_info_array['value'][0] = array('adults'=>$total_adults, 'children'=>$total_children, 'price'=>$total_price);
    }
    return $room_info_array;
}
// Calculates Tax rounding the result
function tep_calculate_tax($price, $tax) {
    return tep_round($price * $tax / 100, 2);
}
//function to change the date format from '2008-12-31' to '12/31/2008'
function tep_get_date_disp($date)
{
    if($date!='')
    {
        $date_disp = strtotime($date);
        $date_return = date("m/d/Y",$date_disp);
    }
    else
    {
        $date_return='';
    }
    return $date_return;
}

function show_rc_heading_repeat($heading){
    $div_rc_heading='';
    $div_rc_heading='<div class="rc_heading_bg">'.
    '<h2>'.$heading.'</h2>'.
    '<span><a href="http://tours4fun.com">'.'<img src="image/rc_logo.png" alt=""  />'.'</a></span></div>';
    return $div_rc_heading;
}
function get_next_lavel_remainting_points($poinst_now_available){
    $points='';
    if($poinst_now_available<5000){ //level 1 bronze
        $points=5000-$poinst_now_available;
        if($points<=100) {
            $points = $points."~".Yii::t('repeat-customer-summary', TEXT_LEVEL_SILVER);
        }else{
            $points = $points."~".'true';
        }
    }
    else if($poinst_now_available>=5000 && $poinst_now_available <=8999){ //level 2 silver
        $points=9000-$poinst_now_available;
        if($points<=100){
            $points = $points."~".Yii::t('repeat-customer-summary', TEXT_LEVEL_GOLD);
        }else{
            $points = $points."~".'true';
        }
    }
    else if($poinst_now_available>=9000 && $poinst_now_available <=17499){ // level 3 gold
        $points=17500-$poinst_now_available;
        if($points<=100){
            $points = $points."~".Yii::t('repeat-customer-summary', TEXT_LEVEL_PLATINUM);
        }else{
            $points = $points."~".'true';
        }
    }
    else{
        $points = $poinst_now_available."~".Yii::t('repeat-customer-summary', TEXT_LEVEL_PLATINUM);
    }
    return $points;
}

function send_visa_card_type($poinst_now_available){
    if($poinst_now_available<=4999){
        return Yii::t('repeat-customer-summary',TEXT_LEVEL_BRONZE_CARD);
    }else if($poinst_now_available>=5000 && $poinst_now_available<=8999){
        return Yii::t('repeat-customer-summary',TEXT_LEVEL_SILVER_CARD);
    }else if($poinst_now_available>=9000 && $poinst_now_available<=17499){
        return Yii::t('repeat-customer-summary', TEXT_LEVEL_GOLD_CARD);
    }else if($poinst_now_available>=17500){
        return Yii::t('repeat-customer-summary', TEXT_LEVEL_PLATINUM_CARD);
    }
}

function check_previos_level($previos_level){
    $customers_shopping_points=$previos_level;
    if($previos_level<=4999){
        $customers_shopping_points=Yii::t('repeat-customer-summary', BRONZE_LEVEL_POINTS);
    }else if($previos_level>=5000 && $previos_level<=8999){
        $customers_shopping_points=Yii::t('repeat-customer-summary', SILVER_LEVEL_POINTS);
    }else if($previos_level>=9000 && $previos_level<=17499){
        $customers_shopping_points=Yii::t('repeat-customer-summary', GOLD_LEVEL_POINTS);
    }else if($previos_level>=17500 && $previos_level<20000){
        $customers_shopping_points=Yii::t('repeat-customer-summary', PLATINUM_LEVEL_POINTS);
    }
    return $customers_shopping_points;
}
function date_add_day($length,$format,$date_passed){
    $new_timestamp = -1;
    if($date_passed != ''){
        $date_passed_array = explode('/',$date_passed);
        $date_actual["mon"] = $date_passed_array[0];
        $date_actual["mday"] = $date_passed_array[1];
        $date_actual["year"] = $date_passed_array[2];


        switch(strtolower($format)){
            case 'd':
                $new_timestamp = @mktime(0,0,0,$date_actual["mon"],$date_actual["mday"]+$length,$date_actual["year"]);
                break;
            case 'm':
                $new_timestamp = @mktime(0,0,0,$date_actual["mon"]+$length,$date_actual["mday"],$date_actual["year"]);
                break;
            case 'y':
                $new_timestamp = @mktime(0,0,0,$date_actual["mon"],$date_actual["mday"],$date_actual["year"]+$length);
                break;
            default:
                break;
        }

        return @date('m/d/Y',$new_timestamp);

    }else{
        return '';
    }
}
function tep_calc_shopping_pvalue($points) {
    return tep_round(((float)$points * (float)REDEEM_POINT_VALUE), 2);
}
function tep_date_long($raw_date,$date_format_type='%A %d %B, %Y') {
    if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '0000-00-00') || ($raw_date == '') ) return false;

    $year = (int)substr($raw_date, 0, 4);
    $month = (int)substr($raw_date, 5, 2);
    $day = (int)substr($raw_date, 8, 2);
    $hour = (int)substr($raw_date, 11, 2);
    $minute = (int)substr($raw_date, 14, 2);
    $second = (int)substr($raw_date, 17, 2);

    return strftime($date_format_type, mktime($hour,$minute,$second,$month,$day,$year));
}
function check_web_url($url) {
    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, $url);
    curl_setopt($c, CURLOPT_HEADER, 1); // get the header
    curl_setopt($c, CURLOPT_NOBODY, 1); // and only get the header
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1); // get the response as a string from curl_exec(), rather than echoing it
    curl_setopt($c, CURLOPT_FRESH_CONNECT, 1); // don't use a cached version of the url
    if (!curl_exec($c)) { return false; }

    $httpcode = curl_getinfo($c, CURLINFO_HTTP_CODE);
    return ($httpcode < 400);
}

function tep_draw_separator($image = 'pixel_black.gif', $width = '100%', $height = '1') {
    $image = '<img src="'.Yii::app()->baseUrl.'/image/'.$image.'" width="'.$width.'" alt="" height="'.$height.'" />';
    return $image;
}
function clean_html_comments($clean_html) {
    global $its_cleaned;

    if ( strpos($clean_html,'<!--//*')>1 ) {
        $the_end1= strpos($clean_html,'<!--//*')-1;
        $the_start2= strpos($clean_html,'*//-->')+7;

        $its_cleaned= substr($clean_html,0,$the_end1);
        $its_cleaned.= substr($clean_html,$the_start2);
    } else {
        $its_cleaned= $clean_html;
    }
    return $its_cleaned;
}
function get_travel_companion_status($status_id){
    $status_array = array();
    $status_array[0] = Yii::t('main',TXT_FUN_UNPAID);
    $status_array[1] = Yii::t('main',TXT_FUN_PAID_PENDING);
    $status_array[2] = Yii::t('main',TXT_FUN_PAYMENT_COMPLETED);
    return $status_array[(int)$status_id];
}
function tep_display_tax_value($value, $padding = TAX_DECIMAL_PLACES) {
    if (strpos($value, '.')) {
        $loop = true;
        while ($loop) {
            if (substr($value, -1) == '0') {
                $value = substr($value, 0, -1);
            } else {
                $loop = false;
                if (substr($value, -1) == '.') {
                    $value = substr($value, 0, -1);
                }
            }
        }
    }

    if ($padding > 0) {
        if ($decimal_pos = strpos($value, '.')) {
            $decimals = strlen(substr($value, ($decimal_pos+1)));
            for ($i=$decimals; $i<$padding; $i++) {
                $value .= '0';
            }
        } else {
            $value .= '.';
            for ($i=0; $i<$padding; $i++) {
                $value .= '0';
            }
        }
    }

    return $value;
}
function scs_rss_safe($strText) {
    //returns safe code for preloading in the RTE
    $tmpString = trim($strText);
    //convert all types of single quotes
    $tmpString = str_replace(chr(145), chr(39), $tmpString);
    $tmpString = str_replace(chr(146), chr(39), $tmpString);
    //$tmpString = str_replace("'", "&#39;", $tmpString);
    //convert all types of double quotes
    $tmpString = str_replace('"', '\'', $tmpString);
    $tmpString = str_replace(chr(147), chr(34), $tmpString);
    $tmpString = str_replace(chr(148), chr(34), $tmpString);
    //replace carriage returns & line feeds
    $tmpString = str_replace(chr(10), "", $tmpString);
    $tmpString = str_replace(chr(13), " ", $tmpString);
    $tmpString = str_replace("'", "'", $tmpString);
    $tmpString = str_replace('"', '"', $tmpString);
    $tmpString = str_replace('"', '"', $tmpString);
    $tmpString = str_replace('?', '...', $tmpString);

    return $tmpString;
}
/*
* @param image absulate path
* @functions - give a height size of image
* @return - return height size of image
*/
function getHeight($image) {
    $sizes = @getimagesize($image);
    $height = $sizes[1];
    return $height;
}
/*
* @param image absulate path
* @functions - give a width size of image
* @return - return width size of image
*/
function getWidth($image) {
    $sizes = @getimagesize($image);
    $width = $sizes[0];
    return $width;
}
/*
* @param string
* @functions - remove any special caracter from string
* @return - return actual string
*/
function Unaccent($string){
    return preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', htmlentities($string, ENT_QUOTES, 'UTF-8'));
}

/*
* @param string
* @functions - generate month array
* @return - return return month array
*/
function getMonthInDrp(){
    $months=array();
    $months[''] = Yii::t('travel_companion_profile',TXT_MONTH);
    $months[1]  = 'January';
    $months[2]  = 'February';
    $months[3]  = 'March';
    $months[4]  = 'April';
    $months[5]  = 'May';
    $months[6]  = 'June';
    $months[7]  = 'July';
    $months[8]  = 'August';
    $months[9]  = 'September';
    $months[10] = 'October';
    $months[11] = 'November';
    $months[12] = 'December';
    return $months;
}
function daysInArray(){
    $days = range (1, 31);
    $days_in=array();
    $days_in[''] = Yii::t('travel_companion_profile', TXT_DD);
    foreach ($days as $value) {
        $days_in[$value] = $value;
    }
    return $days_in;
}
function yearInArray(){
    $current_year=date('Y');
    $years = range (1950, $current_year);
    $years_in=array();
    $years_in[''] = Yii::t('travel_companion_profile', TXT_YRS);
    foreach ($years as $value_y) {
        $years_in[$value_y] = $value_y;
    }
    return $years_in;
}

/* Added for - addslashes */
function dbInput($string) { return addslashes($string); }

function tep_db_output($string) { return htmlspecialchars($string); }

/* Get Age Count */
function determineAge($birth_date)
{
    $to_date = date('m/d/Y', strtotime($birth_date));
    list($birth_month, $birth_day, $birth_year) = explode('/', $to_date);
    $now = time();
    $current_year = date("Y");
    $this_year_birth_date = $birth_month.'/'.$birth_day.'/'.$current_year;
    $this_year_birth_date_timestamp = strtotime($this_year_birth_date);
    $years_old = $current_year - $birth_year;
    if($now < $this_year_birth_date_timestamp)
    {
        $years_old = $years_old - 1;
    }
    return $years_old;
}
function tep_display_points($products_price, $products_tax, $quantity = 1) {

    if ((DISPLAY_PRICE_WITH_TAX == 'true') && (USE_POINTS_FOR_TAX == 'true')) {
        $products_price_points_query = tep_add_tax($products_price, $products_tax) * $quantity;
    } else {
        $products_price_points_query = $products_price * $quantity;
    }

    return $products_price_points_query;
}
function tep_add_tax($price, $tax) {
    if ( (DISPLAY_PRICE_WITH_TAX == 'true') && ($tax > 0) ) {
        return tep_round($price, 2) + tep_calculate_tax($price, $tax);
    } else {
        return tep_round($price, 2);
    }
}
function tep_calc_price_pvalue($products_points_total) {
    $products_points_value = tep_calc_shopping_pvalue($products_points_total);
    return($products_points_value);
}
function tep_calc_products_price_points($products_price_points_query) {
    $products_points_total = $products_price_points_query * POINTS_PER_AMOUNT_PURCHASE;
    return $products_points_total;
}


/*
* @ return review rating array
*/
function get_reviews_array(){
    $array = array();
    $array[0]['title'] = Yii::t('main',REVIEWS_RATING_RESERVATION);
    $array[0]['opction'] = array('20'=>Yii::t('main', Yii::t('main', TXT_EXCELLENT)), '13'=>Yii::t('main',TXT_FAIR), '7'=>Yii::t('main', TXT_POOR));
    $array[1]['title'] = Yii::t('main', REVIEWS_RATING_CUSTOMER_SERVICE);
    $array[1]['opction'] = array('20'=>Yii::t('main', TXT_EXCELLENT), '13'=>Yii::t('main', TXT_FAIR), '7'=>Yii::t('main', TXT_POOR));
    $array[2]['title'] = Yii::t('main', REVIEWS_RATING_ACCOMMODATION);
    $array[2]['opction'] = array('15'=>Yii::t('main', TXT_EXCELLENT), '10'=>Yii::t('main', TXT_FAIR), '5'=>Yii::t('main', TXT_POOR));
    $array[3]['title'] = Yii::t('main',REVIEWS_RATING_TRANSPORTATION);
    $array[3]['opction'] = array('15'=>Yii::t('main', TXT_EXCELLENT), '10'=>Yii::t('main', TXT_FAIR), '5'=>Yii::t('main', TXT_POOR));
    $array[4]['title'] = Yii::t('main', REVIEWS_RATING_TOUR_GUIDE);
    $array[4]['opction'] = array('15'=>Yii::t('main', TXT_EXCELLENT), '10'=>Yii::t('main', TXT_FAIR), '5'=>Yii::t('main',TXT_POOR));
    $array[5]['title'] = Yii::t('main', REVIEWS_RATING_TOUR_ITINERARY);
    $array[5]['opction'] = array('15'=>Yii::t('main', TXT_EXCELLENT), '10'=>Yii::t('main', TXT_FAIR), '5'=>Yii::t('main', TXT_POOR));
    return $array;
}

function tep_get_compareDates_yyyy_mm_dd($date1, $date2) {
    $date1_array = explode("-",$date1);
    $date2_array = explode("-",$date2);
    $timestamp1 = mktime(0, 0, 0, $date1_array[1], $date1_array[2], $date1_array[0]);
    $timestamp2 = mktime(0, 0, 0, $date2_array[1], $date2_array[2], $date2_array[0]);
    if ($timestamp1 >= $timestamp2) {
        $ret_str = 'valid';
    } else {
        $ret_str = 'invalid';
    }
    return $ret_str;
}

/*
* @author - Pravin
* @param - 2 dates
* @return - its valid or not
*/
function tep_get_compareDates_mm_dd_yyyy($date1,$date2) {
    $date1_array = explode("-",$date1);
    $date2_array = explode("-",$date2);
    $timestamp1 = mktime(0,0,0,$date1_array[0],$date1_array[1],$date1_array[2]);
    $timestamp2 = mktime(0,0,0,$date2_array[0],$date2_array[1],$date2_array[2]);
    if (($timestamp1>$timestamp2 || $timestamp1 == $timestamp2) && (int)$date2_array[2] != 0) {
        $ret_str = 'valid';
    } else {
        $ret_str = 'invalid';
    }
    return $ret_str;
}
/*
* @author - Pravin
* @param - 2 dates
* @return - its valid or not
*/
function check_valid_available_date($startDate,$checkDate,$endDate){
    if(strlen($endDate) == 10){
        if(tep_get_compareDates_mm_dd_yyyy($checkDate,$startDate) == "valid" &&  tep_get_compareDates_mm_dd_yyyy($endDate,$checkDate) == "valid") {
            return "valid";
        } else {
            return "invalid";
        }
    }else{

        if($startDate <= $checkDate && $checkDate <= $endDate) {
            return "valid";
        } else {
            return "invalid";
        }

    }
}
function check_valid_available_date_endate($endDate){
    $checkDate = date("m-d-Y");
    if(strlen($endDate) == 10){

        if(tep_get_compareDates_mm_dd_yyyy($endDate,$checkDate) == "valid") {
            return "valid";
        } else {
            return "invalid";
        }

    }else{

        if($checkDate <= $endDate) {
            return "valid";
        } else {
            return "invalid";
        }

    }
}
function tep_get_display_reg_special_picing_title($products_start_day,$opestartdate,$opeenddate){

    $operate = '';
    $day1 ='';
    $operator_result['week_day'] = $products_start_day;

    if($operator_result['week_day'] == 1)
    {
        $day1 .= 'Sun/';
    }
    if($operator_result['week_day'] == 2)
    {
        $day1 .= 'Mon/';
    }

    if($operator_result['week_day'] == 3)
    {
        $day1 .= 'Tue/';
    }
    if($operator_result['week_day'] == 4)
    {
        $day1 .= 'Wed/';
    }
    if($operator_result['week_day'] == 5)
    {
        $day1 .= 'Thu/';
    }
    if($operator_result['week_day'] == 6)
    {
        $day1 .= 'Fri/';
    }
    if($operator_result['week_day'] == 7)
    {
        $day1 .= 'Sat/';
    }

    $opestartdayarray = explode('-',$opestartdate);
    $operatetomodistart = strftime('%b', mktime(0,0,0,$opestartdayarray[0],15)).' '.date("jS", mktime(0, 0, 0, 0,$opestartdayarray[1], 0));

    $opeenddayarray = explode('-',$opeenddate);
    $operatetomodiend = strftime('%b', mktime(0,0,0,$opeenddayarray[0],15)).' '.date("jS", mktime(0, 0, 0, 0,$opeenddayarray[1], 0));

    if($opestartdate == '01-01' && $opeenddate == '12-31'){
        $operate .= $day1.' ';
    }else{
        $operate .= $opestartdayarray[1].'/'.$opestartdayarray[0].'/'.$opestartdayarray[2].'-'.$opeenddayarray[1].'/'.$opeenddayarray[0].'/'.$opeenddayarray[2].': '.$day1.' ';
    }
    return $operate;
}//End

/**
 * validate email
 *
 * @param string $email
 * @return boolean
 */
function is_CheckvalidEmail($email){
    $isValid = true;
    $atIndex = strrpos($email, "@");
    if (is_bool($atIndex) && !$atIndex)
    {
        $isValid = false;
    }
    else
    {
        $domain = substr($email, $atIndex+1);
        $local = substr($email, 0, $atIndex);
        $localLen = strlen($local);
        $domainLen = strlen($domain);
        if ($localLen < 1 || $localLen > 64)
        {
            // local part length exceeded
            $isValid = false;
        }
        else if ($domainLen < 1 || $domainLen > 255)
        {
            // domain part length exceeded
            $isValid = false;
        }
        else if ($local[0] == '.' || $local[$localLen-1] == '.')
        {
            // local part starts or ends with '.'
            $isValid = false;
        }
        else if (preg_match('/\\.\\./', $local))
        {
            // local part has two consecutive dots
            $isValid = false;
        }
        else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
        {
            // character not valid in domain part
            $isValid = false;
        }
        else if (preg_match('/\\.\\./', $domain))
        {
            // domain part has two consecutive dots
            $isValid = false;
        }
        else if(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local)))
        {
            // character not valid in local part unless
            // local part is quoted
            if (!preg_match('/^"(\\\\"|[^"])+"$/',
            str_replace("\\\\","",$local)))
            {
                $isValid = false;
            }
        }
        /*if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
        {
            // domain not found in DNS
            $isValid = false;
        } */

    }
    return true;
}

//start validate email
function tep_validate_email($email) {
    $valid_address = true;

    $mail_pat = '/^(.+)@(.+)$/';
    $valid_chars = "[^] \(\)<>@,;:\.\\\"\[]";
    $atom = "$valid_chars+";
    $quoted_user='(\"[^\"]*\")';
    $word = "($atom|$quoted_user)";
    $user_pat = "/^$word(\.$word)*$/";
    $ip_domain_pat='/^\[([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\]$/';
    $domain_pat = "/^$atom(\.$atom)*$/";

    if (preg_match($mail_pat, $email, $components)) {
        $user = $components[1];
        $domain = $components[2];
        // validate user
        if (preg_match($user_pat, $user)) {
            // validate domain
            if (preg_match($ip_domain_pat, $domain, $ip_components)) {
                // this is an IP address
                for ($i=1;$i<=4;$i++) {
                    if ($ip_components[$i] > 255) {
                        $valid_address = false;
                        break;
                    }
                }
            }
            else {
                // Domain is a name, not an IP
                if (preg_match($domain_pat, $domain)) {
                    /* domain name seems valid, but now make sure that it ends in a valid TLD or ccTLD
                    and that there's a hostname preceding the domain or country. */
                    $domain_components = explode(".", $domain);
                    // Make sure there's a host name preceding the domain.
                    if (sizeof($domain_components) < 2) {
                        $valid_address = false;
                    } else {
                        $top_level_domain = strtolower($domain_components[sizeof($domain_components)-1]);
                        // Allow all 2-letter TLDs (ccTLDs)
                        if (preg_match('/^[a-z][a-z]$/', $top_level_domain) != 1) {
                            $tld_pattern = '';
                            // Get authorized TLDs from text file
                            $tlds = file(WEBEEZ_LIB.DIRECTORY_SEPARATOR.'tld.txt');
                            while (list(,$line) = each($tlds)) {
                                // Get rid of comments
                                $words = explode('#', $line);
                                $tld = trim($words[0]);
                                // TLDs should be 3 letters or more
                                if (preg_match('/^[a-z]{3,}$/', $tld) == 1) {
                                    $tld_pattern .= '^' . $tld . '$|';
                                }
                            }
                            // Remove last '|'
                            $tld_pattern = substr($tld_pattern, 0, -1);
                            if (preg_match("/$tld_pattern/", $top_level_domain) == 0) {
                                $valid_address = false;
                            }
                        }
                    }
                }
                else {
                    $valid_address = false;
                }
            }
        }
        else {
            $valid_address = false;
        }
    }
    else {
        $valid_address = false;
    }
    if ($valid_address && ENTRY_EMAIL_ADDRESS_CHECK == 'true') {
        if (!checkdnsrr($domain, "MX") && !checkdnsrr($domain, "A")) {
            $valid_address = false;
        }
    }
    return $valid_address;
}
//girish vadher
function tep_get_blog_status_name($blog_status_id){
    if($blog_status_id==1){
        $blog_status_name = 'Approved';
    }else if($blog_status_id==0){
        $blog_status_name = 'Disapproved';
    }else{
        $blog_status_name = 'Pending Review';
    }
    return $blog_status_name;
}

//end validate email

/*
* image compress function
*/
function imageCompression($imgfile="",$thumbsize=0,$savePath=NULL) {
    if($savePath==NULL) {
        header('Content-type: image/jpeg');
    }
    list($width,$height)=getimagesize($imgfile);
    $imgratio=$width/$height;

    if($imgratio>1) {
        $newwidth=$thumbsize;
        $newheight=$thumbsize/$imgratio;
    } else {
        $newheight=$thumbsize;
        $newwidth=$thumbsize*$imgratio;
    }

    $thumb=imagecreatetruecolor($newwidth,$newheight);

    $source=imagecreatefromfile($imgfile);
    imagecopyresampled($thumb,$source,0,0,0,0,$newwidth,$newheight,$width,$height);

    imagejpeg($thumb,$savePath,93);
    imagedestroy($thumb);
}
/*
* if the file is not in jpg format function for other files
*/
function imagecreatefromfile($path, $user_functions = false)
{
    $info = @getimagesize($path);
    if(!$info)
    {  return false; }
    $functions = array(
    IMAGETYPE_GIF => 'imagecreatefromgif',
    IMAGETYPE_JPEG => 'imagecreatefromjpeg',
    IMAGETYPE_PNG => 'imagecreatefrompng',
    IMAGETYPE_WBMP => 'imagecreatefromwbmp',
    IMAGETYPE_XBM => 'imagecreatefromwxbm',
    );
    if($user_functions)
    { $functions[IMAGETYPE_BMP] = 'imagecreatefrombmp'; }
    if(!$functions[$info[2]])
    { return false; }
    if(!function_exists($functions[$info[2]]))
    { return false; }
    return $functions[$info[2]]($path);
}//End
function GetWorkingDays($fromDate, $interval ) {
    $date_array = explode('-', $fromDate );
    $day = $date_array[2];
    $month = $date_array[1];
    $year = $date_array[0];
    //$working_date = array();
    for ($i = 1; $i <= $interval; $i++){
        $day_text = date("D", mktime( 0, 0, 0,$month,$day + (int)$i,$year));
        if( $day_text == 'Sat' || $day_text == 'Sun' ) {
            $interval++;
            continue;
        }
        $working_date = date("Y-m-d", mktime(0, 0, 0,$month,$day +(int)$i,$year));
    }
    return $working_date;
}//End
function tep_get_cart_get_extra_field_value($fieldname, $extra_values_str=''){
    $fieldvalue = '';
    if(tep_not_null($extra_values_str)){
        $extra_values_array = explode("|##!##|", $extra_values_str);
        $total_ext_values = count($extra_values_array)-1;
        $new_extra_values_str = '';
        for($ext=0; $ext<$total_ext_values; $ext++){
            $get_field_info = explode("|#|", $extra_values_array[$ext]);
            if($get_field_info[0] == $fieldname){
                $fieldvalue = $get_field_info[1];
            }
        }
    }
    return $fieldvalue;
}//End

function date_add_day_product($length,$format,$date_passed){
    $new_timestamp = -1;
    if($date_passed != ''){
        $date_passed_split_array = explode('::',$date_passed);
        $date_passed_array = explode('-',$date_passed_split_array[0]);
        $date_actual["mon"] = $date_passed_array[1];
        $date_actual["mday"] = $date_passed_array[2];
        $date_actual["year"] = $date_passed_array[0];
        if((int)$length < 1) { $length = 0; }
        switch(strtolower($format)){
            case 'd':
                $new_timestamp = @mktime(0,0,0,$date_actual["mon"],$date_actual["mday"]+$length,$date_actual["year"]);
                break;
            case 'm':
                $new_timestamp = @mktime(0,0,0,$date_actual["mon"]+$length,$date_actual["mday"],$date_actual["year"]);
                break;
            case 'y':
                $new_timestamp = @mktime(0,0,0,$date_actual["mon"],$date_actual["mday"],$date_actual["year"]+$length);
                break;
            default:
                break;
        }
        return @strftime('%m/%d/%Y (%A)',$new_timestamp); //@date('m/d/Y (D)',$new_timestamp);
    }else{
        return '';
    }
}
function tour_code_decode($string) {
    $n = -(int)Yii::t('main',TEXT_TOUR_CODE_ENCODE_ROTATE_VALUE);
    $length = strlen($string);
    $result = '';

    for($i = 0; $i < $length; $i++) {
        $ascii = ord($string{$i});
        $rotated = $ascii;
        if ($ascii > 64 && $ascii < 91) {
            $rotated += $n;
            $rotated > 90 && $rotated += -90 + 64;
            $rotated < 65 && $rotated += -64 + 90;
        } elseif ($ascii > 96 && $ascii < 123) {
            $rotated += $n;
            $rotated > 122 && $rotated += -122 + 96;
            $rotated < 97 && $rotated += -96 + 122;
        }
        $result .= chr($rotated);
    }
    return $result;
}

function tep_db_input($string) {
    return addslashes($string);
}

function tep_create_random_value($length, $type = 'mixed') {
    if ( ($type != 'mixed') && ($type != 'chars') && ($type != 'digits')) return false;
    $rand_value = '';
    while (strlen($rand_value) < $length) {
        if ($type == 'digits') {
            $char = tep_rand(0,9);
        }
        else {
            $char = chr(tep_rand(0,255));
        }
        if ($type == 'mixed') {
            if (preg_match('/^[a-z0-9]$/i', $char)) $rand_value .= $char;
        }
        elseif ($type == 'chars') {
            if (preg_match('/^[a-z]$/i', $char)) $rand_value .= $char;
        }
        elseif ($type == 'digits') {
            if (preg_match('/^[0-9]$/', $char)) $rand_value .= $char;
        }
    }
    return $rand_value;
}

// Return a random value
function tep_rand($min = null, $max = null) {
    static $seeded;
    if (!isset($seeded)) {
        mt_srand((double)microtime()*1000000);
        $seeded = true;
    }
    if (isset($min) && isset($max)) {
        if ($min >= $max) {
            return $min;
        }
        else {
            return mt_rand($min, $max);
        }
    }
    else {
        return mt_rand();
    }
}

function tep_get_ip_address() {
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } else {
            $ip = getenv('REMOTE_ADDR');
        }
    }
    return $ip;
}

/**
 * Check is image
 *
 * @param string $ext
 * @return boolean
 */
function tep_is_image($ext){
    $types = array('gif','jpg','jpeg','png','bmp');//image type
    if(in_array(strtolower($ext), $types)){
        return true;
    }else{
        return false;
    }
}

function tep_change_brand_name($brandname){
    if(strtolower($brandname) == 'monograms'){
        $brandname = Yii::t('main',TXT_FUN_GLOBUS_NAME);
    }else if(strtolower($brandname) == 'avalon'){
        $brandname = Yii::t('main',TXT_FUN_AVALON_NAME);
    }else{
        $brandname = $brandname;
    }
    return $brandname;
}

/**
 * check category id
 *
 * Developed BY Hardik Chavda
 * Date : 2012-06-22
 */
function weather_display($place,$lang='en',$title){
    $placename = $place;
    $place=urlencode($placename);
    $place = utf8_encode($place);
    $day = array('Sun'=>'Sunday','Mon'=>'Monday','Tue'=>'Tuesday','Wed'=>'Wednesday',
    'Thu'=>'Thursday','Fri'=>'Friday','Sat'=>'Saturday');
    $url = 'http://www.google.com/ig/api?weather='.$place.',$&hl='.$lang.'';
    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 0);
    $raw_data = curl_exec ($ch);
    curl_close ($ch);
    $xml = @simplexml_load_string($raw_data);
    $condition = $xml->weather->current_conditions->condition['data'];
    $temp_c = $xml->weather->current_conditions->temp_c['data'];
    $humidity = $xml->weather->current_conditions->humidity['data'];
    $icon = $xml->weather->current_conditions->icon['data'];
    $err_set = 0;

    $weather_box .="<div class='blue_box_mn'>";
    $weather_box .="<div class='blue_box_heading'>";
    $weather_box .="<div class='blue_box_heading_crv_left'></div>";
    $weather_box .="<div class='blue_box_heading_bg3'><h2 class='sm_12'>".$title."</h2></div>";
    $weather_box .="<div class='blue_box_heading_crv_right'></div>";
    $weather_box .="</div>";
    $weather_box .="<div class='wether_box_bg'>";

    for ($i = 0; $i < 3; $i++){
        $data = $xml->weather->forecast_conditions[$i];
        $condition = $data->condition['data'];
        $day_of_week = utf8_decode($day_of_week);
        $img = 'http://img0.gmodules.com/' . str_replace('/ig/','',$data->icon['data']);
        if($data->icon['data'] == '' && $day_of_week == '') {$err_set=1;}

        $weather_box .="<div class='wether_box_con'>";
        $n = $data->day_of_week['data'];
        $weather_box .='<p>'.$day["$n"]."'s Forecast</p>";
        $weather_box .='<img src='.$img.' '. ' class="fleft"/>';
        $weather_box .="<span>".$low = $data->low['data'].'&#176;F | '.$high = round((5/9)*($data->low['data']-32),0).'&#176;C'."</span>";//(5/9)*(Tf-32)

        $weather_box .="</div>";
    }
    $weather_box .="</div>";
    $weather_box .="<div class='wether_box_bot_crv'></div>";
    $weather_box .="</div>";
    if($err_set!=1){ return $weather_box; }else{return false;}
}

function tep_get_total_fares_includes_agency($roomtotal, $persiontage=0){
    $roomtotal = $roomtotal + ($roomtotal*($persiontage/100));
    return $roomtotal;
}

// Return a product ID with attributes
function tep_get_uprid($prid, $params) {
    $uprid = $prid;
    if ( (is_array($params)) && (!strstr($prid, '{')) ) {
        while (list($option, $value) = each($params)) {
            if(!is_array($value)) $value=array($value);
            foreach($value as $v) {
                $uprid .= '{' . $option . '}' . $v;
            }
        }
    }
    return $uprid;
}//End

function get_rooms($data){
    $result = array();
    $array = explode("###",$data);
    for($i=1;$i<count($array)-1;$i++){
        $result[] = explode("!!",$array[$i]);
    }
    if(count($result)==0){
        $result[] = explode("!!",$array[1]);
    }
    return $result;
}

/**
 * Dealing with special symbols
 *
 * @param string $html
 * @return string
 * @author yichi.sun@toursforfun.com
 */
function to_text($html){
    $search = array("<br>","&nbsp;");
    $replace = array("/n"," ");
    return str_replace($search,$replace,$html);
}


/**
  * for ajax pagination function with url link update
  */
  function paginator($base_path, $cur_page, $total_items, $per_page=10, $footer_bar=true, $name=false) {
           $remainder = $total_items % $per_page;
           $total_pages = ($total_items - $remainder)/$per_page;
          if($remainder > 0) $total_pages += 1;

           $start_num = 1 + ($cur_page-1)*$per_page;
           $end_num = $start_num + $per_page - 1;
          if($end_num > $total_items) $end_num = $total_items;

          $next_page = $cur_page + 1;
          $prev_page = $cur_page - 1;

          $start_page = $cur_page - 2;
          $end_page = $cur_page + 2;

          if($end_page > $total_pages) {
            $end_page = $total_pages;
            $start_page = $total_pages - 4;
          }

          if($start_page < 1) {
            $start_page = 1;
            if ($total_pages >= 5) $end_page = 5;
            else $end_page = $total_pages;
          }

          $class = $footer_bar ? "footer_bar" : "summary_bar";

          $ret  = '<div class="paging_mn">';
          if(!$footer_bar) $ret .= '<div class="summary">Displaying ' . $name . ' ' . $start_num . ' - ' . $end_num . ' of ' . $total_items . '.</div>';

          if($total_pages > 1) {
            $ret .= '<ul id="ajaxpagination1">';
            if($cur_page != 1) $ret .= '<li id="'.$prev_page.'"><b><a href="javascript:void(0);" onclick="set_value_to_hidden_vars(\'page\','.$prev_page.',\'page\',\'1\')">'.Yii::t('main',PREVNEXT_BUTTON_PREV).'</a></b></li>';
            for($i=$start_page;$i<=$end_page;$i++) {
              $ret .= '<li';
              if($i == $cur_page) { $ret .= ' class="cur_page"';
                 $ret .= ' id="' . $i . '">';
                 $ret .= '<span>'.$i . '</span></li>';
              } else {
                 $ret .= ' id="' . $i . '"><a href="javascript:void(0);" onclick="set_value_to_hidden_vars(\'page\','.$i.',\'page\',\'1\')">';
                 $ret .= '<span>'.$i . '</span></a></li>';
              }
        }
            if ($cur_page != $total_pages) $ret .= '<li id="' . $next_page . '"><b><a href="javascript:void(0);" onclick="set_value_to_hidden_vars(\'page\','.$next_page.',\'page\',\'1\')">'.Yii::t('main',PREVNEXT_BUTTON_NEXT).'</a></b></li>';
            $ret .= '</ul>';
          }

          $ret .= '</div>';

          return $ret;
     }

/**
 * @todo build pagination information, this function is origin from paginator but modified
 */
function getPagination($cur_page, $total_items, $per_page=10, $footer_bar=true, $name=false, $categoryPage=false) {
    $url = secure_string($_SERVER['REQUEST_URI']);
    if($categoryPage == true){
        if(strpos($url, '?') > 0){
            $urlArr = explode('?',$url);
            $url = $urlArr[0]; 
        }
        if(preg_match('/-p-/',$url)){
            $url = str_replace('-p-'.$cur_page,'',$url);
            if(substr($url,-1) == '/'){
                $url = substr(str_replace('-p-'.$cur_page,'',$url),0,-1);
            }
            $url .= '-p-{page}/';
        }else{
            $url = substr($url, 0, -1);
            $url .= '-p-{page}/';
        }
    }else{
        if(strpos($url, '?') > 0){
            if(preg_match('/(\?|&)page=\d+/',$url)){
                $url = preg_replace('/(\?|&)page=\d+/',"\\1page={page}",$url);
            }else{
                $url .= '&page={page}';
            }
        }else{
            $url .= '?page={page}';
        }
    }
    $remainder = $total_items % $per_page;
    $total_pages = ($total_items - $remainder)/$per_page;
    if($remainder > 0) $total_pages += 1;

    $start_num = 1 + ($cur_page-1)*$per_page;
    $end_num = $start_num + $per_page - 1;
    if($end_num > $total_items) $end_num = $total_items;

    $next_page = $cur_page + 1;
    $prev_page = $cur_page - 1;

    $start_page = $cur_page - 2;
    $end_page = $cur_page + 2;

    if($end_page > $total_pages) {
        $end_page = $total_pages;
        $start_page = $total_pages - 4;
    }

    if($start_page < 1) {
        $start_page = 1;
        if ($total_pages >= 5) $end_page = 5;
        else $end_page = $total_pages;
    }

    $class = $footer_bar ? "footer_bar" : "summary_bar";

    $ret  = '<div class="paging_mn">';
    if(!$footer_bar) $ret .= '<div class="summary">Displaying ' . $name . ' ' . $start_num . ' - ' . $end_num . ' of ' . $total_items . '.</div>';

    if($total_pages > 1) {
        $ret .= '<ul class="pagination">';
        if($cur_page != 1) $ret .= '<li id="'.$prev_page.'"><b><a href="'.preg_replace('/\{page\}/', $prev_page, $url).'" class="page_link" value="'.$prev_page.'">'.Yii::t('main',PREVNEXT_BUTTON_PREV).'</a></b></li>';
        for($i=$start_page;$i<=$end_page;$i++) {
            $ret .= '<li';
            if($i == $cur_page) {
                $ret .= ' class="cur_page"><span>'.$i . '</span></li>';
            } else {
                $ret .= '><a href="'.preg_replace('/\{page\}/', $i, $url).'" class="page_link" value="'.$i.'">';
                $ret .= '<span>'.$i . '</span></a></li>';
            }
        }
        if ($cur_page != $total_pages) $ret .= '<li><b><a href="'.preg_replace('/\{page\}/', $next_page, $url).'" class="page_link" value="' . $next_page . '">'.Yii::t('main',PREVNEXT_BUTTON_NEXT).'</a></b></li>';
        $ret .= '</ul>';
    }

    $ret .= '</div>';

    return $ret;
}


  function dropDownMenuArray($current_cat_id,$local_id=1){
                if($local_id == 1){
                $main_dropdown_menu_array[536] = array();
                $main_dropdown_menu_array[536][] = 'National Parks';
                $main_dropdown_menu_array[536][] = array('id'=>'537', 'text'=>'Grand Canyon');
                $main_dropdown_menu_array[536][] = array('id'=>'37', 'text'=>'Mount Rushmore');
                $main_dropdown_menu_array[536][] = array('id'=>'35', 'text'=>'Yellowstone');
                $main_dropdown_menu_array[536][] = array('id'=>'48', 'text'=>'Yosemite');

                $main_dropdown_menu_array[25] = array();
                $main_dropdown_menu_array[25][] = 'US East Coast';
                $main_dropdown_menu_array[25][] = array('id'=>'71', 'text'=>'American East Coast Multi-City Travel Packages');
                $main_dropdown_menu_array[25][] = array('id'=>'59', 'text'=>'Boston City Tours');
                $main_dropdown_menu_array[25][] = array('id'=>'68', 'text'=>'Martha\'s Vineyard Tours');
                $main_dropdown_menu_array[25][] = array('id'=>'55', 'text'=>'New York Tours');
                $main_dropdown_menu_array[25][] = array('id'=>'57', 'text'=>'Niagara Falls Tours (US Side)');
                $main_dropdown_menu_array[25][] = array('id'=>'56', 'text'=>'Philadelphia Tours');
                $main_dropdown_menu_array[25][] = array('id'=>'61', 'text'=>'Rhode Island Tours');
                $main_dropdown_menu_array[25][] = array('id'=>'52', 'text'=>'Washington D.C. Tours');
                $main_dropdown_menu_array[25][] = array('id'=>'25', 'text'=>'<b>More US East Coast Tours</b>');

                $main_dropdown_menu_array[34] = array();
                $main_dropdown_menu_array[34][] = 'US Florida';
                $main_dropdown_menu_array[34][] = array('id'=>'104', 'text'=>'Florida Theme Park Travel Packages');
                $main_dropdown_menu_array[34][] = array('id'=>'153', 'text'=>'Miami Tours');
                $main_dropdown_menu_array[34][] = array('id'=>'152', 'text'=>'Orlando Tours'); //&mnu=day-trips

                $main_dropdown_menu_array[24] = array();
                $main_dropdown_menu_array[24][] = 'US West Coast';
                $main_dropdown_menu_array[24][] = array('id'=>'51', 'text'=>'American West Coast Multi-City Travel Packages');
                $main_dropdown_menu_array[24][] = array('id'=>'119', 'text'=>'17-Mile Drive Tours');
                $main_dropdown_menu_array[24][] = array('id'=>'107', 'text'=>'Disneyland/California Adventure Tours');
                $main_dropdown_menu_array[24][] = array('id'=>'521', 'text'=>'Grand Canyon Airplane/Helicopter Tours');
                $main_dropdown_menu_array[24][] = array('id'=>'31', 'text'=>'Grand Canyon Bus Tours');
                $main_dropdown_menu_array[24][] = array('id'=>'138&mnu=day-trips', 'text'=>'Grand Canyon West Rim, Skywalk Tours');
                $main_dropdown_menu_array[24][] = array('id'=>'29', 'text'=>'Los Angeles Tours');
                $main_dropdown_menu_array[24][] = array('id'=>'32', 'text'=>'Las Vegas Tours');
                $main_dropdown_menu_array[24][] = array('id'=>'37', 'text'=>'Mount Rushmore National Park Tours');
                $main_dropdown_menu_array[24][] = array('id'=>'38', 'text'=>'Napa Valley Tours');
                $main_dropdown_menu_array[24][] = array('id'=>'30', 'text'=>'San Francisco Tours');
                $main_dropdown_menu_array[24][] = array('id'=>'45&mnu=day-trips', 'text'=>'San Diego Tours');
                $main_dropdown_menu_array[24][] = array('id'=>'487', 'text'=>'Seattle');
                $main_dropdown_menu_array[24][] = array('id'=>'110&mnu=day-trips', 'text'=>'Sea World (San Diego) Tours');
                $main_dropdown_menu_array[24][] = array('id'=>'108&mnu=day-trips', 'text'=>'Universal Studios Tours');
                $main_dropdown_menu_array[24][] = array('id'=>'35', 'text'=>'Yellowstone National Park Tours');
                $main_dropdown_menu_array[24][] = array('id'=>'48', 'text'=>'Yosemite National Park Tours');
                $main_dropdown_menu_array[24][] = array('id'=>'24', 'text'=>'<b>More US West Coast Tours</b>');

                $main_dropdown_menu_array[33] = array();
                $main_dropdown_menu_array[33][] = 'US Hawaii';
                $main_dropdown_menu_array[33][] = array('id'=>'77', 'text'=>'Hawaii Multi-Day Travel Packages');
                $main_dropdown_menu_array[33][] = array('id'=>'535', 'text'=>'Helicopter Tours');
                $main_dropdown_menu_array[33][] = array('id'=>'85', 'text'=>'Island of Oahu Tours');
                $main_dropdown_menu_array[33][] = array('id'=>'82', 'text'=>'Island of Maui Tours');
                $main_dropdown_menu_array[33][] = array('id'=>'83', 'text'=>'Island of Hawaii (The Big Island) Tours');

                $main_dropdown_menu_array[54] = array();
                $main_dropdown_menu_array[54][] = 'Canada';
                $main_dropdown_menu_array[54][] = array('id'=>'389', 'text'=>'Canada Multi-City Travel Packages');
                $main_dropdown_menu_array[54][] = array('id'=>'386', 'text'=>'Montreal Tours');
                $main_dropdown_menu_array[54][] = array('id'=>'381', 'text'=>'Niagara Falls (Canadian Side) Tours');
                $main_dropdown_menu_array[54][] = array('id'=>'66', 'text'=>'Ottawa Tours');
                $main_dropdown_menu_array[54][] = array('id'=>'67', 'text'=>'Quebec Tours');
                $main_dropdown_menu_array[54][] = array('id'=>'390', 'text'=>'Rocky Mountain Tours');
                $main_dropdown_menu_array[54][] = array('id'=>'65', 'text'=>'Toronto Tours');
                $main_dropdown_menu_array[54][] = array('id'=>'385', 'text'=>'Vancouver Tours');

                $main_dropdown_menu_array[337] = array();
                $main_dropdown_menu_array[337][] = 'Central &amp; Eastern Europe';
                $main_dropdown_menu_array[337][] = array('id'=>'339', 'text'=>'Austria Tours');
                $main_dropdown_menu_array[337][] = array('id'=>'346', 'text'=>'Czech Republic &amp; Croatia Tours');
                $main_dropdown_menu_array[337][] = array('id'=>'338', 'text'=>'Germany Tours');
                $main_dropdown_menu_array[337][] = array('id'=>'347', 'text'=>'Hungary Tours');
                $main_dropdown_menu_array[337][] = array('id'=>'343', 'text'=>'Poland Tours');
                $main_dropdown_menu_array[337][] = array('id'=>'340', 'text'=>'Switzerland Tours');

                $main_dropdown_menu_array[355] = array();
                $main_dropdown_menu_array[355][] = 'Northern Europe';
                $main_dropdown_menu_array[355][] = array('id'=>'358', 'text'=>'Iceland Tours');
                $main_dropdown_menu_array[355][] = array('id'=>'356', 'text'=>'Russia Tours');
                $main_dropdown_menu_array[355][] = array('id'=>'357', 'text'=>'Scandinavia Tours');

                $main_dropdown_menu_array[349] = array();
                $main_dropdown_menu_array[349][] = 'Southern Europe';
                $main_dropdown_menu_array[349][] = array('id'=>'354', 'text'=>'Greece &amp; Turkey Tours');
                $main_dropdown_menu_array[349][] = array('id'=>'353', 'text'=>'Morocco Tours');
                $main_dropdown_menu_array[349][] = array('id'=>'352', 'text'=>'Portugal Tours');
                $main_dropdown_menu_array[349][] = array('id'=>'351', 'text'=>'Spain Tours');

                $main_dropdown_menu_array[333] = array();
                $main_dropdown_menu_array[333][] = 'Western Europe';
                $main_dropdown_menu_array[333][] = array('id'=>'336', 'text'=>'Belgium Tours');
                $main_dropdown_menu_array[333][] = array('id'=>'334', 'text'=>'France Tours');
                $main_dropdown_menu_array[333][] = array('id'=>'335', 'text'=>'Holland Tours');
                }else{
                    $main_dropdown_menu_array[24] = array();
                    $main_dropdown_menu_array[24][] = Yii::t('index',HEADER_LINKS_US_WEST_COAST);
                    $main_dropdown_menu_array[24][] = array('id'=>'51', 'text'=>Yii::t('main',HEADER_LINKS_AMERICAN_WEST_COAST_MULTI_CITY));
                    $main_dropdown_menu_array[24][] = array('id'=>'119', 'text'=> Yii::t('main',HEADER_LINKS_17_MILE_DRIVE_TOURS));
                    $main_dropdown_menu_array[24][] = array('id'=>'107&mnu=day-trips', 'text'=> Yii::t('main',HEADER_LINKS_DISNEYLAND_ADVENTURE_TOURS));
                    $main_dropdown_menu_array[24][] = array('id'=>'521', 'text'=> Yii::t('main',HEADER_LINKS_GRAND_CANYON));
                    $main_dropdown_menu_array[24][] = array('id'=>'31', 'text'=>Yii::t('main',HEADER_LINKS_GRAND_CANYON_BUS_TOURS));
                    $main_dropdown_menu_array[24][] = array('id'=>'138&mnu=day-trips', 'text'=> Yii::t('main',HEADER_LINKS_GRAND_CANYON_WEST_RIM));
                    $main_dropdown_menu_array[24][] = array('id'=>'29', 'text'=> Yii::t('main',HEADER_LINKS_LOS_ANGELES_TOURS));
                    $main_dropdown_menu_array[24][] = array('id'=>'32', 'text'=> Yii::t('main',HEADER_LINKS_LAS_VEGAS_TOURS));
                    $main_dropdown_menu_array[24][] = array('id'=>'37', 'text'=> Yii::t('main',HEADER_LINKS_MT_RUSHMORE_NATIONAL_PARK_TOURS));
                    $main_dropdown_menu_array[24][] = array('id'=>'38', 'text'=> Yii::t('main',HEADER_LINKS_NAPA_VALLEY_TOURS));
                    $main_dropdown_menu_array[24][] = array('id'=>'30', 'text'=> Yii::t('main',HEADER_LINKS_SAN_FRANCISCO_TOURS));
                    $main_dropdown_menu_array[24][] = array('id'=>'45&mnu=day-trips', 'text'=> Yii::t('main',HEADER_LINKS_SAN_DIEGO_TOURS));
                    $main_dropdown_menu_array[24][] = array('id'=>'487', 'text'=> Yii::t('main',HEADER_LINKS_SEATTLE));
                    $main_dropdown_menu_array[24][] = array('id'=>'110&mnu=day-trips', 'text'=> Yii::t('main',HEADER_LINKS_SEA_WORLD_TOURS));
                    $main_dropdown_menu_array[24][] = array('id'=>'108&mnu=day-trips', 'text'=> Yii::t('main',HEADER_LINKS_UNIVERSAL_STUDIOS_TOURS));
                    $main_dropdown_menu_array[24][] = array('id'=>'35', 'text'=> Yii::t('main',HEADER_LINKS_YELLOWSTONE_NATIONAL_PARK_TOURS));
                    $main_dropdown_menu_array[24][] = array('id'=>'48', 'text'=> Yii::t('main',HEADER_LINKS_YOSEMITE_NATIONAL_PARK_TOURS));

                    $main_dropdown_menu_array[25] = array();
                    $main_dropdown_menu_array[25][] = Yii::t('index',HEADER_LINKS_US_EAST_COAST);
                    $main_dropdown_menu_array[25][] = array('id'=>'71', 'text'=>Yii::t('main',HEADER_LINKS_AMERICAN_EAST_COAST));
                    $main_dropdown_menu_array[25][] = array('id'=>'59', 'text'=>Yii::t('main',HEADER_LINKS_BOSTON_TOURS));
                    $main_dropdown_menu_array[25][] = array('id'=>'68', 'text'=>Yii::t('main',HEADER_LINKS_MARTHAS_VINEYARD_TOURS));
                    $main_dropdown_menu_array[25][] = array('id'=>'55', 'text'=>Yii::t('main',HEADER_LINKS_NEW_YORK));
                    $main_dropdown_menu_array[25][] = array('id'=>'57', 'text'=>Yii::t('main',HEADER_LINKS_NIAGARA_FALLS));
                    $main_dropdown_menu_array[25][] = array('id'=>'56', 'text'=>Yii::t('main',HEADER_LINKS_PHILADELPHIA_TOURS));
                    $main_dropdown_menu_array[25][] = array('id'=>'61', 'text'=>Yii::t('main',HEADER_LINKS_RHODE_ISLAND_TOURS));
                    $main_dropdown_menu_array[25][] = array('id'=>'52', 'text'=>Yii::t('main',HEADER_LINKS_WASHINGTON_D_C));

                    $main_dropdown_menu_array[54] = array();
                    $main_dropdown_menu_array[54][] = array('id'=>'389', 'text'=>Yii::t('main',HEADER_LINKS_CANADA_MULTI_CITY_TRAVEL_PACKAGES));
                    $main_dropdown_menu_array[54][] = array('id'=>'381', 'text'=>Yii::t('main',HEADER_LINKS_NIAGARA_FALLS_TOURS));
                    $main_dropdown_menu_array[54][] = array('id'=>'67', 'text'=>Yii::t('main',HEADER_LINKS_QUEBEC_TOURS));
                    $main_dropdown_menu_array[54][] = array('id'=>'390', 'text'=>Yii::t('main',HEADER_LINKS_ROCKY_MOUNTAIN_TOURS));
                    $main_dropdown_menu_array[54][] = array('id'=>'65', 'text'=>Yii::t('main',HEADER_LINKS_TORONTO_TOURS));
                    $main_dropdown_menu_array[54][] = array('id'=>'385', 'text'=>Yii::t('main',HEADER_LINKS_VANCOUVER_TOURS));

                    $main_dropdown_menu_array[34] = array();
                    $main_dropdown_menu_array[34][] = Yii::t('index',HEADER_LINKS_US_EAST_COAST);
                    $main_dropdown_menu_array[34][] = array('id'=>'104', 'text'=>Yii::t('main',HEADER_LINKS_FLORIDA_THEME_PARK));
                    $main_dropdown_menu_array[34][] = array('id'=>'153', 'text'=>Yii::t('main',HEADER_LINKS_MIAMI_TOURS));
                    $main_dropdown_menu_array[34][] = array('id'=>'152', 'text'=>Yii::t('main',HEADER_LINKS_ORLANDO_TOURS)); //&mnu=day-trips

                    $main_dropdown_menu_array[33] = array();
                    $main_dropdown_menu_array[33][] = Yii::t('index',HEADER_LINKS_US_EAST_COAST);
                    $main_dropdown_menu_array[33][] = array('id'=>'77', 'text'=>Yii::t('main',HEADER_LINKS_HAWAII_MULTI_DAY));
                    $main_dropdown_menu_array[33][] = array('id'=>'535', 'text'=>Yii::t('main',HEADER_LINKS_HELICOPTER_TOURS));
                    $main_dropdown_menu_array[33][] = array('id'=>'85', 'text'=>Yii::t('main',HEADER_LINKS_ISLAND_OF_OAHU_TOURS));
                    $main_dropdown_menu_array[33][] = array('id'=>'82', 'text'=>Yii::t('main',HEADER_LINKS_ISLAND_OF_MAUI_TOURS));
                    $main_dropdown_menu_array[33][] = array('id'=>'83', 'text'=>Yii::t('main',HEADER_LINKS_ISLAND_OF_HAWAII_TOURS));
                }
                if(isset($current_cat_id)){
                    return $main_dropdown_menu_array[$current_cat_id];
                }
            }
    // Return a product ID from a product ID with attributes
  function tep_get_prid($uprid) {
        $pieces = explode('{', $uprid);
        return (int)$pieces[0];
  }//End

  function dCookie($ck_Var,$ck_Value = NULL,$ck_Time = "F",$ckpath='/',$ckdomain='',$httponly = true){
    $timestamp = time();
    //GET
    if($ck_Value === NULL){//=== eq,can not ==
        $ck_Value=isset($_COOKIE[$ck_Var])?$_COOKIE[$ck_Var]:'';
        return $ck_Value;
    }else{
    //SET
        //ckpath & ckdomain
        $islocalhost = ($_SERVER['HTTP_HOST']=='localhost' || preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}(\:[0-9]{2,})?$/',$_SERVER['HTTP_HOST'])) ? 1 : 0;
        if ($islocalhost) {
            $ckpath = '/'; $ckdomain = '';
        } else {
            if (!$ckdomain) {
                $pre_host = strtolower(substr($_SERVER['HTTP_HOST'],0,strpos($_SERVER['HTTP_HOST'],'.'))+1);
                $ckdomain = substr($_SERVER['HTTP_HOST'],strpos($_SERVER['HTTP_HOST'],'.')+1);
                $ckdomain = '.'.((strpos($ckdomain,'.')===false) ? $_SERVER['HTTP_HOST'] : $ckdomain);
                if (strpos($B_url,$pre_host)!==false) {
                    $ckdomain = $pre_host.$ckdomain;
                }
            }
        }
        //cktime
        if(!is_numeric($ck_Time)){
            $ck_Time = (trim($ck_Value)!='')?0:$timestamp-31536000;
        }else{
            $ck_Time = ($ck_Time==0)?0:$timestamp+$ck_Time;
        }
        //https
        $ishttps=false;
        $https = array();
        isset($_SERVER['REQUEST_URI']) && $https = @parse_url(secure_string($_SERVER['REQUEST_URI']));
        if (empty($https['scheme'])) {
            if (!empty($_SERVER['HTTP_SCHEME'])) {
                $https['scheme'] = $_SERVER['HTTP_SCHEME'];
            } else {
                $under_ssl = (isset($_SERVER['HTTPS']) && !strcasecmp($_SERVER['HTTPS'],'on')) ||
                             (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https');

                $https['scheme'] = $under_ssl ? 'https' : 'http';
            }
        }
        if ($https['scheme'] == 'https'){
            $ishttps=true;
        }
        //save
        if (version_compare(PHP_VERSION, '5.2.0', '>=')) {
            return setcookie($ck_Var, $ck_Value, $ck_Time, $ckpath, $ckdomain, $ishttps, $httponly);
        } else {
            return setcookie($ck_Var, $ck_Value, $ck_Time, $ckpath.($httponly ? '; HttpOnly' : ''), $ckdomain, $ishttps);
        }
    }
}//End
    function convert_datetime($str) {
      list($date, $time) = explode(' ', $str);
      list($year, $month, $day) = explode('-', $date);
      list($hour, $minute, $second) = explode(':', $time);
      $timestamp = @mktime(0, 0, 0, $month, $day, $year);
      return @strftime("%A",$timestamp); //date("l",$timestamp);
    }//End

    function get_date_working_date($date='',$n=0){
        if($date=='') $date=date('Y-m-d H:i:s');
        $max_day_num = (int)$n;

        for($i=1; $i<($max_day_num+1); $i++){
            $date_loop = date("w", strtotime($date.'+'.$i.' day'));
            $date_day =  date("Y-m-d", strtotime($date.'+'.$i.' day'));

            if($date_loop=='0' || $date_loop=='6'){
                $max_day_num++;
            }elseif(in_array($date_day, $date_array)){
                $max_day_num++;
            }
        }

        $expired_date = date("Y-m-d H:i:s", strtotime($date.'+'.$max_day_num.' day'));
        return $expired_date;
    }//End
function list_all_dates_btwn_two_dates_in_array($strDateFrom,$strDateTo){
    // Y-m-d format parameter
    $aryRange=array();
    $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),substr($strDateFrom,8,2),substr($strDateFrom,0,4));
    $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),(substr($strDateTo,8,2) - 1),substr($strDateTo,0,4));
    if ($iDateTo>=$iDateFrom){
        array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
        while ($iDateFrom<$iDateTo){
            $iDateFrom+=86400; // add 24 hours
            array_push($aryRange,date('Y-m-d',$iDateFrom));
        }
    }
    return $aryRange;
}

function date_add_duration_days_depature($length, $date_passed) {
    $new_timestamp = -1;
    if ($date_passed != '') {
        $date_passed_array = explode('-', $date_passed);
        $date_actual["mon"] = $date_passed_array[1];
        $date_actual["mday"] = $date_passed_array[2];
        $date_actual["year"] = $date_passed_array[0];
        $new_timestamp = @mktime(0, 0, 0, $date_actual["mon"], $date_actual["mday"] + $length, $date_actual["year"]);

        return @strftime('%m/%d/%Y', $new_timestamp);
    } else {
        return '';
    }
}

function tep_get_repeat_rewards_balance_tier($bal){
    $customer_repeat_tier = 0;
    if($bal>=3000 && $bal<=4999){
        $customer_repeat_tier = 1;
    }else if($bal>=5000 && $bal<=8999){
        $customer_repeat_tier = 2;
    }else if($bal>=9000 && $bal<=17499){
        $customer_repeat_tier = 3;
    }else if($bal>=17500){
        $customer_repeat_tier = 4;
    }

    return $customer_repeat_tier;
}

function tep_regen_provider_key($val_old){
    if( $val_old == Yii::t('index',PRO_POLY_OAHU_DOLE_PLANTATION)){
        $val_old = 'Dole Pineapple Plantation';
    }
    if( $val_old == Yii::t('index',PRO_POLY_OAHU_ISALAND )){
        $val_old = 'Experience American history while also exploring the island of Oahu and all its grandeur';
    }
    if( $val_old == Yii::t('index',PRO_POLY_OAHU_PEARL_HARBOR )){
        $val_old = 'Stand in the shadow of heroes';
    }
    if( $val_old == Yii::t('index',PRO_POLY_MAUI_HALEAKALA )){
        $val_old = 'House of the Sun';
    }
    if( $val_old == Yii::t('index',PRO_POLY_MAUI_LAHAINA)){
        $val_old = 'Upcountry Maui Kula District';
    }
    if( $val_old == Yii::t('index',PRO_POLY_HAWAII_GRAND_CIRCLE)){
        $val_old = 'Marvel at the wonders of the Big Island in all their grandeur';
    }
    if( $val_old == Yii::t('index',PRO_POLY_HAWAII_GRAND_VOLCANO)){
        $val_old = 'a scene of wild beauty';
    }
    if( $val_old == Yii::t('index',PRO_POLY_KAUAI_WAIMEA)){
        $val_old = 'beautiful Waimea';
    }
    if( $val_old == Yii::t('index',PRO_POLY_KAUAI_HANALEI)){
        $val_old = 'adventurous movie locations in the world';
    }
    return $val_old;
}
function tep_parse_search_string($search_str = '', &$objects) {
    $search_str = trim(strtolower($search_str));

// Break up $search_str on whitespace; quoted string will be reconstructed later
    $pieces = preg_split('/[[:space:]]+/', $search_str);
    $objects = array();
    $tmpstring = '';
    $flag = '';

    for ($k=0; $k<count($pieces); $k++) {
    /*
      while (substr($pieces[$k], 0, 1) == '(') {
        $objects[] = '(';
        if (strlen($pieces[$k]) > 1) {
          $pieces[$k] = substr($pieces[$k], 1);
        } else {
          $pieces[$k] = '';
        }
      }
*/
      $post_objects = array();

/*
      while (substr($pieces[$k], -1) == ')')  {
        $post_objects[] = ')';
        if (strlen($pieces[$k]) > 1) {
          $pieces[$k] = substr($pieces[$k], 0, -1);
        } else {
          $pieces[$k] = '';
        }
      }
*/
// Check individual words

      if ( (substr($pieces[$k], -1) != '"') && (substr($pieces[$k], 0, 1) != '"') ) {
        $objects[] = trim($pieces[$k]);

        for ($j=0; $j<count($post_objects); $j++) {
          $objects[] = $post_objects[$j];
        }
      } else {
/* This means that the $piece is either the beginning or the end of a string.
   So, we'll slurp up the $pieces and stick them together until we get to the
   end of the string or run out of pieces.
*/

// Add this word to the $tmpstring, starting the $tmpstring
        $tmpstring = trim(preg_replace('/"/', ' ', $pieces[$k]));

// Check for one possible exception to the rule. That there is a single quoted word.
        if (substr($pieces[$k], -1 ) == '"') {
// Turn the flag off for future iterations
          $flag = 'off';

          $objects[] = trim($pieces[$k]);

          for ($j=0; $j<count($post_objects); $j++) {
            $objects[] = $post_objects[$j];
          }

          unset($tmpstring);

// Stop looking for the end of the string and move onto the next word.
          continue;
        }

// Otherwise, turn on the flag to indicate no quotes have been found attached to this word in the string.
        $flag = 'on';

// Move on to the next word
        $k++;

// Keep reading until the end of the string as long as the $flag is on

        while ( ($flag == 'on') && ($k < count($pieces)) ) {
          while (substr($pieces[$k], -1) == ')') {
            $post_objects[] = ')';
            if (strlen($pieces[$k]) > 1) {
              $pieces[$k] = substr($pieces[$k], 0, -1);
            } else {
              $pieces[$k] = '';
            }
          }

// If the word doesn't end in double quotes, append it to the $tmpstring.
          if (substr($pieces[$k], -1) != '"') {
// Tack this word onto the current string entity
            $tmpstring .= ' ' . $pieces[$k];

// Move on to the next word
            $k++;
            continue;
          } else {
/* If the $piece ends in double quotes, strip the double quotes, tack the
   $piece onto the tail of the string, push the $tmpstring onto the $haves,
   kill the $tmpstring, turn the $flag "off", and return.
*/
            $tmpstring .= ' ' . trim(preg_replace('/"/', ' ', $pieces[$k]));

// Push the $tmpstring onto the array of stuff to search for
            $objects[] = trim($tmpstring);

            for ($j=0; $j<count($post_objects); $j++) {
              $objects[] = $post_objects[$j];
            }

            unset($tmpstring);

// Turn off the flag to exit the loop
            $flag = 'off';
          }
        }
      }
    }

// add default logical operators if needed
    $temp = array();
    for($i=0; $i<(count($objects)-1); $i++) {
      $temp[] = $objects[$i];
      if ( ($objects[$i] != 'and') &&
           ($objects[$i] != 'or') &&
           ($objects[$i] != '(') &&
           ($objects[$i+1] != 'and') &&
           ($objects[$i+1] != 'or') &&
           ($objects[$i+1] != ')') ) {
        $temp[] = ADVANCED_SEARCH_DEFAULT_OPERATOR;
      }
    }
    $temp[] = $objects[$i];
    $objects = $temp;

    $keyword_count = 0;
    $operator_count = 0;
    $balance = 0;
    for($i=0; $i<count($objects); $i++) {
      if ($objects[$i] == '(') $balance --;
      if ($objects[$i] == ')') $balance ++;
      if ( ($objects[$i] == 'and') || ($objects[$i] == 'or') ) {
        $operator_count ++;
      } elseif ( ($objects[$i]) && ($objects[$i] != '(') && ($objects[$i] != ')') ) {
        $keyword_count ++;
      }
    }

    if ( ($operator_count < $keyword_count) && ($balance == 0) ) {
      return true;
    } else {
      return false;
    }
  }

function tep_small_get_keyword_lable($get_keyword_title){//Polynesian Adventure Tours-Dole Pineapple Plantation
    if( $get_keyword_title == 'Polynesian Adventure Tours-Experience American history while also exploring the island of Oahu and all its grandeur' ){
        $get_keyword_title = 'Polynesian Adventure Tours - Oahu Island Tour';
    }
    if( $get_keyword_title == 'Polynesian Adventure Tours-Marvel at the wonders of the Big Island in all their grandeur' ){
        $get_keyword_title = 'Polynesian Adventure Tours - Big Island Grand Circle Island';
    }
    if( $get_keyword_title == 'Polynesian Adventure Tours-Dole Pineapple Plantation' ){
        $get_keyword_title = ' Polynesian Adventure Tours - '.Yii::t('index',PRO_POLY_OAHU_DOLE_PLANTATION);
    }
    if( $get_keyword_title == 'Polynesian Adventure Tours-adventurous movie locations in the world' ){
        $get_keyword_title = ' Polynesian Adventure Tours - Hanalei';
    }
    if( $get_keyword_title == 'Polynesian Adventure Tours-Stand in the shadow of heroes' ){
        $get_keyword_title = ' Polynesian Adventure Tours - '.Yii::t('index',PRO_POLY_OAHU_PEARL_HARBOR);
    }
    if( $get_keyword_title == 'Polynesian Adventure Tours-Upcountry Maui Kula District' ){
        $get_keyword_title = ' Polynesian Adventure Tours - '.Yii::t('index',PRO_POLY_MAUI_LAHAINA);
    }
    if( $get_keyword_title == 'Polynesian Adventure Tours-House of the Sun' ){
        $get_keyword_title = ' Polynesian Adventure Tours - '.Yii::t('index',PRO_POLY_MAUI_HALEAKALA);
    }
    if( $get_keyword_title == 'Polynesian Adventure Tours-a scene of wild beauty' ){
        $get_keyword_title = ' Polynesian Adventure Tours - '.Yii::t('index',PRO_POLY_HAWAII_GRAND_VOLCANO);
    }
    if( $get_keyword_title == 'Polynesian Adventure Tours-beautiful Waimea' ){
        $get_keyword_title = ' Polynesian Adventure Tours - '.Yii::t('index',PRO_POLY_KAUAI_WAIMEA);
    }
    if( $get_keyword_title == 'Polynesian Adventure Tours-adventurous movie locations in the world' ){
        $get_keyword_title = ' Polynesian Adventure Tours - '.Yii::t('index',PRO_POLY_KAUAI_HANALEI);
    }

    return $get_keyword_title;
}//End
    function tep_get_globus_discount_price($price, $discount=0){
        $discounted_price = $price - ($price * ($discount/100));
        return $discounted_price;
    }

//The string to extract the correct phone number, and return correct number
function check_phone_num(&$strTelephoneNumber) {

 $strTelephoneNumberCopy = str_replace(array('-', ' ', '+','(',')'), array('','','','',''), $strTelephoneNumber);



  // check string is not a empty
  if (empty($strTelephoneNumberCopy)) {
    return false;
  }

  //count valid phone number digit
  $strTelephoneNumberCount=substr($strTelephoneNumberCopy,-10);
  if(strlen($strTelephoneNumberCount)!=10){
    return false;
  }else{
        //Check US phone number
        $rgx = '/^'; //beginning of string
        $rgx .= '(?:\([2-9]\d{2}\)\ ?'; //either (200-999) with optional space
        $rgx .= '|'; //or
        $rgx .= '[2-9]\d{2}[- \.]?)'; //200-999 with optional seperator '- .'
        $rgx .= '[2-9]\d{2}'; //middle 4 digits 200-999
        $rgx .= '[- \.]?'; //optional seperator '- .'
        $rgx .= '\d{4}'; //last 4 digits 0000-9999
        $rgx .= '[- \.]?'; //optional seperator '- .'
        $rgx .= '(?:x|ext)?\.?\ ?\d{0,5}'; //optional extension
        $rgx .= '$/'; //end of string

        if (!preg_match($rgx, $strTelephoneNumberCount)){
            return false;
        }
        //check country code if exist in number
        if(strpos($strTelephoneNumberCopy,$strTelephoneNumberCount)>0){
            $country_code=substr($strTelephoneNumberCopy,0,strpos($strTelephoneNumberCopy,$strTelephoneNumberCount));
            //remove leading zero
            $country_code = preg_replace('/^0+(.)/', "$1", $country_code);
            $uscountrycode_array=array("1");
            if(!in_array($country_code,$uscountrycode_array)){
                return false;
            }else{
                 $strTelephoneNumberCopy='+1'.$strTelephoneNumberCount; //$country_code.$strTelephoneNumberCount;
            }
        }
  }

  // Seems to be valid - return the stripped telephone number
  $strTelephoneNumber = $strTelephoneNumberCopy;
  return true;
}
function tep_get_compareDates($date1,$date2) {
    $date1_array = explode("-",$date1);
    $date2_array = explode("-",$date2);
    $timestamp1 = mktime(0,0,0,$date1_array[1],$date1_array[2],$date1_array[0]);
    $timestamp2 = mktime(0,0,0,$date2_array[1],$date2_array[2],$date2_array[0]);
    if ($timestamp1>$timestamp2 || $timestamp1 == $timestamp2) {
        $ret_str = 'valid';
    } else {
        $ret_str = 'invalid';
    }
    return $ret_str;
}
function tep_get_cart_add_update_extra_values($fieldname, $fieldvalue, $extra_values_str=''){
    if(tep_not_null($extra_values_str)){
        $extra_values_array = explode("|##!##|", $extra_values_str);
        $total_ext_values = count($extra_values_array)-1;
        $new_extra_values_str = '';
        $update_count = 0;
        for($ext=0; $ext<$total_ext_values; $ext++){
            $get_field_info = explode("|#|", $extra_values_array[$ext]);
            if($get_field_info[0] == $fieldname){
                $get_field_info[1] = $fieldvalue;
                $update_count++;
            }
            if(tep_not_null($get_field_info[0]) && tep_not_null($get_field_info[1])){
                $new_extra_values_str .= $get_field_info[0] . '|#|' . $get_field_info[1] . '|##!##|';
            }
        }
        if($update_count==0){
            if(tep_not_null($fieldname) && tep_not_null($fieldvalue)){
                $new_extra_values_str .= $fieldname . '|#|' . $fieldvalue . '|##!##|';
            }
        }
    }
    else{
        $new_extra_values_str = $fieldname . '|#|' . $fieldvalue . '|##!##|';
    }
    return $new_extra_values_str;
}
// Check date
function tep_checkdate($date_to_check, $format_string, &$date_array) {
    $separator_idx = -1;
    $separators = array('-', ' ', '/', '.');
    $month_abbr = array('jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec');
    $no_of_days = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

    $format_string = strtolower($format_string);
    if (strlen($date_to_check) != strlen($format_string)) {
        return false;
    }

    $size = count($separators);
    for ($i=0; $i<$size; $i++) {
        $pos_separator = strpos($date_to_check, $separators[$i]);
        if ($pos_separator != false) {
            $date_separator_idx = $i;
            break;
        }
    }

    for ($i=0; $i<$size; $i++) {
        $pos_separator = strpos($format_string, $separators[$i]);
        if ($pos_separator != false) {
            $format_separator_idx = $i;
            break;
        }
    }

    if ($date_separator_idx != $format_separator_idx) {
        return false;
    }

    if ($date_separator_idx != -1) {
        $format_string_array = explode( $separators[$date_separator_idx], $format_string );
        if (count($format_string_array) != 3) {
            return false;
        }
        $date_to_check_array = explode( $separators[$date_separator_idx], $date_to_check );
        if (count($date_to_check_array) != 3) {
            return false;
        }

        $size = count($format_string_array);
        for ($i=0; $i<$size; $i++) {
            if ($format_string_array[$i] == 'mm' || $format_string_array[$i] == 'mmm') $month = $date_to_check_array[$i];
            if ($format_string_array[$i] == 'dd') $day = $date_to_check_array[$i];
            if (($format_string_array[$i] == 'yyyy') || ($format_string_array[$i] == 'aaaa')) $year = $date_to_check_array[$i];
        }
    }
    else {
        if (strlen($format_string) == 8 || strlen($format_string) == 9) {
            $pos_month = strpos($format_string, 'mmm');
            if ($pos_month != false) {
                $month = substr( $date_to_check, $pos_month, 3 );
                $size = count($month_abbr);
                for ($i=0; $i<$size; $i++) {
                    if ($month == $month_abbr[$i]) {
                        $month = $i;
                        break;
                    }
                }
            }
            else {
                $month = substr($date_to_check, strpos($format_string, 'mm'), 2);
            }
        }
        else {
            return false;
        }
        $day = substr($date_to_check, strpos($format_string, 'dd'), 2);
        $year = substr($date_to_check, strpos($format_string, 'yyyy'), 4);
    }

    if (strlen($year) != 4) {
        return false;
    }
    if (!settype($year, 'integer') || !settype($month, 'integer') || !settype($day, 'integer')) {
        return false;
    }
    if ($month > 12 || $month < 1) {
        return false;
    }
    if ($day < 1) {
        return false;
    }
    if (tep_is_leap_year($year)) {
        $no_of_days[1] = 29;
    }
    if ($day > $no_of_days[$month - 1]) {
        return false;
    }

    $date_array = array($year, $month, $day);
    return true;
}

// Check if year is a leap year
function tep_is_leap_year($year) {
    if ($year % 100 == 0) {
        if ($year % 400 == 0) return true;
    }
    else {
        if (($year % 4) == 0) return true;
    }
    return false;
}

function GetDateDifference($StartDateString=NULL, $EndDateString=NULL) {
    $ReturnArray = array();
    $SDSplit = explode('/',$StartDateString);
    $StartDate = mktime(0,0,0,$SDSplit[0],$SDSplit[1],$SDSplit[2]);
    $EDSplit = explode('/',$EndDateString);
    $EndDate = mktime(0,0,0,$EDSplit[0],$EDSplit[1],$EDSplit[2]);
    $DateDifference = $EndDate-$StartDate;

    $ReturnArray['YearsSince'] = $DateDifference/60/60/24/365;
    $ReturnArray['MonthsSince'] = $DateDifference/60/60/24/365*12;
    $ReturnArray['DaysSince'] = $DateDifference/60/60/24;
    $ReturnArray['HoursSince'] = $DateDifference/60/60;
    $ReturnArray['MinutesSince'] = $DateDifference/60;
    $ReturnArray['SecondsSince'] = $DateDifference;

    $y1 = date("Y", $StartDate);
    $m1 = date("m", $StartDate);
    $d1 = date("d", $StartDate);
    $y2 = date("Y", $EndDate);
    $m2 = date("m", $EndDate);
    $d2 = date("d", $EndDate);

    $diff = '';
    $diff2 = '';
    if (($EndDate - $StartDate)<=0) {
        // Start date is before or equal to end date!
        $diff = "0 days";
        $diff2 = "Days: 0";
    }
    else {
        $y = $y2 - $y1;
        $m = $m2 - $m1;
        $d = $d2 - $d1;
        $daysInMonth = date("t",$StartDate);
        if ($d<0) {$m--;$d=$daysInMonth+$d;}
        if ($m<0) {$y--;$m=12+$m;}
        $daysInMonth = date("t",$m2);

        // Nicestring ("1 year, 1 month, and 5 days")
        if ($y>0) $diff .= $y==1 ? "1" : "$y";
        if ($m>0) $diff .= $m==1? ".1" : ".$m";

        // Nicestring 2 ("Years: 1, Months: 1, Days: 1")
        if ($y>0) $diff2 .= $y==1 ? "Years: 1" : "Years: $y";
        if ($y>0 && $m>0) $diff2 .= ", ";
        if ($m>0) $diff2 .= $m==1? "Months: 1" : "Months: $m";
        if (($m>0||$y>0) && $d>0) $diff2 .= ", ";
        if ($d>0) $diff2 .= $d==1 ? "Days: 1" : "Days: $d";
    }
    $ReturnArray['NiceString'] = $diff;
    $ReturnArray['NiceString2'] = $diff2;
    // return $ReturnArray;
    return $ReturnArray['NiceString'];
}
function tep_globus_date_display($raw_date) {
    if ( ($raw_date == '0000-00-00 00:00:00') || empty($raw_date) ) return false;

    if ($raw_date != '') {
      return date('D, M d, Y', strtotime($raw_date));
    }
}
function tep_globus_safe_phone_number($phonestr){
    $phonestr = str_replace(' ','',$phonestr);
    $phonestr = str_replace('+','',$phonestr);
    $phonestr = str_replace('-','',$phonestr);
    $phonestr = str_replace('(','',$phonestr);
    $phonestr = str_replace(')','',$phonestr);
    //if(strlen($phonestr) > 10){
    $phonestr = substr($phonestr,-10);
    //}
    return $phonestr;
}
function get_total_room_from_str_globus($str){
     $get_room_no_array = explode('###',$str);
     return $get_room_no_array[0];
}
function tep_get_room_total_persion_from_str_globus($str,$room_no){
     $get_room_no_array = explode('###',$str);
     $adu_and_chile_val_array = explode('!!',$get_room_no_array[$room_no]);
     $total_ad_ch_val = $adu_and_chile_val_array[0] + $adu_and_chile_val_array[1];
     return $total_ad_ch_val;
}
function tep_get_room_total_persion_accommodationname_only_globus($str,$room_no){
 $get_room_no_array = explode('###',$str);
 $adu_and_chile_val_array = explode('!!',$get_room_no_array[$room_no]);
 $total_ad_ch_val = $adu_and_chile_val_array[0];
 $acc_name_str = '';
 if($total_ad_ch_val == 3){
    $acc_name_str = 'TRIPLE';
 }else if($total_ad_ch_val == 2){
    $acc_name_str = 'TWIN';
 }else if($total_ad_ch_val == 1){
    $acc_name_str = 'SINGLE';
 }

 return $acc_name_str;
}
function tep_get_room_adult_child_persion_on_room_str_globus($str,$room_no){
     $get_room_no_array = explode('###',$str);
     $adu_and_chile_val_array = explode('!!',$get_room_no_array[$room_no]);

     $total_ad_ch_val_array[0] = $adu_and_chile_val_array[0];

     if($adu_and_chile_val_array[1] != ''){

        $explode_child_total = explode("||child||",$adu_and_chile_val_array[1]);
        $total_ad_ch_val_array[0] = $total_ad_ch_val_array[0] - $explode_child_total[0];
     }
     $total_ad_ch_val_array[1] = $adu_and_chile_val_array[1];

     return $total_ad_ch_val_array;
}
// nl2br() prior PHP 4.2.0 did not convert linefeeds on all OSs (it only converted \n)
function tep_convert_linefeeds($from, $to, $string) {
    if ((PHP_VERSION < "4.0.5") && is_array($from)) {
      return preg_replace('/(' . implode('|', $from) . ')/', $to, $string);
    } else {
      return str_replace($from, $to, $string);
    }
}
function globus_scs_datediff($interval, $datefrom, $dateto, $using_timestamps = false) {
      /*
        $interval can be:
        yyyy - Number of full years
        q - Number of full quarters
        m - Number of full months
        y - Difference between day numbers
          (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
        d - Number of full days
        w - Number of full weekdays
        ww - Number of full weeks
        h - Number of full hours
        n - Number of full minutes
        s - Number of full seconds (default)
      */

      if (!$using_timestamps) {
        $datefrom = strtotime($datefrom, 0);
        $dateto = strtotime($dateto, 0);
      }
      $difference = $dateto - $datefrom; // Difference in seconds

      switch($interval) {

        case 'yyyy': // Number of full years

          $years_difference = floor($difference / 31536000);
          if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
            $years_difference--;
          }
          if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
            $years_difference++;
          }
          $datediff = $years_difference;
          break;

        case "q": // Number of full quarters

          $quarters_difference = floor($difference / 8035200);
          while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
            $months_difference++;
          }
          $quarters_difference--;
          $datediff = $quarters_difference;
          break;

        case "m": // Number of full months

          $months_difference = floor($difference / 2678400);
          while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
            $months_difference++;
          }
          $months_difference--;
          $datediff = $months_difference;
          break;

        case 'y': // Difference between day numbers

          $datediff = date("z", $dateto) - date("z", $datefrom);
          break;

        case "d": // Number of full days

          $datediff = floor($difference / 86400);
          break;

        case "w": // Number of full weekdays

          $days_difference = floor($difference / 86400);
          $weeks_difference = floor($days_difference / 7); // Complete weeks
          $first_day = date("w", $datefrom);
          $days_remainder = floor($days_difference % 7);
          $odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
          if ($odd_days > 7) { // Sunday
            $days_remainder--;
          }
          if ($odd_days > 6) { // Saturday
            $days_remainder--;
          }
          $datediff = ($weeks_difference * 5) + $days_remainder;
          break;

        case "ww": // Number of full weeks

          $datediff = floor($difference / 604800);
          break;

        case "h": // Number of full hours

          $datediff = floor($difference / 3600);
          break;

        case "n": // Number of full minutes

          $datediff = floor($difference / 60);
          break;

        default: // Number of full seconds (default)

          $datediff = $difference;
          break;
      }

      return $datediff;
}
function scs_datediff($interval, $datefrom, $dateto, $using_timestamps = false) {
  /*
    $interval can be:
    yyyy - Number of full years
    q - Number of full quarters
    m - Number of full months
    y - Difference between day numbers
      (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
    d - Number of full days
    w - Number of full weekdays
    ww - Number of full weeks
    h - Number of full hours
    n - Number of full minutes
    s - Number of full seconds (default)
  */

  if (!$using_timestamps) {
    $datefrom = strtotime($datefrom, 0);
    $dateto = strtotime($dateto, 0);
  }
  $difference = $dateto - $datefrom; // Difference in seconds

  switch($interval) {

    case 'yyyy': // Number of full years

      $years_difference = floor($difference / 31536000);
      if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
        $years_difference--;
      }
      if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
        $years_difference++;
      }
      $datediff = $years_difference;
      break;

    case "q": // Number of full quarters

      $quarters_difference = floor($difference / 8035200);
      while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
        $months_difference++;
      }
      $quarters_difference--;
      $datediff = $quarters_difference;
      break;

    case "m": // Number of full months

      $months_difference = floor($difference / 2678400);
      while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
        $months_difference++;
      }
      $months_difference--;
      $datediff = $months_difference;
      break;

    case 'y': // Difference between day numbers

      $datediff = date("z", $dateto) - date("z", $datefrom);
      break;

    case "d": // Number of full days

      $datediff = floor($difference / 86400);
      break;

    case "w": // Number of full weekdays

      $days_difference = floor($difference / 86400);
      $weeks_difference = floor($days_difference / 7); // Complete weeks
      $first_day = date("w", $datefrom);
      $days_remainder = floor($days_difference % 7);
      $odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
      if ($odd_days > 7) { // Sunday
        $days_remainder--;
      }
      if ($odd_days > 6) { // Saturday
        $days_remainder--;
      }
      $datediff = ($weeks_difference * 5) + $days_remainder;
      break;

    case "ww": // Number of full weeks

      $datediff = floor($difference / 604800);
      break;

    case "h": // Number of full hours

      $datediff = floor($difference / 3600);
      break;

    case "n": // Number of full minutes

      $datediff = floor($difference / 60);
      break;

    default: // Number of full seconds (default)

      $datediff = $difference;
      break;
  }

  return $datediff;

}
function min2hoursmin($mins) {
        if ($mins < 0) {
            $min = Abs($mins);
        } else {
            $min = $mins;
        }
        $H = Floor($min / 60);
        $M = ($min - ($H * 60)) / 100;
        $hours = $H +  $M;
        if ($mins < 0) {
            $hours = $hours * (-1);
        }
        $expl = explode(".", $hours);
        $H = $expl[0];
        if (empty($expl[1])) {
            $expl[1] = 00;
        }
        $M = $expl[1];
        if (strlen($M) < 2) {
            $M = $M . 0;
        }
        if($M > 0){
        $hours = $H . "hr " . $M."min";
        }else{
        $hours = $H . "hr";
        }
        return $hours;
}
function tep_get_long_distance($lat1, $lon1, $lat2, $lon2, $unit='') {
      $theta = $lon1 - $lon2;
      $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
      $dist = acos($dist);
      $dist = rad2deg($dist);
      $miles = $dist * 60 * 1.1515;
      $unit = strtoupper($unit);

      if ($unit == "K") {
        return ($miles * 1.609344);
      } else if ($unit == "N") {
          return ($miles * 0.8684);
      } else {
            if($miles >0){
            return ' | Approx. '.number_format($miles).' miles';
            }else{
            return '';
            }
      }
}
function tep_get_all_get_params($exclude_array = '') {
    $CHttpSession = new CHttpSession;
    if (!is_array($exclude_array)) $exclude_array = array();
    $get_url = '';
    if (is_array($_GET) && (sizeof($_GET) > 0)) {
        reset($_GET);
        while (list($key, $value) = each($_GET)) {
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $get_url .= "{$key}%5B{$k}%5D={$v}" . '&';
                }
            } elseif ( (strlen($value) > 0) && ($key != $CHttpSession->getSessionName()) && (!in_array($key, $exclude_array)) && (!in_array($key, array('utm_source','utm_medium','utm_campaign', 'utm_term', 'OVRAW', 'OVADID', 'OVKEY', 'x', 'y','error'))) ) {
                $get_url .= $key . '=' . rawurlencode(stripslashes($value)) . '&';
            }
        }
    }

    return $get_url;
}
function get_points_toadd($order, $oid = '') {

      if ($order->info['total'] > 0) {
          if($oid > 0){
              $order_integer_total = OrderTotal::tep_get_order_final_price_of_oid($oid);
          }else{
              $order_integer_total = str_replace(',','',$order->info['total']);
          }
          if ((USE_POINTS_FOR_SHIPPING == 'false') && (USE_POINTS_FOR_TAX == 'false'))
          $points_toadd = $order_integer_total - $order->info['shipping_cost'] - $order->info['tax'];
          else if ((USE_POINTS_FOR_SHIPPING == 'false') && (USE_POINTS_FOR_TAX == 'true'))
          $points_toadd = $order_integer_total - $order->info['shipping_cost'];
          else if ((USE_POINTS_FOR_SHIPPING == 'true') && (USE_POINTS_FOR_TAX == 'false'))
          $points_toadd = $order_integer_total - $order->info['tax'];
          else $points_toadd = $order_integer_total;


          if (USE_POINTS_FOR_SPECIALS == 'false') {
              for ($i=0; $i<sizeof($order->products); $i++) {
                  if (tep_get_products_special_price($order->products[$i]['id']) >0) {
                      if (USE_POINTS_FOR_TAX == 'true') {
                          $points_toadd = $points_toadd - (tep_add_tax($order->products[$i]['final_price'],$order->products[$i]['tax'])*$order->products[$i]['quantity']);
                      } else {
                          $points_toadd = $points_toadd - ($order->products[$i]['final_price']*$order->products[$i]['quantity']);
                      }
                  }
              }
          }

          //amit added to remove reward points for gift certificate products start
          for ($i=0; $i<sizeof($order->products); $i++) {
                if(in_array((int)$order->products[$i]['id'],explode(',',GIFT_CERTIFICATES_TOURS_IDS))){
                      if (USE_POINTS_FOR_TAX == 'true') {
                          $points_toadd = $points_toadd - (tep_add_tax($order->products[$i]['final_price'],$order->products[$i]['tax'])*$order->products[$i]['quantity']);
                      } else {
                          $points_toadd = $points_toadd - ($order->products[$i]['final_price']*$order->products[$i]['quantity']);
                      }
                 }
          }
          //amit added to remove reward points for gift certificate products start

         //amit added to extra 50 points for auto confirmed tours start
          $_SESSION['total_earn_extra_point'] = 0;
          for ($i=0; $i<sizeof($order->products); $i++) {
              $show_auto_conf_btn_rights = tep_check_allow_show_auto_confirmed_button((int)$order->products[$i]['id'],$order->products[$i]['dateattributes']['date'],date('Y-m-d'));
              if($show_auto_conf_btn_rights == true){
                  $points_toadd = $points_toadd + (50/POINTS_PER_AMOUNT_PURCHASE); // add extra points [50 points / 0.75  = 66.6667]
                  $_SESSION['total_earn_extra_point'] = (int)$_SESSION['total_earn_extra_point'] + 50;
              }
          }
          //amit added to extra 50 points for auto confirmed tours end

          return $points_toadd;
      } else {
          return false;
      }
}
function tep_check_allow_show_auto_confirmed_button($product_id,$dep_date,$ord_date,$return_type = ''){
    $show_auto_btn = false;
    $default_duration_diff_check = 0;
    $get_product_detail = Product::model()->findByPk((int)$product_id);
    $providerFreeSale = ProviderFreeSale::model()->find('provider_id='.(int)$get_product_detail['provider_id']);
    $productFreeSale = ProductFreeSale::model()->find('product_id='.(int)$product_id);
    $provider_detail_array = array();
    if(count($providerFreeSale) >0 ){
        if($providerFreeSale['is_free_sale'] == 1){
            $provider_detail_array[$get_product_detail['provider_id']]['day'] =   $providerFreeSale['auto_confirm_duration'];
            $provider_detail_array[$get_product_detail['provider_id']]['email'] =   $providerFreeSale['auto_confirm_email'];
            $provider_detail_array[$get_product_detail['provider_id']]['auto_send_mail_to_free_sale'] =   $providerFreeSale['send_mail_to_provider'];
        }else{
            $provider_detail_array = array();
        }
        

    }
    if(count($productFreeSale) > 0){
        if($productFreeSale['is_free_sale'] == 1){
            $provider_detail_array[$get_product_detail['provider_id']]['day'] =   $productFreeSale['auto_confirm_duration'];
            $provider_detail_array[$get_product_detail['provider_id']]['email'] =   $productFreeSale['auto_confirm_email'];
            $provider_detail_array[$get_product_detail['provider_id']]['auto_send_mail_to_free_sale'] =   $productFreeSale['send_mail_to_provider'];    
        }else{
            $provider_detail_array = array(); 
        }
    }
    if( (in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_LOCAL_PAR))) || in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_LOCAL_LL))) || in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_LOCAL_AAC))) || in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_LOCAL_SEA))) || in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_LOCAL_LASSEN))) || in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_LOCAL_LAAA))) || in_array((int)$product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_BSSB))) || in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_LOCAL_JOY))) || in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_LOCAL_MIAMI))) || in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_LOCAL_LS))) || in_array((int)$product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_LLTS))) || in_array((int)$product_id, explode(',', Yii::t('checkout_process', 'AUTO_CONFIRMED_LOCAL_TB'))) || in_array((int)$product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_AST))) || in_array($product_id, explode(',', Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_MS))) || in_array($product_id, explode(',', Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_HGH))) || in_array($product_id, explode(',', Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_NGH))) || in_array($product_id, explode(',', Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_BSSB_CUSTOME))) || in_array($product_id, explode(',', Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_TP))) || ( $get_product_detail['vacation_package'] == '0'  &&  array_key_exists((int)$get_product_detail['provider_id'],$provider_detail_array)) ) && $dep_date != '0000-00-00 00:00:00'){
            $default_duration_diff_check = 3;
            if(in_array($product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_LAAA))) || in_array($product_id, explode(',', Yii::t('checkout_process', 'AUTO_CONFIRMED_LOCAL_TB'))) ){
                $default_duration_diff_check = 7;
            }else if(in_array((int)$product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_JOY))) || in_array($product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_LOCAL_MIAMI))) ){
                $default_duration_diff_check = 4;
            }else if(in_array((int)$product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_SEA))) || in_array((int)$product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_HGH)))){
				$default_duration_diff_check = 7;			
			}else if(in_array((int)$product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_LASSEN)))){
			    $default_duration_diff_check = 10;			
	        }else if(in_array((int)$product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_AST)))){
			    $default_duration_diff_check = 12;			
	        }else if(in_array((int)$product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_MS)))){
				$default_duration_diff_check = 5;			
			}else if(in_array((int)$product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_LLTS))) || in_array((int)$product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_LL)))){
				$default_duration_diff_check = 1;			
			}
            
            //custom duration setup for selected tour
            if(in_array((int)$product_id,explode(',',YII::t('checkout_process',CUSTOME_DURATION_LOCAL_21)))){
                $default_duration_diff_check = 21;
            }else if(in_array((int)$product_id,explode(',',YII::t('checkout_process',CUSTOME_DURATION_LOCAL_45)))){
                $default_duration_diff_check = 45;
            }else if(in_array((int)$product_id,explode(',',YII::t('checkout_process',CUSTOME_DURATION_LOCAL_30)))){
                $default_duration_diff_check = 30;
            }else if(in_array((int)$product_id,explode(',',YII::t('checkout_process',CUSTOME_DURATION_LOCAL_14)))){
                $default_duration_diff_check = 14;
            }else if(in_array((int)$product_id,explode(',',YII::t('checkout_process',CUSTOME_DURATION_LOCAL_60)))){
                $default_duration_diff_check = 60;
            }else if(in_array((int)$product_id,explode(',',YII::t('checkout_process',CUSTOME_DURATION_LOCAL_3)))){
                $default_duration_diff_check = 3;
            }

            //hide auto confirm for date range for LL and PAR
            if(in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_LOCAL_LL))) || in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_LOCAL_PAR)))){
                if($dep_date > '2013-12-31 00:00:00' && $dep_date < '2014-04-01 00:00:00'){
                    $default_duration_diff_check = 730;
                }
                if(in_array((int)$product_id,explode(',','138'))){ // custom block for 138
                    if($dep_date > '2012-10-31 00:00:00' && $dep_date < '2013-04-01 00:00:00'){
                        $default_duration_diff_check = 730;
                    }
                }
            }
            //hide auto confirm for date range for LL and PAR

            //block SEA autoconfrim for depture location for date range
            if(in_array((int)$product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_SEA)))){
                if($dep_date > '2013-12-17 00:00:00' && $dep_date < '2014-01-01 00:00:00'){
                    $default_duration_diff_check = 730;
                }
            }

            //hide auto confirm for LAAA date range
            if(in_array((int)$product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_LAAA)))){
                if($dep_date > '2013-12-20 00:00:00' && $dep_date < '2014-01-02 00:00:00'){
                    $default_duration_diff_check = 730;
                }
            }

            //hide auto confirm for BSSB date range
            if(in_array((int)$product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_BSSB)))){
                if($dep_date >= '2015-07-01 00:00:00' && $dep_date <= '2015-09-01 00:00:00'){
                    $default_duration_diff_check = 5;
                }else if(in_array((int)$product_id,explode(',',Yii::t('checkout_process',CUSTOME_DURATION_LOCAL_3)))){
					$default_duration_diff_check = 3;
				}else{
					$default_duration_diff_check = 730;
				}
            }
			//override the auto conform stop for provider local ls and lassen for till 2014-11-01 
		    if((in_array($product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_LASSEN))) || in_array($product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_LS)))) && $dep_date > '2014-09-19' && $dep_date < '2014-11-01'){
				$default_duration_diff_check = 730;
		    }
			//override the auto conform stop for provider local ls and lassen end
			
			//hide auto confirm for HGH local date range start
			if(in_array($product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_HGH)))){
				if($dep_date > '2014-12-16' && $dep_date < '2015-01-05'){
					$default_duration_diff_check = 730;
				}
			}
			//hide auto confirm for HGH date range end
			//hide auto confirm for NGH date range
			if(in_array($product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_NGH)))){
				$default_duration_diff_check = 15;
				if($dep_date < '2015-06-01' || $dep_date > '2015-09-30'){
					$default_duration_diff_check = 365;
				}
			}			
			//hide auto confirm for NGH date range end
			//hide auto confirm for BSSB CUSTOME date range
			if(in_array($product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_BSSB_CUSTOME)))){
				$default_duration_diff_check = 7;
				if(($dep_date >= '2015-05-22' && $dep_date <= '2015-05-25') || ($dep_date >= '2015-07-03' && $dep_date <= '2015-07-06') || ($dep_date >= '2015-09-04' && $dep_date <= '2015-09-07') ){
					$default_duration_diff_check = 365;
				}
			}
			//hide auto confirm for BSSB CUSTOME date range end	
			//hide auto confirm for TP date range
			if(in_array($product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_LOCAL_TP)))){
				$default_duration_diff_check = 4;
				if($dep_date > '2014-12-31'){
					$default_duration_diff_check = 365;
				}
			}			
			//hide auto confirm for TP date range end
            if(array_key_exists((int)$get_product_detail['provider_id'],$provider_detail_array)){
                $default_duration_diff_check = $provider_detail_array[$get_product_detail['provider_id']]['day'];
            }

            if(@scs_datediff('d', $ord_date, $dep_date, false) >= $default_duration_diff_check){
                $show_auto_btn = true;
            }
        }else if((in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_PACKAGE_PAR))) || in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_PACKAGE_LL))) || in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_PACKAGE_SEA))) || in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_PACKAGE_LASSEN))) || in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_PACKAGE_LAAA))) || in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_PACKAGE_RITZ))) || in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_PACKAGE_ORIENT))) || in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_PACKAGE_JOY))) || in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_PACKAGE_MIAMI))) || in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_PACKAGE_LS))) || in_array((int)$product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_PACKAGE_LLTS))) || in_array($product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_PACKAGE_AST))) || ( $get_product_detail['vacation_package'] == '1'  &&  array_key_exists((int)$get_product_detail['provider_id'],$provider_detail_array)) || in_array($product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_PACKAGE_AAC))) || in_array($product_id, explode(',', Yii::t('checkout_process', 'AUTO_CONFIRMED_PACKAGE_TB'))) || in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_PACKAGE_JC))) || in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_PACKAGE_MS))) || in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_PACKAGE_HGH))) || in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_PACKAGE_NGH))) ) && $dep_date != '0000-00-00 00:00:00'){
            $default_duration_diff_check = 7;
            if(in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_PACKAGE_LL)))){
                $default_duration_diff_check = 1;
            }else if(in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_PACKAGE_LAAA)))){
                $default_duration_diff_check = 21;
                //hide auto confirm for date range
                if($dep_date > '2013-12-20 00:00:00' && $dep_date < '2014-01-02 00:00:00'){
                    $default_duration_diff_check = 730;
                }
            }else if(in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_PACKAGE_SEA)))){
                $default_duration_diff_check = 21;
                if($dep_date > '2014-07-14 00:00:00' && $dep_date < '2014-09-01 00:00:00'){
                    $default_duration_diff_check = 730;
                }              
            }else if(in_array((int)$product_id,explode(',',YII::t('checkout_process',AUTO_CONFIRMED_PACKAGE_ORIENT)))){
                $default_duration_diff_check = 10;
                //hide auto confirm for date range start
                if($dep_date > '2012-12-22 00:00:00' && $dep_date < '2013-01-05 00:00:00'){
                    $default_duration_diff_check = 365;
                }
                //hide auto confirm for date range end
            }else if(in_array((int)$product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_PACKAGE_JOY))) || in_array($product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_PACKAGE_MIAMI))) ){
                $default_duration_diff_check = 4;
            }elseif(in_array($product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_PACKAGE_LS))) || in_array((int)$product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_PACKAGE_LLTS))) || in_array($product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_PACKAGE_PAR))) || in_array($product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_PACKAGE_RITZ)))){
              $default_duration_diff_check = 3;
              if(in_array((int)$product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_PACKAGE_LLTS)))){
                $default_duration_diff_check = 1;
              }
            }elseif(in_array($product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_PACKAGE_AAC)))){
              $default_duration_diff_check = 30;
            }elseif(in_array($product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_PACKAGE_LASSEN)))){
              $default_duration_diff_check = 10;
            }elseif(in_array($product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_PACKAGE_AST)))){
              $default_duration_diff_check = 12;
				//hide auto confirm for date range
				$departureBlockProductsArray = array(52809,48537,48507);
				if(in_array($product_id, $departureBlockProductsArray) && ($dep_date > '2015-03-23 00:00:00' && $dep_date < '2015-03-29 00:00:00')){
					$default_duration_diff_check = 365;
				}	
            }elseif(in_array($product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_PACKAGE_MS))) || in_array($product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_PACKAGE_JC)))){
			  $default_duration_diff_check = 5;
			}elseif(in_array($product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_PACKAGE_NGH))) ){
			  $default_duration_diff_check = 15;
			  //hide auto confirm for date range start
			  if($dep_date < '2015-06-01 00:00:00' || $dep_date > '2015-09-30 00:00:00'){
					$default_duration_diff_check = 365;
				}
			  //hide auto confirm for date range end	
			}
			//custom duration setup for selected tour
            if(in_array((int)$product_id,explode(',',Yii::t('checkout_process',CUSTOME_DURATION_PACKAGE_45)))){
                $default_duration_diff_check = 45;
            }else if(in_array((int)$product_id,explode(',',Yii::t('checkout_process',CUSTOME_DURATION_PACKAGE_14)))){
                $default_duration_diff_check = 14;
            }else if(in_array((int)$product_id,explode(',',Yii::t('checkout_process',CUSTOME_DURATION_PACKAGE_60)))){
                $default_duration_diff_check = 60;
            }else if(in_array((int)$product_id,explode(',',Yii::t('checkout_process',CUSTOME_DURATION_PACKAGE_7)))){
                $default_duration_diff_check = 7;
            }else if(in_array((int)$product_id,explode(',',Yii::t('checkout_process',CUSTOME_DURATION_PACKAGE_3)))){
				$default_duration_diff_check = 3;
		    }else if(in_array((int)$product_id,explode(',',Yii::t('checkout_process',CUSTOME_DURATION_PACKAGE_30)))){
                $default_duration_diff_check = 30;
				if($dep_date > '2014-07-14 00:00:00' && $dep_date < '2014-09-01 00:00:00'){
                    $default_duration_diff_check = 730;
                }
            }
			// block auto confirm for HGH for date range
			if(in_array($product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_PACKAGE_HGH))) && $dep_date > '2014-12-16' && $dep_date < '2015-01-05'){
				$default_duration_diff_check = 730;
			}
			// block auto confirm for HGH for date range end	

            //override the auto conform stop for provider package ls and lassen for till 2014-11-01 
			if((in_array($product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_PACKAGE_LS))) || in_array($product_id,explode(',',Yii::t('checkout_process',AUTO_CONFIRMED_PACKAGE_LASSEN)))) && $dep_date > '2014-09-19' && $dep_date < '2014-11-01'){
				$default_duration_diff_check = 730;
			}
			//override the auto conform stop for provider package ls and lassen end
            if(array_key_exists((int)$get_product_detail['provider_id'],$provider_detail_array)){
                $default_duration_diff_check = $provider_detail_array[$get_product_detail['provider_id']]['day'];
            }

            if(@scs_datediff('d', $ord_date, $dep_date, false) >= $default_duration_diff_check){
                $show_auto_btn = true;
            }
        }

    if($return_type == 'duration'){
        return $default_duration_diff_check;
    }else{
        return $show_auto_btn;
    }
}
function get_redemption_awards($customer_shopping_points_spending) {
      if (USE_POINTS_FOR_REDEEMED == 'false') {
          if (!$customer_shopping_points_spending) {
              return true;
          }
          return false;
      } else {
          return true;
      }
}
function tep_date_short_with_day($raw_date) {
    if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '') ) return false;

    $year = substr($raw_date, 0, 4);
    $month = (int)substr($raw_date, 5, 2);
    $day = (int)substr($raw_date, 8, 2);
    $hour = (int)substr($raw_date, 11, 2);
    $minute = (int)substr($raw_date, 14, 2);
    $second = (int)substr($raw_date, 17, 2);

    if (@date('Y', mktime($hour, $minute, $second, $month, $day, $year)) == $year) {
      return date(Yii::t('main',DATE_FORMAT_WITH_DAY), mktime($hour, $minute, $second, $month, $day, $year));
    } else {
      return preg_replace('/2037' . '$/', $year, date(Yii::t('main',DATE_FORMAT), mktime($hour, $minute, $second, $month, $day, 2037)));
    }
}

function kco_make_gc_code(){
    $gc_code=substr(preg_replace('/[\$\/\.]/', '', crypt(strval(time()))), 0, 11);
    return $gc_code;
}

if (!function_exists('array_merge')) {
    function array_merge($array1, $array2, $array3 = '') {
        if (empty($array3) && !is_array($array3)) $array3 = array();
        while (list($key, $val) = each($array1)) $array_merged[$key] = $val;
        while (list($key, $val) = each($array2)) $array_merged[$key] = $val;
        if (sizeof($array3) > 0) while (list($key, $val) = each($array3)) $array_merged[$key] = $val;
        return (array) $array_merged;
    }
}

//amit added to autho charged autorized.net order
function auto_charged_authorized_net_order($response_auth_trans_id = '', $x_type_req='PRIOR_AUTH_CAPTURE',$x_Amount=0,$x_card_num='',$x_invoice_num=''){
    $response_charged[0] = '0';
    if(tep_not_null($response_auth_trans_id)) {
        unset($auto_form_data);
        //Austin519 - added transaction key, ccmode
        $auto_form_data = array(
            x_Login => MODULE_PAYMENT_AUTHORIZENET_LOGIN,
            x_Tran_Key => MODULE_PAYMENT_AUTHORIZENET_TRANSKEY,
            x_Delim_Data => 'TRUE',
            x_Version => '3.1',
            x_trans_id => $response_auth_trans_id,
            x_Type => $x_type_req,
            x_Method => 'CC',
            x_Relay_Response => 'FALSE'
        );

        if($x_Amount > 0){ //$x_type_req=='VOID' &&
            $auto_form_data['x_Amount'] = "$x_Amount";
        }
        if($x_card_num > 0){ //$x_type_req=='CREDIT' &&
            $auto_form_data['x_card_num'] = "$x_card_num";
            $auto_form_data['x_email_customer'] = 'FALSE';
        }
        if($x_invoice_num != ''){ //$x_type_req=='CREDIT' &&
            $auto_form_data['x_invoice_num'] = "$x_invoice_num";
        }
        // concatenate order information variables to $charge_request_data
        $charge_request_data = '';
        while(list($key, $value) = each($auto_form_data)) {
            $charge_request_data .= $key . '=' . urlencode(str_replace(',', '', $value)) . '&';
        }
        $charge_request_data = substr($charge_request_data, 0, -1);

        unset($response_charged);
        if (MODULE_PAYMENT_AUTHORIZENET_CURL == 'Not Compiled') {
            if (function_exists('exec')) {
                exec('which curl', $curl_output);
                if ($curl_output) {
                    $curl_path = $curl_output[0];
                }else {
                    $curl_path = MODULE_PAYMENT_AUTHORIZENET_CURL_PATH;
                }
            }
            if((MODULE_PAYMENT_AUTHORIZENET_TESTMODE == 'Test') || (MODULE_PAYMENT_AUTHORIZENET_TESTMODE == 'Test And Debug') ) {
                exec("$curl_path -d \"$charge_request_data\" https://certification.authorize.net/gateway/transact.dll", $response_charged);
            } else {
                exec("$curl_path -d \"$charge_request_data\" https://secure.authorize.net/gateway/transact.dll", $response_charged);
            }
        }
        else {
            if((MODULE_PAYMENT_AUTHORIZENET_TESTMODE == 'Test') || (MODULE_PAYMENT_AUTHORIZENET_TESTMODE == 'Test And Debug') ) {
                $charged_req_url = "https://certification.authorize.net/gateway/transact.dll";
            } else {
                $charged_req_url = "https://secure.authorize.net/gateway/transact.dll";
            }
            $agent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)";
            $ch_req = curl_init();
            curl_setopt($ch_req, CURLOPT_URL,$charged_req_url);
            curl_setopt($ch_req, CURLOPT_VERBOSE, 1);
            curl_setopt($ch_req, CURLOPT_POST, 1);
            curl_setopt($ch_req, CURLOPT_POSTFIELDS, $charge_request_data);
            curl_setopt($ch_req, CURLOPT_TIMEOUT, 120);
            curl_setopt($ch_req, CURLOPT_USERAGENT, $agent);
            curl_setopt($ch_req, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch_req, CURLOPT_SSL_VERIFYPEER, FALSE);  //Windows 2003 Compatibility
            $auto_charged_authorize = curl_exec($ch_req);
            curl_close($ch_req);
            $response_charged = explode(',', $auto_charged_authorize);
        }
    }
    return $response_charged;
}
function email_track_code($mail_type='newsletter', $mail_address='xmzhh2000@126.com', $orders_id=0, $orders_eticket_log_id=0 ){
    $img_rul = HTTP_SERVER.'/email_track.php';
    $img_rul .='?mail_type='.$mail_type;
    $img_rul .='&mail_address='.$mail_address;
    if((int)$orders_id){
        $img_rul .='&orders_id='.$orders_id;
    }
    //E-ticket Log Start
    if((int)$orders_eticket_log_id > 0){
        $img_rul .='&orders_eticket_log_id='.$orders_eticket_log_id;
    }
    //E-ticket Log End
    $img_str = '<img src="'.$img_rul.'" width="1" height="1" style="display:none" />';
    return $img_str;
}
function get_facebook_cookie($app_id, $application_secret) {
     if(isset($_COOKIE['fbsr_' . $app_id])){
         list($encoded_sig, $payload) = explode('.', $_COOKIE['fbsr_' . $app_id], 2);

         $sig = base64_decode(strtr($encoded_sig, '-_', '+/'));
         $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

         if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
             return null;
         }
         $expected_sig = hash_hmac('sha256', $payload,
         $application_secret, $raw = true);
          if ($sig !== $expected_sig) {
              return null;
          }
          $token_url = "https://graph.facebook.com/oauth/access_token?"
         . "client_id=" . $app_id . "&client_secret=" . $application_secret. "&redirect_uri=" . "&code=" . $data['code'];

          $response = @file_get_contents($token_url);
          $params = null;
          parse_str($response, $params);
          $data['access_token'] = $params['access_token'];
          return $data;
      }else{
          return null;
     }
}


function tep_catalog_href_link($page = '', $parameters = '', $connection = 'NONSSL') {
    //FIXME Fix this function
    return $page;


    if ($connection == 'NONSSL') {
        $link = HTTP_CATALOG_SERVER . DIR_WS_CATALOG;
    } elseif ($connection == 'SSL') {
        if (ENABLE_SSL_CATALOG == 'true') {
            $link = HTTPS_CATALOG_SERVER . DIR_WS_CATALOG;
        } else {
            $link = HTTP_CATALOG_SERVER . DIR_WS_CATALOG;
        }
    } else {
        die('</td></tr></table></td></tr></table><br><br><font color="#ff0000"><b>Error!</b></font><br><br><b>Unable to determine connection method on a link!<br><br>Known methods: NONSSL SSL<br><br>Function used:<br><br>tep_href_link(\'' . $page . '\', \'' . $parameters . '\', \'' . $connection . '\')</b>');
    }


    if ($page == FILENAME_DEFAULT) {
        if (preg_match('/cPath=([0-9_]+)/', $parameters, $m)) {
            $cPath = $m[1];
            $parameters = preg_replace('/cPath=[0-9_]+&?/', '', $parameters);
            $page = seo_get_path_from_cpath($cPath);
        }
        if (preg_match('/page=([0-9]+)/', $parameters, $m)) {
            $page1 = $m[1];
            $parameters = preg_replace('/page=[0-9]+&?/', '', $parameters);
            $page = $page . 'p-' . $page1 . '/';
        }
        if (preg_match('/sort=([0-9a-zA-Z]+)/', $parameters, $m)) {
            $sort1 = $m[1];
            $parameters = preg_replace('/sort=[0-9a-zA-Z]+&?/', '', $parameters);
            $page = $page . 's-' . $sort1 . '/';
        }
    } elseif ($page == FILENAME_PRODUCT_INFO) {
        if (preg_match('/products_id=([0-9]+)/', $parameters, $m)) {
            $products_id = $m[1];
            $parameters = preg_replace('/products_id=[0-9]+&?/', '', $parameters);
            $parameters = preg_replace('/cPath=[0-9_]+&?/', '', $parameters);
            $page = seo_get_products_path($products_id, true, $tablink);
            $parameters = '';
        }
    } else if ($page == FILENAME_HR_VIEW_REGISTRY) {
        $page = 'honeymoon-view-registry/';
        if (preg_match('/hrid=([[0-9]+)/', $parameters, $m)) {
            $page1 = $m[1];
            $parameters = preg_replace('/hrid=[0-9]+&?/', '', $parameters);
            $page = $page . seo_get_path_of_hr_couples($page1);
        }
    }

    if ($parameters == '') {
        $link .= $page;
    } else {
        $link .= $page . '?' . $parameters;
    }

    while ((substr($link, -1) == '&') || (substr($link, -1) == '?'))
        $link = substr($link, 0, -1);

    return $link;
}


// SEOurls - function to create category path from cPath
function seo_get_path_from_cpath($cPath) {
    $ret = '';
    $cPath_array = explode('_', $cPath);
    $category_id = $cPath_array[count($cPath_array) - 1];

    $res = "select c1.url_path from " . Category::model()->tableName(). " c1 where c1.category_id = '" . (int) $category_id . "'";
    $cat_array = Yii::app()->db->createCommand($res)->queryRow();

    return $cat_array['url_path'];
}




// SEOurls - new funtion to get category path for a given product
// WARNING: works only for products 3 levels deep in category hierarchy

//function seo_get_products_path($products_id, $full = false, $tablink = '') { // $full == true => include product
//    $ret = '';
//    if ($full) {
//        $res = "select p.product_urlname from " . Product::model()->tableName() . " p where p.product_id = '" . (int) $products_id . "'";
//        $cat_array = Yii::app()->db->createCommand($res)->queryRow();
//        if ($tablink != '') {
//            //return $cat_array['products_urlname'] .$tablink.SEO_EXTENSION;
//            if ($tablink == '-question-answer') {
//                $tablink = '/question-&-answer/';
//            }
//            if ($tablink == 'traveler-photos') {
//                $tablink = '/traveler-photos/';
//            }
//
//            return $cat_array['products_urlname'] . $tablink;
//        } else {
//            //return $cat_array['products_urlname'] . SEO_EXTENSION;
//            return $cat_array['products_urlname'];
//        }
//    } else {
//        return '';
//    }
//}
function remote_file_exists ($url)
{
/*
    Return error codes:
    1 = Invalid URL host
    2 = Unable to connect to remote host
*/
    $head = "";
    $url_p = parse_url ($url);

    if (isset ($url_p["host"]))
    { $host = $url_p["host"]; }
    else
    { return 1; }

    if (isset ($url_p["path"]))
    { $path = $url_p["path"]; }
    else
    { $path = ""; }

    $fp = @fsockopen ($host, 80, $errno, $errstr, 20);
    if (!$fp)
    { return 2; }
    else
    {
        $parse = parse_url($url);
        $host = $parse['host'];

        fputs($fp, "HEAD ".$url." HTTP/1.1\r\n");
        fputs($fp, "HOST: ".$host."\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
        $headers = "";
        while (!feof ($fp))
        { $headers .= fgets ($fp, 128); }
    }
    fclose ($fp);
    $arr_headers = explode("\n", $headers);
    $return = false;
    if (isset ($arr_headers[0]))
    { $return = strpos ($arr_headers[0], "404") === false; }
    return $return;
}
if ( !function_exists('json_decode') ){
function json_decode($json)
{
    $comment = false;
    $out = '$x=';
    for ($i=0; $i<strlen($json); $i++)
    {
        if (!$comment)
        {
            if(($json[$i] == '{') || ($json[$i] == '[')) $out .= ' array(';
            elseif(($json[$i] == '}') || ($json[$i] == ']')) $out .= ')';
            elseif($json[$i] == ':') $out .= '=>';
            else $out .= $json[$i];
        }
        else $out .= $json[$i];
        if($json[$i] == '"' && $json[($i-1)]!="\\") $comment = !$comment;
    }
    eval($out . ';');
    return $x;
}
}

function scs_rte_safe($strText) {
    //returns safe code for preloading in the RTE
    $tmpString = trim($strText);
    //convert all types of single quotes
    $tmpString = str_replace(chr(145), chr(39), $tmpString);
    $tmpString = str_replace(chr(146), chr(39), $tmpString);
    //$tmpString = str_replace("'", "&#39;", $tmpString);
    //convert all types of double quotes
    $tmpString = str_replace('"', '\'', $tmpString);
    $tmpString = str_replace(chr(147), chr(34), $tmpString);
    $tmpString = str_replace(chr(148), chr(34), $tmpString);
    //replace carriage returns & line feeds
    $tmpString = str_replace(chr(10), "", $tmpString);
    $tmpString = str_replace(chr(13), " ", $tmpString);
    return $tmpString;
}
function endate_to_dbdate($date){
    if(strlen(trim($date))!=10){return false;}
    $array = explode('/',$date);
    if(count($array)!=3){ return false;}
    $format_date = $array[2].'-'.$array[0].'-'.$array[1];
    return $format_date;
}
function tep_string_to_int($string) {
    return (int)$string;
}
function tep_parse_category_path($cPath) {
    $cPath_array = array_map('tep_string_to_int', explode('_', $cPath));
    $tmp_array = array();
    $n = sizeof($cPath_array);
    for ($i=0; $i<$n; $i++) {
      if (!in_array($cPath_array[$i], $tmp_array)) {
        $tmp_array[] = $cPath_array[$i];
      }
    }
    return $tmp_array;
}
function tep_banner_image_extension() {
    if (function_exists('imagetypes')) {
      if (imagetypes() & IMG_PNG) {
        return 'png';
      } elseif (imagetypes() & IMG_JPG) {
        return 'jpg';
      } elseif (imagetypes() & IMG_GIF) {
        return 'gif';
      }
    } elseif (function_exists('imagecreatefrompng') && function_exists('imagepng')) {
      return 'png';
    } elseif (function_exists('imagecreatefromjpeg') && function_exists('imagejpeg')) {
      return 'jpg';
    } elseif (function_exists('imagecreatefromgif') && function_exists('imagegif')) {
      return 'gif';
    }

    return false;
}

function tep_get_admin_customer_name($admin_id='', $user_type='0'){//0=Admin, 1=Providers
    if($user_type=='1')
    {
        $the_admin_customer_query="select p.name as admin_name p.email as admin_email_address FROM ".Provider::tableName(). " p where p.provider_id = '".$admin_id."'";
    }
    else
    {
        $the_admin_customer_query= "select first_name, last_name, email  from " . User::tableName() . " where user_id = '" . $admin_id . "'";
    }
    $the_admin_customer = Yii::app()->db->createCommand($the_admin_customer_query)->queryRow();
    if($the_admin_customer['first_name'] != ''){
        $str_admin = $the_admin_customer['first_name']."  ".$the_admin_customer['last_name'];
    }else{
        $str_admin = $the_admin_customer['admin_email_address'];
    }
    return $str_admin;
}

function tep_get_all_get_params_aff_sales($exclude_array = '') {
    global $_GET;
    if ($exclude_array == '') $exclude_array = array();
    $get_url = '';
    reset($_GET);
    while (list($key, $value) = each($_GET)) {
      if (($key != tep_session_name()) && ($key != 'error') && (!in_array($key, $exclude_array))) $get_url .= $key . '=' . $value . '&';
    }
    return $get_url;
}

function tep_session_name($name = '') {
    if ($name != '') {
      return session_name($name);
    } else {
      return session_name();
    }
}

function tep_get_product_name_by_order_id($order_id)
{
    $display_all_tours_name_code = '';
    $display_all_departure_date = '';
    $display_all_retune_array = array();
    $product_query = Yii::app()->db->createCommand("select op.product_name,op.product_code  , op.product_departure_date, op.product_departure_time  from `" . Order::tableName() . "` o,  " . OrderProduct::tableName() . " op where o.order_id = op.order_id and op.order_id = '".$order_id."' order by op.product_departure_date")->queryAll();
    foreach($product_query as $rows){
        $display_all_retune_array[] = array('name'=>$rows['product_name'], 'code'=>$rows['product_code'], 'departure'=>tep_date_short($rows['product_departure_date']), 'depttime'=>$rows['product_departure_time']);
    }
    return $display_all_retune_array;
}

function tep_date_raw($date, $reverse = false) {
  if ($reverse) {
    return substr($date, 3, 2) . substr($date, 0, 2) . substr($date, 6, 4);
  } else {
    return substr($date, 6, 4) . substr($date, 0, 2) . substr($date, 3, 2);
  }
}

function getGenderIntValue($gender){
    if($gender == 'f'){
        return 2;
    }elseif($gender == 'm'){
        return 1;
    }else {
        return 0;
    }
}

function xml2array($xml_file, $get_attributes=1, $priority = 'tag', $xml_data='') {
    if (tep_not_null($xml_data)){
        $contents = $xml_data;
    }else{
        $contents = file_get_contents($xml_file);
    }
    if(!$contents) return array();

    if(!function_exists('xml_parser_create')) {
        //print "'xml_parser_create()' function not found!";
        return array();
    }

    //Get the XML parser of PHP - PHP must have this module for the parser to work
    $parser = xml_parser_create('');
    @xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    @xml_parse_into_struct($parser, trim($contents), $xml_values);
    xml_parser_free($parser);

    if(!$xml_values) return;//Hmm...

    //Initializations
    $xml_array = array();
    $parents = array();
    $opened_tags = array();
    $arr = array();
    $current = &$xml_array; //Refference

    //Go through the tags.
    $repeated_tag_index = array();//Multiple tags with same name will be turned into an array
    foreach($xml_values as $data) {
        unset($attributes,$value);//Remove existing values, or there will be trouble
        //This command will extract these variables into the foreach scope
        //tag(string), type(string), level(int), attributes(array).
        extract($data);//We could use the array by itself, but this cooler.
        $result = array();
        $attributes_data = array();

        if(isset($value)) {
            if($priority == 'tag') $result = $value;
            else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
        }

        //Set the attributes too.
        if(isset($attributes) and $get_attributes) {
            foreach($attributes as $attr => $val) {
                if($priority == 'tag') $attributes_data[$attr] = $val;
                else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
            }
        }

        //See tag status and do the needed.
        if($type == "open") {//The starting of the tag '<tag>'
            $parent[$level-1] = &$current;
            if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                $current[$tag] = $result;
                if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
                $repeated_tag_index[$tag.'_'.$level] = 1;
                $current = &$current[$tag];
            } else { //There was another element with the same tag name
                if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                    $repeated_tag_index[$tag.'_'.$level]++;
                } else {//This section will make the value an array if multiple tags with the same name appear together
                    $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
                    $repeated_tag_index[$tag.'_'.$level] = 2;
                    if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                        $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                        unset($current[$tag.'_attr']);
                    }
                }
                $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
                $current = &$current[$tag][$last_item_index];
            }
        } elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
            //See if the key is already taken.
            if(!isset($current[$tag])) { //New Key
                $current[$tag] = $result;
                $repeated_tag_index[$tag.'_'.$level] = 1;
                if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;
            } else { //If taken, put all things inside a list(array)
                if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...
                    // ...push the new element into that array.
                    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;

                    if($priority == 'tag' and $get_attributes and $attributes_data) {
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                    }
                    $repeated_tag_index[$tag.'_'.$level]++;
                } else { //If it is not an array...
                    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                    $repeated_tag_index[$tag.'_'.$level] = 1;
                    if($priority == 'tag' and $get_attributes) {
                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well

                            $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                            unset($current[$tag.'_attr']);
                        }
                        if($attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                        }
                    }
                    $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
                }
            }
        } elseif($type == 'close') { //End of tag '</tag>'
            $current = &$parent[$level-1];
        }
    }
    return($xml_array);
}

/**
 * Shorten string
 *
 * @param string $cutstring
 * @param int $cutno
 * @param string $endstr
 * @return string Shortend string
 */
function cutword($cutstring, $cutno, $endstr = "...") {
    if (strlen($cutstring) > $cutno) {
        for ($i = 0; $i < $cutno; $i++) {
            $ch = substr($cutstring, $i, 1);
            if (ord($ch) > 127)
                $i++;
        }
        $cutstring = mb_substr($cutstring, 0, $i, 'utf-8') . $endstr;
    }
    return $cutstring;
}

/**
 * Get TFF links
 *
 * @param string $texts Text string
 * @param string $target anchor target. Default _blank
 * @param string $class css classes
 * @return string Formatted string
 */
function auto_add_tff_links($texts,$target="_blank", $class=""){
    $pet = '/(http:\/\/)*((www|cn)+\.toursforfun\.com[\w\/\?\&\.\=%\-]*)/';
    $texts = tep_db_output($texts);
    $texts = preg_replace($pet,'<a target="'.$target.'" class="'.$class.'" href="http://$2">$1$2</a>',$texts);
    return $texts;
}

/**
 * auto pasrse link from answer or question
 *
 * @param string $input Text string
 * @return string Formatted string with auto link with href
 */
function tep_get_auto_pase_link($input) {
//$output = preg_replace("/\b((http(s?):\/\/)|(www\.))([\w\.]+)([%&-~\/\w+\.-?]+)\b/i", "<a href=\"http$3://$4$5$6\" class=\"sp3\" target=\"_blank\">$2$4$5$6</a>", $input);
$regex =
"{ \\b # start at word\n"
. " # boundary\n"
. "( # capture to $1\n"
. "(https?|telnet|gopher|file|wais|ftp) : \n"
. " # resource and colon\n"
. "[\\w/\\#~:.?+=&%@!\\-]+? # one or more valid\n"
. " # characters\n"
. " # but take as little as\n"
. " # possible\n"
. ")\n"
. "(?= # lookahead\n"
. "[.:?\\-]* # for possible punct\n"
. "(?:[^\\w/\\#~:.?+=&%@!\\-] # invalid character\n"
. "|$) # or end of string\n"
. ") }x";
if(preg_match_all($regex, $input, $matches)){
    foreach($matches[0] as $k=>$v){
        $http_url_pos = strpos($input, $v);
        if((int)$http_url_pos > 0){
            $prefix_text = substr($input, ($http_url_pos - 6), 6);
            if($prefix_text != 'href="'){
                $input = str_replace($v, "<a class=\"sp3\" target=\"_blank\" href=\"$v\">$v</a>", $input);
            }

        }
    }
}
$output = $input;
//$output = preg_replace($regex, "<a class=\"sp3\" target=\"_blank\" href=\"$1\">$1</a>", $input);
//$output = nl2br($output);
return $output;
}

/**
 * Get the Disnelyland tours ticket exception message for order email confirmations
 *
 * @param int $orderId Order id
 * @return string If order includes disnly land product an string will return else empty string will return
 * @author Gihan S<gihanshp@gmail.com>
 */
function getDisneylandExceptionMessage($orderId){
    $orderProductIds = Order::model()->getProductIds($orderId);
    $disneylandProductIds = array();
    $disneylandProductIds = @str_getcsv(Configuration::getVal('DISNEYLAND_PRODUCT_IDS'));
    $retStr = '';
    if(count(array_intersect($orderProductIds, $disneylandProductIds))){
        // include disneyland message
        $retStr = "\n<b>".Configuration::getVal('DISNEYLAND_EXCEPTION_MESSAGE')."</b>\n";
    }
    return $retStr;
}

function mailchimpSubscriberSynchronize($email, $fname, $lname, $group_str)
{
    //mailchimp code - start
    if (
        Configuration::getVal('ENABLE_MAILCHIMP_SYNCHRONIZED') == 'true' 
        && IS_PROD_SITE === true
    ) {
        $api = new MCAPI(Configuration::getVal('MCAPI_KEY'));
        if ($fname != '') {
            $merge_vars = array(
                'FNAME'     => $fname,
                'LNAME'     => $lname,
                'GROUPINGS' => array(
                    array('name' => 'Newsletter Opt', 'groups' => $group_str),
                )
            );
        } else {
            $merge_vars = array(
                'GROUPINGS' => array(
                    array('name' => 'Newsletter Opt', 'groups' => $group_str),
                )
            );
        }
        $listId = Configuration::getVal('MCAPI_LIST_ID');
        if ($group_str != '') {
            return $api->listSubscribe(
                $listId, 
                $email, 
                $merge_vars, 
                'html',
                true, 
                true, 
                true, 
                false
            );
        } else {
            return $api->listUnsubscribe($listId, $email);
        }
    } else {
        return true;
    }
    //mailchimp code - end
}

//Arrange cart array, let product with its extended hotel in the same array
function arrangeCartProducts($products){
    $new_products_arr = array();//Arrange cart array of new array (let product with its extended hotel in the same array)
    foreach($products as $key => $value){
        if($value['parent_product_id']==0){
            if($value['product_entity_type']==Product::ENTIRY_HOTEL){
                $value['name'] = htmlspecialchars($value['name']);
                $new_products_arr[] = $value;
                unset($products[$key]);
            }
            else{
                $hotel_info_arr = explode('|=|', $value['hotel_extension_info']);
                if((tep_not_null($hotel_info_arr[0]) && tep_not_null($hotel_info_arr[2])) || (tep_not_null($hotel_info_arr[7]) && tep_not_null($hotel_info_arr[8]))){
                    foreach ($products as $k => $v) {
                        if($v['product_entity_type']==Product::ENTIRY_HOTEL && $v['parent_product_id']==$value['id']){
                            if($v['hotel_extension_flag'] == '1'){
                                $value['hotel_early'] = $v;
                            }
                            elseif($v['hotel_extension_flag'] == '2'){
                                $value['hotel_late'] = $v;
                            }
                            unset($products[$k]);
                        }
                    }
                }
                $value['name'] = htmlspecialchars($value['name']);
                $new_products_arr[] = $value;
                unset($products[$key]);
            }
        }
    }
    return $new_products_arr;
}

function getMainMenuItems() {
    return array(
        Yii::t('main', 'Tours') => array(
            327 => Yii::t('main', 'North America'),
            326 => Yii::t('main', 'Europe'),
            320 => Yii::t('main', 'South America'),
            146 => Yii::t('main', 'Asia'),
            323 => Yii::t('main', 'Australia'),
            'companions' => Yii::t('main', 'Travel Companions'),
            'city' => Yii::t('main', 'By Departure City'),
            'type' => Yii::t('main', 'By Type'),
            'brand' => Yii::t('main', 'By Brand'),
            'blog' => Yii::t('main', 'Blog'),
        ),
        Yii::t('main', 'Flights') => array(
            'car' => Yii::t('main', 'Cars'),
        ),
        Yii::t('main', 'Hotels') => array(),
        Yii::t('main', 'Deals') => array(),
    );
}

function getSubMenuItems(){
    $main_dropdown_menu_array_536 = array();
    $main_dropdown_menu_array_536[] = 'National Parks';
    $main_dropdown_menu_array_536[] = array('id' => '537', 'text' => 'Grand Canyon');
    $main_dropdown_menu_array_536[] = array('id' => '37', 'text' => 'Mount Rushmore');
    $main_dropdown_menu_array_536[] = array('id' => '35', 'text' => 'Yellowstone');
    $main_dropdown_menu_array_536[] = array('id' => '48', 'text' => 'Yosemite');

    $main_dropdown_menu_array_25 = array();
    $main_dropdown_menu_array_25[] = 'US East Coast';
    $main_dropdown_menu_array_25[] = array('id' => '71', 'text' => 'American East Coast Multi-City Travel Packages');
    $main_dropdown_menu_array_25[] = array('id' => '59', 'text' => 'Boston City Tours');
    $main_dropdown_menu_array_25[] = array('id' => '68', 'text' => 'Martha\'s Vineyard Tours');
    $main_dropdown_menu_array_25[] = array('id' => '55', 'text' => 'New York Tours');
    $main_dropdown_menu_array_25[] = array('id' => '57', 'text' => 'Niagara Falls Tours (US Side)');
    $main_dropdown_menu_array_25[] = array('id' => '56', 'text' => 'Philadelphia Tours');
    $main_dropdown_menu_array_25[] = array('id' => '61', 'text' => 'Rhode Island Tours');
    $main_dropdown_menu_array_25[] = array('id' => '52', 'text' => 'Washington D.C. Tours');
    $main_dropdown_menu_array_25[] = array('id' => '25', 'text' => '<b>More US East Coast Tours</b>');

    $main_dropdown_menu_array_34 = array();
    $main_dropdown_menu_array_34[] = 'US Florida';
    $main_dropdown_menu_array_34[] = array('id' => '104', 'text' => 'Florida Theme Park Travel Packages');
    $main_dropdown_menu_array_34[] = array('id' => '153', 'text' => 'Miami Tours');
    $main_dropdown_menu_array_34[] = array('id' => '152', 'text' => 'Orlando Tours'); //&mnu=day-trips

    $main_dropdown_menu_array_24 = array();
    $main_dropdown_menu_array_24[] = 'US West Coast';
    $main_dropdown_menu_array_24[] = array('id' => '51', 'text' => 'American West Coast Multi-City Travel Packages');
    $main_dropdown_menu_array_24[] = array('id' => '119', 'text' => '17-Mile Drive Tours');
    $main_dropdown_menu_array_24[] = array('id' => '107', 'text' => 'Disneyland/California Adventure Tours');
    $main_dropdown_menu_array_24[] = array('id' => '521', 'text' => 'Grand Canyon Airplane/Helicopter Tours');
    $main_dropdown_menu_array_24[] = array('id' => '31', 'text' => 'Grand Canyon Bus Tours');
    $main_dropdown_menu_array_24[] = array('id' => '138&mnu=day-trips', 'text' => 'Grand Canyon West Rim, Skywalk Tours');
    $main_dropdown_menu_array_24[] = array('id' => '29', 'text' => 'Los Angeles Tours');
    $main_dropdown_menu_array_24[] = array('id' => '32', 'text' => 'Las Vegas Tours');
    $main_dropdown_menu_array_24[] = array('id' => '37', 'text' => 'Mount Rushmore National Park Tours');
    $main_dropdown_menu_array_24[] = array('id' => '38', 'text' => 'Napa Valley Tours');
    $main_dropdown_menu_array_24[] = array('id' => '30', 'text' => 'San Francisco Tours');
    $main_dropdown_menu_array_24[] = array('id' => '45&mnu=day-trips', 'text' => 'San Diego Tours');
    $main_dropdown_menu_array_24[] = array('id' => '487', 'text' => 'Seattle');
    $main_dropdown_menu_array_24[] = array('id' => '110&mnu=day-trips', 'text' => 'Sea World (San Diego) Tours');
    $main_dropdown_menu_array_24[] = array('id' => '108&mnu=day-trips', 'text' => 'Universal Studios Tours');
    $main_dropdown_menu_array_24[] = array('id' => '35', 'text' => 'Yellowstone National Park Tours');
    $main_dropdown_menu_array_24[] = array('id' => '48', 'text' => 'Yosemite National Park Tours');
    $main_dropdown_menu_array_24[] = array('id' => '24', 'text' => '<b>More US West Coast Tours</b>');

    $main_dropdown_menu_array_33 = array();
    $main_dropdown_menu_array_33[] = 'US Hawaii';
    $main_dropdown_menu_array_33[] = array('id' => '77', 'text' => 'Hawaii Multi-Day Travel Packages');
    $main_dropdown_menu_array_33[] = array('id' => '535', 'text' => 'Helicopter Tours');
    $main_dropdown_menu_array_33[] = array('id' => '85', 'text' => 'Island of Oahu Tours');
    $main_dropdown_menu_array_33[] = array('id' => '82', 'text' => 'Island of Maui Tours');
    $main_dropdown_menu_array_33[] = array('id' => '83', 'text' => 'Island of Hawaii (The Big Island) Tours');

    $main_dropdown_menu_array_54 = array();
    $main_dropdown_menu_array_54[] = 'Canada';
    $main_dropdown_menu_array_54[] = array('id' => '389', 'text' => 'Canada Multi-City Travel Packages');
    $main_dropdown_menu_array_54[] = array('id' => '386', 'text' => 'Montreal Tours');
    $main_dropdown_menu_array_54[] = array('id' => '381', 'text' => 'Niagara Falls (Canadian Side) Tours');
    $main_dropdown_menu_array_54[] = array('id' => '66', 'text' => 'Ottawa Tours');
    $main_dropdown_menu_array_54[] = array('id' => '67', 'text' => 'Quebec Tours');
    $main_dropdown_menu_array_54[] = array('id' => '390', 'text' => 'Rocky Mountain Tours');
    $main_dropdown_menu_array_54[] = array('id' => '65', 'text' => 'Toronto Tours');
    $main_dropdown_menu_array_54[] = array('id' => '385', 'text' => 'Vancouver Tours');

    $main_dropdown_menu_array_337 = array();
    $main_dropdown_menu_array_337[] = 'Central &amp; Eastern Europe';
    $main_dropdown_menu_array_337[] = array('id' => '339', 'text' => 'Austria Tours');
    $main_dropdown_menu_array_337[] = array('id' => '346', 'text' => 'Czech Republic &amp; Croatia Tours');
    $main_dropdown_menu_array_337[] = array('id' => '338', 'text' => 'Germany Tours');
    $main_dropdown_menu_array_337[] = array('id' => '347', 'text' => 'Hungary Tours');
    $main_dropdown_menu_array_337[] = array('id' => '343', 'text' => 'Poland Tours');
    $main_dropdown_menu_array_337[] = array('id' => '340', 'text' => 'Switzerland Tours');

    $main_dropdown_menu_array_355 = array();
    $main_dropdown_menu_array_355[] = 'Northern Europe';
    $main_dropdown_menu_array_355[] = array('id' => '358', 'text' => 'Iceland Tours');
    $main_dropdown_menu_array_355[] = array('id' => '356', 'text' => 'Russia Tours');
    $main_dropdown_menu_array_355[] = array('id' => '357', 'text' => 'Scandinavia Tours');

    $main_dropdown_menu_array_349 = array();
    $main_dropdown_menu_array_349[] = 'Southern Europe';
    $main_dropdown_menu_array_349[] = array('id' => '354', 'text' => 'Greece &amp; Turkey Tours');
    $main_dropdown_menu_array_349[] = array('id' => '353', 'text' => 'Morocco Tours');
    $main_dropdown_menu_array_349[] = array('id' => '352', 'text' => 'Portugal Tours');
    $main_dropdown_menu_array_349[] = array('id' => '351', 'text' => 'Spain Tours');

    $main_dropdown_menu_array_333 = array();
    $main_dropdown_menu_array_333[] = 'Western Europe';
    $main_dropdown_menu_array_333[] = array('id' => '336', 'text' => 'Belgium Tours');
    $main_dropdown_menu_array_333[] = array('id' => '334', 'text' => 'France Tours');
    $main_dropdown_menu_array_333[] = array('id' => '335', 'text' => 'Holland Tours');

    return array(
        327 => array(
            '536' => $main_dropdown_menu_array_536,
            '25' => $main_dropdown_menu_array_25,
            '34' => $main_dropdown_menu_array_34,
            '24' => $main_dropdown_menu_array_24,
            '33' => $main_dropdown_menu_array_33,
            '54' => $main_dropdown_menu_array_54,
            '337' => $main_dropdown_menu_array_337,
            '355' => $main_dropdown_menu_array_355,
            '349' => $main_dropdown_menu_array_349,
            '333' => $main_dropdown_menu_array_333
        )
    );
}

function array_record_sort($records, $field, $reverse=false){
    $hash = array();
    $i = 0;
    foreach($records as $record){
        $hash[$record[$field].$i] = $record;
        $i++;
    }
    ($reverse)? krsort($hash) : ksort($hash);
    $records = array();
    foreach($hash as $record){
        $records []= $record;
    }
    return $records;
}

/**
 * Check current request is from a mobile browser
 *
 * @return boolean Returns True if detected as mobile browser, else false
 *
 * @author Gihan S <gihanshp@gmail.com>
 */
function isMobile(){
    $user_agent = Yii::app()->request->userAgent;
    $accept = Yii::app()->request->acceptTypes;
    return false
        || (preg_match('/ipod/i',$user_agent)||preg_match('/iphone/i',$user_agent))
        || (preg_match('/android/i',$user_agent))
        || (preg_match('/opera mini/i',$user_agent))
        || (preg_match('/blackberry/i',$user_agent))
        || (preg_match('/(pre\/|palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine)/i',$user_agent))
        || (preg_match('/(iris|3g_t|windows ce|opera mobi|windows ce; smartphone;|windows ce; iemobile)/i',$user_agent))
        || (preg_match('/(mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320|vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|psp|treo)/i',$user_agent))
        || ((strpos($accept,'text/vnd.wap.wml')>0)||(strpos($accept,'application/vnd.wap.xhtml+xml')>0))
        || (isset($_SERVER['HTTP_X_WAP_PROFILE'])||isset($_SERVER['HTTP_PROFILE']))
        || (in_array(strtolower(substr($user_agent,0,4)),array('1207'=>'1207','3gso'=>'3gso','4thp'=>'4thp','501i'=>'501i','502i'=>'502i','503i'=>'503i','504i'=>'504i','505i'=>'505i','506i'=>'506i','6310'=>'6310','6590'=>'6590','770s'=>'770s','802s'=>'802s','a wa'=>'a wa','acer'=>'acer','acs-'=>'acs-','airn'=>'airn','alav'=>'alav','asus'=>'asus','attw'=>'attw','au-m'=>'au-m','aur '=>'aur ','aus '=>'aus ','abac'=>'abac','acoo'=>'acoo','aiko'=>'aiko','alco'=>'alco','alca'=>'alca','amoi'=>'amoi','anex'=>'anex','anny'=>'anny','anyw'=>'anyw','aptu'=>'aptu','arch'=>'arch','argo'=>'argo','bell'=>'bell','bird'=>'bird','bw-n'=>'bw-n','bw-u'=>'bw-u','beck'=>'beck','benq'=>'benq','bilb'=>'bilb','blac'=>'blac','c55/'=>'c55/','cdm-'=>'cdm-','chtm'=>'chtm','capi'=>'capi','cond'=>'cond','craw'=>'craw','dall'=>'dall','dbte'=>'dbte','dc-s'=>'dc-s','dica'=>'dica','ds-d'=>'ds-d','ds12'=>'ds12','dait'=>'dait','devi'=>'devi','dmob'=>'dmob','doco'=>'doco','dopo'=>'dopo','el49'=>'el49','erk0'=>'erk0','esl8'=>'esl8','ez40'=>'ez40','ez60'=>'ez60','ez70'=>'ez70','ezos'=>'ezos','ezze'=>'ezze','elai'=>'elai','emul'=>'emul','eric'=>'eric','ezwa'=>'ezwa','fake'=>'fake','fly-'=>'fly-','fly_'=>'fly_','g-mo'=>'g-mo','g1 u'=>'g1 u','g560'=>'g560','gf-5'=>'gf-5','grun'=>'grun','gene'=>'gene','go.w'=>'go.w','good'=>'good','grad'=>'grad','hcit'=>'hcit','hd-m'=>'hd-m','hd-p'=>'hd-p','hd-t'=>'hd-t','hei-'=>'hei-','hp i'=>'hp i','hpip'=>'hpip','hs-c'=>'hs-c','htc '=>'htc ','htc-'=>'htc-','htca'=>'htca','htcg'=>'htcg','htcp'=>'htcp','htcs'=>'htcs','htct'=>'htct','htc_'=>'htc_','haie'=>'haie','hita'=>'hita','huaw'=>'huaw','hutc'=>'hutc','i-20'=>'i-20','i-go'=>'i-go','i-ma'=>'i-ma','i230'=>'i230','iac'=>'iac','iac-'=>'iac-','iac/'=>'iac/','ig01'=>'ig01','im1k'=>'im1k','inno'=>'inno','iris'=>'iris','jata'=>'jata','java'=>'java','kddi'=>'kddi','kgt'=>'kgt','kgt/'=>'kgt/','kpt '=>'kpt ','kwc-'=>'kwc-','klon'=>'klon','lexi'=>'lexi','lg g'=>'lg g','lg-a'=>'lg-a','lg-b'=>'lg-b','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-f'=>'lg-f','lg-g'=>'lg-g','lg-k'=>'lg-k','lg-l'=>'lg-l','lg-m'=>'lg-m','lg-o'=>'lg-o','lg-p'=>'lg-p','lg-s'=>'lg-s','lg-t'=>'lg-t','lg-u'=>'lg-u','lg-w'=>'lg-w','lg/k'=>'lg/k','lg/l'=>'lg/l','lg/u'=>'lg/u','lg50'=>'lg50','lg54'=>'lg54','lge-'=>'lge-','lge/'=>'lge/','lynx'=>'lynx','leno'=>'leno','m1-w'=>'m1-w','m3ga'=>'m3ga','m50/'=>'m50/','maui'=>'maui','mc01'=>'mc01','mc21'=>'mc21','mcca'=>'mcca','medi'=>'medi','meri'=>'meri','mio8'=>'mio8','mioa'=>'mioa','mo01'=>'mo01','mo02'=>'mo02','mode'=>'mode','modo'=>'modo','mot '=>'mot ','mot-'=>'mot-','mt50'=>'mt50','mtp1'=>'mtp1','mtv '=>'mtv ','mate'=>'mate','maxo'=>'maxo','merc'=>'merc','mits'=>'mits','mobi'=>'mobi','motv'=>'motv','mozz'=>'mozz','n100'=>'n100','n101'=>'n101','n102'=>'n102','n202'=>'n202','n203'=>'n203','n300'=>'n300','n302'=>'n302','n500'=>'n500','n502'=>'n502','n505'=>'n505','n700'=>'n700','n701'=>'n701','n710'=>'n710','nec-'=>'nec-','nem-'=>'nem-','newg'=>'newg','neon'=>'neon','netf'=>'netf','noki'=>'noki','nzph'=>'nzph','o2 x'=>'o2 x','o2-x'=>'o2-x','opwv'=>'opwv','owg1'=>'owg1','opti'=>'opti','oran'=>'oran','p800'=>'p800','pand'=>'pand','pg-1'=>'pg-1','pg-2'=>'pg-2','pg-3'=>'pg-3','pg-6'=>'pg-6','pg-8'=>'pg-8','pg-c'=>'pg-c','pg13'=>'pg13','phil'=>'phil','pn-2'=>'pn-2','pt-g'=>'pt-g','palm'=>'palm','pana'=>'pana','pire'=>'pire','pock'=>'pock','pose'=>'pose','psio'=>'psio','qa-a'=>'qa-a','qc-2'=>'qc-2','qc-3'=>'qc-3','qc-5'=>'qc-5','qc-7'=>'qc-7','qc07'=>'qc07','qc12'=>'qc12','qc21'=>'qc21','qc32'=>'qc32','qc60'=>'qc60','qci-'=>'qci-','qwap'=>'qwap','qtek'=>'qtek','r380'=>'r380','r600'=>'r600','raks'=>'raks','rim9'=>'rim9','rove'=>'rove','s55/'=>'s55/','sage'=>'sage','sams'=>'sams','sc01'=>'sc01','sch-'=>'sch-','scp-'=>'scp-','sdk/'=>'sdk/','se47'=>'se47','sec-'=>'sec-','sec0'=>'sec0','sec1'=>'sec1','semc'=>'semc','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','sk-0'=>'sk-0','sl45'=>'sl45','slid'=>'slid','smb3'=>'smb3','smt5'=>'smt5','sp01'=>'sp01','sph-'=>'sph-','spv '=>'spv ','spv-'=>'spv-','sy01'=>'sy01','samm'=>'samm','sany'=>'sany','sava'=>'sava','scoo'=>'scoo','send'=>'send','siem'=>'siem','smar'=>'smar','smit'=>'smit','soft'=>'soft','sony'=>'sony','t-mo'=>'t-mo','t218'=>'t218','t250'=>'t250','t600'=>'t600','t610'=>'t610','t618'=>'t618','tcl-'=>'tcl-','tdg-'=>'tdg-','telm'=>'telm','tim-'=>'tim-','ts70'=>'ts70','tsm-'=>'tsm-','tsm3'=>'tsm3','tsm5'=>'tsm5','tx-9'=>'tx-9','tagt'=>'tagt','talk'=>'talk','teli'=>'teli','topl'=>'topl','hiba'=>'hiba','up.b'=>'up.b','upg1'=>'upg1','utst'=>'utst','v400'=>'v400','v750'=>'v750','veri'=>'veri','vk-v'=>'vk-v','vk40'=>'vk40','vk50'=>'vk50','vk52'=>'vk52','vk53'=>'vk53','vm40'=>'vm40','vx98'=>'vx98','virg'=>'virg','vite'=>'vite','voda'=>'voda','vulc'=>'vulc','w3c '=>'w3c ','w3c-'=>'w3c-','wapj'=>'wapj','wapp'=>'wapp','wapu'=>'wapu','wapm'=>'wapm','wig '=>'wig ','wapi'=>'wapi','wapr'=>'wapr','wapv'=>'wapv','wapy'=>'wapy','wapa'=>'wapa','waps'=>'waps','wapt'=>'wapt','winc'=>'winc','winw'=>'winw','wonu'=>'wonu','x700'=>'x700','xda2'=>'xda2','xdag'=>'xdag','yas-'=>'yas-','your'=>'your','zte-'=>'zte-','zeto'=>'zeto','acs-'=>'acs-','alav'=>'alav','alca'=>'alca','amoi'=>'amoi','aste'=>'aste','audi'=>'audi','avan'=>'avan','benq'=>'benq','bird'=>'bird','blac'=>'blac','blaz'=>'blaz','brew'=>'brew','brvw'=>'brvw','bumb'=>'bumb','ccwa'=>'ccwa','cell'=>'cell','cldc'=>'cldc','cmd-'=>'cmd-','dang'=>'dang','doco'=>'doco','eml2'=>'eml2','eric'=>'eric','fetc'=>'fetc','hipt'=>'hipt','http'=>'http','ibro'=>'ibro','idea'=>'idea','ikom'=>'ikom','inno'=>'inno','ipaq'=>'ipaq','jbro'=>'jbro','jemu'=>'jemu','java'=>'java','jigs'=>'jigs','kddi'=>'kddi','keji'=>'keji','kyoc'=>'kyoc','kyok'=>'kyok','leno'=>'leno','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-g'=>'lg-g','lge-'=>'lge-','libw'=>'libw','m-cr'=>'m-cr','maui'=>'maui','maxo'=>'maxo','midp'=>'midp','mits'=>'mits','mmef'=>'mmef','mobi'=>'mobi','mot-'=>'mot-','moto'=>'moto','mwbp'=>'mwbp','mywa'=>'mywa','nec-'=>'nec-','newt'=>'newt','nok6'=>'nok6','noki'=>'noki','o2im'=>'o2im','opwv'=>'opwv','palm'=>'palm','pana'=>'pana','pant'=>'pant','pdxg'=>'pdxg','phil'=>'phil','play'=>'play','pluc'=>'pluc','port'=>'port','prox'=>'prox','qtek'=>'qtek','qwap'=>'qwap','rozo'=>'rozo','sage'=>'sage','sama'=>'sama','sams'=>'sams','sany'=>'sany','sch-'=>'sch-','sec-'=>'sec-','send'=>'send','seri'=>'seri','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','siem'=>'siem','smal'=>'smal','smar'=>'smar','sony'=>'sony','sph-'=>'sph-','symb'=>'symb','t-mo'=>'t-mo','teli'=>'teli','tim-'=>'tim-','tosh'=>'tosh','treo'=>'treo','tsm-'=>'tsm-','upg1'=>'upg1','upsi'=>'upsi','vk-v'=>'vk-v','voda'=>'voda','vx52'=>'vx52','vx53'=>'vx53','vx60'=>'vx60','vx61'=>'vx61','vx70'=>'vx70','vx80'=>'vx80','vx81'=>'vx81','vx83'=>'vx83','vx85'=>'vx85','wap-'=>'wap-','wapa'=>'wapa','wapi'=>'wapi','wapp'=>'wapp','wapr'=>'wapr','webc'=>'webc','whit'=>'whit','winw'=>'winw','wmlb'=>'wmlb','xda-'=>'xda-',)))
    ;
}

/**
 * Check whether this request should redirect to mobile site
 *
 * @return boolean
 *
 * @author Gihan S <gihanshp@gmail.com>
 */
function shouldRedirectMobile($language_id){
//    if($language_id == 1 && IS_PROD_SITE == false){
    if($language_id == 1){
        // check session variable for stay in full site is set
        if(isset($_REQUEST['full_site']))
            Yii::app()->session['full_site'] = 'true';
        if(!isset(Yii::app()->session['full_site']))
            return true;
    }
    return false;
}

function zh_strlen($string) {
    return mb_strwidth($string, 'UTF8');
}

function zh_cut($string, $length, $suffix = '...') {
    return mb_strimwidth($string, 0, $length, $suffix, 'UTF8');
}

function zh_filter($string) {
    return preg_replace('/\s*[a-z&().,\/ -]{2,}/i', '', $string);
}

function object2Array($d){
    if (is_object($d)){
        $d = get_object_vars($d);
    }
    if (is_array($d)){
        return array_map(__FUNCTION__, $d);
    }else{
        return $d;
    }
}

function add_link($content){
    return preg_replace('/(https?:[a-z0-9\/.-]+\.tours(?:4|for)fun\.com[a-z0-9\/.+&=# -]+)/i','<a href="$1" target="_blank">$1</a>',$content);
}

function parseToXML($htmlStr){
    $xmlStr=str_replace('<','&lt;',$htmlStr);
    $xmlStr=str_replace('>','&gt;',$xmlStr);
    $xmlStr=str_replace('"','&quot;',$xmlStr);
    $xmlStr=str_replace("'",'&apos;',$xmlStr);
    $xmlStr=str_replace("&",'&amp;',$xmlStr);
    return $xmlStr;
}


/**
 * Convert Killograms to Pounds
 *
 * @param float $kg        Weight in Killogrames
 * @param int   $precision Precision for the return value. Default 3
 *
 * @return float Weight in Pounds
 *
 * @author Gihan S <gihanshp@gmail.com>
 */
function convertKgToLb($kg, $precision = 3){
    return round($kg * 2.20462262185, $precision);
}


/**
 * Convert Pounds to Killograms
 *
 * @param float $lb        Weight in Pounds
 * @param int   $precision Precision for the return value. Default 2
 *
 * @return float Weight in Killograms
 *
 * @author Gihan S <gihanshp@gmail.com>
 */
function convertLbToKg($lb, $precision = 2){
    return round($lb/2.20462262185, $precision);
}
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

/**
 * Secure a string to avoid security issues (mainly used to solve the PCI scanner issues).
 */
function secure_string($str) {
    // Remove the contents of <script>*</script>,
    // it's actually not needed for security since strip_tags
    // already remove the tags, but the PCI scanner still finds
    // problems if we don't remove the contents of the <script> tag.
    $safe_str = preg_replace('#<script(.*?)>(.*?)</script>#is', '',  $str);
    $safe_str = preg_replace('/%3C.*%3E/', '',$safe_str);
    // The PCI scanner fails even it if sees an "alert()",
    // so we must remove that too:
    $safe_str = preg_replace('#alert\((.*?)\)#is', '',  $safe_str);

    // Now strip the tags:
    $safe_str = strip_tags($safe_str, '<br><br/><br />');

    return $safe_str;
}
/**
 * Secure a array to avoid  PCI security issue 
 */
function secure_array($data){
    if(is_array($data)){
       foreach($data as $k=>$v){
           if(preg_match('/^[a-z0-9_-]+$/i' ,$k)){
                $data[$k] = secure_array($v);               
           }else{
                unset($data[$k]);
           }
       }
    }else{
        $data = secure_string($data);
    }
   return $data ;
}

/**
 * Convert local timestamp to GMT
 * @param $timestamp
 * @return timestamp
 *
 * @author aaron.yang@toursforfun.com
 */
function local_to_gmt($timestamp){
    $now = time();
    $difference = $now - strtotime(gmdate('Y-m-d H:i:s', $now));
    return $timestamp - $difference;
}

/**
 * Convert GMT to local timestamp
 * @param $timestamp
 * @return timestamp
 *
 * @author aaron.yang@toursforfun.com
 */
function gmt_to_local($timestamp){
    $now = time();
    $difference = $now - strtotime(gmdate('Y-m-d H:i:s', $now));
    return $timestamp + $difference;
}

/**
 * Return the base CDN url:
 */
function cdn_url() {
    if (Yii::app()->request->isSecureConnection == 1) {
        return Yii::app()->params['cdnSSLUrl'];
    } else {
        return Yii::app()->params['cdnUrl'];
    }
}

function cdn_img_url() {
    return cdn_url() . '/img/';
}

function cdn_image_url() {
    return cdn_url() . '/image/';
}

function cdn_images_url() {
    return cdn_url() . '/images/';
}

function cdn_url_for_css() {
    if (Yii::app()->request->isSecureConnection == 1) {
        return Yii::app()->params['cdnCSSSSLUrl'];
    } else {
        return Yii::app()->params['cdnCSSUrl'];
    }
}

function cdn_css_url() {
    return cdn_url_for_css() . '/css/';
}

/**
 * Get CDN safe names
 *
 * @param type $name
 * @return type
 */
function getCdnSafeName($fileName) {
    $pathInfo = pathinfo($fileName);
    $name = preg_replace("/[^a-zA-Z0-9\-_]/", "", str_replace(' ', '_', trim($pathInfo['filename'])));
    // if empty, might be non ascii name. Add randome name
    if($name == ''){
        $name = uniqid();
    }
    if(isset($pathInfo['extension'])){
        return $name.'.'.$pathInfo['extension'];
    }
    return $name;
}


/**
 * Compress image quality for images >200Kb
 *
 * @param string $imageFile
 * @param string $destination
 * @return boolean
 */
function compressImage($imageFile, $destination){
    // first check image type
    $info = @getimagesize($imageFile);

    if(!$info)
        return false;

    // dupliate gif images because gif images doesn't compress
    if($info['mime'] == 'image/gif'){
        return copy($imageFile, $destination);
    }

    $widthOriginal = $info[0];
    $heightOriginal = $info[1];

    $functions = array(
        'image/png' => 'imagepng',
        'image/gif' => 'imagegif',
        'image/jpeg' => 'imagejpeg',
    );
    $functionsR = array(
        'image/png' => 'imagecreatefrompng',
        'image/gif' => 'imagecreatefromgif',
        'image/jpeg' => 'imagecreatefromjpeg',
    );

    if(!array_key_exists($info['mime'], $functions))
        return false;

    $size = filesize($imageFile)/1024;

    if($size > 2000){
        $quality = 45;
    }elseif($size > 1500){
        $quality = 50;
    }elseif($size > 1000){
        $quality = 60;
    }elseif($size > 900){
        $quality = 65;
    }elseif($size > 800){
        $quality = 70;
    }elseif($size > 700){
        $quality = 75;
    }elseif($size > 600){
        $quality = 80;
    }elseif($size > 500){
        $quality = 85;
    }elseif($size > 400){
        $quality = 87;
    }elseif($size > 300){
        $quality = 90;
    }elseif($size > 200){
        $quality = 95;
    }else{
        // no need to reduce quality
        return false;
    }

    //png image quality is 0-9
    if($info['mime'] == 'image/png'){
        $quality = floor($quality/10);
    }

//    $image = imagecreatetruecolor($widthOriginal,$heightOriginal);
//    $source=imagecreatefromfile($imageFile);
//    imagecopyresampled($image,$source,0,0,0,0,$widthOriginal,$heightOriginal,$widthOriginal,$heightOriginal);

    $image = $functionsR[$info['mime']]($imageFile);
    $return = $functions[$info['mime']]($image, $destination, $quality);
    imagedestroy($image);
    return $return;
}

/**
 *
 * @param type        $image
 * @param string|null $destination Default null. If null it will overwrite by the compressed image
 * @return boolean
 */
function compressImageForCDN($image, $destination = null){
    if(is_null($destination)){
        $destination = $image;
    }
    return compressImage($image, $destination);
}

/**
* easy image resize function
* @param $file - file name to resize
* @param $maxHeightWidth - new image width
* @param $quality - enter 1-100 (100 is best quality) default is 100
* @return boolean|resource
*/
function resizeImage($file,$maxHeightWidth = 0, $quality = 100) {

    if ($maxHeightWidth <= 0 ) return false;

    # Setting defaults and meta
    $info = @getimagesize($file);
    $image = '';
    $final_width = 0;
    $final_height = 0;
    list($width_old, $height_old) = $info;

    # Generate height or width for ratio
    if($width_old > $maxHeightWidth && $width_old > 0){
        $final_width = $maxHeightWidth;
        $ratio = $final_width  / $width_old;
        $final_height =  round($height_old * $ratio);
    }else if($height_old > $maxHeightWidth && $height_old > 0){
        $final_height = $maxHeightWidth;
        $ratio = $final_height  / $height_old;
        $final_width =  round($width_old * $ratio);
    }else{
        return false; # If height and width not set
    }

    # Loading image to memory according to type
    switch ( $info[2] ) {
      case IMAGETYPE_GIF: $image = @imagecreatefromgif($file); break;
      case IMAGETYPE_JPEG: $image = @imagecreatefromjpeg($file); break;
      case IMAGETYPE_PNG: $image = @imagecreatefrompng($file); break;
      default: return false;
    }

    # This is the resizing/resampling/transparency-preserving magic
    $image_resized = @imagecreatetruecolor( $final_width, $final_height );
    if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
      $transparency = @imagecolortransparent($image);
      if ($transparency >= 0) {
        $transparent_color = @imagecolorsforindex($image, $trnprt_indx);
        $transparency = @imagecolorallocate($image_resized, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
        @imagefill($image_resized, 0, 0, $transparency);
        @imagecolortransparent($image_resized, $transparency);
      }
      elseif ($info[2] == IMAGETYPE_PNG) {
        @imagealphablending($image_resized, false);
        $color = @imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
        @imagefill($image_resized, 0, 0, $color);
        @imagesavealpha($image_resized, true);
      }
    }
    @imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old);

    $output = $file; // if need to copy other location
    # Writing image according to type to the output destination and image quality
    switch ( $info[2] ) {
      case IMAGETYPE_GIF: @imagegif($image_resized, $output); break;
      case IMAGETYPE_JPEG: @imagejpeg($image_resized, $output, $quality); break;
      case IMAGETYPE_PNG:
        $quality = 9 - (int)((0.9*$quality)/10.0);
        @imagepng($image_resized, $output, $quality);
        break;
      default: return false;
    }
    @imagedestroy($image_resized);
    return true;
}

/**
 * solve path traversal loophole
 * @author tuzki.zhang@toursforfun.com
 * @param string
 * @return string
 */
function pathTraversalFilter($input) {
    return preg_replace('/\.{2,}/', '', $input);
}

function showStar($star){
    $star;
    $star_view = '';
    if(is_numeric($star)){
        list($int, $parse) = explode('.', $star);
    }else{
        return $star_view;
    }
    $int;
    $parse;
    for ($i = 1; $i <= $int; $i++){
        $star_view .= "<IMG src=\"http://images.tours4fun.com/image/star_full.png\">";
    }
    if($parse==5){
        $star_view .= "<IMG src=\"http://images.tours4fun.com/image/star_half.png\">";
        $dim_star = 4-$int;
    }else{
        $star_view .= "";
        $dim_star = 5-$int;}
    $dim_star;
    if($dim_star!=""){
        for ($i = 1; $i <= $dim_star; $i++){
            $star_view .= "<IMG src=\"http://images.tours4fun.com/image/star_none.png\">";
        }
    }
    return $star_view;
}

/**
 * Strip invalid chars in xml (e.g chr(17) chr(7))
 * reference: http://www.w3.org/TR/2004/REC-xml-20040204/#charsets
 * @author daniel.qiu@toursforfun.com
 * @param string   the whole xml string or xml node value
 * @return string  the valid string
 */
function stripInvalidXMLChars($string) {
    return preg_replace('/[^\x{9}\x{A}\x{D}\x{20}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', '', $string);
}

/**
 * @author tuzki.zhang@toursforfun.com 2014-3-12
 * @desc find nearby coordinates Range by location
 * @param $lng float Longitude
 * @param $lat float Latitude
 * @param $distance float KM
 * @return array(
 * 'lng' => array(
 *              array(min_lng, max_lng),
 *              array(min_lng, max_lng),
 *          )
 * 'lat' => array(min_lat, min_lat)
 *          )
 */
function nearbyCoordinatesRange($lng, $lat, $distance = 15) {
    if($lng > 180 || $lng < -180 || $lat > 90 || $lat < -90 || $distance <= 0) {
        return null;
    }
    $lat_distance_unit = 111;
    $lat_range = $distance / $lat_distance_unit;
    $lat_range_interval = array(
        $lat - $lat_range < -90 ? -90 : $lat - $lat_range,
        $lat + $lat_range > 90 ? 90 : $lat + $lat_range
    );
    $lng_distance_unit = $lat_distance_unit * cos(deg2rad($lat));
    $lng_range = $distance / $lng_distance_unit;
    $lng_range_interval = array();
    if($lng + $lng_range > 180) {
        $lng_range_interval[] = array(
            $lng - $lng_range, 180
        );
        $lng_range_interval[] = array(
            -180, $lng + $lng_range - 360
        );
    } elseif($lng - $lng_range < -180) {
        $lng_range_interval[] = array(
            $lng + $lng_range, -180
        );
        $lng_range_interval[] = array(
            360 + $lng - $lng_range, 180
        );
    } else {
        $lng_range_interval[] = array(
            $lng - $lng_range, $lng + $lng_range
        );
    }
    return array(
        'lng' => $lng_range_interval,
        'lat' => $lat_range_interval
    );
}

/**
 * @desc get distance by coordinates
 * @param float $lng longitude
 * @param float $lat latitude
 * @return integer the distance in kilometers
 */
function getDistance($lng1, $lat1, $lng2, $lat2)
{
    $earthRadius = 6367; //approximate radius of earth in meters

    /*
      Convert these degrees to radians
      to work with the formula
    */

    $lat1 = ($lat1 * pi() ) / 180;
    $lng1 = ($lng1 * pi() ) / 180;

    $lat2 = ($lat2 * pi() ) / 180;
    $lng2 = ($lng2 * pi() ) / 180;

    /*
      Using the
      Haversine formula

      http://en.wikipedia.org/wiki/Haversine_formula

      calculate the distance
    */

    $calcLongitude = $lng2 - $lng1;
    $calcLatitude = $lat2 - $lat1;
    $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);  $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
    $calculatedDistance = $earthRadius * $stepTwo;

    return round($calculatedDistance);
}
function getDatesFromRange($start, $end){
    $dates = array($start);
    while(end($dates) < $end){
        $dates[] = date('Y-m-d', strtotime(end($dates).' +1 day'));
    }
    return $dates;
}
function tep_get_distance($lat1, $lon1, $lat2, $lon2, $unit='') {
        $M =  69.09 * rad2deg(acos(sin(deg2rad(floatval($lat1))) * sin(deg2rad(floatval($lat2))) +  cos(deg2rad(floatval($lat1))) * cos(deg2rad(floatval($lat2))) * cos(deg2rad($lon1 - $lon2))));
		
		switch(strtoupper($unit))
		{
			case 'K':
				// kilometers
				$ret_m = number_format($M * 1.609344, 2, ".", "")."km";
				break;
			case 'N':
				// nautical miles
				$ret_m = number_format($M * 0.868976242, 2, ".", "")."mi";
				break;
			case 'F':
				// feet
				$ret_m = number_format($M * 5280, 2, ".", "")."f";
				break;            
			case 'I':
				// inches
				$ret_m = number_format($M * 63360, 2, ".", "")."in";
				break;            
			case 'M':
			default:
				// miles
				$ret_m = number_format($M, 2, ".", "")."mi";
				break;
		}
        return $ret_m;
}

function requestFromChina() {
    Yii::import('webeez.extensions.Ip2city');
    $ip2city = new Ip2city();
    $ip_data = $ip2city->getIpData(Yii::app()->request->userHostAddress);
    if (array_key_exists('isoCode', $ip_data) && $ip_data['isoCode'] != 'CN') {
        return false;
    }
    return true;
}

/**
 * Create directory if not exists
 * 
 * @param $path       Directory path
 * @param $permission Permission to set
 * @param $recursive  Recursively create directory
 * 
 * @return boolean true if exists or directory create succes else false
 * 
 * @author Gihan S <gihanshp@gmail.com>
 */
function createDir($path, $permission = 0644, $recursive = true)
{
    // if already exists
    if (is_dir($path) && !is_file($path)) {
        return true;
    }

    // check is writable
    if (!is_writable(dirname($path))) {
        return false;
    }

    return mkdir($path, $permission, $recursive);
}

/**
 * Get tracking Meta params to forward
 *
 * @return string Returns a query string ready to send over GET
 *
 * @author Gihan S <gihanshp@gmail.com>
 */
function getMetaParamsAsQueryString()
{
    $queryString = '';
    $params      = array(
        'utm_source', 
        'utm_medium', 
        'utm_term', 
        'utm_content', 
        'utm_campaign',
        'ref', 
        'affiliate_banner_id'
    );
    $i           = 0;
    foreach ($_GET as $key => $value) {
        if (in_array($key, $params)) {
            if ($i) {
                $queryString .= '&';
            }
            $queryString .= $key . '=' . $value;
        }
        $i++;
    }
    // forward http referer
    if (isset($_SERVER['HTTP_REFERER']) && trim($_SERVER['HTTP_REFERER'])) {
        // avoid this server
        $urlFragments = parse_url($_SERVER['HTTP_REFERER']);
        if ($urlFragments['hostname'] != $_SERVER['SERVER_NAME']) {
            if (trim($queryString)) {
                $queryString .= '&';
            }
            $queryString .= '_ref=' . urlencode(secure_string($_SERVER['HTTP_REFERER']));
        }

        // append tracking params in http_referer but not in $_GET
        $items = explode('&', urldecode($urlFragments['query']));
        foreach ($items as $item) {
            list($key, $value) = explode('=', $item);
            if (!isset($_GET[$key]) && in_array($key, $params)) {
                if (trim($queryString)) {
                    $queryString .= '&';
                }
                $queryString .= $key . '=' . urlencode($value);
            }
        }
    }
    return $queryString;
}
