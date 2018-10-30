<?php
/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2018/3/30
 * Time: 17:51
 */
class um_user_cardModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('um_user_card');
    }

    public function getListByUserID($userId) {
        $userId = qstr($userId);

        $sql = <<<SQL
select a.*, b.expire_time 
from um_user_card a
inner join common_ic_card b on b.card_no = a.card_no
where a.user_id = $userId and a.state = 1
SQL;


        return $this->reader->getRows($sql);
    }

    public function checkCardOwner($ownerUserId, $cardNo) {
        $bind_info = $this->getRow(array(
            'user_id' => $ownerUserId,
            'card_no' => $cardNo,
            'state' => 1
        ));
        if (!$bind_info)
            return false;
        else
            return true;
    }
}