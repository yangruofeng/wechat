<?php

/**
 * Created by PhpStorm.
 * User: sahara
 * Date: 2017/11/1
 * Time: 11:20
 */
class core_treeModel extends tableModelBase
{
    public function __construct()
    {
        parent::__construct('core_tree');
    }

    /**
     * 添加节点
     * @param $param
     * @return result
     * @throws Exception
     */
    public function addNode($param)
    {
        $root_key = trim($param['root_key']);
        $node_text = trim($param['node_text']);
        $node_text_alias = $param['node_text_alias'];
        $pid = intval($param['pid']);
        $node_sort = intval($param['node_sort']);

        if (empty($node_text)) {
            return new result(false, 'Node cannot be empty!');
        }

        if ($pid) {
            $parent_row = $this->getRow(array('uid' => $pid, 'root_key' => $root_key));
            if (empty($parent_row)) {
                return new result(false, 'Parent region does not exist!');
            }
            $node_level = $parent_row->node_level + 1;
            $node_path = $parent_row->node_path . $pid . '@';
        } else {
            $pid = 0;
            $node_level = 1;
            $node_path = '';
        }

        $ckh_node = $this->getRow(array('root_key' => $root_key, 'node_text' => $node_text, 'node_level' => $node_level));
        if ($ckh_node) {
            return new result(false, 'Node text exists!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $row = $this->newRow();
            $row->root_key = $root_key;
            $row->node_text = $node_text;
            $row->node_text_alias = $node_text_alias;
            $row->node_path = $node_path;
            $row->node_level = $node_level;
            $row->node_sort = $node_sort;
            $row->pid = $pid;
            $row->is_leaf = 1;
            $rt = $row->insert();
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Add failed!');
            }

            if ($parent_row->is_leaf == 1) {
                $parent_row->is_leaf = 0;
                $rt_2 = $parent_row->update();
                if (!$rt_2->STS) {
                    $conn->rollback();
                    return new result(false, 'Add failed!');
                }
            }

            $conn->submitTransaction();
            return new result(true, 'Add successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }

    }

    /**
     * 编辑节点
     * @param $param
     * @return result
     * @throws Exception
     */
    public function editNode($param)
    {
        $uid = trim($param['uid']);
        $root_key = trim($param['root_key']);
        $node_text = trim($param['node_text']);
        $node_text_alias = $param['node_text_alias'];
        $node_sort = intval($param['node_sort']);

        if (empty($node_text)) {
            return new result(false, 'Node cannot be empty!');
        }

        $row = $this->getRow(array('uid' => $uid, 'root_key' => $root_key));
        if (empty($row)) {
            return new result(false, 'Invalid Id!');
        }

        $ckh_node = $this->getRow(array('uid' => array('neq', $uid), 'root_key' => $root_key, 'node_text' => $node_text, 'node_level' => $row->node_level));
        if ($ckh_node) {
            return new result(false, 'Node text exists!');
        }

        $row->node_text = $node_text;
        $row->node_text_alias = $node_text_alias;
        $row->node_sort = $node_sort;
        $rt = $row->update();
        if ($rt->STS) {
            return new result(true, 'Edit successful!');
        } else {
            return new result(false, 'Edit failed!');
        }
    }

    /**
     * 移除节点
     * @param $param
     * @return result
     * @throws Exception
     */
    public function deleteNode($param)
    {
        $uid = intval($param['uid']);
        $root_key = intval($param['root_key']);
        $row = $this->getRow(array('uid' => $uid, 'root_key' => $root_key));
        if (empty($row)) {
            return new result(false, 'Invalid Id!');
        }
        $is_parent = $this->find(array('pid' => $uid));
        if ($is_parent) {
            return new result(false, 'Cannot delete because have subcategories!');
        }

        $conn = ormYo::Conn();
        $conn->startTransaction();
        try {
            $pid = $row->pid;
            $rt = $row->delete();
            if (!$rt->STS) {
                $conn->rollback();
                return new result(false, 'Delete failed!');
            }

            $is_leaf = $this->find(array('pid' => $pid));
            if (!$is_leaf) {
                $row_parent = $this->getRow($pid);
                $row_parent->is_leaf = 1;
                $rt = $row_parent->update();
                if (!$rt->STS) {
                    $conn->rollback();
                    return new result(false, 'Delete failed!');
                }
            }

            $conn->submitTransaction();
            return new result(true, 'Delete successful!');
        } catch (Exception $ex) {
            $conn->rollback();
            return new result(false, $ex->getMessage());
        }
    }

    /**
     * 获取tree
     * @param $root_key
     * @param int $has_leaf
     * @return array
     */
    public function getTree($root_key, $has_leaf = 1)
    {
        $param = array('root_key' => $root_key);
        if (!$has_leaf) {
            $param['is_leaf'] = 0;
        }
        $tree_arr = $this->orderBy('pid asc,node_sort asc')->select($param);
        $tree = $this->makeTree($tree_arr, 0);
        return $tree;
    }

    /**
     * 生成tree
     * @param $data
     * @param $pid
     * @return array
     */
    private function makeTree($data, $pid)
    {
        $tree = array();
        foreach ($data as $k => $v) {
            if ($v['pid'] == $pid) {
                $v['child'] = $this->makeTree($data, $v['uid']);
                $tree[] = $v;
                unset($data[$k]);
            }
        }
        return $tree;
    }

    /**
     * 根据父级id获取子集
     * @param $pid
     * @param $root_key
     * @return mixed
     */
    public function getChildByPid($pid, $root_key)
    {
        $list = $this->orderBy('node_sort asc')->select(array('pid' => $pid, 'root_key' => $root_key));
        return $list;
    }

    /**
     * 获取父级及兄弟元素
     * @param $id
     * @param $root_key
     * @param array $arr
     * @return array
     */
    public function getParentAndBrotherById($id, $root_key, $arr = array())
    {
        $node = $this->find(array('uid' => $id, 'root_key' => $root_key));
        if (empty($node)) {
            return array();
        }
        $node_list = $this->orderBy('node_sort asc')->select(array('pid' => $node['pid'], 'root_key' => $root_key));
        $node_list[$node['uid']]['selected'] = 1;
        $arr[] = $node_list;
        if ($node['pid']) {
            return $this->getParentAndBrotherById($node['pid'], $root_key, $arr);
        } else {
            return array_reverse($arr);
        }
    }

    public function getFullNodeText($uid, $root_key, $full_node_arr = array())
    {
        $node = $this->find(array('uid' => $uid, 'root_key' => $root_key));
        if (!$node) {
            return '';
        } else {
            array_unshift($full_node_arr, $node['node_text']);
        }

        if ($node['pid']) {
            return $this->getFullNodeText($node['pid'], $root_key, $full_node_arr);
        } else {
            return implode(', ', $full_node_arr);
        }
    }
}