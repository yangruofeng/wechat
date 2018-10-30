<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/1
 * Time: 11:20
 */
class core_dictionaryModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('core_dictionary');
    }

    public function getDictionary($dict_key)
    {
        return $this->find(array('dict_key' => $dict_key));
    }

    /**
     * 更新字典
     * @param $dict_key
     * @param $dict_value
     * @return ormResult|result
     * @throws Exception
     */
    public function updateDictionary($dict_key, $dict_value)
    {
        $row = $this->getRow(array('dict_key' => $dict_key));
        if (!$row) {
            $row = $this->newRow();
            $row->dict_key = $dict_key;
            $row->dict_value = $dict_value;
            $rt = $row->insert();
            if ($rt->STS) {
                $rt = new result(true, 'Save Successful!');
            } else {
                $rt = new result(true, 'Save failed--' . $rt->MSG);
            }
        } elseif ($row->dict_value != $dict_value) {
            $row->dict_value = $dict_value;
            $rt = $row->update();
            if ($rt->STS) {
                $rt = new result(true, 'Save Successful!');
            } else {
                $rt = new result(true, 'Save failed--' . $rt->MSG);
            }
        } else {
            $rt = new result(true, 'No Change!');
        }
        return $rt;
    }

    public function getCloseSystemDictionary()
    {
        $arr = array(
            dictionaryKeyEnum::SYSTEM_CLOSE_MEMBER_APP,
            dictionaryKeyEnum::SYSTEM_CLOSE_CREDIT_OFFICER_APP,
            dictionaryKeyEnum::SYSTEM_CLOSE_CONSOLE,
            dictionaryKeyEnum::SYSTEM_CLOSE_OPERATOR,
            dictionaryKeyEnum::SYSTEM_CLOSE_BRANCH_MANAGER,
            dictionaryKeyEnum::SYSTEM_CLOSE_COUNTER,
        );
        $str = join(",", $arr);
        $keys = "'" . str_replace(",", "','", $str) . "'";
        $sql = "select * from core_dictionary where dict_key in($keys);";
        return $this->reader->getRows($sql);
    }

    public function getDictValue($dict_key)
    {
        $setting = $this->find(array(
            'dict_key' => $dict_key,
        ));
        if ($setting) {
            $val = @my_json_decode($setting['dict_value']);
        } else {
            $val = array();
        }
        return $val;
    }

}