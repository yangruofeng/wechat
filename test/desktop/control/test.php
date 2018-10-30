<?php

class testControl
{
    public function test1Op()
    {
        var_dump($_SESSION);
        echo("<br/>");
        echo(session_id());
        setSessionVar("test1", "123");
        echo(" After Set ");
        echo(session_id());
        echo("<br/>");
        var_dump($_SESSION);
    }

    public function test2Op()
    {
        var_dump($_SESSION);
    }

    public function test3Op()
    {
        var_dump($_SESSION);
        echo("<br/>");
        echo(session_id());
        $_SESSION['test3'] = "aaa";
        echo(" After Set ");
        echo(session_id());
        echo("<br/>");
        var_dump($_SESSION);
    }

    /**
     * Add Seven
     */
    public function iosTestOp()
    {
        $key = trim($_REQUEST['key']);
        $m_test = M('test');
        $row = $m_test->find(array('key' => $key));
        if (!$row) {
            return new result(false, 'Invalid Key!');
        }
        $data = array(
            'value' => $row['value']
        );
        return new result(true, '', $data);
    }

    public function iosAddTestOp()
    {
        $key = trim($_REQUEST['key']);
        $value = trim($_REQUEST['value']);
        if (empty($key) || empty($value)) {
            return new result(false, 'Param error!');
        }

        $m_test = M('test');
        $row = $m_test->getRow(array('key' => $key));
        if ($row) {
            return new result(false, 'Key Existed!');
        }

        $row = $m_test->newRow();
        $row->key = $key;
        $row->value = $value;
        $row->update_time = Now();
        $rt = $row->insert();
        return $rt;
    }

    public function iosEditTestOp()
    {
        $key = trim($_REQUEST['key']);
        $value = trim($_REQUEST['value']);
        $m_test = M('test');
        $row = $m_test->getRow(array('key' => $key));
        if (!$row) {
            return new result(false, 'Invalid Key!');
        }
        $row->value = $value;
        $row->update_time = Now();
        $rt = $row->update();
        return $rt;
    }


    public function testMultiFileOp()
    {
        Tpl::setDir('test');
        if ($_POST['submit_form'] == 'ok') {
            print_r($_FILES);
        }

        Tpl::showPage('multi.file');
    }

    public function testAjaxOp()
    {
        Tpl::setDir('test');
        Tpl::showPage('test.ajax');
    }

    public function ajaxHandleOp()
    {
        $data = array(
            'a' => 1,
            'b' => 2,
            'uid' => $_POST['uid']
        );

        echo json_encode($data);
        exit;
//        return $data;
    }

    public function formatNumberAsSpellOp() {
        echo formatNumberAsSpell(123.45);
        echo "<br/>";
        if (class_exists('NumberFormatter')) {
            echo "true";
        } else {
            echo "false";
        };
    }
}