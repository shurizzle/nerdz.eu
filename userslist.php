<?php
    ob_start('ob_gzhandler');
    require_once $_SERVER['DOCUMENT_ROOT'].'/class/autoload.php';
    
    $core = new Core();
    $tplcfg = $core->getTemplateCfg();

    ob_start(array('Core','minifyHtml'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="author" content="Paolo Galeone" />
    <meta name="keywords" content="nerdz, social network, user profile, paste, source code, programming" />
    <meta name="description" content="NERDZ is a mix between a social network and a forum. You can share your code, enjoy information technology, talk about nerd stuff and more. Join in!" />
    <meta name="robots" content="index,follow" />
    <title><?= $core->getSiteName(), ' - ', $core->lang('USERS_LIST'); ?></title>
<?php
    $headers = $tplcfg->getTemplateVars('userslist');
    require_once $_SERVER['DOCUMENT_ROOT'].'/pages/common/jscssheaders.php';
?>
</head>
<?php ob_flush(); ?>
<body>
<div id="body">
<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/pages/header.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/pages/userslist.php';
    require_once $_SERVER['DOCUMENT_ROOT'].'/pages/footer.php';
?>
</div>
</body>
</html>
