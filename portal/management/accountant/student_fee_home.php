<?php
require_once './includes/accountant.inc.php';
?>
<style>
    .menu {
        cursor: pointer;
    }

    .menu:hover {
        background-color: #373580 !important;
    }
</style>
<div class="col-12 grid-margin stretch-card">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary">Student Fees</h4>
            <div class="card-header bg-primary rounded text-center text-white menu" onclick="getPage('management/accountant/pay_school_fees_home.php')">
                <span>
                    Pay School Fee
                </span>
            </div>
            <div class="card-header bg-primary rounded text-center text-white menu" onclick="getPage('management/accountant/pay_reg_fee.php')">
                <span>
                  Pay Registration Fee
                </span>
            </div>
            <div class="card-header bg-primary rounded text-center text-white menu" onclick="getPage('management/accountant/school_fee_transactions.php')">
                <span>
                    School Fee Transactions
                </span>
            </div>
            <div class="card-header bg-primary rounded text-center text-white menu" onclick="getPage('management/accountant/reg_fee_transactions.php')">
                <span>
                    Registration Fee Transactions
                </span>
            </div>
        </div>
    </div>
</div>
