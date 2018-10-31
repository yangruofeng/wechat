<div class="row-fluid">
    <div class="col-xs-6 col-md-3">
        <div class="stat info">
            <h2><?php echo formatQuantity($client['count_register_client']); ?></h2>
            <h6>Register</h6>
        </div>
    </div>
    <div class="col-xs-6 col-md-3">
        <div class="stat success">
            <h2><?php echo formatQuantity($client['count_checked_client']); ?></h2>
            <h6>Checked</h6>
        </div>
    </div>
    <div class="col-xs-6 col-md-3">
        <div class="stat danger">
            <h2><?php echo formatQuantity($client['count_lock_client']); ?></h2>
            <h6>Locked</h6>
        </div>
    </div>
    <div class="col-xs-6 col-md-3">
        <div class="stat primary">
            <h2><?php echo formatQuantity($client['count_verify_client']); ?></h2>
            <h6>Verified</h6>
        </div>
    </div>
</div>