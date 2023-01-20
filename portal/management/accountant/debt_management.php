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
            <h4 class="card-title text-primary">Debt Management Menus</h4>
            <div class="card-header bg-primary rounded text-center text-white menu" onclick="getPage('management/accountant/salary_payables.php')">
                <span>
                    Salary Payables
                </span>
            </div>
            <div class="card-header bg-primary rounded text-center text-white menu" onclick="getPage('management/accountant/school_fee_receivables.php')">
                <span>
                    School Fee Receivables
                </span>
            </div>
            <div class="card-header bg-primary rounded text-center text-white menu" onclick="getPage('management/accountant/reg_fee_receivables.php')">
                <span>
                    Registration Fee Receivables
                </span>
            </div>
        </div>
    </div>
</div>
