<div class="row-fluid">
    <div class="col-xs-6 col-md-3">
        <div class="stat info">
            <h2><?php echo formatQuantity($credit_arr['count_consult']); ?></h2>
            <h6>Consult</h6>
        </div>
    </div>
    <div class="col-xs-6 col-md-3">
        <div class="stat success">
            <h2><?php echo formatQuantity($credit_arr['count_assets']); ?></h2>
            <h6>Assets</h6>
        </div>
    </div>
    <div class="col-xs-6 col-md-3">
        <div class="stat danger">
            <h2><?php echo formatQuantity($credit_arr['count_credit_request']); ?></h2>
            <h6>Request Credit</h6>
        </div>
    </div>
    <div class="col-xs-6 col-md-3">
        <div class="stat primary">
            <h2><?php echo formatQuantity($credit_arr['count_credit_grant']); ?></h2>
            <h6>Grant Credit</h6>
        </div>
    </div>
    <div class="col-xs-6 col-md-3">
        <div class="stat warning">
            <h2><?php echo formatQuantity($credit_arr['count_business_research']); ?></h2>
            <h6>Business Research</h6>
        </div>
    </div>
</div>