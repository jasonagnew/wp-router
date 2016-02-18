<?php

class WP_Template_Response extends WP_Response {

    public function __construct( $file, $varibales = array(), $status = 200, $headers = array() )
    {
    	$path = $this->file_path( $file );

    	ob_start();
	    	extract($varibales);
	    	require $path;
			$data = ob_get_contents();
		ob_end_clean();

    	return parent::__construct( $data, $status, $headers );
    }

    protected function file_path( $path )
    {
		$path = str_replace('{root}', ABSPATH, $path);
		$path = str_replace('{wp-content}', WP_CONTENT_DIR, $path);
		$path = str_replace('{active-theme}', get_stylesheet_directory(), $path);

		return $path;
    }
}
