<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 10/23/2015
 * Time: 10:24 AM
 */
class tools{
    /**
     *
     * @todo 创建分页
     * @param int $total_rows
     *        	总记录数
     * @param int $page
     *        	第几页
     * @param int $page_size
     *        	每页显示的记录数
     * @param int $show_links_num
     *        	显示多少个分页链接
     *
     * @return array
     * @author Hugo $
     */
    public static function build_page($total_rows, $page = 1, $page_size = 20, $show_links_num = 10) {
        $arr = array ();
        $max_page = ceil ( $total_rows / $page_size );

        $arr ['min_page'] = 1;
        $arr ['max_page'] = $max_page;
        $arr ['current_page'] = $page;
        $arr ['total_rows'] = $total_rows;
        $arr ['page_size'] = $page_size;
        $arr ['show_links_num'] = $show_links_num;
        $show_links_num += 1;

        if ($page > 1) {
            $arr ['previous'] = $page - 1;
        } else {
            $arr ['previous'] = 1;
        }

        if ($page < $max_page) {
            $arr ['next'] = $page + 1;
        } else {
            $arr ['next'] = $max_page;
        }

        if ($max_page < $show_links_num) {
            $arr ['links'] = range ( 1, $max_page );
        } else {

            $mid = floor ( $show_links_num / 2 );
            if ($page < $mid) {
                $arr ['links'] = range ( 1, $show_links_num );
            } else {
                $a = $page - $mid + 1;
                $b = $page + $mid;
                if ($b > $max_page) {
                    $a = $max_page - $show_links_num + 1;
                    $b = $max_page;
                }
                $arr ['links'] = range ( $a, $b );
            }
        }
        return $arr;
    }

    public static function paginate_bootstrap($page_list, $url = '', $js = '',$set_id = 'p_1') {
        if (( int ) $page_list ['total_rows'] > ( int ) $page_list ['page_size']) {
            $str = '<nav><ul class="pagination">';

            if ($page_list ['max_page'] > 10 && $page_list ['current_page'] > 5) {
                $str .= '<li class= "'.$set_id.'"><a href="' . $url . '?&page=1" ' . $js . '>首页</a></li>';
            }
            if (! ($page_list ['current_page'] > 1)) {
                $str .= '<li class="disabled"><span>上一页</span></li>';
            } else {
                $str .= '<li class= "'.$set_id.'"><a href="' . $url . '?&page=' . ($page_list ['current_page'] - 1) . '" ' . $js . '>上一页</a></li>';
            }
            foreach ( $page_list ['links'] as $k => $v ) {
                if ($v == $page_list ['current_page']) {
                    $str .= '<li class="active"><a href="' . $url . '?&page=' . $v . '" ' . $js . '>' . $v . '</a></li>';
                } else {
                    $str .= '<li class= "'.$set_id.'"><a href="' . $url . '?&page=' . $v . '" ' . $js . '>' . $v . '</a></li>';
                }
            }
            if ($page_list ['current_page'] < $page_list ['max_page']) {
                $str .= '<li class= "'.$set_id.'"><a href="' . $url . '?&page=' . ($page_list ['current_page'] + 1) . '" ' . $js . '>下一页</a></li>';
            } else {
                $str .= '<li class="disabled"><span>下一页</span></li>';
            }
            if ($page_list ['max_page'] > 10) {
                if (($page_list ['links'] [($n - 1)] < $page_list ['max_page']) && ($page_list ['current_page'] < ($page_list ['max_page'] - 4))) {
                    $str .= '<li class= "'.$set_id.'"><a href="' . $url . '?&page=' . $page_list ['max_page'] . '" ' . $js . '>末页</a></li>';
                }
            }
            $str .= '</ul></nav>';
            echo $str;
        }
    }


    public static function getCountryCodeOptions($value = ''){
        $str="";
        $arr=array("855","84","86","66");
        foreach($arr as $k){
            if($value==$k){
                $str.='<option value="'.$k.'" selected>+'.$k.'</option>';
            }else{
                $str.='<option value="'.$k.'">+'.$k.'</option>';
            }
        }
        return $str;
    }
    public static function getCommunicateTool(){
        $str='<option value="1">FaceBook</option>';
        $str.='<option value="2">Wechat</option>';
        $str.='<option value="3">Line</option>';
        $str.='<option value="4">Phone</option>';
        return $str;
    }
    public static function getMemberNotice(){
        return "<strong>Test</strong>";
    }
    public static function compareFloat($a,$b,$esp=0.000001){
        if(abs($a-$b) < $esp) {
            return true;
        }
        return false;
    }
    public static function getSystemRemainKeywords(){
        return array(
            "root","admin","super","fuck","shit","administrator","officer","khbuy","mother","father","children","god","china","cambodia"
        );
    }
    public static function getFormatPhone($country_code,$phone_number){
        $arr=array();
        $arr['STS']=true;
        $phone_number=preg_replace("@^[0]+@","",trim($phone_number));
        $phone = self::separatePhone($phone_number);
        $phone_number = $phone[1];
        $country_code = $country_code?:$phone[0];
       /* $phone_number=trim($phone_number);
        if(!preg_match("@^\d{5,20}$@",$phone_number)){
            $arr['STS']=false;
            $arr['MSG']="Invalid Phone Format!";
            return $arr;
        }*/
        $phone_code=trim($country_code?:"855");
        $contact_phone="+".$phone_code.trim($phone_number);
        $arr['country_code']=trim($country_code);
        $arr['phone_number']=trim($phone_number);
        $arr['contact_phone']=$contact_phone;
        return $arr;
    }
    //把区分和电话号分开，如 +855906338 分开成 855,906388
    public static function separatePhone($phone){
        if(!$phone){
            return array("","");
        }
        if(substr($phone,0,1)=="+"){
            $arr=array("+855","+84","+86","+66");
            foreach($arr as $r){
                if(strlen($phone)>strlen($r) && substr($phone,0,strlen($r))==$r){
                    return array(substr($r,1),substr($phone,strlen($r)));
                }
            }
            return array("",substr($phone,1));
        }else{
            return array("",$phone);
        }
    }






}