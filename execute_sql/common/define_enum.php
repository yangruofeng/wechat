<?php

defined('InKHBuy') or exit('Access Invalid!');

class gameStateEnum extends Enum {
    const CANCELLED = 0;
    const TEMPORARY = 10;
    const STARTED = 20;
    const ENDED = 30;
    const EXPIRED = 31;
}