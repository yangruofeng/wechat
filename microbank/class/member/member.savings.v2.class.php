<?php

class memberSavingsV2Class extends member_savingsClass {
    public static function getAssetsSummary($memberId) {
        $formatted_data = array();

        foreach (passbookClass::getSavingsPassbookOfMemberId($memberId)->getAccountBalance() as $c => $v) {
            $formatted_data[$c] = array(
                'currency' => $c,
                'total_assets' => $v,
                'balance' => $v,
                'exchange_rate' => global_settingClass::getCurrencyRateBetween($c, 'USD')
            );
        }

        $ret = self::getSavingsAssetsOfMember($memberId);
        if (!$ret->STS) return $ret;

        $list = $ret->DATA;
        foreach ($list as $row) {
            $ccy = $row['currency'];
            $ct = $row['category_type'];

            if (!$formatted_data[$ccy]) {
                $formatted_data[$ccy] = array(
                    'currency' => $ccy,
                    'total_assets' => 0,
                    'balance' => 0
                );
            }
            if (!$formatted_data[$ccy][$ct]) {
                $formatted_data[$ccy][$ct] = array(
                    'total' => 0,
                    'earnings' => array(
                        'yesterday' => 0,
                        'last_month' => 0,
                        'total' => 0
                    )
                );
            }

            if ($row['state'] == savingsContractStateEnum::PROCESSING ||
                $row['state'] == savingsContractStateEnum::SETTLING
            ) {
                // 未计算的合同才统计持仓，否则不能算持仓了
                $formatted_data[$ccy]['total_assets'] += $row['amount'];
                $formatted_data[$ccy][$ct]['total'] += $row['amount'];
            }

            $formatted_data[$ccy][$ct]['earnings']['yesterday'] += self::calculateYesterdayEarnings(
                $row['amount'],
                $row['interest_rate'],
                $row['interest_rate_unit'],
                $row['interest_date'],
                $row['end_date']);
            $formatted_data[$ccy][$ct]['earnings']['last_month'] += self::calculateLastMonthEarnings(
                $row['amount'],
                $row['interest_rate'],
                $row['interest_rate_unit'],
                $row['interest_date'],
                $row['end_date']);
            $formatted_data[$ccy][$ct]['earnings']['total'] += self::calculateAllEarnings(
                $row['amount'],
                $row['interest_rate'],
                $row['interest_rate_unit'],
                $row['interest_date'],
                $row['end_date']);
        }

        return new result(true, null, $formatted_data);
    }

    public static function getPositionSummary($memberId, $categoryType) {
        $formatted_data = array();

        $ret = self::getSavingsAssetsOfMember($memberId, $categoryType);
        if (!$ret->STS) return $ret;

        $category_model = new savings_categoryModel();

        $list = $ret->DATA;
        foreach ($list as $row) {
            $ccy = $row['currency'];
            $ct = $row['category_type'];
            $cid = $row['category_id'];

            if (!$formatted_data[$ct]) {
                $category_list = $category_model->getCategoryListWithProductSummary(array('category_type' => $ct));
                $categories = array();
                foreach ($category_list as $item) {
                    $categories[$item["uid"]] = $item;
                }
                $formatted_data[$ct] = array(
                    'summary' => array(),
                    'categories' => $categories
                );
            }
            if (!$formatted_data[$ct]['summary'][$ccy]) {
                $formatted_data[$ct]['summary'][$ccy] = array(
                    'total' => 0,
                    'earnings' => array(
                        'yesterday' => 0,
                        'last_month' => 0,
                        'total' => 0
                    )
                );
            }
            if (!$formatted_data[$ct]['categories'][$cid][$ccy]) {
                $formatted_data[$ct]['categories'][$cid][$ccy] = array(
                    'total' => 0,
                    'earnings' => array(
                        'yesterday' => 0,
                        'last_month' => 0,
                        'total' => 0
                    )
                );
            }

            if ($row['state'] == savingsContractStateEnum::PROCESSING ||
                $row['state'] == savingsContractStateEnum::SETTLING
            ) {
                // 未计算的合同才统计持仓，否则不能算持仓了
                $formatted_data[$ct]['summary'][$ccy]['total'] += $row['amount'];
                $formatted_data[$ct]['categories'][$cid][$ccy]['total'] += $row['amount'];
            }

            $earnings_yesterday = self::calculateYesterdayEarnings(
                $row['amount'],
                $row['interest_rate'],
                $row['interest_rate_unit'],
                $row['interest_date'],
                $row['end_date']);
            $earnings_last_month = self::calculateLastMonthEarnings(
                $row['amount'],
                $row['interest_rate'],
                $row['interest_rate_unit'],
                $row['interest_date'],
                $row['end_date']);
            $earnings_total = self::calculateAllEarnings(
                $row['amount'],
                $row['interest_rate'],
                $row['interest_rate_unit'],
                $row['interest_date'],
                $row['end_date']);

            $formatted_data[$ct]['summary'][$ccy]['earnings']['yesterday'] += $earnings_yesterday;
            $formatted_data[$ct]['summary'][$ccy]['earnings']['last_month'] += $earnings_last_month;
            $formatted_data[$ct]['summary'][$ccy]['earnings']['total'] += $earnings_total;

            $formatted_data[$ct]['categories'][$cid][$ccy]['earnings']['yesterday'] += $earnings_yesterday;
            $formatted_data[$ct]['categories'][$cid][$ccy]['earnings']['last_month'] += $earnings_last_month;
            $formatted_data[$ct]['categories'][$cid][$ccy]['earnings']['total'] += $earnings_total;
        }

        return new result(true, null, $formatted_data);
    }

    public static function getPositionDetail($memberId, $categoryId) {
        $formatted_data = array();

        $ret = self::getSavingsAssetsOfMember($memberId, null, $categoryId);
        if (!$ret->STS) return $ret;

        $list = $ret->DATA;
        foreach ($list as $row) {
            if ($row['state'] == savingsContractStateEnum::PROCESSING ||
                $row['state'] == savingsContractStateEnum::SETTLING
            ) {
                // 未计算的合同才统计持仓，否则不能算持仓了
                $row['earnings'] = array(
                    'yesterday' => self::calculateYesterdayEarnings(
                        $row['amount'],
                        $row['interest_rate'],
                        $row['interest_rate_unit'],
                        $row['interest_date'],
                        $row['end_date']),
                    'last_month' => self::calculateLastMonthEarnings(
                        $row['amount'],
                        $row['interest_rate'],
                        $row['interest_rate_unit'],
                        $row['interest_date'],
                        $row['end_date']),
                    'total' => self::calculateAllEarnings(
                        $row['amount'],
                        $row['interest_rate'],
                        $row['interest_rate_unit'],
                        $row['interest_date'],
                        $row['end_date'])
                );
                $formatted_data[]= $row;
            }
        }

        return new result(true, null, $formatted_data);
    }

    public static function getTransactionList($memberId, $dateStart, $dateEnd, $page, $pageSize,
                                              $categoryId = null, $productId = null) {
        $member_model = new client_memberModel();
        $member_info = $member_model->getRow($memberId);
        if (!$member_info) return new result(false, 'Invalid member id', null, errorCodesEnum::INVALID_PARAM);

        $transaction_model = new savings_transactionModel();
        return $transaction_model->getListWithProduct(array(
            'client_obj_type' => clientObjTypeEnum::MEMBER,
            'client_obj_guid' => $member_info->obj_guid,
            'date_start' => date("Y-m-d", strtotime($dateStart)),
            'date_end' => date("Y-m-d", strtotime($dateEnd)) . " 23:59:59",
            'category_id' => $categoryId,
            'product_id' => $productId
        ), $page, $pageSize);
    }

    public static function getSavingsAssetsOfMember($memberId, $categoryType = null, $categoryId = null) {
        $member_model = new client_memberModel();
        $member_info = $member_model->getRow($memberId);
        if (!$member_info) return new result(false, 'Invalid member id', null, errorCodesEnum::INVALID_PARAM);

        $contract_model = new savings_contractModel();
        $filters = array(
            'client_obj_type' => clientObjTypeEnum::MEMBER,
            'client_obj_guid' => $member_info->obj_guid,
            'state' => array(savingsContractStateEnum::PROCESSING, savingsContractStateEnum::SETTLING, savingsContractStateEnum::FINISHED),
            'end_date' => array(">=", date("Y-m", strtotime("-1 month")) . "01")
        );
        if ($categoryType) {
            $filters['category_type'] = $categoryType;
        }
        if ($categoryId) {
            $filters['category_id'] = $categoryId;
        }

        $data = $contract_model->getListWithProduct($filters);
        foreach ($data as $i => $row) {
            if ($row['redeemed_amount'] > 0) {
                $row['amount'] -= $row['redeemed_amount'];
                $data[$i] = $row;
            }
        }

        return new result(true, null, $data);
    }

    public static function calculateDailyEarnings($amount, $interestRate, $interestRateUnit) {
        switch ($interestRateUnit) {
            case interestRatePeriodEnum::YEARLY:
                $interest_rate_daily = $interestRate / 365;
                break;
            case interestRatePeriodEnum::MONTHLY:
                $interest_rate_daily = $interestRate / 30;
                break;
            case interestRatePeriodEnum::DAILY:
                $interest_rate_daily = $interestRate;
                break;
            case interestRatePeriodEnum::QUARTER:
                $interest_rate_daily = $interestRate / 92;
                break;
            case interestRatePeriodEnum::SEMI_YEARLY:
                $interest_rate_daily = $interestRate / 183;
                break;
            case interestRatePeriodEnum::WEEKLY:
                $interest_rate_daily = $interestRate / 7;
                break;
            default:
                throw new Exception("Unknown interest unit - $interestRateUnit", errorCodesEnum::UNEXPECTED_DATA);
        }

        return $amount * $interest_rate_daily / 100;
    }

    public static function calculateYesterdayEarnings($amount, $interestRate, $interestRateUnit, $interestDate, $endDate) {
        return self::calculateEarningsInDateRange($amount, $interestRate, $interestRateUnit, $interestDate, $endDate, array(
            'start' => date("Y-m-d", strtotime("-1 day")),
            'end' => date("Y-m-d")
        ));
    }

    public static function calculateLastMonthEarnings($amount, $interestRate, $interestRateUnit, $interestDate, $endDate) {
        return self::calculateEarningsInDateRange($amount, $interestRate, $interestRateUnit, $interestDate, $endDate, array(
            'start' => date("Y-m", strtotime("-1 months")) . "-01",
            'end' => date("Y-m") . "-01"
        ));
    }

    public static function calculateAllEarnings($amount, $interestRate, $interestRateUnit, $interestDate, $endDate) {
        return self::calculateEarningsInDateRange($amount, $interestRate, $interestRateUnit, $interestDate, $endDate, array(
            'start' => $interestDate,
            'end' => date("Y-m-d")
        ));
    }

    public static function calculateEarningsInDateRange($amount, $interestRate, $interestRateUnit, $interestDate, $endDate, $dateRange) {
        $interest_time_start = strtotime($interestDate);
        $interest_time_end = strtotime(date("Y-m-d", strtotime($endDate) + 24 * 3600));  // endDate当天计息，所以计算时，算到endDate下一天
        $lms = strtotime($dateRange['start']);
        if ($lms < $interest_time_start) $lms = $interest_time_start;
        $lme = strtotime($dateRange['end']);
        if ($lme > $interest_time_end) $lme = $interest_time_end;

        if ($lms >= $lme)
            return 0;   // 起息日期在日期范围之后，或者计算日期在结束日期之后，没有收益
        else {
            return self::calculateDailyEarnings($amount, $interestRate, $interestRateUnit) * ($lme - $lms) / (24 * 3600);
        }
    }

    public static function createContract($memberId, $amount, $currency, $termOpts, $productOpts, $createOpts = array()) {
        $member_model = new memberModel();
        $member_info = $member_model->getMemberInfoById($memberId);
        if (!$member_info) return new result(false, 'Invalid member id', null, errorCodesEnum::INVALID_PARAM);

        if ($productOpts['product_id']) {
            $product_id = $productOpts['product_id'];
            $product_model = new savings_productModel();
            $product_info = $product_model->getProductInfoById($product_id);
            if (!$product_info)
                return new result(false, 'Invalid product id', null, errorCodesEnum::INVALID_PARAM);
            if ($product_info['state'] != savingsProductStateEnum::ACTIVE)
                return new result(false, 'Invalid product state', null, errorCodesEnum::INVALID_STATE);
            if ($product_info['currency'] != $currency)
                return new result(false, "Product [$product_id] does not support currency[$currency]", null, errorCodesEnum::INVALID_PARAM);
        } else {
            if (!$productOpts['category_id'])
                return new result(false, 'Must specify product id or category id', null, errorCodesEnum::INVALID_PARAM);

            $category_id = $productOpts['category_id'];
            $ret = savingsProductClass::findMatchedProductInCategory($category_id, $amount, $currency, $termOpts);
            if (!$ret->STS) return $ret;
            $product_info = $ret->DATA;
            if (!$product_info)
                return new result(false, 'No match product', null, errorCodesEnum::NO_DATA);
            $product_id = $product_info['uid'];
        }

        $contract_model = new savings_contractModel();
        $contract_amount = round($amount / (1 + $product_info['purchase_fee_rate'] / 100), 2);
        return $contract_model->create(array(
            'client_obj_type' => clientObjTypeEnum::MEMBER,
            'client_obj_guid' => $member_info['obj_guid'],
            'product_id' => $product_id,
            'product_category_id' => $product_info['category_id'],
            'product_code' => $product_info['product_code'],
            'product_name' => $product_info['product_name'],
            'product_description' => $product_info['product_description'],
            'product_qualification' => $product_info['product_qualification'],
            'product_feature' => $product_info['product_feature'],
            'product_required' => $product_info['product_required'],
            'product_notice' => $product_info['product_notice'],
            'interest_rate' => $product_info['interest_rate'],
            'interest_rate_unit' => $product_info['interest_rate_unit'],
            'min_terms' => $product_info['min_terms'],
            'max_terms' => $product_info['max_terms'],
            'interest_start_type' => $product_info['interest_start_type'],
            'purchase_fee_rate' => $product_info['purchase_fee_rate'],
            'redeem_fee_rate' => $product_info['redeem_fee_rate'],
            'is_allow_auto_renew' => $product_info['is_allow_auto_renew'],
            'is_allow_prior_withdraw' => $product_info['is_allow_prior_withdraw'],
            'is_withdraw_need_password' => $product_info['is_withdraw_need_password'],
            'is_withdraw_need_id_card' => $product_info['is_withdraw_need_id_card'],
            'is_withdraw_need_book' => $product_info['is_withdraw_need_book'],
            'withdraw_book_days' => $product_info['withdraw_book_days'],
            'is_withdraw_allow_agency' => $product_info['is_withdraw_allow_agency'],
            'amount' => $contract_amount,
            'purchase_fee' => $amount - $contract_amount,
            'currency' => $currency,
            'term' => $termOpts['term'],
            'end_date' => $termOpts['end_date'],
            'create_source' => $createOpts['source'],
            'creator_id' => $createOpts['creator_id'],
            'creator_name' => $createOpts['creator_name']
        ));
    }

    public static function confirmContractBegin($contractId) {
        $contract_model = new savings_contractModel();
        $contract_info = $contract_model->getRow($contractId);
        if (!$contract_info)
            return new result(false, 'Invalid contract id', null, errorCodesEnum::INVALID_PARAM);
        if ($contract_info->state != savingsContractStateEnum::TEMP)
            return new result(false, 'Invalid contract state', null, errorCodesEnum::INVALID_STATE);

        $transaction_model = new savings_transactionModel();
        $trx_info = $transaction_model->getRow($contract_info->trx_id);
        if (!$trx_info)
            return new result(false, 'Imcompleted contract', null, errorCodesEnum::UNEXPECTED_DATA);
        if ($trx_info->state != savingsTransactionStateEnum::TEMP)
            return new result(false, 'Invalid transaction state', null, errorCodesEnum::UNEXPECTED_DATA);

        $contract_info->pay_time = Now();
        switch($contract_info->interest_start_type) {
            case savingsInterestStartTypeEnum::NEXT_DAY:
                $contract_info->interest_date = date("Y-m-d", strtotime($contract_info->pay_time) + 24*3600);
                break;
            case savingsInterestStartTypeEnum::IMMEDIATELY:
                $contract_info->interest_date = date("Y-m-d", strtotime($contract_info->pay_time));
                break;
            default:
                return new result(false, 'Unknown interest_start_type - ' . $contract_info->interest_start_type, null, errorCodesEnum::UNEXPECTED_DATA);
        }

        if ($contract_info->term) {
            $contract_info->end_date = date("Y-m-d", strtotime($contract_info->pay_time) + 24*3600 * ($contract_info->term + 1));
        } else if ($contract_info->end_date) {
            $contract_info->term = (strtotime($contract_info->end_date) - strtotime($contract_info->interest_date)) / (24*3600);
        }

        if (($contract_info->min_terms && $contract_info->term < $contract_info->min_terms) ||
            ($contract_info->max_term && $contract_info->term > $contract_info->max_term)) {
            $contract_info->state = savingsContractStateEnum::CANCELLED;
            $contract_info->update();

            $trx_info->state = savingsTransactionStateEnum::CANCELLED;
            $trx_info->update();

            return new result(false, 'Term out of range', null, errorCodesEnum::SAVINGS_TERM_OUT_OF_RANGE);
        }

        $contract_info->state = savingsContractStateEnum::CONFIRMING;
        $up = $contract_info->update();
        if (!$up->STS)
            return new result(false, 'Update contract failed - ' . $up->MSG, null, errorCodesEnum::DB_ERROR);

        $trx_info->state = savingsTransactionStateEnum::PROCESSING;
        $up = $trx_info->update();
        if (!$up->STS)
            return new result(false, 'Update transaction failed - ' . $up->MSG, null, errorCodesEnum::DB_ERROR);

        return new result(true, null, $contract_info->toArray());
    }

    public static function confirmContractFinish($contractId, $paymentTradingId) {
        $contract_model = new savings_contractModel();
        $contract_info = $contract_model->getRow($contractId);
        if (!$contract_info)
            return new result(false, 'Invalid contract id', null, errorCodesEnum::INVALID_PARAM);

        if ($contract_info->state != savingsContractStateEnum::CONFIRMING)
            return new result(false, 'Invalid contract state', null, errorCodesEnum::INVALID_STATE);

        $transaction_model = new savings_transactionModel();
        $trx_info = $transaction_model->getRow($contract_info->trx_id);
        if (!$trx_info)
            return new result(false, 'Imcompleted contract', null, errorCodesEnum::UNEXPECTED_DATA);
        if ($trx_info->state != savingsTransactionStateEnum::PROCESSING)
            return new result(false, 'Invalid transaction state', null, errorCodesEnum::UNEXPECTED_DATA);

        $trx_info->trade_id = $paymentTradingId;
        $trx_info->state = savingsTransactionStateEnum::FINISHED;
        $up = $trx_info->update();
        if (!$up->STS) return new result(false, 'Update contract failed - ' . $up->MSG, null, errorCodesEnum::DB_ERROR);

        $contract_info->state = savingsContractStateEnum::PROCESSING;
        $up = $contract_info->update();
        if (!$up->STS) return new result(false, 'Update contract failed - ' . $up->MSG, null, errorCodesEnum::DB_ERROR);

        return new result(true, null, $contract_info->toArray());
    }

    private static function calculateRedeemFee($row) {
        return round($row['redeem_amount'] * $row['redeem_fee_rate'] / 100, 2);
    }

    private static function checkRedeem($memberInfo, $productInfo, $amount) {
        if ($amount < $productInfo['limit_withdraw_lowest_per_time']) {
            return new result(false, 'Amount is less than lowest limit per time', null, errorCodesEnum::OUT_OF_PER_WITHDRAW);
        }
        if ($amount > $productInfo['limit_withdraw_highest_per_time']) {
            return new result(false, 'Amount is more than highest limit per time', null, errorCodesEnum::OUT_OF_PER_WITHDRAW);
        }

        $redeem_model = new savings_redeemModel();
        $today_redeemed_amount = $redeem_model->getRedeemAmountTodayOfClient(array(
            'client_obj_type' => clientObjTypeEnum::MEMBER,
            'client_obj_guid' => $memberInfo->obj_guid
        ), $productInfo->uid);
        if ($amount + $today_redeemed_amount > $productInfo['limit_withdraw_highest_per_day']) {
            return new result(false, 'Exceed highest limit per day', null, errorCodesEnum::OUT_OF_DAY_WITHDRAW);
        }

        return new result(true);
    }

    public static function createRedeemApply($memberId, $amount, $currency, $productId, $createOpts = array()) {
        $member_model = new memberModel();
        $member_info = $member_model->getMemberInfoById($memberId);
        if (!$member_info) return new result(false, 'Invalid member id', null, errorCodesEnum::INVALID_PARAM);

        $product_model = new savings_productModel();
        $product_info = $product_model->getRow($productId);
        if (!$product_info) return new result(false, 'Invalid product id', null, errorCodesEnum::INVALID_PARAM);
        if ($product_info->currency != $currency) return new result(false, 'Currency is wrong', null, errorCodesEnum::INVALID_PARAM);

        $ret = self::checkRedeem($member_info, $product_info, $amount);
        if (!$ret->STS)
            return $ret;

        $contract_model = new savings_contractModel();
        $filters = array(
            'client_obj_type' => clientObjTypeEnum::MEMBER,
            'client_obj_guid' => $member_info->obj_guid,
            'state' => savingsContractStateEnum::PROCESSING,
            'product_id' => $product_info->uid
        );

        $to_redeem_amount = $amount;
        $total_redeem_fee = 0;
        $redeem_detail = array();
        $positions = $contract_model->getListWithProduct($filters);
        foreach ($positions as $row) {
            $contract_remaining_amount = $row['amount'] - $row['redeeming_amount'] - $row['redeemed_amount'];
            if ($contract_remaining_amount > 0) {
                if ($contract_remaining_amount > $to_redeem_amount) {
                    $row['redeem_amount'] = $to_redeem_amount;
                } else {
                    $row['redeem_amount'] = $contract_remaining_amount;
                }
                $row['redeem_fee'] = self::calculateRedeemFee($row);
                $total_redeem_fee += $row['redeem_fee'];

                $redeem_detail[]=array(
                    'contract_id' => $row['uid'],
                    'amount' => $row['redeem_amount'],
                    'redeem_fee' => $row['redeem_fee']
                );

                $to_redeem_amount -= $row['redeem_amount'];
                if ($to_redeem_amount <= 0) {
                    break;
                }
            }
        }

        if ($to_redeem_amount > 0)
            return new result(false, 'Lack of position', null, errorCodesEnum::BALANCE_NOT_ENOUGH);

        $redeem_model = new savings_redeemModel();
        $ret = $redeem_model->createRedeemApply(array(
            'client_obj_type' => clientObjTypeEnum::MEMBER,
            'client_obj_guid' => $member_info->obj_guid,
            'product_id' => $productId,
            'amount' => $amount,
            'currency' => $currency,
            'fee' => $total_redeem_fee,
            'create_source' => $createOpts['create_source'],
            'creator_id' => $createOpts['creator_id'],
            'creator_name' => $createOpts['creator_name']
        ), $redeem_detail);
        return $ret;
    }

    public static function confirmRedeemBegin($redeemId) {
        $redeem_model = new savings_redeemModel();
        $redeem_info = $redeem_model->getRow($redeemId);
        if (!$redeem_info)
            return new result(false, 'Invalid redeem id', null, errorCodesEnum::INVALID_PARAM);
        if ($redeem_info->state != savingsRedeemStateEnum::TEMP)
            return new result(false, 'Invalid redeem state', null, errorCodesEnum::INVALID_STATE);

        $redeem_info->state = savingsRedeemStateEnum::PROCESSING;
        $redeem_info->update_time = Now();
        $up = $redeem_info->update();
        if (!$up->STS)
            return new result(false, 'Update redeem failed - ' . $up->MSG, null, errorCodesEnum::DB_ERROR);

        return new result(true, null, $redeem_info->toArray());
    }

    public static function confirmRedeemFinish($redeemId, $tradingId) {
        $redeem_model = new savings_redeemModel();
        $redeem_info = $redeem_model->getRow($redeemId);
        if (!$redeem_info)
            return new result(false, 'Invalid redeem id', null, errorCodesEnum::INVALID_PARAM);
        if ($redeem_info->state != savingsRedeemStateEnum::PROCESSING)
            return new result(false, 'Invalid redeem state', null, errorCodesEnum::INVALID_STATE);

        $redeem_info->trade_id = $tradingId;
        $redeem_info->state = savingsRedeemStateEnum::FINISHED;
        $redeem_info->update_time = Now();
        $up = $redeem_info->update();
        if (!$up->STS)
            return new result(false, 'Update redeem failed - ' . $up->MSG, null, errorCodesEnum::DB_ERROR);

        return new result(true, null, $redeem_info->toArray());
    }

    public static function cancelRedeemFinish($redeemId) {
        $redeem_model = new savings_redeemModel();
        $redeem_info = $redeem_model->getRow($redeemId);
        if (!$redeem_info)
            return new result(false, 'Invalid redeem id', null, errorCodesEnum::INVALID_PARAM);
        if ($redeem_info->state != savingsRedeemStateEnum::PROCESSING)
            return new result(false, 'Invalid redeem state', null, errorCodesEnum::INVALID_STATE);

        $redeem_info->state = savingsRedeemStateEnum::CANCELLED;
        $redeem_info->update_time = Now();
        $up = $redeem_info->update();
        if (!$up->STS)
            return new result(false, 'Update redeem failed - ' . $up->MSG, null, errorCodesEnum::DB_ERROR);

        return new result(true, null, $redeem_info->toArray());
    }
}