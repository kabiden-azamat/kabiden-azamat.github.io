<!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js"> <!--<![endif]-->
<head>

    <!-- Meta-Information -->
    <?= $this->header() ?>

</head>
<body>
<!--[if lt IE 7]>
<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
    your browser</a> to improve your experience.</p>
<![endif]-->

<!-- Our Website Content Goes Here -->
<header class="simple-normal">
     <div class="top-bar">
          <div class="logo">
               <a href="<?=Core::getRouter()->genUrl(Core::getRouter()->getAction())?>" title=""><i class="fa fa-bullseye"></i> <?=Config::get('admin.panel.name')?></a>
          </div>
          <div class="menu-options"><span class="menu-action"><i></i></span></div>
     </div><!-- Top Bar -->
     <div class="side-menu-sec" id="header-scroll">
          <div class="side-menus">
               <span style="padding-top:20px;"><?=Lang::get('navigation')?></span>
              <?= $this->inc_tpl('inc/main-menu') ?>
          </div>
     </div>
</header>

<?=$this->inc_tpl('inc/content')?>


<?= $this->footer() ?>

</body>
</html>