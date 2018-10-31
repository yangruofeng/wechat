<div class="business-condition" style="margin-top: 10px;margin-bottom: 10px">
    <form class="input-search-box" id="frm_search_condition">
        <div class="input-group" style="width: 400px">
            <span id="span_phone_country" class="input-group-addon" style="padding: 0;border: 0;">
                <select class="form-control" name="country_code" style="min-width: 85px;">
                    <option value="855" <?php if($_GET['phone_country'] && $_GET['phone_country'] == 855){echo 'selected';}?>>+855</option>
                    <option value="66"<?php if($_GET['phone_country'] && $_GET['phone_country'] == 66){echo 'selected';}?>>+66</option>
                    <option value="86"<?php if($_GET['phone_country'] && $_GET['phone_country'] == 86){echo 'selected';}?>>+86</option>
                </select>
            </span>
            <input type="text" class="form-control input-search" id="txt_search_phone" name="s_phone" value="<?php echo $_GET['phone_number'];?>" placeholder="">
            <span class="input-group-btn">
                <button type="button" class="btn btn-primary btn-search" onclick="btnSearch_onclick()" id="btn_search" style="border-radius: 0">
                    <i class="fa fa-search"></i>
                    Search
                </button>
            </span>
        </div>
        <div class="btn-group" data-toggle="buttons" style="padding-top: 10px;width: 400px">
            <label class="btn btn-default active">
                <i class="fa fa-phone"></i>
                <input type="radio" onchange="btn_search_by_onclick(this)" value="1" name="rbn_search_by" id="rbn_option1" autocomplete="off" checked>Phone
            </label>
            <label class="btn btn-default">
                <i class="fa fa-id-card"></i>
                <input type="radio" onchange="btn_search_by_onclick(this)" value="2" name="rbn_search_by" id="rbn_option2" autocomplete="off">CID
            </label>
            <label class="btn btn-default">
                <i class="fa fa-at"></i>
                <input type="radio" onchange="btn_search_by_onclick(this)" value="3" name="rbn_search_by" id="rbn_option3" autocomplete="off">LoginAccount
            </label>
            <label class="btn btn-default">
                <i class="fa fa-at"></i>
                <input type="radio" onchange="btn_search_by_onclick(this)" value="4" name="rbn_search_by" id="rbn_option4" autocomplete="off">Name
            </label>
        </div>
    </form>
</div>