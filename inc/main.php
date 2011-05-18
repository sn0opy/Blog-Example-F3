<?php

class main extends F3instance
{
    function __construct()
    {
        if(!file_exists(F3::get('data')))
        {
            $this->get('DB')->sql(
                'CREATE TABLE blogs ('.
                    'title TEXT,'.
                    'entry TEXT,'.
                    'time TEXT,'.
                    'PRIMARY KEY(time)'.
                ');'
            );
            $this->get('DB')->sql(
                'CREATE TABLE comments ('.
                    'blogref TEXT,'.
                    'comment TEXT,'.
                    'time TEXT,'.
                    'PRIMARY KEY(blogref,time)'.
                ');'
            );
        }
    }
    
    function rss()
    {
        $blog = new Axon('blogs');
        $this->set('entries', $blog->find());
        echo Template::serve('rss.xml', 'text/xml');   
    }
    
    function showBlog()
    {
        $blog = new Axon('blogs');
        $this->set('entries', $blog->find());

        $this->set('pagetitle', 'Home');
        $this->set('template', 'home');
        $this->render();
    }
    
    function showAbout()
    {
        $this->set('pagetitle', 'About');
        $this->set('template', 'about');
        $this->render();
    }

        
    function doLogin() {
        F3::input('userID',
            function($value) {
                if (!F3::exists('message')) {
                    if (empty($value))
                        F3::set('message','User ID should not be blank');
                    elseif (strlen($value)>24)
                        F3::set('message','User ID is too long');
                    elseif (strlen($value)<3)
                        F3::set('message','User ID is too short');
                }
                F3::set('REQUEST.userID', strtolower($value));
            }
        );

        $this->input('password',
            function($value) {
                if (!F3::exists('message')) {
                    if (empty($value))
                        F3::set('message','Password must be specified');
                    elseif (strlen($value)>24)
                        F3::set('message','Invalid password');
                }
            }
        );
        
        /*$this->input('captcha',
            function($value) {
                if (!$this->exists('message') && $this->exists('SESSION.captcha')) {
                    $captcha=F3::get('SESSION.captcha');
                    if (empty($value))
                        F3::set('message','Verification code required');
                    elseif (strlen($value)>strlen($captcha))
                        F3::set('message','Verification code is too long');
                    elseif (strtolower($value)!=$captcha)
                        F3::set('message','Invalid verification code');
                }
            }
        );*/
        
        if (!$this->exists('message')) {
            if (preg_match('/^admin$/i', $this->get('REQUEST.userID')) && preg_match('/^admin$/i', $this->get('REQUEST.password'))) {
                $this->set('SESSION.user', '{{@REQUEST.userID}}');
                $this->reroute('/');
            }
            else
                $this->set('message','Invalid user ID or password');
        }
        
        $this->reroute('/login');
    }
    
    function showLogin() {
        $this->clear('SESSION.user');
        $this->clear('SESSION.captcha');

        $this->set('pagetitle','Login');
        $this->set('template','login');
        $this->render();
    }  
    
    function doLogout()
    {
        session_destroy();
        $this->reroute('/');
    }
    
    function showCreate()
    {
        F3::set('pagetitle', 'Add Blog Entry');
        F3::set('template', 'blog');
        $this->render();  
    }
    
    function doCreate()
    {
        F3::clear('message');
        #F3::call(':common');

        if(!F3::exists('message')) {
            $blog=new Axon('blogs');
            $blog->copyFrom('REQUEST');
            $blog->time=date(F3::get('timeformat'));
            $blog->save();
            F3::reroute('/');
        }
        
        self::showCreate();
    }
    
    function doDelete()
    {
        if(F3::get('SESSION.user')) {
            $blog=new Axon('blogs');
            $blog->load('time="'.date(F3::get('timeformat'), F3::get('PARAMS.time')).'"');
            $blog->erase();
            F3::reroute('/');
        }
        else {
            F3::set('pagetitle', 'Delete Blog Entry');
            F3::set('template', 'blog');
            $this->render();
        }
    }
    
    function showEdit()
    {
        $blog=new Axon('blogs');
        $blog->load('time="'. date(F3::get('timeformat'), F3::get('PARAMS.time')). '"');
        if (!$blog->dry()) {
            $blog->copyTo('REQUEST');
            F3::set('pagetitle', 'Edit Blog Entry');
            F3::set('template', 'blog');
            $this->render();
        }
        else
            F3::http404();
    }
    
    function doEdit()
    {
        F3::clear('message');
        #F3::call(:common);
        
        if (!F3::exists('message')) {
            $blog=new Axon('blogs');
            $blog->load('time="'. date(F3::get('timeformat'), F3::get('PARAMS.time')).'"');
            $blog->copyFrom('REQUEST');
            $blog->time='"'.date(F3::get('timeformat')).'"';
            $blog->save();
            F3::reroute('/');
        }
        self::showEdit();
    }
    
    function render() {
        echo Template::serve('layout.htm');
    }
}