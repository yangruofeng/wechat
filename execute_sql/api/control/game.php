<?php

class gameControl extends control {
    public function startOp() {
        $params = array_merge(array(), $_GET, $_POST);

        $member_info = $_SESSION['game_dig_member_info'];
        if (!$member_info) {
            if (C("debug")) {
                // For test
                $params['member_id'] = 1;
                $params['currency'] = 'USD';
            } else {
                return new result(false, "No session", null, errorCodes::SESSION_EXPIRED);
            }
        } else {
            $params['member_id'] = $member_info['member_id'];
            $params['currency'] = $member_info['currency'];
        }

        $rt = dig_gameClass::create($params);
        if (!$rt->STS) return $rt;

        $proprt = dig_gameClass::createMemberProp($params);
        if (!$proprt->STS) return $proprt;

        $rt = dig_gameClass::start($rt->DATA, $_SESSION['game_dig_access_token']);
        return $rt;
    }

    public function digOp() {
        $params = array_merge(array(), $_GET, $_POST);

        $member_info = $_SESSION['game_dig_member_info'];
        if (!$member_info) {
            if (C("debug")) {
                // For test
                $member_id = 1;
            } else {
                return new result(false, "No session", null, errorCodes::SESSION_EXPIRED);
            }
        } else {
            $member_id = $member_info['member_id'];
        }

        $game_id = $params['game_order_id'];
        $grid_id = $params['grid_id'];

        $rt = dig_gameClass::dig($game_id, $grid_id, $member_id);
        return $rt;
    }

    public function dig_oddsOp() {
        $rt = dig_gameClass::getDigOdds();
        return $rt;
    }
}
