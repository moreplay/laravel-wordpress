<?php
/**
 * Created by IntelliJ IDEA.
 * User: dev
 * Date: 2018/5/4
 * Time: 下午7:18
 */

namespace App;

use Illuminate\Foundation\Application;
use Symfony\Component\HttpFoundation\Response;

class Kernel extends Application {
	
	private $exited = false;
	
	/** @var \Illuminate\Http\Request */
	private $_request;
	
	/** @var \Illuminate\Foundation\Http\Kernel */
	private $_kernel;
	
	public function exit($exit = null)
	{
		if(isset($exit)) {
			$this->exited = $exit;
		}
		
		return $this->exited;
	}
	
	public function init(){
		//$app = app();
		/** @var \Illuminate\Foundation\Http\Kernel $kernel */
		$this->_kernel = $this->make(\Illuminate\Contracts\Http\Kernel::class);
		$this->_request = \Illuminate\Http\Request::capture();
		$this->_request->enableHttpMethodParameterOverride();
		$this->instance('request', $this->_request);
		$this->_kernel->bootstrap();
		
		//kd(config('system.filter_log'));
		add_action('wp', function() {
			global $wp_query;
			if($wp_query->is_404()) {
				status_header(200);
				$wp_query->is_404 = false;
			}
		});
		
		if(defined('WP_ADMIN')) {
			add_action('admin_init', [ app(), 'run' ], 100);
		} elseif(defined('WP_USE_THEMES') && WP_USE_THEMES) {
			add_action('template_redirect', [ app(), 'run' ], 100);
		}
	}
	
	public function run()
	{
		$request = $this->_request;
		$kernel = $this->_kernel;
		$response = $kernel->handle($request);
		$response->sendHeaders()->sendContent();
		$kernel->terminate($request, $response);
		if($this->exited || $response instanceof Response && $response->getContent()  ) {
			while(ob_get_level() > 0) {
				ob_end_flush();
			}
			exit;
		}
	}
}
