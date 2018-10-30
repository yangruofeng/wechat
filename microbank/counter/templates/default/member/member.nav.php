<div class="content-nav">
    <ul class="nav nav-tabs">
        <li role="presentation" class="<?php echo $_GET['op'] == 'register' ? 'active' : ''; ?>">
            <a href="<?php echo $_GET['op'] == 'register' ? '#' : getUrl('member', 'register', array(), false, ENTRY_COUNTER_SITE_URL);?>">Register</a>
        </li>
        <li role="presentation" class="<?php echo ($_GET['op'] == 'documentCollection' || $_GET['nav_op'] == 'documentCollection') ? 'active' : ''; ?>">
            <a href="<?php echo ($_GET['op'] == 'documentCollection' || $_GET['nav_op'] == 'documentCollection') ? '#' : getUrl('member', 'documentCollection', array(), false, ENTRY_COUNTER_SITE_URL);?>">Document collection</a>
        </li>
        <li role="presentation" class="<?php echo $_GET['op'] == 'fingerprintCollection' ? 'active' : ''; ?>">
            <a href="<?php echo $_GET['op'] == 'fingerprintCollection' ? '#' : getUrl('member', 'fingerprintCollection', array(), false, ENTRY_COUNTER_SITE_URL);?>">Fingerprint Collection</a>
        </li>
        <li role="presentation" class="<?php echo (in_array($_GET['op'], array('loan', 'addContract', 'showCreateContract'))) ? 'active' : ''; ?>">
            <a href="<?php echo (in_array($_GET['op'],array('loan', 'addContract', 'showCreateContract'))) ? '#' : getUrl('member', 'loan', array(), false, ENTRY_COUNTER_SITE_URL);?>">Loan</a>
        </li>
        <li role="presentation" class="<?php echo $_GET['op'] == 'deposit' ? 'active' : ''; ?>">
            <a href="<?php echo $_GET['op'] == 'deposit' ? '#' : getUrl('member', 'deposit', array(), false, ENTRY_COUNTER_SITE_URL);?>">Deposit</a>
        </li>
        <li role="presentation" class="<?php echo $_GET['op'] == 'withdrawal' ? 'active' : ''; ?>">
            <a href="<?php echo $_GET['op'] == 'withdrawal' ? '#' : getUrl('member', 'withdrawal', array(), false, ENTRY_COUNTER_SITE_URL);?>">Withdrawal</a>
        </li>
        <li role="presentation" class="<?php echo $_GET['op'] == 'profile' ? 'active' : ''; ?>">
            <a href="<?php echo $_GET['op'] == 'exchange' ? '#' : getUrl('member', 'profile', array(), false, ENTRY_COUNTER_SITE_URL);?>">Profile</a>
        </li>
    </ul>
</div>
