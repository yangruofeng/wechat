<?php

class committee_voteControl
{
    public function __construct()
    {
        Tpl::output("html_title", "Vote");
        Tpl::setDir("loan_committee");
    }

    /**
     * 投票页面
     */
    public function committeeVoteCreditApplicationOp()
    {
        $uid = intval($_GET['uid']);
        $m_member_credit_grant = M('member_credit_grant');
        $credit_grant = $m_member_credit_grant->find(array('uid' => $uid));
        Tpl::output('credit_suggest', $credit_grant);

        $package = M('loan_product_package')->find(array('uid' => intval($credit_grant['package_id'])));
        Tpl::output('package', $package);

        $client_info = M('client_member')->find(array('uid' => $credit_grant['member_id']));
        Tpl::output('client_info', $client_info);

        Tpl::showPage('committee.vote.credit.application');
    }

    /**
     * 投票
     */
    public function submitVoteCreditApplicationOp()
    {
        $param = array_merge(array(), $_GET, $_POST);
        $class_credit_grant = new member_credit_grantClass();
        $rt = $class_credit_grant->submitVoteCreditApplication($param);
        if ($rt->STS) {
            Tpl::output('html_title', 'Vote');
            Tpl::showPage('vote.successful');
        } else {
            showMessage($rt->MSG);
        }
    }

    /**
     * 投票页面（核销）
     */
    public function committeeVoteWrittenOffOp()
    {
        $uid = intval($_GET['uid']);
        $m_loan_written_off = new loan_writtenoffModel();
        $data = $m_loan_written_off->getWrittenOffDetail($uid);
        Tpl::output('written_off', $data);
        Tpl::showPage('committee.vote.written.off');
    }

    /**
     * 投票（核销）
     */
    public function submitVoteWrittenOffOp()
    {
        $param = array_merge(array(), $_GET, $_POST);
        $class_loan_written_off = new loan_written_offClass();
        $rt = $class_loan_written_off->submitVoteWrittenOff($param);
        if ($rt->STS) {
            Tpl::output('html_title', 'Vote');
            Tpl::showPage('vote.successful');
        } else {
            showMessage($rt->MSG);
        }
    }
}
