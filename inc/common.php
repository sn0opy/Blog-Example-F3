<?php

// Execute validation rules
F3::call('title|entry');

/**
	Functions below are used by both saveblog.php and updateblog.php import
	files for validation of form fields in the blog entry page
**/

function title() {
	// Validate title
	F3::input('title',
		function($value) {
			if (!F3::exists('message')) {
				if (empty($value))
					F3::set('message','Title should not be blank');
				elseif (strlen($value)>127)
					F3::set('message','Title is too long');
				elseif (strlen($value)<3)
					F3::set('message','Title is too short');
			}
			// Do post-processing of title here
			F3::set('REQUEST.title',ucfirst($value));
		}
	);
}

function entry() {
	// Validate blog entry
	F3::input('entry',
		function($value) {
			if (!F3::exists('message')) {
				if (empty($value))
					F3::set('message','Entry should not be blank');
				elseif (strlen($value)>32768)
					F3::set('message','Entry is too long');
				elseif (strlen($value)<3)
					F3::set('message','Entry is too short');
			}
		},
		// Allow these HTML tags in the textarea, so we can make it
		// compatible with TinyMCE, CKEditor, etc.
		'p|b|i|u|br|a|ul|li'
	);
}

?>
