<?php

class settleControl extends control {
    public function __construct()
    {
    }

    public function expiredOp() {
        $game_model = new dig_gameModel();
        $api = gameApi::Instance();
        $result_messages = array();
        foreach ($game_model->getExpiredGames() as $game) {
            $game->game_state = gameStateEnum::EXPIRED;
            $game->end_time = time();

            $rt = $game->update();
            if (!$rt->STS) {
                $result_messages[]= $game->game_order_id. ": Prepare Failed - " . $rt->MSG;
            } else {
                $settle_params = array(
                    'platform_order_sn' => $game->platform_order_sn
                );
                if ($game->won_amount > $game->bet_amount) {
                    // 已经开出的红包金额超过下注金额，返奖
                    $settle_params['is_refund'] = 0;
                    $settle_params['return_amount'] = $game->won_amount;
                } else {
                    $settle_params['is_refund'] = 1;
                    $settle_params['return_amount'] = $game->bet_amount;
                }

                $ret = $api->settleOrder($settle_params);
                if ($ret['STS']) {
                    $game->settle_time = time();
                    $rt = $game->update();
                    if (!$rt->STS) {
                        $result_messages[]= $game->game_order_id. ": Finish Failed - " . $rt->MSG;
                    } else {
                        $result_messages[]= $game->game_order_id. ": Succeed";
                    }
                } else {
                    $result_messages[]= $game->game_order_id. ": Settle Failed - " . $ret['MSG'];
                }
            }
        }

        header("Content-Type: text/plain");
        echo(join("\n", $result_messages));
        die;
    }
}