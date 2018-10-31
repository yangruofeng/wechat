<?php

class gl_accountControl extends back_office_baseControl
{
    public function __construct()
    {
        parent::__construct();
        Tpl::setLayout("empty_layout");
        Tpl::output("html_title", "Gl Account List");
        Tpl::setDir("financial");
    }

    /**
     * region list
     */
    public function listOp()
    {
        showMessage("Coming Soon");

        $m = new gl_accountModel();
        $top_level_accounts = $m->getTopLevelAccounts();
        Tpl::output('gl_account_tree', $this->procHtml($top_level_accounts));
        Tpl::showPage("gl_account.list");
    }

    public function getHtmlOp($p)
    {
        $uid = intval($p['uid']);
        $m = new gl_accountModel();
        $children = $m->getChildren($uid);
        $html = $this->procHtml($children);
        return new result(true, '', $html);
    }

    /**
     * 添加地址
     */
    public function addOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $m = new gl_accountModel();
        if ($p['form_submit'] == 'ok') {
            if ($p['account_parent']) {
                $parent = $m->getRow($p['account_parent']);
            } else {
                $parent = null;
            }

            $row = $m->newRow();
            $row->account_code = $p['account_code'];
            $row->account_name = $p['account_name'];
            $row->account_parent = $p['account_parent'];
            $row->is_leaf = 1;
            $row->is_system = 0;
            $row->gl_code = $p['gl_code'];
            $row->is_gl_leaf = $p['is_gl_leaf'] ?: 0;
            foreach ((new currencyEnum())->Dictionary() as $k => $v) {
                $key = "gl_code_" . strtolower($k);
                $row[$key] = $p[$key];
            }

            if ($parent) {
                $row->account_path = $parent->account_path . '/' . $p['account_code'];
                $row->category = $parent->category;
                $row->account_level = $parent->account_level + 1;
            } else {
                $row->account_path = $p['account_code'];
                $row->category = $p['category'];
                $row->account_level = 1;
            }

            $rt = $row->insert();
            if ($rt->STS) {
                if ($parent && $parent->is_leaf) {
                    $parent->is_leaf = 0;
                    $rt=$parent->update();
                }
            }

            if ($rt->STS) {
                showMessage('Add Success', getUrl('gl_account', 'list', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                unset($p['form_submit']);
                showMessage('Add Failed', getUrl('gl_account', 'add', $p, false, BACK_OFFICE_SITE_URL));
            }
        } else {
            if (intval($p['pid'])) {
                $parent = $m->getRow($p['pid']);
                Tpl::output('parent', $parent);
            }

            Tpl::showPage("gl_account.edit");
        }
    }

    /**
     * 修改地址
     */
    public function editOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $m = new gl_accountModel();
        if ($p['form_submit'] == 'ok') {
            $row = $m->getRow($p['uid']);
            if (!$row) {
                showMessage('Data not found');
            }
            $row->account_code = $p['account_code'];
            $row->account_name = $p['account_name'];
            $row->gl_code = $p['gl_code'];
            $row->is_gl_leaf = $p['is_gl_leaf'];
            foreach ((new currencyEnum())->Dictionary() as $k => $v) {
                $key = "gl_code_" . strtolower($k);
                $row[$key] = $p[$key];
            }
            $rt = $row->update();

            if ($rt->STS) {
                showMessage('Edit Sucess', getUrl('gl_account', 'list', array(), false, BACK_OFFICE_SITE_URL));
            } else {
                showMessage('Edit Failed');
            }
        } else {
            $row = $m->getRow($p['uid']);
            if (empty($row)) {
                showMessage('Invalid Id');
            }

            if ($row->account_parent) {
                $parent = $m->getRow($row->account_parent);
                Tpl::output('parent', $parent);
            }

            Tpl::output('editing_row', $row);
            Tpl::showPage("gl_account.edit");
        }
    }

    /**
     * 删除地址
     */
    public function deleteOp()
    {
        $p = array_merge(array(), $_GET, $_POST);
        $m = new gl_accountModel();
        $row = $m->getRow($p['uid']);
        if (!$row) {
            showMessage('Data not found');
        }
        if ($row->account_parent) {
            $children_cnt = count($m->getChildren($row->account_parent));
            if ($children_cnt == 1) {
                $parent = $m->getRow($row->account_parent);
                $parent->is_leaf = 1;
                $rt = $parent->update();
                if (!$rt->STS) showMessage("Delete failed - update parent failed");
            }
        }

        $rt =$m->deleteRow($row);
        if ($rt->STS)
            showMessage("Delete success");
        else
            showMessage("Delete failed");
    }

    /**
     * 生成tree html
     * @param $tree
     * @return string
     */
    private function procHtml($tree)
    {
        $html = '';

        foreach ($tree as $t) {
            $html .= '<li class="list-group-item">';
            $html .= '<div class="input-group">';
            if (!$t['is_leaf']) {
                $html .= '<span class="input-group-btn up-away btn-away"  uid="' . $t['uid'] . '" is_child="0"><a class="btn btn-default" href="#"><i class="fa fa-chevron-circle-right"></i></a></span>';
            }
            $html .= '<input type="text" class="form-control" readonly value="' . $t['account_name'] . '">';
            $html .= '<span class="input-group-btn">';
            if (!$t['is_system']) {
                if ($t['is_leaf']) {
                    $html .= '<a class="btn btn-default" title="Delete" href="' . getUrl('gl_account', 'delete', array('uid' => $t['uid']), false, BACK_OFFICE_SITE_URL) . '"><i class="fa fa-trash-o"></i></a>';
                }
                $html .= '<a class="btn btn-default" title="Edit" href="' . getUrl('gl_account', 'edit', array('uid' => $t['uid']), false, BACK_OFFICE_SITE_URL) . '"><i class="fa fa-edit"></i></a>';
            }
            if (!$t['is_gl_leaf']) {
                $html .= '<a class="btn btn-default" title="Add" href="' . getUrl('gl_account', 'add', array('pid' => $t['uid']), false, BACK_OFFICE_SITE_URL) . '"><i class="fa fa-plus"></i></a>';
            }
            $html .= '</span></div>';
            $html .= '<ul class="list-group" style="padding-left: 50px;margin-bottom: 10px;display: none">';
            $html .= "</ul></li>";
        }
        return $html;
    }

}