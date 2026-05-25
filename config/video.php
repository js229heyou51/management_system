<?php  
// config/video.php
return [
	// 允许的视频格式
	'allow_ext' => ['mp4', 'mov', 'avi', 'wmv', 'flv', 'mkv', 'webm'],
	// 最大上传大小（默认200MB）
	'max_size' => 200 * 1024 * 1024,
	// 是否使用ffmpeg获取信息
	'use_ffmpeg' => true,
	// ffmpeg路径（如果不在环境变量中）
	'ffmpeg_path' => '/usr/bin/ffmpeg',
	'ffprobe_path' => '/usr/bin/ffprobe',
];
?>