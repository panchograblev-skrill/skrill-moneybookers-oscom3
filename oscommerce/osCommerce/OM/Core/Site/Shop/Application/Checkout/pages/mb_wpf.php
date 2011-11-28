<?php 

    $url = isset($_SESSION['Shop']['PM']['WPF']['url']) ? $_SESSION['Shop']['PM']['WPF']['url'] : '';
    unset($_SESSION['Shop']['PM']['WPF']['url']);
?>
<iframe style="width:99%; height: 400px; border: 0px solid;" src="<?= $url ?>"></iframe>