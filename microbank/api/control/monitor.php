<?php

class monitorControl {
    public function getOp()
    {
        $params = array_merge(array(), $_GET, $_POST);
        return (new monitorClass())->getMonitor($params);
    }
}