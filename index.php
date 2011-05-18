<?php

require_once __DIR__.'/lib/base.php';
include 'lib/graphics.php';

F3::set('RELEASE', FALSE);
F3::set('DEBUG', 3);
F3::set('FONTS', 'fonts/');
F3::set('AUTOLOAD', 'inc/');
F3::set('GUI', 'gui/');
F3::set('PROXY', 1);
F3::set('EXTEND', 1);
F3::set('CACHE', 'folder=cache/');

F3::set('E404', 'layout.htm');
F3::set('site', 'Blog/RSS Demo');
F3::set('data', 'db/demo.db'); // Can be an absolute or relative path
F3::set('timeformat', 'd M Y H:i:s');

F3::set('DB', new DB('sqlite:' .F3::get('data')));
F3::set('extlink','window.open(this.href); return false;');

F3::set('menu',
	array_merge(
		array(
			'Home'=>'/'
		),
            
		F3::get('SESSION.user')?
			array(
				'Logout'=>'/logout'
			):
			array(
				'About'=>'/about',
				'Login'=>'/login'
			)
	)
);

F3::route('GET /', 'main->showBlog');
F3::route('GET /about','main->showAbout', 3600);
F3::route('GET /login','main->showLogin', 3600);
F3::route('GET /rss','main->rss');
F3::route('GET /logout','main->doLogout');
F3::route('GET /captcha', function() {
    Graphics::captcha(150, 60, 5, 'jester');
});
F3::route('GET /create','main->showCreate');
F3::route('GET /edit/@time','main->showEdit');
F3::route('GET /delete/@time','main->doDelete');

F3::route('POST /login','main->doLogin');
F3::route('POST /create','main->doCreate');
F3::route('POST /edit/@time','main->showEdit');

F3::run();

?>
