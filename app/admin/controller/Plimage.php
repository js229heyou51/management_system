<?php  
namespace app\admin\controller;



class Plimage extends Base{
	public $config = [
		'table' => 'pl_image',
		'sy_id' => 1,
		'sesname' => 'demo_image_id',
		'mlang' => true,
		'seo' => true,
		'link_url' => false,
		'img_sl' => true,
	];
	public $confup = [
		'path' => 'storage/upimg',
		'allowext' => 'jpg|gif|png|bmp|jpeg',
		'maxsize' => '2000000',
		'allownum' => '0',
		'text' => '',
		'sm' => true,
	];

}
?>