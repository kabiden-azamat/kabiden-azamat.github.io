<div class="main-content">
     <div class="panel-content" style="margin-top: 67px;">
          <?if(!empty($this->getTitle())):?>
          <div class="main-title-sec">
               <div class="row">
                    <div class="col-md-3 column">
                         <div class="heading-profile">
                              <h2><?=$this->getTitle()?></h2>
                         </div>
                    </div>
                    <div class="col-md-9 column">
                         <div class="quick-btn-title">
                              <a href="javascript:void(0)" title=""><i class="fa fa-plus"></i> <?=Lang::get('add')?></a>
                         </div>
                    </div>
               </div>
          </div><!-- Heading Sec -->
          <?endif;?>
          <?php
               $aBreadCrumbs = $this->getBreadCrumbs();
          ?>
          <?if(!empty($aBreadCrumbs)):?>
          <ul class="breadcrumbs">
               <li><a href="<?=Core::getRouter()->genUrl(Core::getRouter()->getAction())?>" title=""><?=Config::get('admin.panel.name')?></a></li>
               <?foreach($aBreadCrumbs as $aBread):?>
                    <li><?if($aBread['url']):?><a href="<?=$aBread['url']?>"><?endif;?><?=$aBread['title']?><?if($aBread['url']):?></a><?endif;?></li>
               <?endforeach;?>
          </ul>
          <?endif;?>
          <div class="main-content-area">
               <div class="row">
                    <div class="col-md-12">
                         <div class="widget blank">
                              <?=$this->loadTemplate()?>
                         </div>
                    </div>
               </div>
          </div>
     </div><!-- Panel Content -->
</div>