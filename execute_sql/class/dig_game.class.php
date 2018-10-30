<?php

class dig_gameClass {
    public static function create($params) {
        if (!$params['member_id'])
            return new result(false, 'Session expired', null, errorCodes::SESSION_EXPIRED);

        $game_model = new dig_gameModel();
        $game = $game_model->newRow();
        $game->member_id = $params['member_id'];
        $game->game_type = $params['game_type'];
        $game->bomb_count = (int)$params['game_type'] - (int)$params['red_packet_count'];
        $game->bet_amount = $params['bet_amount'];
        $game->currency = $params['currency'];
        $game->red_packet_count = (int)$params['red_packet_count'];
        $game->opened_grids = my_json_encode(array());
        $game->game_state = gameStateEnum::TEMPORARY;
        $game->start_time = 0;

        // 计算odds
        $odds_model = new dig_oddsModel();
        $odds_config = $odds_model->getRow(array('game_type' => $game->game_type, 'red_packet_count' => $game->red_packet_count));
        if (!$odds_config)
            return new result(false, 'Odds config not found', null, errorCodes::UNEXPECTED_DATA);
        $game->odds = $odds_config->odds;

        // 随机产生每个格子的奖金或者地雷
        $currency_model = new dig_currencyModel();
        $currency_config = $currency_model->getRow(array('currency' => $params['currency']));
        if (!$currency_config)
            return new result(false, 'Currency config not found', null, errorCodes::UNEXPECTED_DATA);
        $grids = array();
        $prize = $game->odds * $game->bet_amount;
        $precision = min(2-round(log($prize/$game->red_packet_count,10)), $currency_config->precision);
        for ($i=0;$i<$game->game_type;$i++) {
            $grids[]= mt_rand(1, 100) + $i;      // 随机一个权重
        }
        sort($grids);
        for ($i=0;$i<$game->bomb_count;$i++) {
            $grids[$i] = -1;        // 设置炸弹
        }
        $t = array_sum($grids) + $game->bomb_count;  // 总权重, 炸弹设置为-1了，所以加上bomb_count
        $r = $prize;
        for ($i=0;$i<$game->game_type-1;$i++) {
            if ($grids[$i] != -1) {
                $grids[$i] = round($grids[$i] / $t * $prize, $precision);
                if ($r >= $grids[$i]) {
                    $r -= $grids[$i];
                } else {
                    $grids[$i] = 0;
                }
            }
        }
        $grids[$game->game_type-1] = $r;
        shuffle($grids);
        $game->game_detail = my_json_encode($grids);

        $rt = $game->insert();
        if (!$rt->STS) {
            return new result(false, 'Insert dig_game failed - ' . $rt->MSG, null, errorCodes::DB_ERROR);
        } else {
            return new result(true, '', $game);
        }
    }

    private static function formatGameType($game_type) {
        if ($game_type == 16) {
            return "4x4";
        } else if ($game_type == 9) {
            return "3x3";
        } else {
            return $game_type;
        }
    }

    public static function start($game, $member_access_token) {
        // 调用API创建Order完成支付
        $api = gameApi::Instance();
        $ret = $api->createOrder(array(
            'game_order_sn' => $game->game_order_id,
            'member_id' => $game->member_id,
            'game_title' => 'Lucky Dig #' . $game->game_order_id,
            'game_type' => self::formatGameType($game->game_type) . " - B" . $game->bomb_count,
            'bet_amount' => $game->bet_amount,
            'currency' => $game->currency,
            'expire_time' => date("Y-m-d H:i:s", time()+C("expire_timeout")),
            'access_token' => $member_access_token
        ));
        if (!$ret['STS']) {
            return new result(false, 'Create order api failed - ' . $ret['MSG'], null, $ret['CODE']);
        }

        // 游戏开始
        $game->platform_order_sn = $ret['DATA']['order_sn'];
        $game->start_time = time();
        $game->game_state = gameStateEnum::STARTED;
        $rt = $game->update();
        if (!$rt->STS) {
            return new result(false, 'Update dig_game failed - ' . $rt->MSG, null, errorCodes::DB_ERROR);
        } else {
            return new result(true, null, array(
                'game_order_id' => $game->game_order_id,
                'game_type' => $game->game_type,
                'bomb_count' => $game->bomb_count,
                'odds' => $game->odds,
                'bet_amount' => $game->bet_amount,
                'currency' => $game->currency,
                'won_amount' => $game->won_amount,
                'red_packet_count' => $game->red_packet_count,
                'grids' => array()
            ));
        }
    }

    public static function dig($game_order_id, $grid_id, $member_id) {
        $game_model = new dig_gameModel();
        $game = $game_model->getRow($game_order_id);

        if (!$game) {
            return new result(false, 'Game is not exists - ' . $game_order_id, null, errorCodes::UNEXPECTED_DATA);
        }
        if ($game->game_state != gameStateEnum::STARTED) {
            return new result(false, 'Game state not supported this action - ' . $game->game_state, null, errorCodes::GAME_OVER);
        }
        if ($game->member_id != $member_id) {
            return new result(false, 'Game owner dismatch', null, errorCodes::UNEXPECTED_DATA);
        }

        $opened_grids = my_json_decode($game->opened_grids);
        if (in_array($grid_id, $opened_grids)) {
            return new result(false, 'Grid is opened', null, errorCodes::UNEXPECTED_DATA);
        }
        $opened_grids[]=$grid_id;
        $game->opened_grids = my_json_encode($opened_grids);

        $log_model = M('dig_game_play_log');
        $log = $log_model->newRow();
        $log->game_order_id = $game->game_order_id;
        $log->grid_id = $grid_id;
        $log->click_time = date("Y-m-d H:i:s");
        $rt = $log->insert();
        if (!$rt->STS) {
            return new result(false, 'Insert game_play_log failed - '.$rt->MSG, null, errorCodes::DB_ERROR);
        }

        $game_detail = my_json_decode($game->game_detail);
        if ($game_detail[$grid_id] == -1) {
            // 碰到炸弹，游戏结束
            $game->game_state = gameStateEnum::ENDED;
            $game->end_time = time();
        } else {
            $game->won_amount += $game_detail[$grid_id];
            $game->red_packet_count -= 1;
            if ($game->red_packet_count == 0) {
                $game->game_state = gameStateEnum::ENDED;
                $game->end_time = time();
            }
        }

        $rt = $game->update();
        if (!$rt->STS) {
            return new result(false, 'Update game failed - '.$rt->MSG, null, errorCodes::DB_ERROR);
        }

        if ($game->game_state == gameStateEnum::ENDED) {
            // 调用API完成订单结算
            $api = gameApi::Instance();
            $ret = $api->settleOrder(array(
                'platform_order_sn' => $game->platform_order_sn,
                'is_refund' => 0,   // 始终不会是退款，退款只在后台检测中出现
                'return_amount' => $game->won_amount
            ));
            if ($ret['STS']) {
                $game->settle_time = time();
                $game->update(); // 不管更新结果如何，更新失败当作api未成功也没有问题
            }  // 不管api调用结果如何，失败的话手工检测处理

            // 返回
            return new result(true, null, array(
                'result' => $game_detail[$grid_id],
                'game_info' => array(
                    'game_state' => $game->game_state,
                    'won_amount' => $game->won_amount,
                    'red_packet_count' => $game->red_packet_count,
                    'game_detail' => $game_detail,
                    'opened_grids' => $opened_grids
                )
            ));
        } else {
            return new result(true, null, array(
                'result' => $game_detail[$grid_id],
                'game_info' => array(
                    'game_state' => $game->game_state,
                    'won_amount' => $game->won_amount,
                    'red_packet_count' => $game->red_packet_count
                )
            ));
        }
    }

    public static function getUnfinishedGame($member_id) {
        $game_model = new dig_gameModel();
        $data = $game_model->getRows(array(
            'game_state' => gameStateEnum::STARTED,
            'member_id' => $member_id
        ));
        if (count($data) <= 0) {
            return new result(false);
        } else {
            $game = $data[0];

            $opened_grids = my_json_decode($game->opened_grids);
            $grids = array();
            if (!empty($opened_grids)) {
                $game_detail = my_json_decode($game->game_detail);
                foreach ($opened_grids as $grid_id) {
                    $grids[$grid_id] = $game_detail[$grid_id];
                }
            }

            return new result(true, '', array(
                'game_order_id' => $game->game_order_id,
                'game_type' => $game->game_type,
                'bomb_count' => $game->bomb_count,
                'odds' => $game->odds,
                'bet_amount' => $game->bet_amount,
                'currency' => $game->currency,
                'won_amount' => $game->won_amount,
                'red_packet_count' => $game->red_packet_count,
                'grids' => $grids
            ));
        }
    }

    public static function getDigOdds() {
      $odds_model = new dig_oddsModel();
      $sql = "select * from `dig_odds`";
      $odds = $odds_model->reader->getRows($sql);
      if (empty($odds)) {
          return new result(true);
      } else {
        return new result(true, '', $odds);
      }
    }

    public static function getDigChips($member_currency = '') {
      $cur_currency = $member_currency ? : 'USD';
      $currency_model = new dig_currencyModel();
      $sql = "select * from `dig_currency` where currency = '$cur_currency'";
      $chips = $currency_model->reader->getRows($sql);
      if (empty($chips)) {
          return new result(false, 'Config for currency [' . $cur_currency . '] not found', null, errorCodes::UNEXPECTED_DATA);
      } else {
        return new result(true, '', $chips);
      }
    }

    public static function createMemberProp($params) {
      if (!$params['member_id'])
          return new result(false, 'Session expired', null, errorCodes::SESSION_EXPIRED);
      $prop_model = new dig_member_propModel();

      $member = $prop_model->getRow(array('member_id' => $params['member_id']));
      if($member){
        $member->game_type = $params['game_type'];
        $member->red_packet_count = (int)$params['red_packet_count'];
        $member->lmt = date("Y-m-d H:i:s");
        $rt = $member->update();
        if (!$rt->STS) {
            return new result(false, 'Insert dig_game failed - ' . $rt->MSG, null, errorCodes::DB_ERROR);
        } else {
            return new result(true, '', $member);
        }
      }else{
        $prop = $prop_model->newRow();
        $prop->member_id = $params['member_id'];
        $prop->game_type = $params['game_type'];
        $prop->red_packet_count = (int)$params['red_packet_count'];
        $rt = $prop->insert();
        if (!$rt->STS) {
            return new result(false, 'Insert dig_game failed - ' . $rt->MSG, null, errorCodes::DB_ERROR);
        } else {
            return new result(true, '', $prop);
        }
      }

    }

    public static function getMemberProp($member_id) {
      $prop_model = new dig_member_propModel();
      $sql = "select * from `dig_member_prop` where member_id = '".$member_id."'";
      $latest_game = $prop_model->reader->getRows($sql);
      if (empty($latest_game[0])) {
          return new result(true);
      } else {
        return new result(true, '', $latest_game[0]);
      }
    }
}
