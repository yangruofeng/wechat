<?php

class docControl {

    private function searchApiDocuments($path, &$dict) {
        foreach (scandir($path) as $d) {
            if (substr($d, 0, 1) === ".") continue;
            if (is_dir("$path/$d")) {
                $children = array();
                $this->searchApiDocuments("$path/$d", $children);
                $dict[$d] = $children;
            } else if (endWith($d, ".php")) {
                $dict[$d] = 1;
            }
        }
    }

    public function listOp() {
        $root_tree = array();
        $this->searchApiDocuments(PROJECT_ROOT . "/apis/", $root_tree);
        Tpl::output('root_tree', $root_tree);
        Tpl::showPage("list", "test_layout", "test");
    }

    public function testOp() {
        $apiPath = $_GET['api'] ;
        if (@include_once(PROJECT_ROOT . "/apis/" . $apiPath . ".php")) {
            $segments = explode('/', $apiPath);
            $className = preg_replace_callback('/\.(\w)/', function($m) {
                    return strtoupper($m[1]);
            }, $segments[count($segments) - 1]) . "ApiDocument";
            Tpl::output('doc', new $className());
            Tpl::showPage("index", "test_layout", "test");
        }
    }
}