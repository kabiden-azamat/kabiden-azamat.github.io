<?php
    $aMetaData = MetaData::get();
?>
<nav>
   <ul class="parent-menu">
       <?foreach($aMetaData as $aMeta):?>
       <?php
       $bHasSub = false;
       $sSubMenus = '';
       $bIsActive = (strtolower($aMeta['module']) == Core::getRouter()->getEvent()) ? true : false;
       $aClass = [];
       $sLink = 'href="' . Core::getRouter()->genUrl(Config::get('admin.action'), $aMeta['module']) . '"';
        if(isset($aMeta['menuItems'])) {
            if(!empty($aMeta['menuItems'])) {
                $sLink = 'title=""';
                $bHasSub = true;
                $aClass[] = 'menu-item-has-children';
                $sDisplay = ($bIsActive) ? ' style="display:block;"' : '';
                $sSubMenus = '<ul'.$sDisplay.'>' . PHP_EOL;
                foreach($aMeta['menuItems'] as $aSub) {
                    $sSubLink = '#';
                    if(isset($aSub['url'])) {
                        $sSubLink = Core::getRouter()->genUrl(Config::get('admin.action'), $aMeta['module'], $aSub['url']);
                    }
                    $sSubMenus .= '<li><a href="'.$sSubLink.'">'.Lang::get($aSub['name']).'</a></li>' . PHP_EOL;
                }
                $sSubMenus .= '</ul>' . PHP_EOL;
            }
        }
        if($bIsActive) {
            $aClass[] = 'active';
        }
       ?>
       <li<?if(!empty($aClass)):?> class="<?=implode(' ', $aClass)?>"<?endif;?>><a <?=$sLink?>><?=$aMeta['module_name']?></a><?=$sSubMenus?></li>      
       <?endforeach;?>
   </ul>
</nav> 