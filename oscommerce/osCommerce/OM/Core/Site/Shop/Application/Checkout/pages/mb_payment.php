<?php 

    $sid = isset($_SESSION['Shop']['PM']['MONEYBOOKERS']['sid']) ? $_SESSION['Shop']['PM']['MONEYBOOKERS']['sid'] : '';
    unset($_SESSION['Shop']['PM']['MONEYBOOKERS']['sid']);
?>
<iframe style="width:99%; height: 400px; border: 0px solid;" src="https://www.moneybookers.com/app/payment.pl?sid=<?= $sid ?>">
</iframe>