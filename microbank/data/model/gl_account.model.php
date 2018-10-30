<?php

class gl_accountModel extends tableModelBase {
    public function __construct()
    {
        parent::__construct('gl_account');
    }

    public function getAccountInfoById($uid){
        $info = $this->find(array('uid' => $uid));
        return $info;
    }

    public function getAccountList($params){
        $info = $this->getRows($params);
        return $info;
    }

    public function getIncomingStatementTotal(){
        $where = "g.category in (" . qstr(passbookTypeEnum::PROFIT) . "," . qstr(passbookTypeEnum::COST) .")";

        $sql = <<<SQL
select SUBSTRING_INDEX(account_path,'/',1) lv1, SUBSTRING_INDEX(account_path,'/',2) lv2, SUBSTRING_INDEX(account_path,'/',3) lv3, g.uid, g.account_parent, g.account_name, a.currency, sum(f.credit - f.debit) amount
from gl_account g 
left join passbook p on g.obj_guid = p.obj_guid
left join passbook_account a on p.uid = a.book_id  
left join passbook_account_flow f on a.uid = f.account_id 
where $where  
group by lv1, lv2, lv3, a.currency  
order by g.category desc 
SQL;

        $ret = $this->reader->getRows($sql);
        
        foreach ($ret as $k => $v) {
            if($v['currency'] == 'CNY' || $v['currency'] == 'THB' || $v['currency'] == 'VND'){
                unset($ret[$k]);
            }
        }

        $tree = $this->list_to_tree($ret);
        //$data = $this->getTotalAmount($tree);
        print_r($tree);
        //echo json_encode($ret);die;
        foreach ($tree as $k => $v) {

        }

        $data = array();
        foreach ($ret as $v) {
            $num = substr_count($v['lv3'], '/');
            $temp;
            switch ($num) {
                case 0:
                    # code...
                    break;
                case 1:
                    # code...
                    break;
                case 2:
                    /*if($v['currency'] && $v['amount']){
                        print_r($v);
                        $temp[$v['account_name']]['amount'][$v['currency']] = $v['amount'];
                        print_r($temp);
                    echo '----------------';
                    }*/
                    
                    
                    break;
                default:
                    # code...
                    break;
            }
            
            //$data[$v['account_name']] = '';

            //$second_temp[] = '';

            

            /*$data['amount'][$row['currency']] += $row['amount'];
            if(!$data[$row['lv1']]){
                $temp = array();
            }
            $temp[$row['account_code']]['amount'][$row['currency']] = $row['amount'];
            $data[$row['lv1']]['children'] = $temp; //三级统计
            $data[$row['lv1']]['amount'][$row['currency']] += $row['amount']; //二级统计*/
        }
        //print_r($data);
    }

    public function getTotalAmount($data){
        $usdTotal = 0;
        $khrTotal = 0;

        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $temp = $v['amount'] + $this->getTotalAmount($v['children']);
                $v['amount'] = $temp;
                if($v['currency'] == 'USD'){
                    $usdTotal += $temp;
                }
                if($v['currency'] == 'KHR'){
                    $khrTotal += $temp;
                }
            }
        }else{
            return 0;
        }

        return $data;
        

        /*const getNum = (data) => {
            let num = 0;
            if (data instanceof Array) {
              data.forEach(v => {
                let temp = (+(v.amount || 0) + getNum(v.children));
                v.amount = temp;
                num += temp;
              });
            } else {
              return 0;
            }
            return num;
        }*/




        /*$data;
        $usdTotal = 0;
        $khrTotal = 0;
        foreach ($list as $k => $v) {
            if($v['children'] && count($v['children']) > 0){
                if($v['currency'] == 'USD'){
                    $usdTotal += $v['amount'] ? $v['amount'] : 0;
                }
                if($v['currency'] == 'KHR'){
                    $khrTotal += $v['amount']? $v['amount'] : 0;
                }
                $this->getTotalAmount($v['children'], $v['account_name']);
            }else{
                if($v['currency'] == 'USD'){
                    $usdTotal += $v['amount'] ? $v['amount'] : 0;
                }
                if($v['currency'] == 'KHR'){
                    $khrTotal += $v['amount']? $v['amount'] : 0;
                }
            }
            $data[$v['account_name']]['amount']['USD'] = $usdTotal;
            $data[$v['account_name']]['amount']['KHR'] = $khrTotal;
        }
        if($el){
            $data[$el]['amount']['USD'] = $usdTotal;
            $data[$el]['amount']['KHR'] = $khrTotal;
        }
        return $data;*/
    }


    /*const getTotal = (data,el) => {
        let numTotal = 0
        data.forEach((item,index) => {
          if (item.children && item.children.length>0) {
            numTotal += isNaN(item.amount)?0:Number(item.amount)
            getTotal(item.children,item)
          }else{
            numTotal += isNaN(item.amount)?0:Number(item.amount)
          }
        })
        if(el){
          el.amount = numTotal
        }
    }*/







    function list_to_tree($list, $pk='uid', $pid = 'account_parent', $k_name = 'account_name', $child = 'children', $root = 0) {
        //创建Tree
        $tree = array();
        
        if (is_array($list)) {
            //创建基于主键的数组引用
            $refer = array();
            
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] = &$list[$key];
            }
            
            foreach ($list as $key => $data) {
                //判断是否存在parent
                $parantId = $data[$pid];
                
                if ($root == $parantId) {
                    $tree[] = &$list[$key];
                } else {
                    if (isset($refer[$parantId])) {
                        $parent = &$refer[$parantId];
                        $parent[$child][] = &$list[$key];
                    }
                }
            }
        }
        
        return $tree;
    }

    public function getIncomingStatementTotal1(){
        $where = "g.category = " . qstr(passbookTypeEnum::PROFIT);
        $sql = <<<SQL
select SUBSTRING_INDEX(account_path,'/',1) lv1, SUBSTRING_INDEX(account_path,'/',2) lv2,g.account_code, g.account_name, a.currency, sum(f.credit - f.debit) amount
from gl_account g 
right join passbook p on g.obj_guid = p.obj_guid
right join passbook_account a on p.uid = a.book_id  
right join passbook_account_flow f on a.uid = f.account_id 
where $where 
group by lv1, lv2,a.currency 
having amount != 0
SQL;

        $ret = $this->reader->getRows($sql);
        $data = array();
        foreach ($ret as $row) {
            $data['amount'][$row['currency']] += $row['amount'];
            if(!$data[$row['lv1']]){
                $temp = array();
            }
            $temp[$row['account_code']]['amount'][$row['currency']] = $row['amount'];
            $data[$row['lv1']]['children'] = $temp; //三级统计
            $data[$row['lv1']]['amount'][$row['currency']] += $row['amount']; //二级统计
        }
        
        return $data;
    }

    public function getChildren($id) {
        return $this->getRows(array('account_parent' => $id));
    }

    public function getTopLevelAccounts() {
        return $this->getChildren(0);
    }

    public function getIncomentStatementFirstItems(){
        $where = "g.category in (" . qstr(passbookTypeEnum::PROFIT) . ',' . qstr(passbookTypeEnum::COST) .') and g.account_parent = 0';
        $sql = <<<SQL
select g.* from gl_account g 
where $where 
order by g.category desc
SQL;

        $ret = $this->reader->getRows($sql);
        return $ret;
    }

    public function getIncomentStatementChild($parent_id, $category){

    }
}