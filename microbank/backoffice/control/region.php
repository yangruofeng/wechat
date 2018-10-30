<?php

class regionControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Region List");
        Tpl::setDir("region");
    }

    /**
     * region list
     */
    public function listOp()
    {
        $m_core_tree = M('core_tree');
        $region_arr = $m_core_tree->getChildByPid(0, 'region');
        Tpl::output('region_tree', $this->procHtml($region_arr));
        Tpl::showPage("region.list");
    }

    public function getRegionHtmlOp($p)
    {
        $uid = intval($p['uid']);
        $m_core_tree = M('core_tree');
        $region_arr = $m_core_tree->getChildByPid($uid, 'region');
        $html = $this->procHtml($region_arr);
        return new result(true, '', $html);
    }

    /**
     * 添加地址
     */
    public function addOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $m_core_tree = M('core_tree');
        if ($p['form_submit'] == 'ok') {
            $lang_list = $this->getLangWithoutDefault();
            $text_alias = array('en' => trim($p['region_text']));
            foreach ($lang_list as $key => $lang) {
                $text_alias[$key] = trim($p[$key]);
            }
            $param = array(
                'root_key' => 'region',
                'node_text' => trim($p['region_text']),
                'node_text_alias' => my_json_encode($text_alias),
                'pid' => intval($p['pid']),
                'node_sort' => intval($p['node_sort']),
            );
            $rt = $m_core_tree->addNode($param);

            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('region', 'list', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage($rt->MSG, getUrl('region', 'add', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {

            if (intval($p['pid'])) {
                $parent = $m_core_tree->find(array('uid' => intval($p['pid'])));
                Tpl::output('parent', $parent);
            } else {
                Tpl::output('parent', array('uid' => 0));
            }

            Tpl::output("lang_list", $this->getLangWithoutDefault());
            Tpl::showPage("region.add");
        }
    }

    /**
     * 修改地址
     */
    public function editOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $m_core_tree = M('core_tree');
        if ($p['form_submit'] == 'ok') {
            $lang_list = $this->getLangWithoutDefault();
            $text_alias = array('en' => trim($p['region_text']));
            foreach ($lang_list as $key => $lang) {
                $text_alias[$key] = trim($p[$key]);
            }
            $param = array(
                'uid' => intval($p['uid']),
                'root_key' => 'region',
                'node_text' => trim($p['region_text']),
                'node_text_alias' => my_json_encode($text_alias),
                'node_sort' => intval($p['node_sort']),
            );
            $rt = $m_core_tree->editNode($param);

            if ($rt->STS) {
                showMessage($rt->MSG, getUrl('region', 'list', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                showMessage($rt->MSG);
            }
        } else {
            $uid = intval($p['uid']);
            $region_row = $m_core_tree->find(array('uid' => $uid));
            if (empty($region_row)) {
                showMessage('Invalid Id');
            }

            if ($region_row['pid']) {
                $parent = $m_core_tree->find(array('uid' => $region_row['pid']));
                Tpl::output('parent', $parent);
            } else {
                Tpl::output('parent', array('uid' => 0));
            }
            if ($region_row['pid']) {
                $region_parent = $m_core_tree->find(array('uid' => $region_row['pid']));
                $region_row['parent'] = $region_parent['node_text'];
            } else {
                $region_row['parent'] = 'Top-level';
            }

            Tpl::output('region_row', $region_row);
            Tpl::output("lang_list", $this->getLangWithoutDefault());
            Tpl::showPage("region.edit");
        }
    }

    /**
     * 删除地址
     */
    public function deleteOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $m_core_tree = M('core_tree');
        $param = array(
            'uid' => intval($p['uid']),
            'root_key' => 'region',
        );
        $rt = $m_core_tree->deleteNode($param);
        showMessage($rt->MSG);
    }

    /**
     * 生成tree html
     * @param $tree
     * @param string $type
     * @return string
     */
    private function procHtml($tree, $type = 'ul')
    {
        $html = '';
        if ($type == 'option') {
            foreach ($tree as $t) {
                $html .= "<option value='{$t['uid']}'>";
                for ($i = 0; $i < $t['node_level']; ++$i) {
                    $html .= "&nbsp;&nbsp;&nbsp;";
                }
                $html .= "{$t['node_text']}</option>";
                if ($t['child']) {
                    $html .= $this->procHtml($t['child'], 'option');
                }
            }
        } else {
            foreach ($tree as $t) {
                $html .= '<li class="list-group-item">';
                $html .= '<div class="input-group">';
                if (!$t['is_leaf']) {
                    $html .= '<span class="input-group-btn up-away btn-away"  uid="' . $t['uid'] . '" is_child="0"><a class="btn btn-default" href="#"><i class="fa fa-chevron-circle-right"></i></a></span>';
                }
                $html .= '<input type="text" class="form-control" readonly value="' . $t['node_text'] . '">';
                $html .= '<span class="input-group-btn">';
                if ($t['is_leaf']) {
                    $html .= '<a class="btn btn-default" title="Delete" href="' . getUrl('region', 'delete', array('uid' => $t['uid']), false, BACK_OFFICE_SITE_URL) . '"><i class="fa fa-trash-o"></i></a>';
                }
                $html .= '<a class="btn btn-default" title="Edit" href="' . getUrl('region', 'edit', array('uid' => $t['uid']), false, BACK_OFFICE_SITE_URL) . '"><i class="fa fa-edit"></i></a>';
                $html .= '<a class="btn btn-default" title="Add" href="' . getUrl('region', 'add', array('pid' => $t['uid']), false, BACK_OFFICE_SITE_URL) . '"><i class="fa fa-plus"></i></a>';
                $html .= '</span></div>';
                $html .= '<ul class="list-group" style="padding-left: 50px;margin-bottom: 10px;display: none">';
                $html .= "</ul></li>";
            }
        }
        return $html;
    }

    /**
     * 获取地址选项
     * @param $p
     * @return array
     */
    public function getAreaListOp($p)
    {
        $pid = intval($p['uid']);
        $m_core_tree = M('core_tree');
        $list = $m_core_tree->getChildByPid($pid, 'region');
        return array('list' => $list);
    }


/**************************************以下代码未用***************************/
    /**
     * 导入商城tree
     */
    public function copyTreeOp()
    {
        return;
        $m_core_tree_1 = M('core_tree_1');
        $m_core_tree = M('core_tree');
        $tree_list = $m_core_tree_1->select(array('pid' => 3));
        $conn = ormYo::Conn();
        $conn->startTransaction();
        foreach ($tree_list as $tree) {
            $row = $m_core_tree->newRow();
            $row->root_key = 'region';
            $row->node_text = $tree['node_text'];
            $row->node_text_alias = my_json_encode(array('en' => $tree['node_text'], 'zh_cn' => '', 'kh' => $tree['node_text_local']));
            $row->node_path = '';
            $row->node_level = 1;
            $row->node_sort = 0;
            $row->pid = 0;
            $row->is_leaf = 0;
            $rt_1 = $row->insert();
            if (!$rt_1->STS) {
                $conn->rollback();
                var_dump($rt_1->MSG);
                return;
            }
            $pid = $rt_1->AUTO_ID;
            $tree_list_1 = $m_core_tree_1->select(array('pid' => $tree['uid']));
            foreach ($tree_list_1 as $tree_1) {
                $row = $m_core_tree->newRow();
                $row->root_key = 'region';
                $row->node_text = $tree_1['node_text'];
                $row->node_text_alias = my_json_encode(array('en' => $tree_1['node_text'], 'zh_cn' => '', 'kh' => $tree_1['node_text_local']));
                $row->node_path = $pid . '@';
                $row->node_level = 2;
                $row->node_sort = 0;
                $row->pid = $pid;
                $row->is_leaf = 0;
                $rt_2 = $row->insert();
                if (!$rt_2->STS) {
                    $conn->rollback();
                    var_dump($rt_2->MSG);
                    return;
                }
            }
        }
        $conn->submitTransaction();
        var_dump('Successful!');
    }

    /**
     * 文件导入
     * @return result|void
     */
    public function importAddressExcelOp()
    {
        return;
        $fp = _UPLOAD_ . "/default/address.xlsx";
        var_dump(Now());
        $ret = toolExcel::readExcelToArray($fp);
        var_dump(Now());
        if (!$ret->STS) {
            return $ret;
        }
        var_dump($ret->STS);
        $data = $ret->DATA;
        $count = count($data);
        if ($count > 1) {
            $m = M("core_tree_kh");
            //先删除已经导入的记录
            $conn = ormYo::Conn();
            $conn->startTransaction();
            for ($i = 1; $i < $count; $i++) {
                $tr = $data[$i];
                if (empty($tr[0])) break;
                $row = $m->newRow();
                $row->province_id = $tr[0];
                $row->province = $tr[1];
                $row->district_id = $tr[2];
                $row->district = $tr[3];
                $row->commune_id = $tr[4];
                $row->commune = $tr[5];
                $row->villgis_id = $tr[6];
                $row->villgis = $tr[7];
                $rt = $row->insert();
                if (!$rt->STS) {
                    $conn->rollback();
                    var_dump('Failed:ID:' . $tr[6] . ' --- ' . $rt->MSG);
                    return;
                }
            }
            $conn->submitTransaction();
            var_dump("Import Success!");
        }
    }

    public function insertAddress_1Op()
    {
        return;
        $r = new ormReader();
        $sql_1 = "select province_id,province from core_tree_2 group by province_id";
        $sql_2 = "select province_id,province from core_tree_kh group by province_id";

        $province_list = $r->getRows($sql_1);
        $province_kh_list = $r->getRows($sql_2);
        $province_kh_list = resetArrayKey($province_kh_list, 'province_id');

        $m_core_tree = M('core_tree');
        $conn = ormYo::Conn();
        $conn->startTransaction();
        foreach ($province_list as $province) {
            $province_kh = $province_kh_list[$province['province_id']];
            $row = $m_core_tree->newRow();
            $row->root_key = 'region';
            $row->node_text = $province['province'];
            $row->node_text_alias = my_json_encode(array('en' => $province['province'], 'zh_cn' => '', 'kh' => $province_kh['province']));
            $row->node_path = '';
            $row->node_level = 1;
            $row->node_sort = 0;
            $row->pid = 0;
            $row->is_leaf = 0;
            $row->key = $province['province_id'];
            $rt_1 = $row->insert();
            if (!$rt_1->STS) {
                $conn->rollback();
                var_dump($rt_1->MSG);
                return;
            }
        }

        $conn->submitTransaction();
        var_dump("insert address 1 successful!");
    }

    public function insertAddress_2Op()
    {
        return;
        $r = new ormReader();
        $sql_1 = "select district_id,district,province_id from core_tree_2 group by district_id";
        $sql_2 = "select district_id,district from core_tree_kh group by district_id";
        $sql_3 = "select `key`,uid from core_tree where node_level = 1";

        $district_list = $r->getRows($sql_1);
        $district_kh_list = $r->getRows($sql_2);
        $district_kh_list = resetArrayKey($district_kh_list, 'district_id');

        $address_1 = $r->getRows($sql_3);
        $address_1 = resetArrayKey($address_1, 'key');

        $m_core_tree = M('core_tree');
        $conn = ormYo::Conn();
        $conn->startTransaction();
        foreach ($district_list as $district) {
            $district_kh = $district_kh_list[$district['district_id']];
            $pid = $address_1[$district['province_id']]['uid'];
            $row = $m_core_tree->newRow();
            $row->root_key = 'region';
            $row->node_text = $district['district'];
            $row->node_text_alias = my_json_encode(array('en' => $district['district'], 'zh_cn' => '', 'kh' => $district_kh['district']));
            $row->node_path = $pid . '@';
            $row->node_level = 2;
            $row->node_sort = 0;
            $row->pid = $pid;
            $row->is_leaf = 0;
            $row->key = $district['district_id'];
            $rt_1 = $row->insert();
            if (!$rt_1->STS) {
                $conn->rollback();
                var_dump($rt_1->MSG);
                return;
            }
        }

        $conn->submitTransaction();
        var_dump("insert address 2 successful!");
    }

    public function insertAddress_3Op()
    {
        return;
        $r = new ormReader();
        $sql_1 = "select commune_id,commune,district_id from core_tree_2 group by commune_id";
        $sql_2 = "select commune_id,commune from core_tree_kh group by commune_id";
        $sql_3 = "select `key`,uid,node_path from core_tree where node_level = 2";

        $commune_list = $r->getRows($sql_1);
        $commune_kh_list = $r->getRows($sql_2);
        $commune_kh_list = resetArrayKey($commune_kh_list, 'commune_id');

        $address_2 = $r->getRows($sql_3);
        $address_2 = resetArrayKey($address_2, 'key');

        $m_core_tree = M('core_tree');
        $conn = ormYo::Conn();
        $conn->startTransaction();
        foreach ($commune_list as $commune) {
            $commune_kh = $commune_kh_list[$commune['commune_id']];
            $pid = $address_2[$commune['district_id']]['uid'];
            $node_path = $address_2[$commune['district_id']]['node_path'];
            $row = $m_core_tree->newRow();
            $row->root_key = 'region';
            $row->node_text = $commune['commune'];
            $row->node_text_alias = my_json_encode(array('en' => $commune['commune'], 'zh_cn' => '', 'kh' => $commune_kh['commune']));
            $row->node_path = $node_path . $pid . '@';
            $row->node_level = 3;
            $row->node_sort = 0;
            $row->pid = $pid;
            $row->is_leaf = 0;
            $row->key = $commune['commune_id'];
            $rt_1 = $row->insert();
            if (!$rt_1->STS) {
                $conn->rollback();
                var_dump($rt_1->MSG);
                return;
            }
        }

        $conn->submitTransaction();
        var_dump("insert address 3 successful!");
    }

    public function insertAddress_4Op()
    {
        return;
        $r = new ormReader();
        $sql_1 = "select uid,villgis,commune_id from core_tree_2 WHERE is_insert = 0 limit 0,1000";
        $sql_2 = "select villgis_id,villgis from core_tree_kh WHERE is_insert = 0 limit 0,1000";
        $sql_3 = "select `key`,uid,node_path from core_tree where node_level = 3";

        $villgis_list = $r->getRows($sql_1);
        $villgis_kh_list = $r->getRows($sql_2);
        $villgis_kh_list = resetArrayKey($villgis_kh_list, 'villgis_id');

        $address_3 = $r->getRows($sql_3);
        $address_3 = resetArrayKey($address_3, 'key');

        $m_core_tree = M('core_tree');
        $m_core_tree_2 = M('core_tree_2');
        $m_core_tree_kh = M('core_tree_kh');
        $conn = ormYo::Conn();
        $conn->startTransaction();
        foreach ($villgis_list as $villgis) {
            $villgis_kh = $villgis_kh_list[$villgis['villgis_id']];
            $pid = $address_3[$villgis['commune_id']]['uid'];
            $node_path = $address_3[$villgis['commune_id']]['node_path'];
            $row = $m_core_tree->newRow();
            $row->root_key = 'region';
            $row->node_text = $villgis['villgis'];
            $row->node_text_alias = my_json_encode(array('en' => $villgis['villgis'], 'zh_cn' => '', 'kh' => $villgis_kh['villgis']));
            $row->node_path = $node_path . $pid . '@';
            $row->node_level = 4;
            $row->node_sort = 0;
            $row->pid = $pid;
            $row->is_leaf = 0;
            $rt_1 = $row->insert();
            if (!$rt_1->STS) {
                $conn->rollback();
                var_dump($rt_1->MSG);
                return;
            }

            $rt_2 = $m_core_tree_2->update(array('uid' => $villgis['uid'], 'is_insert' => 1));
            if (!$rt_2->STS) {
                $conn->rollback();
                var_dump($rt_2->MSG);
                return;
            }

            $rt_3 = $m_core_tree_kh->update(array('uid' => $villgis['uid'], 'is_insert' => 1));
            if (!$rt_3->STS) {
                $conn->rollback();
                var_dump($rt_3->MSG);
                return;
            }


        }

        $conn->submitTransaction();
        var_dump("insert address 4 successful!" . $villgis['uid']);
    }

    public function updateAddress_3Op()
    {
        return;
        $r = new ormReader();;
        $sql_2 = "select commune from core_tree_kh group by commune_id";
        $sql = "select uid,node_text_alias from core_tree where node_level = 3";

        $commune_kh_list = $r->getRows($sql_2);
        $tree_3 = $r->getRows($sql);

        $m_core_tree = M('core_tree');
        $conn = ormYo::Conn();
        $conn->startTransaction();
        foreach ($tree_3 as $key => $tree) {
            $commune_kh = $commune_kh_list[$key]['commune'];
            $node_text_alias = my_json_decode($tree['node_text_alias']);
            $node_text_alias['kh'] = $commune_kh;
            $node_text_alias = my_json_encode($node_text_alias);
            $rt_1 = $m_core_tree->update(array('uid' => $tree['uid'], 'node_text_alias' => $node_text_alias));
            if (!$rt_1->STS) {
                $conn->rollback();
                var_dump($rt_1->MSG);
                return;
            }
        }

        $conn->submitTransaction();
        var_dump("update address 3 successful!");
    }
}