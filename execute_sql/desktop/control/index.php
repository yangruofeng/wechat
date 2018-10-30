<?php

defined('InKHBuy') or exit('Access Invalid!');

class indexControl {
    public function __construct()
    {
        Tpl::setDir('home');
        Tpl::setLayout('null_layout');
    }

    public function indexOp() {
      Tpl::showpage('sub_sql');
    }

    public function submit_scriptOp()
    {
        $text = $_POST['text'];

        $execsql_model = new execute_sqlModel();
        $conn = ormYo::Conn("db_remote");
        $lines = explode(";", $text);
        foreach ($lines as $i => $line) {
            $line = preg_replace('/^\s+/', '', $line);
            $line = preg_replace('/\s+$/', '', $line);
            if ($line) {
                if (C("author") != "Demo") {  // author为Demo代表在demo上执行过，不需要再执行
                    try {
                        $rt = $conn->execute($line);
                        if (!$rt->STS) {
                            $data['state'] = false;
                            $data['message'] = $rt->MSG;
                            $data['remaining'] = join(';', array_slice($lines, $i));
                            echo json_encode($data);die;
                        }
                    } catch (Exception $ex) {
                        $data['state'] = false;
                        $data['message'] = $ex->getMessage();
                        $data['remaining'] = join(';', array_slice($lines, $i));
                        echo json_encode($data);die;
                    }
                }

                $row = $execsql_model->newRow();
                $row->src = C("author");
                $row->sql = $line;
                $row->add_time = time();
                $rt = $row->insert();
                if (!$rt->STS) {
                    $data['state'] = false;
                    $data['remaining'] = join(';', array_slice($lines, $i));
                    echo json_encode($data);die;
                }
            }
        }

        $data['state'] = true;
        $data['sql'] = $text;
        $data['add_time'] = date("Y-m-d H:i:s");
        echo json_encode($data);
    }

    public function skipOp() {
        $remote_model = new execute_sqlModel("db_remote");

        $updates = $remote_model->getRows(array(
            'uid' => array("number-between", intval($_GET['from'])+1, intval($_GET['to']))
        ));

        $conn = ormYo::Conn("db_local");

        foreach ($updates as $item) {
            $conn->execute("insert into execute_sql(`uid`,`src`,`sql`,`add_time`) values(".qstr($item['uid']).",".qstr($item['src']).",".qstr($item['sql']).",".qstr($item['add_time']).")");
        }

        $this->listOp();
    }

    public function updateOp() {
        if ($_POST) {
            $remote_model = new execute_sqlModel("db_remote");

            $updates = $remote_model->getRows(array(
                'uid' => array(">", $_POST['ver'])
            ));

            $conn = ormYo::Conn("db_local");

            foreach ($updates as $item) {
                if ($item['src'] != C('author')) {
                    try {
                        $rt = $conn->execute($item['sql']);
                        if (!$rt->STS) {
                            Tpl::output("error", $rt->MSG);
                            break;
                        }
                    } catch (Exception $ex) {
                        Tpl::output("error", $ex->getMessage());
                        break;
                    }
                }

                $conn->execute("insert into execute_sql(`uid`,`src`,`sql`,`add_time`) values(".qstr($item['uid']).",".qstr($item['src']).",".qstr($item['sql']).",".qstr($item['add_time']).")");
            }
        }

        $this->listOp();
    }

    public function listOp()
    {
        $local_model = new execute_sqlModel("db_local");
        $remote_model = new execute_sqlModel("db_remote");

        $local_info = $local_model->field("max(uid) ver")->where("1=1")->getRow();
        $updates = $remote_model->getRows(array(
            'uid' => array(">", $local_info->ver),
            'src' => array("!=", C('author'))
        ));

        Tpl::output('local_ver', $local_info->ver);
        Tpl::output('list', $updates);
        Tpl::showpage('list');
    }

}
