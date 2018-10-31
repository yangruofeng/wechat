<link rel="stylesheet" type="text/css" href="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/css/member.css?v=1">
<?php include_once(template('widget/inc_header'));?>
<div class="wrap cash-wrap">
  <div class="base-info">
    <p class="b">$300</p>
    <p>Balance</p>
  </div>
  <div class="cash-wrapper aui-margin-t-10">
    <div class="aui-tab cash-tab" id="tab">
      <div class="aui-tab-item aui-active"><?php echo 'Flow';?></div>
      <div class="aui-tab-item"><div></div><?php echo 'Cash In';?></div>
      <div class="aui-tab-item"><div></div><?php echo 'Cash Out';?></div>
    </div>
    <div class="aui-refresh-content">
      <div class="limit-calculation tab-panel" id="tab-1">
        <div class="aui-content aui-margin-b-15">
          <ul class="aui-list aui-media-list cash-list flow-list">
            <li class="aui-list-item aui-list-item-middle" onclick="">
              <div class="aui-media-list-item-inner">
                <div class="aui-list-item-inner">
                  <div class="aui-list-item-text">
                    <div class="aui-list-item-title title type">Receive From Customenr</div>
                    <div class="aui-list-item-right text" style="color:red;">$100</div>
                  </div>
                  <div class="aui-list-item-text">
                    <div class="aui-list-item-title title color999">zansan</div>
                    <div class="aui-list-item-right text color999">2018-1-29</div>
                  </div>
                </div>
              </div>
            </li>
            <li class="aui-list-item aui-list-item-middle" onclick="">
              <div class="aui-media-list-item-inner">
                <div class="aui-list-item-inner">
                  <div class="aui-list-item-text">
                    <div class="aui-list-item-title title type">Receive From Customenr</div>
                    <div class="aui-list-item-right text" style="color:red;">$200</div>
                  </div>
                  <div class="aui-list-item-text">
                    <div class="aui-list-item-title title color999">lisi</div>
                    <div class="aui-list-item-right text color999">2018-1-28</div>
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
      <div class="limit-calculation tab-panel" id="tab-2" style="display: none;">
        <div class="aui-content aui-margin-b-15">
          <ul class="aui-list aui-media-list cash-list flow-list">
            <li class="aui-list-item aui-list-item-middle" onclick="">
              <div class="aui-media-list-item-inner">
                <div class="aui-list-item-inner">
                  <div class="aui-list-item-text">
                    <div class="aui-list-item-title title type">Receive From Customenr</div>
                    <div class="aui-list-item-right text" style="color:red;">$300</div>
                  </div>
                  <div class="aui-list-item-text">
                    <div class="aui-list-item-title title color999">wangwu</div>
                    <div class="aui-list-item-right text color999">2018-1-27</div>
                  </div>
                </div>
              </div>
            </li>
            <li class="aui-list-item aui-list-item-middle" onclick="">
              <div class="aui-media-list-item-inner">
                <div class="aui-list-item-inner">
                  <div class="aui-list-item-text">
                    <div class="aui-list-item-title title type">Receive From Customenr</div>
                    <div class="aui-list-item-right text" style="color:red;">$400</div>
                  </div>
                  <div class="aui-list-item-text">
                    <div class="aui-list-item-title title color999">zhaoliu</div>
                    <div class="aui-list-item-right text color999">2018-1-26</div>
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
      <div class="limit-calculation tab-panel" id="tab-3" style="display: none;">
        <div class="aui-content aui-margin-b-15">
          <ul class="aui-list aui-media-list cash-list flow-list">
            <li class="aui-list-item aui-list-item-middle" onclick="">
              <div class="aui-media-list-item-inner">
                <div class="aui-list-item-inner">
                  <div class="aui-list-item-text">
                    <div class="aui-list-item-title title type">Receive From Customenr</div>
                    <div class="aui-list-item-right text" style="color:red;">$500</div>
                  </div>
                  <div class="aui-list-item-text">
                    <div class="aui-list-item-title title color999">rose</div>
                    <div class="aui-list-item-right text color999">2018-1-25</div>
                  </div>
                </div>
              </div>
            </li>
            <li class="aui-list-item aui-list-item-middle" onclick="">
              <div class="aui-media-list-item-inner">
                <div class="aui-list-item-inner">
                  <div class="aui-list-item-text">
                    <div class="aui-list-item-title title type">Receive From Customenr</div>
                    <div class="aui-list-item-right text" style="color:red;">$600</div>
                  </div>
                  <div class="aui-list-item-text">
                    <div class="aui-list-item-title title color999">jack</div>
                    <div class="aui-list-item-right text color999">2018-1-24</div>
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="<?php echo WAP_OPERATOR_SITE_URL;?>/resource/script/aui/aui-tab.js"></script>
<script type="text/javascript">
  var tab = new auiTab({
    element: document.getElementById('tab'),
    index: 1,
    repeatClick: false
  },function(ret){
    var i = ret.index;
    $('.tab-panel').hide();
    $('#tab-' + i).show();
  });
</script>
