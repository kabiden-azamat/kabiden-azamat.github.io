<?php

class EventUser extends Module {
    
    public function init() {
        $this->addEventPreg('/^user$/i', '/^login$/i', 'login');
        
        $this->addEventPreg('/^user$/i', '/^$/i', 'index');
    }
    
    public function index() {
        $this->getRender()->Run('main');
    }
    
    public function login() {
        
        $this->getRender()->setTitle(Lang::get('admin.user.admin_panel_login'));
        $this->getRender()->setTemplate('login');
        $this->getRender()->Run('login');
    }
    
}