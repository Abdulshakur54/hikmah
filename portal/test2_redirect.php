<?php
spl_autoload_register(
    function($class){
        require_once'../classes/'.$class.'.php';
    }
);
echo 'status: '.Utility::escape(Input::get('status')).'<br/>';
echo 'tx_ref: '.Utility::escape(Input::get('tx_ref')).'<br/>';
echo 'transaction_id: '.Utility::escape(Input::get('transaction_id')).'<br/>';