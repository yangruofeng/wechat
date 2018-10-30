<?php


class test_ic_cardControl
{
    public function parse_card_dataOp() {
        return icCardClass::parseCardData($_GET['data']);
    }

    public function confirmOp() {
        return icCardClass::confirm($_GET['card_no'], $_GET['card_data']);
    }
}