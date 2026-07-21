
SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for st_article_co
-- ----------------------------
DROP TABLE IF EXISTS `st_article_co`;
CREATE TABLE `st_article_co` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `lm` int(11) DEFAULT NULL COMMENT '上一级',
  `list_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '所有父级',
  `link_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '跳转链接',
  `apname` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '页面名称',
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '标题',
  `keyword` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '关键词',
  `f_body` text COLLATE utf8mb4_general_ci COMMENT '简要介绍',
  `z_body` text COLLATE utf8mb4_general_ci COMMENT '详细介绍',
  `t_body` text COLLATE utf8mb4_general_ci COMMENT '其他介绍',
  `g_body` text COLLATE utf8mb4_general_ci COMMENT '其他介绍',
  `ym_tit` text COLLATE utf8mb4_general_ci COMMENT 'seo标题',
  `ym_key` text COLLATE utf8mb4_general_ci COMMENT 'seo关键词',
  `ym_des` text COLLATE utf8mb4_general_ci COMMENT 'seo介绍',
  `img_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片',
  `pic_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片2',
  `fil_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '文件',
  `vid_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '视频',
  `ding` tinyint(1) DEFAULT '0' COMMENT '置顶',
  `tuijian` tinyint(1) DEFAULT '0' COMMENT '推荐',
  `hot` tinyint(1) DEFAULT '0' COMMENT '热门',
  `pass` tinyint(1) DEFAULT '1' COMMENT '屏蔽',
  `read_num` int(11) DEFAULT '0' COMMENT '浏览次数',
  `px` int(11) DEFAULT NULL COMMENT '排序',
  `ip` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'ip',
  `lang` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '语言',
  `wtime` datetime DEFAULT NULL COMMENT '创建时间',
  `delete_time` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='文章管理';

-- ----------------------------
-- Table structure for st_article_lm
-- ----------------------------
DROP TABLE IF EXISTS `st_article_lm`;
CREATE TABLE `st_article_lm` (
  `id_lm` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id_lm',
  `fid` int(11) DEFAULT NULL COMMENT '上一级',
  `list_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '所有父级',
  `level_lm` int(11) DEFAULT NULL COMMENT '所有父级',
  `url_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '跳转链接',
  `apname_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '页面名称',
  `title_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '标题',
  `f_body_lm` text COLLATE utf8mb4_general_ci COMMENT '简要介绍',
  `z_body_lm` text COLLATE utf8mb4_general_ci COMMENT '详细介绍',
  `ym_tit` text COLLATE utf8mb4_general_ci COMMENT 'seo标题',
  `ym_key` text COLLATE utf8mb4_general_ci COMMENT 'seo关键词',
  `ym_des` text COLLATE utf8mb4_general_ci COMMENT 'seo介绍',
  `img_sl_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片',
  `pic_sl_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片2',
  `add_xx` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '分类是否可以添加信息',
  `add_xia` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '是否有下一级分类',
  `con_att` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '分类属性',
  `tuijian` tinyint(1) DEFAULT NULL COMMENT '推荐',
  `hot` tinyint(1) DEFAULT NULL COMMENT '热门',
  `pass` tinyint(1) DEFAULT NULL COMMENT '屏蔽',
  `px` int(11) DEFAULT NULL COMMENT '排序',
  `ip` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'ip',
  `lang` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '语言',
  `wtime` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `delete_time` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id_lm`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='文章管理';

-- ----------------------------
-- Table structure for st_book
-- ----------------------------
DROP TABLE IF EXISTS `st_book`;
CREATE TABLE `st_book` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lang` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `id_re` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `num` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `rename` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sex` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `phone` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fax` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `qq` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `wx` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `compname` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `address` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `post` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `z_body` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `wtime` datetime DEFAULT NULL,
  `ip` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `chakan` smallint(1) unsigned NOT NULL,
  `huifu` smallint(1) unsigned NOT NULL,
  `pass` smallint(1) unsigned NOT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_re` (`id_re`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_brand_co
-- ----------------------------
DROP TABLE IF EXISTS `st_brand_co`;
CREATE TABLE `st_brand_co` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `lm` int(11) DEFAULT NULL COMMENT '上一级',
  `list_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '所有父级',
  `link_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '跳转链接',
  `apname` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '页面名称',
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '标题',
  `keyword` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '关键词',
  `f_body` text COLLATE utf8mb4_general_ci COMMENT '简要介绍',
  `z_body` text COLLATE utf8mb4_general_ci COMMENT '详细介绍',
  `t_body` text COLLATE utf8mb4_general_ci COMMENT '其他介绍',
  `g_body` text COLLATE utf8mb4_general_ci COMMENT '其他介绍',
  `ym_tit` text COLLATE utf8mb4_general_ci COMMENT 'seo标题',
  `ym_key` text COLLATE utf8mb4_general_ci COMMENT 'seo关键词',
  `ym_des` text COLLATE utf8mb4_general_ci COMMENT 'seo介绍',
  `img_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片',
  `pic_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片2',
  `fil_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '文件',
  `vid_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '视频',
  `ding` tinyint(1) DEFAULT NULL COMMENT '置顶',
  `tuijian` tinyint(1) DEFAULT NULL COMMENT '推荐',
  `hot` tinyint(1) DEFAULT NULL COMMENT '热门',
  `pass` tinyint(1) DEFAULT NULL COMMENT '屏蔽',
  `read_num` int(11) DEFAULT NULL COMMENT '浏览次数',
  `px` int(11) DEFAULT NULL COMMENT '排序',
  `ip` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'ip',
  `lang` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '语言',
  `wtime` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `delete_time` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `lm` (`lm`,`list_lm`,`title`),
  KEY `pass` (`pass`,`px`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='品牌管理';

-- ----------------------------
-- Table structure for st_brand_lm
-- ----------------------------
DROP TABLE IF EXISTS `st_brand_lm`;
CREATE TABLE `st_brand_lm` (
  `id_lm` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id_lm',
  `fid` int(11) DEFAULT NULL COMMENT '上一级',
  `list_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '所有父级',
  `level_lm` int(11) DEFAULT NULL COMMENT '所有父级',
  `url_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '跳转链接',
  `apname_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '页面名称',
  `title_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '标题',
  `f_body_lm` text COLLATE utf8mb4_general_ci COMMENT '简要介绍',
  `z_body_lm` text COLLATE utf8mb4_general_ci COMMENT '详细介绍',
  `ym_tit` text COLLATE utf8mb4_general_ci COMMENT 'seo标题',
  `ym_key` text COLLATE utf8mb4_general_ci COMMENT 'seo关键词',
  `ym_des` text COLLATE utf8mb4_general_ci COMMENT 'seo介绍',
  `img_sl_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片',
  `pic_sl_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片2',
  `add_xx` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '分类是否可以添加信息',
  `add_xia` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '是否有下一级分类',
  `con_att` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '分类属性',
  `tuijian` tinyint(1) DEFAULT NULL COMMENT '推荐',
  `hot` tinyint(1) DEFAULT NULL COMMENT '热门',
  `pass` tinyint(1) DEFAULT NULL COMMENT '屏蔽',
  `px` int(11) DEFAULT NULL COMMENT '排序',
  `ip` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'ip',
  `lang` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '语言',
  `wtime` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `delete_time` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id_lm`) USING BTREE,
  KEY `fid` (`fid`,`list_lm`,`title_lm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='品牌管理';

-- ----------------------------
-- Table structure for st_config
-- ----------------------------
DROP TABLE IF EXISTS `st_config`;
CREATE TABLE `st_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `lists` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_express
-- ----------------------------
DROP TABLE IF EXISTS `st_express`;
CREATE TABLE `st_express` (
  `id` int(11) NOT NULL,
  `oid` int(11) DEFAULT NULL,
  `express_no` varchar(55) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `company` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `num` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_feed_co
-- ----------------------------
DROP TABLE IF EXISTS `st_feed_co`;
CREATE TABLE `st_feed_co` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `lm` int(11) DEFAULT NULL COMMENT '上一级',
  `list_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '所有父级',
  `link_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '跳转链接',
  `apname` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '页面名称',
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '标题',
  `keyword` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '关键词',
  `num` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '关键词',
  `article_str` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '关键词',
  `web_str` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '关键词',
  `f_body` text COLLATE utf8mb4_general_ci COMMENT '简要介绍',
  `z_body` text COLLATE utf8mb4_general_ci COMMENT '详细介绍',
  `t_body` text COLLATE utf8mb4_general_ci COMMENT '其他介绍',
  `g_body` text COLLATE utf8mb4_general_ci COMMENT '其他介绍',
  `ym_tit` text COLLATE utf8mb4_general_ci COMMENT 'seo标题',
  `ym_key` text COLLATE utf8mb4_general_ci COMMENT 'seo关键词',
  `ym_des` text COLLATE utf8mb4_general_ci COMMENT 'seo介绍',
  `img_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片',
  `pic_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片2',
  `fil_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '文件',
  `vid_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '视频',
  `ding` tinyint(1) DEFAULT NULL COMMENT '置顶',
  `tuijian` tinyint(1) DEFAULT NULL COMMENT '推荐',
  `hot` tinyint(1) DEFAULT NULL COMMENT '热门',
  `pass` tinyint(1) DEFAULT NULL COMMENT '屏蔽',
  `read_num` int(11) DEFAULT NULL COMMENT '浏览次数',
  `px` int(11) DEFAULT NULL COMMENT '排序',
  `ip` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'ip',
  `lang` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '语言',
  `wtime` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `delete_time` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='投喂管理';

-- ----------------------------
-- Table structure for st_feed_lm
-- ----------------------------
DROP TABLE IF EXISTS `st_feed_lm`;
CREATE TABLE `st_feed_lm` (
  `id_lm` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id_lm',
  `fid` int(11) DEFAULT NULL COMMENT '上一级',
  `list_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '所有父级',
  `level_lm` int(11) DEFAULT NULL COMMENT '所有父级',
  `url_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '跳转链接',
  `apname_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '页面名称',
  `title_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '标题',
  `f_body_lm` text COLLATE utf8mb4_general_ci COMMENT '简要介绍',
  `z_body_lm` text COLLATE utf8mb4_general_ci COMMENT '详细介绍',
  `ym_tit` text COLLATE utf8mb4_general_ci COMMENT 'seo标题',
  `ym_key` text COLLATE utf8mb4_general_ci COMMENT 'seo关键词',
  `ym_des` text COLLATE utf8mb4_general_ci COMMENT 'seo介绍',
  `img_sl_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片',
  `pic_sl_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片2',
  `add_xx` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '分类是否可以添加信息',
  `add_xia` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '是否有下一级分类',
  `con_att` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '分类属性',
  `tuijian` tinyint(1) DEFAULT NULL COMMENT '推荐',
  `hot` tinyint(1) DEFAULT NULL COMMENT '热门',
  `pass` tinyint(1) DEFAULT NULL COMMENT '屏蔽',
  `px` int(11) DEFAULT NULL COMMENT '排序',
  `ip` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'ip',
  `lang` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '语言',
  `wtime` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `delete_time` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id_lm`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='投喂管理';

-- ----------------------------
-- Table structure for st_feed_record
-- ----------------------------
DROP TABLE IF EXISTS `st_feed_record`;
CREATE TABLE `st_feed_record` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `lm` int(11) DEFAULT NULL COMMENT '上一级',
  `list_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '所有父级',
  `link_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '跳转链接',
  `apname` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '页面名称',
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '标题',
  `title_lm` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '标题',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '标题',
  `account` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '标题',
  `keyword` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '关键词',
  `num` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '关键词',
  `article_str` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '关键词',
  `web_str` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '关键词',
  `article_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '关键词',
  `web_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '关键词',
  `web_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '关键词',
  `feed_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '关键词',
  `f_body` text COLLATE utf8mb4_general_ci COMMENT '简要介绍',
  `z_body` text COLLATE utf8mb4_general_ci COMMENT '详细介绍',
  `t_body` text COLLATE utf8mb4_general_ci COMMENT '其他介绍',
  `g_body` text COLLATE utf8mb4_general_ci COMMENT '其他介绍',
  `ym_tit` text COLLATE utf8mb4_general_ci COMMENT 'seo标题',
  `ym_key` text COLLATE utf8mb4_general_ci COMMENT 'seo关键词',
  `ym_des` text COLLATE utf8mb4_general_ci COMMENT 'seo介绍',
  `img_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片',
  `pic_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片2',
  `fil_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '文件',
  `vid_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '视频',
  `ding` tinyint(1) DEFAULT NULL COMMENT '置顶',
  `tuijian` tinyint(1) DEFAULT NULL COMMENT '推荐',
  `hot` tinyint(1) DEFAULT NULL COMMENT '热门',
  `pass` tinyint(1) DEFAULT NULL COMMENT '屏蔽',
  `read_num` int(11) DEFAULT NULL COMMENT '浏览次数',
  `px` int(11) DEFAULT NULL COMMENT '排序',
  `ip` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'ip',
  `lang` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '语言',
  `wtime` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `delete_time` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='投喂管理';

-- ----------------------------
-- Table structure for st_files_co
-- ----------------------------
DROP TABLE IF EXISTS `st_files_co`;
CREATE TABLE `st_files_co` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `lm` int(11) DEFAULT NULL COMMENT '上一级',
  `list_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '所有父级',
  `link_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '跳转链接',
  `apname` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '页面名称',
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '标题',
  `keyword` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '关键词',
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '文件类型',
  `f_body` text COLLATE utf8mb4_general_ci COMMENT '简要介绍',
  `z_body` text COLLATE utf8mb4_general_ci COMMENT '详细介绍',
  `t_body` text COLLATE utf8mb4_general_ci COMMENT '其他介绍',
  `g_body` text COLLATE utf8mb4_general_ci COMMENT '其他介绍',
  `ym_tit` text COLLATE utf8mb4_general_ci COMMENT 'seo标题',
  `ym_key` text COLLATE utf8mb4_general_ci COMMENT 'seo关键词',
  `ym_des` text COLLATE utf8mb4_general_ci COMMENT 'seo介绍',
  `img_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片',
  `pic_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片2',
  `fil_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '文件',
  `vid_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '视频',
  `ding` tinyint(1) DEFAULT NULL COMMENT '置顶',
  `tuijian` tinyint(1) DEFAULT NULL COMMENT '推荐',
  `hot` tinyint(1) DEFAULT NULL COMMENT '热门',
  `pass` tinyint(1) DEFAULT NULL COMMENT '屏蔽',
  `read_num` int(11) DEFAULT NULL COMMENT '浏览次数',
  `px` int(11) DEFAULT NULL COMMENT '排序',
  `ip` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'ip',
  `lang` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '语言',
  `wtime` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `delete_time` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='文件';

-- ----------------------------
-- Table structure for st_files_lm
-- ----------------------------
DROP TABLE IF EXISTS `st_files_lm`;
CREATE TABLE `st_files_lm` (
  `id_lm` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id_lm',
  `fid` int(11) DEFAULT NULL COMMENT '上一级',
  `list_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '所有父级',
  `level_lm` int(11) DEFAULT NULL COMMENT '所有父级',
  `url_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '跳转链接',
  `apname_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '页面名称',
  `title_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '标题',
  `f_body_lm` text COLLATE utf8mb4_general_ci COMMENT '简要介绍',
  `z_body_lm` text COLLATE utf8mb4_general_ci COMMENT '详细介绍',
  `ym_tit` text COLLATE utf8mb4_general_ci COMMENT 'seo标题',
  `ym_key` text COLLATE utf8mb4_general_ci COMMENT 'seo关键词',
  `ym_des` text COLLATE utf8mb4_general_ci COMMENT 'seo介绍',
  `img_sl_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片',
  `pic_sl_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片2',
  `add_xx` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '分类是否可以添加信息',
  `add_xia` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '是否有下一级分类',
  `con_att` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '分类属性',
  `tuijian` tinyint(1) DEFAULT NULL COMMENT '推荐',
  `hot` tinyint(1) DEFAULT NULL COMMENT '热门',
  `pass` tinyint(1) DEFAULT NULL COMMENT '屏蔽',
  `px` int(11) DEFAULT NULL COMMENT '排序',
  `ip` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'ip',
  `lang` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '语言',
  `wtime` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `delete_time` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id_lm`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='文件';

-- ----------------------------
-- Table structure for st_gallery
-- ----------------------------
DROP TABLE IF EXISTS `st_gallery`;
CREATE TABLE `st_gallery` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '图片标题',
  `filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '原始文件名',
  `path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '存储路径',
  `size` int(11) DEFAULT '0' COMMENT '文件大小(字节)',
  `mime_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '文件类型',
  `upload_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '上传方式',
  `extension` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '文件扩展名',
  `width` int(11) DEFAULT '0' COMMENT '图片宽度',
  `height` int(11) DEFAULT '0' COMMENT '图片高度',
  `md5` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '文件MD5',
  `category_id` int(11) DEFAULT '0' COMMENT '分类ID',
  `user_id` int(11) DEFAULT '0' COMMENT '上传用户ID',
  `status` int(1) DEFAULT '1' COMMENT '状态:1正常,0删除',
  `meta_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '元数据(JSON)',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '描述',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_md5` (`md5`),
  KEY `idx_category` (`category_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_create_time` (`create_time`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='图片库';

-- ----------------------------
-- Table structure for st_gallery_category
-- ----------------------------
DROP TABLE IF EXISTS `st_gallery_category`;
CREATE TABLE `st_gallery_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned DEFAULT '0' COMMENT '父级ID',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '分类名称',
  `slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '分类标识（URL友好）',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '分类描述',
  `cover_image_id` int(11) unsigned DEFAULT '0' COMMENT '封面图片ID',
  `type` int(1) DEFAULT '2' COMMENT '分类类型:1系统分类,2用户分类',
  `sort_order` int(11) DEFAULT '0' COMMENT '排序',
  `status` int(1) DEFAULT '1' COMMENT '状态:1启用,0禁用',
  `icon` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '分类图标',
  `color` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '分类颜色',
  `seo_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'SEO数据(JSON)',
  `user_id` int(11) unsigned DEFAULT '0' COMMENT '创建用户ID',
  `create_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  `delete_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `idx_parent` (`parent_id`),
  KEY `idx_type` (`type`),
  KEY `idx_status` (`status`),
  KEY `idx_sort_order` (`sort_order`),
  KEY `idx_user` (`user_id`),
  KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='图库分类表';

-- ----------------------------
-- Table structure for st_home_co
-- ----------------------------
DROP TABLE IF EXISTS `st_home_co`;
CREATE TABLE `st_home_co` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lm` int(11) unsigned DEFAULT '0',
  `list_lm` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `title` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `apname` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `color_font` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `keyword` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `link_url` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `info_from` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `info_author` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `f_body` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `z_body` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `img_sl` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `pic_sl` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `fil_sl` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `vid_sl` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ym_tit` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ym_key` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ym_des` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ding` tinyint(1) unsigned DEFAULT NULL,
  `tuijian` tinyint(1) unsigned DEFAULT NULL,
  `hot` tinyint(1) unsigned DEFAULT NULL,
  `pass` tinyint(1) unsigned DEFAULT NULL,
  `read_num` int(11) unsigned DEFAULT '0',
  `px` int(11) unsigned DEFAULT NULL,
  `ip` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `lang` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '语言',
  `wtime` datetime DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `px` (`ding`,`px`,`id`),
  KEY `lm` (`lm`,`title`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_home_lm
-- ----------------------------
DROP TABLE IF EXISTS `st_home_lm`;
CREATE TABLE `st_home_lm` (
  `id_lm` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fid` int(11) unsigned DEFAULT '0',
  `list_lm` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `level_lm` int(11) DEFAULT NULL,
  `title_lm` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `apname_lm` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `url_lm` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `f_body_lm` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `z_body_lm` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `img_sl_lm` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `add_xx` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `add_xia` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `con_att` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `info_apname` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `info_keyword` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `info_link` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `info_from` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `info_f_body` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `info_z_body` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `info_img_sl` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `info_img_txt` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `info_img_sm` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `info_pic_sl` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `info_pic_txt` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `info_pic_sm` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `info_fil_sl` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `info_vid_sl` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `info_duotu` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `info_info` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `info_file` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `info_video` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `info_zu` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `info_wtime` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ym_tit` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ym_key` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ym_des` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `tuijian` tinyint(1) unsigned DEFAULT NULL,
  `hot` tinyint(1) unsigned DEFAULT NULL,
  `pass` tinyint(1) unsigned DEFAULT NULL,
  `px` int(11) unsigned DEFAULT NULL,
  `ip` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `lang` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '' COMMENT '语言',
  `wtime` datetime DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id_lm`) USING BTREE,
  KEY `fid` (`fid`),
  KEY `px` (`px`,`id_lm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_icon_co
-- ----------------------------
DROP TABLE IF EXISTS `st_icon_co`;
CREATE TABLE `st_icon_co` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `lm` int(11) DEFAULT NULL COMMENT '上一级',
  `list_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '所有父级',
  `link_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '跳转链接',
  `apname` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '页面名称',
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '标题',
  `keyword` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '关键词',
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '关键词',
  `f_body` text COLLATE utf8mb4_general_ci COMMENT '简要介绍',
  `z_body` text COLLATE utf8mb4_general_ci COMMENT '详细介绍',
  `t_body` text COLLATE utf8mb4_general_ci COMMENT '其他介绍',
  `g_body` text COLLATE utf8mb4_general_ci COMMENT '其他介绍',
  `ym_tit` text COLLATE utf8mb4_general_ci COMMENT 'seo标题',
  `ym_key` text COLLATE utf8mb4_general_ci COMMENT 'seo关键词',
  `ym_des` text COLLATE utf8mb4_general_ci COMMENT 'seo介绍',
  `img_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片',
  `pic_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片2',
  `fil_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '文件',
  `vid_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '视频',
  `ding` tinyint(1) DEFAULT NULL COMMENT '置顶',
  `tuijian` tinyint(1) DEFAULT NULL COMMENT '推荐',
  `hot` tinyint(1) DEFAULT NULL COMMENT '热门',
  `pass` tinyint(1) DEFAULT NULL COMMENT '屏蔽',
  `read_num` int(11) DEFAULT NULL COMMENT '浏览次数',
  `px` int(11) DEFAULT NULL COMMENT '排序',
  `ip` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'ip',
  `lang` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '语言',
  `wtime` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `delete_time` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Icon';

-- ----------------------------
-- Table structure for st_icon_lm
-- ----------------------------
DROP TABLE IF EXISTS `st_icon_lm`;
CREATE TABLE `st_icon_lm` (
  `id_lm` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id_lm',
  `fid` int(11) DEFAULT NULL COMMENT '上一级',
  `list_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '所有父级',
  `level_lm` int(11) DEFAULT NULL COMMENT '所有父级',
  `url_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '跳转链接',
  `apname_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '页面名称',
  `title_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '标题',
  `f_body_lm` text COLLATE utf8mb4_general_ci COMMENT '简要介绍',
  `z_body_lm` text COLLATE utf8mb4_general_ci COMMENT '详细介绍',
  `ym_tit` text COLLATE utf8mb4_general_ci COMMENT 'seo标题',
  `ym_key` text COLLATE utf8mb4_general_ci COMMENT 'seo关键词',
  `ym_des` text COLLATE utf8mb4_general_ci COMMENT 'seo介绍',
  `img_sl_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片',
  `pic_sl_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片2',
  `add_xx` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '分类是否可以添加信息',
  `add_xia` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '是否有下一级分类',
  `con_att` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '分类属性',
  `tuijian` tinyint(1) DEFAULT NULL COMMENT '推荐',
  `hot` tinyint(1) DEFAULT NULL COMMENT '热门',
  `pass` tinyint(1) DEFAULT NULL COMMENT '屏蔽',
  `px` int(11) DEFAULT NULL COMMENT '排序',
  `ip` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'ip',
  `lang` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '语言',
  `wtime` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `delete_time` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id_lm`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Icon';

-- ----------------------------
-- Table structure for st_invoice
-- ----------------------------
DROP TABLE IF EXISTS `st_invoice`;
CREATE TABLE `st_invoice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `spname` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `comp_name` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `comp_num` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `comp_address` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `comp_phone` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `comp_bank` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `comp_bank_num` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `spphone` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `spprovince` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `spcity` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `spdistrict` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `spaddress` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `post` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `lang` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `type` tinyint(1) DEFAULT '0',
  `wtime` datetime DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_key_co
-- ----------------------------
DROP TABLE IF EXISTS `st_key_co`;
CREATE TABLE `st_key_co` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `link_url` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `pass` tinyint(1) unsigned NOT NULL,
  `px` int(11) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lang` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '语言',
  `wtime` datetime DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_master
-- ----------------------------
DROP TABLE IF EXISTS `st_master`;
CREATE TABLE `st_master` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `rename` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `menu_list` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `action_list` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `login_num` int(11) unsigned NOT NULL DEFAULT '0',
  `error_num` int(11) unsigned NOT NULL DEFAULT '0',
  `error_time` int(11) DEFAULT '0',
  `pass` tinyint(1) unsigned NOT NULL,
  `wip` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `wtime` datetime DEFAULT NULL,
  `lip` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ltime` datetime DEFAULT NULL,
  `eip` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lang` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '语言',
  `remember_token` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '保持登陆',
  `etime` datetime DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_master_action
-- ----------------------------
DROP TABLE IF EXISTS `st_master_action`;
CREATE TABLE `st_master_action` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fid` int(10) unsigned NOT NULL,
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `title_val` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `lang` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '语言',
  `ip` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '语言',
  `pass` smallint(1) unsigned NOT NULL,
  `px` int(11) unsigned NOT NULL,
  `wtime` datetime DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `fid` (`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_master_log
-- ----------------------------
DROP TABLE IF EXISTS `st_master_log`;
CREATE TABLE `st_master_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `z_body` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `lang` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '语言',
  `wtime` int(11) unsigned NOT NULL,
  `delete_time` datetime DEFAULT NULL,
  `create_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `wtime` (`wtime`),
  KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_master_menu
-- ----------------------------
DROP TABLE IF EXISTS `st_master_menu`;
CREATE TABLE `st_master_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ty` smallint(1) unsigned NOT NULL,
  `fid` int(10) unsigned NOT NULL,
  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `link_url` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `title2` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `link_url2` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `icon` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `lang` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '语言',
  `ip` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '语言',
  `pass` smallint(1) unsigned DEFAULT NULL,
  `px` int(10) unsigned DEFAULT NULL,
  `wtime` datetime DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `fid` (`fid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_migrations
-- ----------------------------
DROP TABLE IF EXISTS `st_migrations`;
CREATE TABLE `st_migrations` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `breakpoint` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`version`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_news_co
-- ----------------------------
DROP TABLE IF EXISTS `st_news_co`;
CREATE TABLE `st_news_co` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `lm` int(11) DEFAULT NULL COMMENT '上一级',
  `list_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '所有父级',
  `link_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '跳转链接',
  `apname` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '页面名称',
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '标题',
  `keyword` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '关键词',
  `f_body` text COLLATE utf8mb4_general_ci COMMENT '简要介绍',
  `z_body` text COLLATE utf8mb4_general_ci COMMENT '详细介绍',
  `t_body` text COLLATE utf8mb4_general_ci COMMENT '其他介绍',
  `g_body` text COLLATE utf8mb4_general_ci COMMENT '其他介绍',
  `ym_tit` text COLLATE utf8mb4_general_ci COMMENT 'seo标题',
  `ym_key` text COLLATE utf8mb4_general_ci COMMENT 'seo关键词',
  `ym_des` text COLLATE utf8mb4_general_ci COMMENT 'seo介绍',
  `img_sl` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '图片',
  `pic_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片2',
  `fil_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '文件',
  `vid_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '视频',
  `ding` tinyint(1) DEFAULT NULL COMMENT '置顶',
  `tuijian` tinyint(1) DEFAULT NULL COMMENT '推荐',
  `hot` tinyint(1) DEFAULT NULL COMMENT '热门',
  `pass` tinyint(1) DEFAULT NULL COMMENT '屏蔽',
  `read_num` int(11) DEFAULT NULL COMMENT '浏览次数',
  `px` int(11) DEFAULT NULL COMMENT '排序',
  `ip` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'ip',
  `lang` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '语言',
  `wtime` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `delete_time` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `lm` (`lm`,`list_lm`,`title`,`keyword`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='新闻';

-- ----------------------------
-- Table structure for st_news_lm
-- ----------------------------
DROP TABLE IF EXISTS `st_news_lm`;
CREATE TABLE `st_news_lm` (
  `id_lm` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id_lm',
  `fid` int(11) DEFAULT NULL COMMENT '上一级',
  `list_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '所有父级',
  `level_lm` int(11) DEFAULT NULL COMMENT '所有父级',
  `url_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '跳转链接',
  `apname_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '页面名称',
  `title_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '标题',
  `f_body_lm` text COLLATE utf8mb4_general_ci COMMENT '简要介绍',
  `z_body_lm` text COLLATE utf8mb4_general_ci COMMENT '详细介绍',
  `ym_tit` text COLLATE utf8mb4_general_ci COMMENT 'seo标题',
  `ym_key` text COLLATE utf8mb4_general_ci COMMENT 'seo关键词',
  `ym_des` text COLLATE utf8mb4_general_ci COMMENT 'seo介绍',
  `img_sl_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片',
  `pic_sl_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片2',
  `add_xx` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '分类是否可以添加信息',
  `add_xia` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '是否有下一级分类',
  `con_att` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '分类属性',
  `tuijian` tinyint(1) DEFAULT NULL COMMENT '推荐',
  `hot` tinyint(1) DEFAULT NULL COMMENT '热门',
  `pass` tinyint(1) DEFAULT NULL COMMENT '屏蔽',
  `px` int(11) DEFAULT NULL COMMENT '排序',
  `ip` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'ip',
  `lang` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '语言',
  `wtime` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `delete_time` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id_lm`) USING BTREE,
  KEY `id_lm` (`id_lm`,`fid`,`list_lm`,`title_lm`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='新闻';

-- ----------------------------
-- Table structure for st_order
-- ----------------------------
DROP TABLE IF EXISTS `st_order`;
CREATE TABLE `st_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT '用户ID',
  `address_id` int(11) DEFAULT NULL COMMENT '订单地址ID',
  `invoice_id` int(11) DEFAULT NULL COMMENT '发票ID',
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `num` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` decimal(11,4) DEFAULT NULL,
  `pay_amount` decimal(11,4) DEFAULT '0.0000' COMMENT '实际支付金额',
  `coupon_amount` decimal(11,4) DEFAULT '0.0000' COMMENT '优惠',
  `points` decimal(11,4) DEFAULT '0.0000' COMMENT '积分',
  `postage` decimal(11,4) DEFAULT '0.0000' COMMENT '邮费',
  `user_discounts` decimal(11,4) DEFAULT '0.0000' COMMENT '用户折扣',
  `member_benefits` decimal(11,4) DEFAULT '0.0000' COMMENT '会员福利',
  `wtime` datetime DEFAULT NULL,
  `ip` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `remarks` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `check` smallint(1) unsigned DEFAULT '0',
  `handle` smallint(1) unsigned DEFAULT '0',
  `need_invoice` smallint(1) unsigned DEFAULT '0' COMMENT '需要发票，0不要，1要',
  `pass` smallint(1) unsigned DEFAULT NULL,
  `pay_status` smallint(1) DEFAULT '0' COMMENT '支付状态',
  `pay_time` datetime DEFAULT NULL COMMENT '支付时间',
  `pay_type` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '' COMMENT '支付类型',
  `deliver_status` smallint(1) DEFAULT '0' COMMENT '发货状态',
  `deliver_time` datetime DEFAULT NULL COMMENT '发货时间',
  `receive_status` smallint(1) DEFAULT '0' COMMENT '收货状态',
  `receive_time` datetime DEFAULT NULL COMMENT '收货时间',
  `refund_status` smallint(1) DEFAULT '0' COMMENT '退款状态',
  `refund_time` datetime DEFAULT NULL COMMENT '退款时间',
  `express_id` int(11) DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `order_no` (`order_sn`,`title`,`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_orders
-- ----------------------------
DROP TABLE IF EXISTS `st_orders`;
CREATE TABLE `st_orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '订单编号',
  `user_id` int(11) unsigned DEFAULT '0' COMMENT '用户ID',
  `total_amount` decimal(10,2) DEFAULT '0.00' COMMENT '订单总金额',
  `pay_amount` decimal(10,2) DEFAULT '0.00' COMMENT '实际支付金额',
  `status` int(1) DEFAULT '0' COMMENT '订单状态：0-待支付，1-已支付，2-已发货，3-已完成，4-已取消',
  `pay_status` int(1) DEFAULT '0' COMMENT '支付状态：0-未支付，1-已支付',
  `consignee` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '收货人姓名',
  `mobile` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '收货人手机',
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '收货地址',
  `payment_time` datetime DEFAULT NULL COMMENT '支付时间',
  `ship_time` datetime DEFAULT NULL COMMENT '发货时间',
  `confirm_time` datetime DEFAULT NULL COMMENT '确认收货时间',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_sn` (`order_sn`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='订单表';

-- ----------------------------
-- Table structure for st_order_address
-- ----------------------------
DROP TABLE IF EXISTS `st_order_address`;
CREATE TABLE `st_order_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `province` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `district` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `wtime` datetime DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_order_items
-- ----------------------------
DROP TABLE IF EXISTS `st_order_items`;
CREATE TABLE `st_order_items` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned DEFAULT '0' COMMENT '订单ID',
  `goods_id` int(11) unsigned DEFAULT '0' COMMENT '商品ID',
  `goods_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '商品名称',
  `goods_image` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '商品图片',
  `specifications` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '商品规格',
  `goods_price` decimal(10,2) DEFAULT '0.00' COMMENT '商品单价',
  `quantity` int(11) unsigned DEFAULT '0' COMMENT '购买数量',
  `total_price` decimal(10,2) DEFAULT '0.00' COMMENT '商品总价',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL COMMENT '更新时间',
  `delete_time` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='订单商品表';

-- ----------------------------
-- Table structure for st_order_message
-- ----------------------------
DROP TABLE IF EXISTS `st_order_message`;
CREATE TABLE `st_order_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `wtime` datetime DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_order_notes
-- ----------------------------
DROP TABLE IF EXISTS `st_order_notes`;
CREATE TABLE `st_order_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `notes` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `wtime` datetime DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_order_record
-- ----------------------------
DROP TABLE IF EXISTS `st_order_record`;
CREATE TABLE `st_order_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `record` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `wtime` datetime DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_param_co
-- ----------------------------
DROP TABLE IF EXISTS `st_param_co`;
CREATE TABLE `st_param_co` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `lm` int(11) DEFAULT NULL COMMENT '上一级',
  `list_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '所有父级',
  `link_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '跳转链接',
  `apname` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '页面名称',
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '标题',
  `keyword` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '关键词',
  `f_body` text COLLATE utf8mb4_general_ci COMMENT '简要介绍',
  `z_body` text COLLATE utf8mb4_general_ci COMMENT '详细介绍',
  `t_body` text COLLATE utf8mb4_general_ci COMMENT '其他介绍',
  `g_body` text COLLATE utf8mb4_general_ci COMMENT '其他介绍',
  `ym_tit` text COLLATE utf8mb4_general_ci COMMENT 'seo标题',
  `ym_key` text COLLATE utf8mb4_general_ci COMMENT 'seo关键词',
  `ym_des` text COLLATE utf8mb4_general_ci COMMENT 'seo介绍',
  `img_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片',
  `pic_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片2',
  `fil_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '文件',
  `vid_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '视频',
  `ding` tinyint(1) DEFAULT NULL COMMENT '置顶',
  `tuijian` tinyint(1) DEFAULT NULL COMMENT '推荐',
  `hot` tinyint(1) DEFAULT NULL COMMENT '热门',
  `pass` tinyint(1) DEFAULT NULL COMMENT '屏蔽',
  `read_num` int(11) DEFAULT NULL COMMENT '浏览次数',
  `px` int(11) DEFAULT NULL COMMENT '排序',
  `ip` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'ip',
  `lang` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '语言',
  `wtime` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `delete_time` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `lm` (`lm`,`list_lm`,`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='参数';

-- ----------------------------
-- Table structure for st_param_lm
-- ----------------------------
DROP TABLE IF EXISTS `st_param_lm`;
CREATE TABLE `st_param_lm` (
  `id_lm` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id_lm',
  `fid` int(11) DEFAULT NULL COMMENT '上一级',
  `list_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '所有父级',
  `level_lm` int(11) DEFAULT NULL COMMENT '所有父级',
  `url_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '跳转链接',
  `apname_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '页面名称',
  `title_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '标题',
  `f_body_lm` text COLLATE utf8mb4_general_ci COMMENT '简要介绍',
  `z_body_lm` text COLLATE utf8mb4_general_ci COMMENT '详细介绍',
  `ym_tit` text COLLATE utf8mb4_general_ci COMMENT 'seo标题',
  `ym_key` text COLLATE utf8mb4_general_ci COMMENT 'seo关键词',
  `ym_des` text COLLATE utf8mb4_general_ci COMMENT 'seo介绍',
  `img_sl_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片',
  `pic_sl_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片2',
  `add_xx` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '分类是否可以添加信息',
  `add_xia` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '是否有下一级分类',
  `con_att` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '分类属性',
  `tuijian` tinyint(1) DEFAULT NULL COMMENT '推荐',
  `hot` tinyint(1) DEFAULT NULL COMMENT '热门',
  `pass` tinyint(1) DEFAULT NULL COMMENT '屏蔽',
  `px` int(11) DEFAULT NULL COMMENT '排序',
  `ip` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'ip',
  `lang` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '语言',
  `wtime` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `delete_time` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id_lm`),
  KEY `fid` (`fid`,`list_lm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='参数';

-- ----------------------------
-- Table structure for st_person
-- ----------------------------
DROP TABLE IF EXISTS `st_person`;
CREATE TABLE `st_person` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `rename` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `discounts` decimal(50,2) DEFAULT '0.00' COMMENT '会员折扣',
  `balance` decimal(50,2) DEFAULT '0.00' COMMENT '会员折扣',
  `sex` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `phone` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `fax` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `email` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `qq` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `wx` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `compname` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `address` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `img_sl` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `post` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `z_body` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `login_num` int(11) unsigned DEFAULT NULL,
  `pass` smallint(1) unsigned DEFAULT NULL,
  `lang` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `token` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `wtime` datetime DEFAULT NULL,
  `wip` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `ltime` datetime DEFAULT NULL,
  `lip` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `etime` datetime DEFAULT NULL,
  `eip` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  KEY `wtime` (`wtime`),
  KEY `etime` (`etime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_person_address
-- ----------------------------
DROP TABLE IF EXISTS `st_person_address`;
CREATE TABLE `st_person_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `rename` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `phone` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `province` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `city` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `district` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `address` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `post` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `lang` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `type` tinyint(1) DEFAULT '0',
  `wtime` datetime DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_person_cart
-- ----------------------------
DROP TABLE IF EXISTS `st_person_cart`;
CREATE TABLE `st_person_cart` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `pid` int(11) unsigned DEFAULT NULL,
  `price` decimal(11,4) DEFAULT NULL,
  `total` decimal(11,4) DEFAULT NULL,
  `price_lists` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `num` int(11) unsigned DEFAULT NULL,
  `wtime` datetime DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_person_collect
-- ----------------------------
DROP TABLE IF EXISTS `st_person_collect`;
CREATE TABLE `st_person_collect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `wtime` datetime DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_person_message
-- ----------------------------
DROP TABLE IF EXISTS `st_person_message`;
CREATE TABLE `st_person_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `wtime` datetime DEFAULT NULL,
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `check` int(11) DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_pl_file
-- ----------------------------
DROP TABLE IF EXISTS `st_pl_file`;
CREATE TABLE `st_pl_file` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sy_id` int(11) unsigned NOT NULL DEFAULT '0',
  `pl_id` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `apname` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `link_url` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `z_body` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `img_sl` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `fil_sl` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `ym_tit` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ym_key` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ym_des` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `pass` smallint(1) unsigned NOT NULL,
  `read_num` int(11) unsigned NOT NULL DEFAULT '0',
  `px` int(11) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lang` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '语言',
  `wtime` datetime DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `file_pl_sy` (`pl_id`,`sy_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_pl_image
-- ----------------------------
DROP TABLE IF EXISTS `st_pl_image`;
CREATE TABLE `st_pl_image` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sy_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pl_id` int(10) unsigned NOT NULL DEFAULT '0',
  `g_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `lang` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '语言',
  `img_sl` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `pass` smallint(1) unsigned NOT NULL,
  `px` int(11) unsigned NOT NULL,
  `wtime` datetime DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `image_pl_sy` (`pl_id`,`sy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_pl_info
-- ----------------------------
DROP TABLE IF EXISTS `st_pl_info`;
CREATE TABLE `st_pl_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sy_id` int(11) unsigned NOT NULL DEFAULT '0',
  `pl_id` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `price` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `apname` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `link_url` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `z_body` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `img_sl` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ym_tit` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ym_key` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ym_des` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `pass` smallint(1) unsigned NOT NULL,
  `read_num` int(11) unsigned NOT NULL DEFAULT '0',
  `px` int(11) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lang` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '语言',
  `wtime` datetime DEFAULT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `info_pl_sy` (`pl_id`,`sy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_pro_co
-- ----------------------------
DROP TABLE IF EXISTS `st_pro_co`;
CREATE TABLE `st_pro_co` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `lm` int(11) DEFAULT NULL COMMENT '上一级',
  `list_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '所有父级',
  `link_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '跳转链接',
  `apname` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '页面名称',
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '标题',
  `keyword` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '关键词',
  `param_json` text CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '关键词',
  `param_one` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '关键词',
  `price` decimal(11,4) DEFAULT NULL COMMENT '价格',
  `stock` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '库存',
  `package` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '包装',
  `f_body` text COLLATE utf8mb4_general_ci COMMENT '简要介绍',
  `z_body` text COLLATE utf8mb4_general_ci COMMENT '详细介绍',
  `t_body` text COLLATE utf8mb4_general_ci COMMENT '其他介绍',
  `g_body` text COLLATE utf8mb4_general_ci COMMENT '其他介绍',
  `ym_tit` text COLLATE utf8mb4_general_ci COMMENT 'seo标题',
  `ym_key` text COLLATE utf8mb4_general_ci COMMENT 'seo关键词',
  `ym_des` text COLLATE utf8mb4_general_ci COMMENT 'seo介绍',
  `img_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片',
  `pic_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片2',
  `fil_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '文件',
  `vid_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '视频',
  `ding` tinyint(1) DEFAULT NULL COMMENT '置顶',
  `tuijian` tinyint(1) DEFAULT NULL COMMENT '推荐',
  `hot` tinyint(1) DEFAULT NULL COMMENT '热门',
  `pass` tinyint(1) DEFAULT NULL COMMENT '屏蔽',
  `read_num` int(11) DEFAULT NULL COMMENT '浏览次数',
  `px` int(11) DEFAULT NULL COMMENT '排序',
  `ip` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'ip',
  `lang` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '语言',
  `wtime` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `delete_time` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `lm` (`lm`,`list_lm`,`title`,`keyword`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='产品';

-- ----------------------------
-- Table structure for st_pro_lm
-- ----------------------------
DROP TABLE IF EXISTS `st_pro_lm`;
CREATE TABLE `st_pro_lm` (
  `id_lm` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id_lm',
  `fid` int(11) DEFAULT NULL COMMENT '上一级',
  `list_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '所有父级',
  `level_lm` int(11) DEFAULT NULL COMMENT '所有父级',
  `url_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '跳转链接',
  `apname_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '页面名称',
  `param_json` text CHARACTER SET utf8 COLLATE utf8_general_ci COMMENT '页面名称',
  `title_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '标题',
  `f_body_lm` text COLLATE utf8mb4_general_ci COMMENT '简要介绍',
  `z_body_lm` text COLLATE utf8mb4_general_ci COMMENT '详细介绍',
  `ym_tit` text COLLATE utf8mb4_general_ci COMMENT 'seo标题',
  `ym_key` text COLLATE utf8mb4_general_ci COMMENT 'seo关键词',
  `ym_des` text COLLATE utf8mb4_general_ci COMMENT 'seo介绍',
  `img_sl_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片',
  `pic_sl_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片2',
  `add_xx` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '分类是否可以添加信息',
  `add_xia` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '是否有下一级分类',
  `con_att` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '分类属性',
  `tuijian` tinyint(1) DEFAULT NULL COMMENT '推荐',
  `hot` tinyint(1) DEFAULT NULL COMMENT '热门',
  `pass` tinyint(1) DEFAULT NULL COMMENT '屏蔽',
  `px` int(11) DEFAULT NULL COMMENT '排序',
  `ip` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'ip',
  `lang` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '语言',
  `wtime` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `delete_time` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id_lm`),
  KEY `lm` (`id_lm`,`fid`,`list_lm`,`title_lm`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='产品';

-- ----------------------------
-- Table structure for st_setup_gl
-- ----------------------------
DROP TABLE IF EXISTS `st_setup_gl`;
CREATE TABLE `st_setup_gl` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sy_seo` tinyint(1) unsigned DEFAULT NULL,
  `rewrite` tinyint(1) unsigned DEFAULT NULL,
  `log` tinyint(1) unsigned DEFAULT NULL,
  `key` tinyint(1) unsigned DEFAULT NULL,
  `mlang` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `ym_tit` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ym_key` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ym_des` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ym_bot` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `ym_hcode` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `ym_bcode` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `s_email` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `s_username` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `s_password` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `s_server` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `r_email` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `f_body` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `address` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `logo` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `icon` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `zuobiao` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `lang` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '' COMMENT '语言',
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_setup_sy
-- ----------------------------
DROP TABLE IF EXISTS `st_setup_sy`;
CREATE TABLE `st_setup_sy` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sy_id` int(11) unsigned NOT NULL,
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `pre` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ym_tit` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ym_key` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ym_des` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `lang` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '语言',
  `r_email` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `config` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `delete_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Table structure for st_video
-- ----------------------------
DROP TABLE IF EXISTS `st_video`;
CREATE TABLE `st_video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL COMMENT '视频标题',
  `description` text COLLATE utf8mb4_general_ci COMMENT '视频描述',
  `video_url` varchar(500) COLLATE utf8mb4_general_ci NOT NULL COMMENT '视频URL',
  `video_path` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '视频本地路径',
  `cover_image` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '封面图URL',
  `duration` int(11) DEFAULT '0' COMMENT '时长（秒）',
  `size` bigint(20) DEFAULT '0' COMMENT '文件大小（字节）',
  `format` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '视频格式（mp4, mov等）',
  `width` int(11) DEFAULT '0' COMMENT '视频宽度',
  `height` int(11) DEFAULT '0' COMMENT '视频高度',
  `category_id` int(11) DEFAULT '0' COMMENT '分类ID',
  `user_id` int(11) DEFAULT '0' COMMENT '上传用户ID',
  `tags` json DEFAULT NULL COMMENT '标签数组',
  `status` tinyint(4) DEFAULT '1' COMMENT '状态 1-正常 0-删除',
  `view_count` int(11) DEFAULT '0' COMMENT '播放次数',
  `like_count` int(11) DEFAULT '0' COMMENT '点赞数',
  `sort_order` int(11) DEFAULT '0' COMMENT '排序',
  `is_recommend` tinyint(4) DEFAULT '0' COMMENT '是否推荐',
  `meta_data` json DEFAULT NULL COMMENT '额外元数据（如编码信息）',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `update_time` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `delete_time` datetime DEFAULT NULL COMMENT '软删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_recommend` (`is_recommend`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='视频库';

-- ----------------------------
-- Table structure for st_video_category
-- ----------------------------
DROP TABLE IF EXISTS `st_video_category`;
CREATE TABLE `st_video_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL COMMENT '分类名称',
  `parent_id` int(11) DEFAULT '0' COMMENT '父分类ID',
  `icon` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '分类图标',
  `description` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '分类描述',
  `sort_order` int(11) DEFAULT '0' COMMENT '排序',
  `status` tinyint(4) DEFAULT '1' COMMENT '状态',
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_parent` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='视频分类';

-- ----------------------------
-- Table structure for st_visit_log
-- ----------------------------
DROP TABLE IF EXISTS `st_visit_log`;
CREATE TABLE `st_visit_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'IP地址',
  `url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '访问URL',
  `user_agent` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '用户代理',
  `referer` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '来源页面',
  `session_id` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Session ID',
  `visit_time` datetime DEFAULT NULL COMMENT '访问时间',
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`),
  KEY `visit_time` (`visit_time`),
  KEY `session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='访问记录表';

-- ----------------------------
-- Table structure for st_web_co
-- ----------------------------
DROP TABLE IF EXISTS `st_web_co`;
CREATE TABLE `st_web_co` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `lm` int(11) DEFAULT NULL COMMENT '上一级',
  `list_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '所有父级',
  `link_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '跳转链接',
  `apname` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '页面名称',
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '标题',
  `keyword` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '关键词',
  `tlm` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '对应分类',
  `tfile` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '接口文件',
  `table_co` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '数据表',
  `table_lm` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '分类数据表',
  `web_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '网站链接',
  `release` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '网站链接',
  `folder_path` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '网站链接',
  `f_body` text COLLATE utf8mb4_general_ci COMMENT '简要介绍',
  `z_body` text COLLATE utf8mb4_general_ci COMMENT '详细介绍',
  `t_body` text COLLATE utf8mb4_general_ci COMMENT '其他介绍',
  `g_body` text COLLATE utf8mb4_general_ci COMMENT '其他介绍',
  `ym_tit` text COLLATE utf8mb4_general_ci COMMENT 'seo标题',
  `ym_key` text COLLATE utf8mb4_general_ci COMMENT 'seo关键词',
  `ym_des` text COLLATE utf8mb4_general_ci COMMENT 'seo介绍',
  `img_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片',
  `pic_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片2',
  `fil_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '文件',
  `vid_sl` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '视频',
  `ding` tinyint(1) DEFAULT NULL COMMENT '置顶',
  `tuijian` tinyint(1) DEFAULT NULL COMMENT '推荐',
  `hot` tinyint(1) DEFAULT NULL COMMENT '热门',
  `pass` tinyint(1) DEFAULT NULL COMMENT '屏蔽',
  `read_num` int(11) DEFAULT NULL COMMENT '浏览次数',
  `px` int(11) DEFAULT NULL COMMENT '排序',
  `ip` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'ip',
  `lang` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '语言',
  `wtime` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `delete_time` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='网站管理';

-- ----------------------------
-- Table structure for st_web_lm
-- ----------------------------
DROP TABLE IF EXISTS `st_web_lm`;
CREATE TABLE `st_web_lm` (
  `id_lm` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id_lm',
  `fid` int(11) DEFAULT NULL COMMENT '上一级',
  `list_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '所有父级',
  `level_lm` int(11) DEFAULT NULL COMMENT '所有父级',
  `url_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '跳转链接',
  `apname_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '页面名称',
  `title_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '标题',
  `f_body_lm` text COLLATE utf8mb4_general_ci COMMENT '简要介绍',
  `z_body_lm` text COLLATE utf8mb4_general_ci COMMENT '详细介绍',
  `ym_tit` text COLLATE utf8mb4_general_ci COMMENT 'seo标题',
  `ym_key` text COLLATE utf8mb4_general_ci COMMENT 'seo关键词',
  `ym_des` text COLLATE utf8mb4_general_ci COMMENT 'seo介绍',
  `img_sl_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片',
  `pic_sl_lm` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片2',
  `add_xx` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '分类是否可以添加信息',
  `add_xia` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '是否有下一级分类',
  `con_att` varchar(4) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '分类属性',
  `tuijian` tinyint(1) DEFAULT NULL COMMENT '推荐',
  `hot` tinyint(1) DEFAULT NULL COMMENT '热门',
  `pass` tinyint(1) DEFAULT NULL COMMENT '屏蔽',
  `px` int(11) DEFAULT NULL COMMENT '排序',
  `ip` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'ip',
  `lang` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '语言',
  `wtime` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `delete_time` timestamp NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id_lm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='网站管理';
