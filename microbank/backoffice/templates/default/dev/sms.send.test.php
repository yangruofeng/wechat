
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>SMS Code Test</h3>

        </div>
    </div>
    <div class="container">

        <div class="business-condition">

            <div >
                <form role="form" class="form-horizontal" id="frm_search_condition" style="width: 500px;">
                    <table class="table table-no-background">

                        <tr>
                            <td >
                                <label for="" class="form-label">Company</label>

                            </td>
                            <td>
                                <?php echo C('sms_api'); ?>
                            </td>
                        </tr>

                        <tr>
                            <td >
                                <label for="" class="form-label"> Country Code</label>

                            </td>
                            <td>
                                <select class="form-control" name="country_code" id="">
                                    <option value="855">+855</option>
                                    <option value="86">+86</option>
                                    <option value="84">+84</option>
                                    <option value="66">+66</option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td >
                                <label for="" class="form-label">Phone Number</label>

                            </td>
                            <td>
                                <input type="number" name="phone_number" class="form-control">
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <span class="btn btn-danger" id="test_submit">Test</span>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>

        </div>

    </div>
</div>
<script>

    $('#test_submit').click(function(){
        var _values = getFormJson('#frm_search_condition');
        $('body').waiting();
        yo.loadData({
            _c: "dev",
            _m: "ajaxSmsSendTest",
            param: _values,
            callback: function (_o) {
                $('body').unmask();
                if (_o.STS) {
                    alert('success');
                } else {
                    alert('Send fail:'+_o.MSG);
                }
            }
        });

    });


    function sms_resend(uid) {
        if (uid <= 0) {
            return;
        }
        var _tr = $('tr[uid="' + uid + '"]');
        _tr.find('.resend').hide();
        _tr.find('.resending').show();
        yo.loadData({
            _c: "dev",
            _m: "resendSms",
            param: {uid: uid},
            callback: function (_o) {
                if (_o.STS) {
                    var _data = _o.DATA;
                    _tr.find('.content').html(_data.content);
                    _tr.find('.task_state').html(_data.state);
                    _tr.find('.resending').hide();
                    _tr.find('.resend_success').show();
                } else {
                    var _data = _o.DATA;
                    _tr.find('.task_state').html('<span style="color: red">' + _data.state + '</span>');
                    _tr.find('.resend').show();
                    _tr.find('.resending').hide();
                }
            }
        });
    }
</script>
