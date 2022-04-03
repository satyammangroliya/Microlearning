
<#1>
<?php
\minervis\ToGo\Repository::getInstance()->installTables();
?>
<#2>
<?php
\minervis\ToGo\Repository::getInstance()->installTables();
?>
<#3>
<?php
\minervis\ToGo\Repository::getInstance()->installTables();
?>
<#4>
<?php
\minervis\ToGo\Repository::getInstance()->installTables();
?>
<#5>
<?php
\minervis\ToGo\Repository::getInstance()->installTables();
if(!$ilDB->tableExists('ui_uihk_togo_sess_seq')){
    $offset = \minervis\ToGo\Collection\AnonymousSession::count();
    $ilDB->createSequence('ui_uihk_togo_sess', $offset+1);
}
//migrate data from sess to summary
if($ilDB->tableExists('ui_uihk_togo_sum') && \minervis\ToGo\Collection\AnonymousSummary::count() == 0){
    $sql = 'INSERT INTO ui_uihk_togo_sum (obj_id, tot_ratings, tot_views) select DISTINCT u.obj_id, sum(u.rating), sum(u.view) from ui_uihk_togo_sess u group by obj_id';
    $ilDB->manipulate($sql);
}
?>


