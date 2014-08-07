<?php
if(!$core->isLogged())
    die(header('Location: /'));

use NERDZ\Core;
use NERDZ\Core\Db;

$vals = [];

$Banners = (new Core\Banners())->getBanners();
$vals['banners_a'] = [];
shuffle($Banners);
foreach($Banners as $ban)
    $vals['banners_a'][$ban[1]] = $ban[2];

require_once $_SERVER['DOCUMENT_ROOT'].'/pages/common/vars.php';

$longlangs  = $core->availableLanguages(1);

$vals['langs_a'] = [];
$i = 0;
foreach($longlangs as $id => $val)
{
    $vals['langs_a'][$i]['longlang_n'] = $val;
    $vals['langs_a'][$i]['shortlang_n'] = $id;
    ++$i;
}

$l = $core->getFollow($_SESSION['id']);
$tot = count($l);

if($tot>0)
{
    if(!empty($l[0]))
    {
        $myarray = [];
        $c = 0;
        for($i=0;$i<$tot;++$i)
        {
            if(!($o = Db::query(array('SELECT "birth_date" FROM "users" WHERE "counter" = :id',array(':id' => $l[$i])),Db::FETCH_OBJ)))
            {
                echo $core->lang('ERROR');
                break;
            }
            $myarray[$i]['id_n'] = $l[$i];
            $myarray[$i]['username_n'] = $core->getUsername($l[$i]);
            $myarray[$i]['username4link_n'] = \NERDZ\Core\Utils::userLink($myarray[$i]['username_n']);
            $myarray[$i]['online_b'] = $core->isOnline($l[$i]);
            if($myarray[$i]['online_b'])
                ++$c;
            $myarray[$i]['birthday_b'] = date('d-m',strtotime($o->birth_date)) == date('d-m',time());
        }
        
        function sortbyusername($a, $b)
        {
            $x = strtolower($a['username_n']);
            $y = strtolower($b['username_n']);
            if ($y == $x)
                return 0;
            return $x < $y ? -1 : 1;
        }
        
        function sortbyonlinestatus($a,$b)
        {
            if(($a['online_b'] && $b['online_b']) || (!$a['online_b'] && !$b['online_b']))
                return sortbyusername($a,$b);
                
            return $b['online_b'] ? 1 : -1;
        }
        
        usort($myarray,'sortbyonlinestatus');
    }
    $vals['followed_a'] = $myarray;
}
else
    $c = 0;


$vals['followedtot_n'] = $tot;
$vals['followedonlinetot_n'] = $c;

if(!($r = Db::query(array('SELECT "name" FROM "groups" WHERE "owner" = :id',array(':id' => $_SESSION['id'])),Db::FETCH_STMT)))
    die($core->lang('ERROR'));
    
$vals['ownerof_a'] = [];
$i = 0;
while(($o = $r->fetch(PDO::FETCH_OBJ)))
{
    $vals['ownerof_a'][$i]['name_n'] = $o->name;
    $vals['ownerof_a'][$i]['name4link_n'] = \NERDZ\Core\Utils::projectLink($o->name);
    ++$i;
}

if(!($r = Db::query(array('SELECT "name" FROM "groups" INNER JOIN "groups_members" ON "groups"."counter" = "groups_members"."to" WHERE "from" = :id',array(':id' => $_SESSION['id'])),Db::FETCH_STMT)))
    die($core->lang('ERROR'));
    
$vals['memberof_a'] = [];
$i = 0;
while(($o = $r->fetch(PDO::FETCH_OBJ)))
{
    $vals['memberof_a'][$i]['name_n'] = $o->name;
    $vals['memberof_a'][$i]['name4link_n'] = \NERDZ\Core\Utils::projectLink($o->name);
    ++$i;
}

$core->getTPL()->assign($vals);
$core->getTPL()->draw('home/layout');
?>
