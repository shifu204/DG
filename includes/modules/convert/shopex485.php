<?php

/**
 * shopex4.8.5 To Ecshop2.7.3转换程序插件
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo & pgcao$
 * $Id: shopex485.php 17217 2011-01-19 06:29:08Z liubo $
 */

if (!defined('IN_ECS')){
    die('Hacking attempt');
}

/**
 * 模块信息
 */
if (isset($set_modules) && $set_modules == TRUE){
    $i = isset($modules) ? count($modules) : 0;
    /* 代码 */
    $modules[$i]['code'] = basename(__FILE__, '.php');
    /* 描述对应的语言项 */
    $modules[$i]['desc'] = 'shopex485_desc';
    /* 作者 */
    $modules[$i]['author'] = 'ECSHOP R&D PGCAO';
    return;
}

/* 类 */
class shopex485{
    /* 数据库连接 ADOConnection 对象 */
    var $sdb;

    /* 表前缀 */
    var $sprefix;

    /* 原系统根目录 */
    var $sroot;

    /* 新系统根目录 */
    var $troot;

    /* 新系统网站根目录 */
    var $tdocroot;

    /* 原系统字符集 */
    var $scharset;

    /* 新系统字符集 */
    var $tcharset;

    /* 构造函数 */
    function shopex485(&$sdb, $sprefix, $sroot, $scharset = 'UTF8'){
        $this->sdb = $sdb;
        $this->sprefix = $sprefix;
        $this->sroot = $sroot;
        $this->troot = str_replace('/includes/modules/convert', '', str_replace('\\', '/', dirname(__FILE__)));
        $this->tdocroot = str_replace('/' . ADMIN_PATH, '', dirname(PHP_SELF));
        $this->scharset = $scharset;
        if (EC_CHARSET == 'utf-8'){
            $tcharset = 'UTF8';
        }elseif (EC_CHARSET == 'gbk'){
            $tcharset = 'GB2312';
        }
        $this->tcharset = $tcharset;
    }
	
	#用于开发时生成日志用
	function _L($name, $array, $mod='a'){
		$logfile = ROOT_PATH.'~bug_'.$name.'.log';
		if(@$fp = fopen($logfile, $mod)) {
			@fwrite($fp, date('Y-m-d H:i:s', time())."\r\n");
			@fwrite($fp, '--------------------------------------------'."\r\n");
			@fwrite($fp, var_export($array,TRUE)."\r\n");
			@fwrite($fp, '============================================'."\r\n");
			@fclose($fp);
		}
	}
	
	/**
	 * 清空表数据
	 * @param   string  $table_name 表名称
	 */
	function truncate_table($table_name){
		$sql = 'TRUNCATE TABLE ' .$GLOBALS['ecs']->table($table_name);
		return $GLOBALS['db']->query($sql);
	}

    /**
     * 需要转换的表（用于检查数据库是否完整）
     * @return  array
     */
    function required_tables(){
       return array(
         $this->sprefix.'goods',
       );
    }

    /**
     * 必需的目录
     * @return  array
     */
    function required_dirs(){
        return array(
            '/images/goods/',
            '/images/brand/',
            '/images/link/',
            );
    }

    /**
     * 下一步操作：空表示结束
     * @param   string  $step  当前操作：空表示开始
     * @return  string
     */
    function next_step($step){
        /* 所有操作 */
        $steps = array(
            ''              => 'step_file',
            'step_file'     => 'step_cat',
            'step_cat'      => 'step_brand',
            'step_brand'    => 'step_goods',
            'step_goods'    => 'step_users',
            'step_users'    => 'step_article',
            'step_article'  => 'step_order',
            'step_order'    => 'step_config',
            'step_config'    => '',
        );
        return $steps[$step];
    }

    /**
     * 执行某个步骤
     * @param   string  $step
     */
    function process($step){
        $func = str_replace('step', 'process', $step);
        return $this->$func();
    }

    /**
     * 第一步 复制文件
     * @return  成功返回true，失败返回错误信息
     */
    function process_file(){
		/*
        #复制品牌图片
        $from = $this->sroot . '/images/brand/';
        $to   = $this->troot . '/data/brandlogo/';
        copy_dirs($from, $to);
        #复制商品图片
        $from = $this->sroot . '/images/goods/';
        $to   = $this->troot . '/images/goods/';
        copy_dirs($from, $to);
        #复制友情链接图片
        $from = $this->sroot . '/images/link/';
        $to   = $this->troot . '/data/afficheimg/';
        copy_dirs($from, $to);
		*/
        return TRUE;
    }

     /**
     * 第二步 商品分类 
     * @return  成功返回true，失败返回错误信息
     */
    function process_cat(){
        global $db, $ecs;
		/*
        #清空分类、商品类型、属性
        $this->truncate_table('category');
        $this->truncate_table('goods_type');
        //$this->truncate_table('attribute');
        #查询分类并循环处理
        $sql = "SELECT * FROM ".$this->sprefix."goods_cat";
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res)){
            $cat = array();
            $cat['cat_id']      = $row['cat_id'];//分类ID
            $cat['cat_name']    = $row['cat_name'];//分类名称
            $cat['parent_id']   = $row['parent_id'];//上级ID
            $cat['sort_order']  = $row['p_order'];//排序

            #插入分类
            if (!$db->autoExecute($ecs->table('category'), $cat, 'INSERT', '', 'SILENT')){
                $this->_L('category',$db->error());
            }
        }
        #查询商品类型并循环处理
        $sql = "SELECT * FROM ".$this->sprefix."goods_type";
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res)){
            $type = array();
            $type['cat_id']     = $row['type_id'];
            $type['cat_name']   = $row['name'];
            $type['enabled']    = '1';
            if (!$db->autoExecute($ecs->table('goods_type'), $type, 'INSERT', '', 'SILENT')){
                $this->_L('goods_type',$db->error());
            }
        }
		*/
        #查询属性值并循环处理

        #返回成功
        return true;
    }

    /**
     * 第三步 品牌
     * @return  成功返回true，失败返回错误信息
     */
    function process_brand(){
        global $db, $ecs;
		/*
        #清空品牌
        $this->truncate_table('brand');

        #查询品牌并插入
        $sql = "SELECT * FROM ".$this->sprefix."brand";
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res)){
            $brand_logo = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['brand_logo']));
            $logoarr = explode('|',$brand_logo);
            if(strpos($logoarr[0],'http') === 0){
                $brand_url = $logoarr[0];
            }else{
                $logourl = explode('/',$logoarr[0],3);
                $brand_url = $logourl[2];
            }
            $brand = array(
                'brand_id' => $row['brand_id'],
                'brand_name' => $row['brand_name'],
                'brand_desc' => ecs_iconv($this->scharset, $this->tcharset, addslashes($row['brand_desc'])),
                'site_url' => ecs_iconv($this->scharset, $this->tcharset, addslashes($row['brand_url'])),
                'brand_logo' => $brand_url
            );
            if (!$db->autoExecute($ecs->table('brand'), $brand, 'INSERT', '', 'SILENT')){
                $this->_L('brand',$db->error());
            }
        }
		*/
        return TRUE;//返回成功
    }

    /**
     * 第四步 商品
     * @return  成功返回true，失败返回错误信息
     */
    function process_goods(){
        global $db, $ecs;
		/*
        #清空商品、商品扩展分类、商品属性、商品相册、关联商品、组合商品、赠品
        $this->truncate_table('goods');
        $this->truncate_table('goods_cat');
        $this->truncate_table('goods_attr');
        $this->truncate_table('goods_gallery');
        $this->truncate_table('link_goods');
        $this->truncate_table('group_goods');
		
        #查询品牌列表 name => id
        $brand_list = array();
        $sql = "SELECT brand_id, brand_name FROM " . $ecs->table('brand');
        $res = $db->query($sql);
        while ($row = $db->fetchRow($res)){
            $brand_list[$row['brand_name']] = $row['brand_id'];
        }

        #取得商品标签为推荐数据
        $tag_rel_list = array();
		//打开shopex的sdb_tags表跟随相应键值, 对应ecshop的(精品 新品 热销 促销)
		$best_new_hot_promote = array(4=>'is_best',1=>'is_new',2=>'is_hot',3=>'is_promote');
        $sql = "SELECT tag_id, rel_id FROM ".$this->sprefix."tag_rel";
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res)){
            if(isset($best_new_hot_promote[$row['tag_id']])){
				$tag_rel_key = $best_new_hot_promote[$row['tag_id']];
				$tag_rel_list[$row['rel_id']][$tag_rel_key] = true;
			}
        }

        #取得商店设置
		//............................

        #取得商品分类对应的商品类型
        $cat_type_list = array();
        $sql = "SELECT cat_id, supplier_cat_id FROM ".$this->sprefix."goods_cat";
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res)){
            $cat_type_list[$row['cat_id']] = $row['supplier_cat_id'];
        }

        #查询商品并处理
        $sql = "SELECT * FROM ".$this->sprefix."goods";
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res)){
            $goods = array();
            $goods['goods_id']      = $row['goods_id'];
            $goods['cat_id']        = $row['cat_id'];
            $goods['goods_sn']    	= $row['bn'];
            $goods['goods_name']    = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['name']));
            $goods['brand_id']      = trim($row['brand']) == '' ? '0' : $brand_list[ecs_iconv($this->scharset, $this->tcharset, addslashes($row['brand']))];
            $goods['goods_number']  = $row['store'];//库存数
            $goods['goods_weight']  = $row['weight'];//重量
            $goods['market_price']  = $row['mktprice'];
            $goods['shop_price']    = $row['price'];//商城价
            $goods['goods_brief']   = $row['brief'];
            $goods['goods_desc']    = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['intro']));;//详细
            $goods['is_on_sale']    = $row['marketable']=='true'?'1':'0';//是否下架
            $goods['add_time']      = $row['uptime'];//录入时间
            $goods['is_delete']     = $row['disabled']=='true'?'1':'0';//是否放入回收站
            $goods['sort_order']    = $row['d_order'];//推荐排序
            $goods['is_best']       = isset($tag_rel_list[$goods['goods_id']]['is_best'])?'1':'0';//是否精品
            $goods['is_new']        = isset($tag_rel_list[$goods['goods_id']]['is_new'])?'1':'0';//是否新品
            $goods['is_hot']        = isset($tag_rel_list[$goods['goods_id']]['is_hot'])?'1':'0';//是否热销
            //$goods['is_promote']    = isset($tag_rel_list[$goods['goods_id']]['is_promote'])?'1':'0';//是否促销
            $goods['click_count']   = $row['view_count'];//点击数
            $goods['is_alone_sale'] = '1';//是否作为普通商品销售
			
            //$goods['goods_type']    = isset($cat_type_list[$row['cat_id']]) ? $cat_type_list[$row['cat_id']] : 0;
            //$goods['promote_price'] = $row['cost'];//促销价=>成本价
			
            $small_pic = $row['small_pic'];
            $small_pic_arr = explode('|',$small_pic);
            $thumbnail_pic = $row['thumbnail_pic'];
            $thumbnail_pic_arr = explode('|',$thumbnail_pic);
            $big_pic = $row['big_pic'];
            $big_pic_arr = explode('|',$big_pic);
            $goods['goods_img']     = $small_pic_arr[0];//产品图地址
            $goods['goods_thumb']   = $thumbnail_pic_arr[0];//缩略图地址
            $goods['original_img']  = $big_pic_arr[0];//原图地址
            $goods['last_update'] = gmtime();//最后更新时间
			            
            if (!$db->autoExecute($ecs->table('goods'), $goods, 'INSERT', '', 'SILENT')){#插入商品
				$this->_L('goods',$db->error());
            }
        }
		
		#商品相册
		$sql = "SELECT * FROM ".$this->sprefix."gimages";
		$result = $this->sdb->query($sql);
		while ($row = $this->sdb->fetchRow($result)){
			$goods_gallery = array();
			$goods_gallery['goods_id'] = $row['goods_id'];
			$small_pic = $row['small'];
			$small_pic_arr = explode('|',$small_pic);
			$thumbnail_pic = $row['thumbnail'];
			$thumbnail_pic_arr = explode('|',$thumbnail_pic);
			$big_pic = $row['big'];
			$big_pic_arr = explode('|',$big_pic);
			$goods_gallery['img_original']     = $big_pic_arr[0];
			$goods_gallery['thumb_url']   = $thumbnail_pic_arr[0];
			$goods_gallery['img_url'] = $small_pic_arr[0];
			if (!$db->autoExecute($ecs->table('goods_gallery'), $goods_gallery, 'INSERT', '', 'SILENT')){
				$this->_L('goods_gallery',$db->error());
			}
		}
		*/
        return TRUE;//返回成功
    }

    /**
     * 第五步 会员等级、会员、会员价格
     */
    function process_users(){
        global $db, $ecs;
		/*
        #清空会员、会员等级、会员地址、会员红包、会员价格、会员帐户明细
        $this->truncate_table('users');
        $this->truncate_table('user_rank');
        $this->truncate_table('user_address');
        $this->truncate_table('user_bonus');
        $this->truncate_table('member_price');
        $this->truncate_table('user_account');

        #查询并插入会员等级
        $sql = "SELECT * FROM ".$this->sprefix."member_lv order by point desc";
        $res = $this->sdb->query($sql);
        $max_points = 50000;
        while ($row = $this->sdb->fetchRow($res)){
            $user_rank = array();
            $user_rank['rank_id']       = $row['member'];
            $user_rank['rank_name']     = $row['name'];
            $user_rank['min_points']    = $row['point'];
            $user_rank['max_points']    = $max_points;
            $user_rank['discount']      = round($row['dis_count'] * 100);
            $user_rank['show_price']    = '1';
            $user_rank['special_rank']  = '0';
            if(!$db->autoExecute($ecs->table('user_rank'), $user_rank, 'INSERT', '', 'SILENT')){
                $this->_L('user_rank',$db->error());
            }
            $max_points = $row['point'] - 1;
        }

        #查询并插入会员
        $sql = "SELECT * FROM ".$this->sprefix."members";
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res)){
            $user = array();
            $user['user_id']        = $row['member_id'];
            $user['email']          = $row['email'];
            $user['user_name']      = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['uname']));
            $user['password']       = $row['password'];
            $user['question']       = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['pw_question']));
            $user['answer']         = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['pw_answer']));
            $user['sex']            = $row['sex'];
            if(!empty($row['birthday'])){
                $birthday           = strtotime($row['birthday']);
                if ($birthday != -1 && $birthday !== false){
                    $user['birthday']   = date('Y-m-d', $birthday);
                }
            }
            $user['user_money']     = $row['advance'];
            $user['pay_points']     = $row['point'];
            $user['rank_points']    = $row['point'];
            $user['reg_time']       = $row['regtime'];
            $user['last_login']     = $row['regtime'];
            $user['last_ip']        = $row['reg_ip'];
            $user['visit_count']    = '1';
            $user['user_rank']      = '0';

            if (!$db->autoExecute($ecs->table('users'), $user, 'INSERT', '', 'SILENT')){
                $this->_L('users',$db->error());
            }
			#同步UC用户中心
            //uc_call('uc_user_register', array($user['user_name'], $user['password'], $user['email']));
        }

        #收货人地址
        $sql = "SELECT * FROM ".$this->sprefix."member_addrs";
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res)){
            $address = array();
            $address['address_id']      = $row['addr_id'];
            $address['address_name']    = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['name']));
            $address['user_id']         = $row['member_id'];
            $address['consignee']       = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['name']));
            //$address['email']           = $row['email'];
            $address['address']         = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['addr']));
            $address['zipcode']         = $row['zip'];
            $address['tel']             = $row['tel'];
            $address['mobile']          = $row['mobile'];
            $address['country']         = $row['country'];
            $address['province']        = $row['province'];
            $address['city']            = $row['city'];
            if (!$db->autoExecute($ecs->table('user_address'), $address, 'INSERT', '', 'SILENT')){
                $this->_L('user_address',$db->error());
            }
        }

        #会员价格
        $temp_arr = array();
        $sql = "SELECT * FROM ".$this->sprefix."goods_lv_price";
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res)){
            if ($row['goods_id'] > 0 && $row['level_id'] > 0 && !isset($temp_arr[$row['goods_id']][$row['level_id']])){
                $temp_arr[$row['goods_id']][$row['level_id']] = true;

                $member_price = array();
                $member_price['goods_id']   = $row['goods_id'];
                $member_price['user_rank']  = $row['level_id'];
                $member_price['user_price'] = $row['price'];

                if (!$db->autoExecute($ecs->table('member_price'), $member_price, 'INSERT', '', 'SILENT')){
                     $this->_L('member_price',$db->error());
                }
            }
        }
        unset($temp_arr);

        #帐户明细
        $sql = "SELECT * FROM ".$this->sprefix."advance_logs";
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res)){
            $user_account = array();
            $user_account['user_id']        = $row['member_id'];
            $user_account['admin_user']     = $row['memo'];
            $user_account['amount']         = $row['money'];
            $user_account['add_time']       = $row['mtime'];
            $user_account['paid_time']      = $row['mtime'];
            $user_account['admin_note']     = $row['message'];
            $user_account['payment']        = $row['paymethod'];
            $user_account['process_type']   = $row['money'] >= 0 ? SURPLUS_SAVE : SURPLUS_RETURN;
            $user_account['is_paid']        = '1';

            if (!$db->autoExecute($ecs->table('user_account'), $user_account, 'INSERT', '', 'SILENT')){
                $this->_L('user_account',$db->error());
            }
        }
		*/
        return TRUE;//返回成功
    }

    /**
     * 第六步 文章
     */
    function process_article(){
        global $db, $ecs;
		/*
        #清空文章类型、文章、友情链接、评论回复
        //$this->truncate_table('article_cat');
        //$this->truncate_table('article');
        $this->truncate_table('friend_link');
        $this->truncate_table('comment');

        #文章 
        $sql = "SELECT * FROM ".$this->sprefix."articles";
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res)){
            $article = array();
            $article['article_id']  = $row['article_id'];
            $article['cat_id']      = $row['node_id'];
            $article['title']       = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['title']));
            $article['content']     = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['content']));
            $article['content']     = str_replace('pictures/newsimg/', 'images/upload/Image/', $article['content']);
            $article['article_type']= '0';
            $article['is_open']     = $row['ifpub'];
            $article['add_time']    = $row['uptime'];

            if (!$db->autoExecute($ecs->table('article'), $article, 'INSERT', '', 'SILENT')){
                $this->_L('article',$db->error());
            }
        }
        #友情链接 
        $sql = "SELECT * FROM ".$this->sprefix."link";
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res)){
            $link = array();
            $link['link_id']     = $row['link_id'];
            $link['link_name']   = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['link_name']));
            $link['link_url']    = $row['href'];
            $link['show_order']  = '0';
            $link_logo           = $row['image_url'];
            $logoarr             = explode('|',$link_logo);
            $logourl             = explode('/',$logoarr[0],3);
            $link['link_logo']   = 'data/afficheimg/'.$logourl[2];
            if (!$db->autoExecute($ecs->table('friend_link'), $link, 'INSERT', '', 'SILENT')){
                 $this->_L('friend_link',$db->error());
            }
        }
        #评论回复 
        $sql = "SELECT * FROM ".$this->sprefix."comments";
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res)){
            $comments = array();
            $comments['comment_id']     = $row['comment_id'];
            $comments['parent_id']     = $row['for_comment_id'];
            $comments['id_value']     = $row['goods_id'];
            $comments['user_id']     = intval($row['author_id']);
            $comments['user_name']     = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['author']));
            $comments['comment_rank']     = rand(4,5);
            $comments['ip_address']     = $row['ip'];
            $comments['add_time']     = $row['time'];
            $comments['status']     = $row['display']?1:0;
            $comments['content']     = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['comment']));
            if (!$db->autoExecute($ecs->table('comment'), $comments, 'INSERT', '', 'SILENT')){
                $this->_L('comment',$db->error());
            }
        }
		*/
        return TRUE;//返回成功
    }

    /**
     * 第七步 订单
     */
    function process_order(){
        global $db, $ecs;
/*
        #清空订单、订单商品
        $this->truncate_table('order_info');
        $this->truncate_table('order_goods');
        $this->truncate_table('order_action');
        #订单
        $sql = "SELECT o.* FROM ".$this->sprefix."orders AS o " ;
        $res = $this->sdb->query($sql);
        while ($row = $this->sdb->fetchRow($res)){
            $order = array();
            $order['order_sn']          = $row['order_id'];//订单号
            $order['user_id']           = $row['member_id'];//会员编号
            $order['add_time']          = $row['createtime'];//下单时间
            $order['consignee']         = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['ship_name']));//收货人
            $order['address']           = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['ship_addr']));//收货地址
            $order['zipcode']           = $row['ship_zip'];//收件邮编
            $order['tel']               = $row['ship_tel'];//联系电话
            $order['mobile']            = $row['ship_mobile'];//联系手机
            $order['email']             = $row['ship_email'];//联系邮箱
            $order['postscript']        = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['memo']));//订单备注
            $order['pay_name']     		= ecs_iconv($this->scharset, $this->tcharset, addslashes($row['shipping']));//付款方式
            $order['shipping_name']     = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['shipping']));//快递公司
            $order['inv_payee']         = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['tax_company']));
            $order['goods_amount']      = $row['total_amount'];//数量
            $order['shipping_fee']      = $row['cost_freight'];//重量
            $order['order_amount']      = $row['final_amount'];//总额
            //$order['pay_time']          = $row['paytime'];//付款时间
            $order['shipping_time']     = $row['acttime'];
			
//            #订单状态 
//            if ($row['ordstate'] == '0'){
//            }elseif ($row['ordstate'] == '1'){
//                $order['order_status']      = OS_CONFIRMED;
//                $order['shipping_status']   = SS_UNSHIPPED;
//            }elseif ($row['ordstate'] == '9'){
//                $order['order_status']      = OS_INVALID;
//                $order['shipping_status']   = SS_UNSHIPPED;
//            }else{ // 3 发货 4 归档
//                $order['order_status']      = OS_CONFIRMED;
//                $order['shipping_status']   = SS_SHIPPED;
//            }
			
            $order['order_status']      = OS_UNCONFIRMED;
            $order['shipping_status']   = SS_UNSHIPPED;
			
            #付款状态
            if ($row['pay_status'] == '1'){
                $order['pay_status']        = PS_PAYED;
            }else{// 0 未付款 5 退款
                $order['pay_status']        = PS_UNPAYED;
            }
			
//			#变更状态 
//            if ($row['userrecsts'] == '1'){// 用户操作了
//                if ($row['recsts'] == '1'){// 到货
//                    if ($order['shipping_status'] == SS_SHIPPED){
//                        $order['shipping_status'] = SS_RECEIVED;
//                    }
//                }elseif ($row['recsts'] == '2'){// 取消
//                    $order['order_status']      = OS_CANCELED;
//                    $order['pay_status']        = PS_UNPAYED;
//                    $order['shipping_status']   = SS_UNSHIPPED;
//                }
//            }

            if (!$db->autoExecute($ecs->table('order_info'), $order, 'INSERT', '', 'SILENT')){
                $this->_L('order_info',$db->error());
            }
            #订单商品
        }
*/
        return TRUE;//返回成功
    }

    /**
     * 第八步 商店设置
     */
    function process_config(){
        global $ecs, $db;
        /* 
		#查询设置 
        $sql = "SELECT * FROM ".$this->sprefix."settings";
        $row = $this->sdb->getRow($sql);
        $store = $row['store'];
        $store_arr = unserialize($store);
        $config = array();
        //$config['shop_name']        = ecs_iconv($this->scharset, $this->tcharset, addslashes($store_arr[0]);
        //$config['shop_title']       = ecs_iconv($this->scharset, $this->tcharset, addslashes($store_arr[0]));
        //$config['shop_desc']        = ecs_iconv($this->scharset, $this->tcharset, addslashes($store_arr[1]));
        //$config['shop_address']     = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['store']));
        $config['shop_address'] = $row['store'];
        //$config['service_email']    = $row['offer_email'];
        $config['service_phone']    = $store_arr[2];
        //$config['icp_number']       = ecs_iconv($this->scharset, $this->tcharset, addslashes($row['offer_certtext']));
        //$config['integral_scale']   = $row['offer_pointtype'] == '0' ? '0' : $row['offer_pointnum'] * 100;
        //$config['thumb_width']      = $row['offer_smallsize_w'];
        //$config['thumb_height']     = $row['offer_smallsize_h'];
        //$config['image_width']      = $row['offer_bigsize_w'];
        //$config['image_height']     = $row['offer_bigsize_h'];
        //$config['promote_number']   = $row['offer_tejianums'];
        //$config['best_number']      = $row['offer_tjnums'];
        //$config['new_number']       = $row['offer_newgoodsnums'];
        //$config['hot_number']       = $row['offer_hotnums'];
        //$config['smtp_host']        = $row['offer_smtp_server'];
        //$config['smtp_port']        = $row['offer_smtp_port'];
        //$config['smtp_user']        = $row['offer_smtp_user'];
        //$config['smtp_pass']        = $row['offer_smtp_password'];
        //$config['smtp_mail']        = $row['offer_smtp_email'];

        #更新 
        foreach ($config as $code => $value)
        {
            $sql = "UPDATE " . $ecs->table('shop_config') . " SET " .
                    "value = '$value' " .
                    "WHERE code = '$code' LIMIT 1";
            if (!$db->query($sql, 'SILENT'))
            {
                $this->_L('shop_config',$db->error());
            }
        }
		*/
        return TRUE;//返回成功
    }
}
?>