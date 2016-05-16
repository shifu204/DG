<?php
/*$fp = fopen('D:\wwwroot\web\test.txt', 'r+');
$text = '';
foreach ($_POST as $key => $value) {
	$text .= '$_POST[\'' . $key . '\'] = \'' . $value . "';\r\n";
}
fwrite($fp, $text);
fclose($fp);

// 传过来的参数，用来测试
$_POST['action'] = 'd';
$_POST['start_time'] = '2013-11-19 09:55:51';
$_POST['end_time'] = '2013-11-20 09:55:48';
$_POST['shipping_status'] = '';
$_POST['pay_status'] = '';
$_POST['admin_user'] = 'admin4s';
$_POST['hash_code'] = 'admin789';
$_POST['F'] = 'integral,integral_money,bonus,discount,pack_fee ,card_fee,money_paid,card_message,inv_payee,inv_content,tax,insure_fee,goods_amount,surplus,order_sn_ext,delivery_sn,order_note1,order_note2,order_note3,order_note4,order_note5,order_note6,order_note7,order_note8,order_note9,order_note10';
$_POST['V'] = 'V2.5';*/



 			/**
			 * 讯捷批量打印软件 ECShop 专用接口 新版1.6 API  (GBK UTF-8 通用版) 
			 * QQ：316335930  交流群 QQ:2734276
			 */	 
			define('IN_ECS', true);
			require(dirname(__FILE__) . '/../includes/init.php');
			require_once(ROOT_PATH . 'includes/lib_order.php');
			require_once(ROOT_PATH . 'includes/lib_goods.php');
           
			$T = isset($_POST['T']) && trim($_POST['T']) ? trim($_POST['T']) : '';
			$F = isset($_POST['F']) && trim($_POST['F']) ? trim($_POST['F']) : '';
			$W = isset($_POST['W']) && trim($_POST['W']) ? trim($_POST['W']) : '';
			$action = isset($_POST['action']) && trim($_POST['action']) ? trim($_POST['action']) : '';
			$GetType = isset($_POST['GetType']) && trim($_POST['GetType']) ? trim($_POST['GetType']) : '';
			$admin_user = isset($_POST['admin_user']) && trim($_POST['admin_user']) ? trim($_POST['admin_user']) : '';
			$hash_code = isset($_POST['hash_code']) && trim($_POST['hash_code']) ? trim($_POST['hash_code']) : '';
			 		 
			$sql="SELECT `ec_salt` FROM ". $ecs->table('admin_user') ."WHERE user_name = '" . $admin_user. "'";
			$userrow = $db->getRow($sql);
			if (!$userrow)
			{
			    echo '106';
			    exit;
			}
			$ec_salt =$db->getOne($sql);
			if(!empty($ec_salt))
			{
				 $sql = "SELECT user_id, user_name, password, last_login, action_list, last_login".
					" FROM " . $ecs->table('admin_user') .
					" WHERE user_name = '" . $admin_user. "' AND password = '" . md5(md5($hash_code).$ec_salt) . "'";
			}
			else
			{
				 $sql = "SELECT user_id, user_name, password, last_login, action_list, last_login".
					" FROM " . $ecs->table('admin_user') .
					" WHERE user_name = '" . $admin_user. "' AND password = '" . md5($hash_code) . "'";
			}
			$row = $db->getRow($sql);
			if ($row)
			{
			    $_SESSION['admin_name'] =$admin_user;
			    $_SESSION['user_id'] = $row['user_id'];
				if(empty($row['ec_salt']))
				{
					$ec_salt=rand(1,9999);
					$new_possword=md5(md5($_POST['password']).$ec_salt);
					 $db->query("UPDATE " .$ecs->table('admin_user').
						 " SET ec_salt='" . $ec_salt . "', password='" .$new_possword . "'".
						 " WHERE user_id='$_SESSION[admin_id]'");
				}

				if($row['action_list'] == 'all' && empty($row['last_login']))
				{
					$_SESSION['shop_guide'] = true;
				}

				// 更新最后登录时间和IP
				$db->query("UPDATE " .$ecs->table('admin_user').
						 " SET last_login='" . gmtime() . "', last_ip='" . real_ip() . "'".
						 " WHERE user_id='$_SESSION[admin_id]'");   
			} 
			else
			{
			    echo '107';
			    exit;
			} 

			if ($action=='w')
				{				
			    //clear_cache_files();
			    exit;
				}	
		elseif ($action=='p')
       {
          /* 标记订单为已确认，配货中 */
		  $order_sn = $_POST['porder'];
		  $arr=array();
		  if ($order_sn!="")
		  {
		    $order = order_info('-1', $order_sn);	
		    $order_id=$order["order_id"];
			if ($order['order_status'] != OS_CONFIRMED)
			{
				$arr['order_status']    = OS_CONFIRMED;
				$arr['confirm_time']    = gmtime();
			}
			$arr['shipping_status']     = SS_PREPARING;
			update_order($order_id, $arr);
			/* 记录log */
            order_action($order['order_sn'], OS_CONFIRMED, SS_PREPARING, $order['pay_status'], 'prepare');
            /* 清除缓存 */
			//clear_cache_files();
	    	 echo '100';
		     exit;
		}
		echo '106';
	    exit;
       }				
	         elseif ($action=='g')
		 	 {
				  header('Content-Type: text/xml;');
					echo '<?xml version="1.0" encoding="utf-8" ?>';
					echo '<DefaultUserlist>';
					echo '<'.$T.'>';
					$Psql="SELECT ".$F." FROM " . $ecs->table($T);
				 if ($W!='') $Psql=$Psql." where  ".$W;
				 $GetDbregion = $db->getAll($Psql);
				 $arraylist=split(",",$F);
					foreach ($GetDbregion as $Getval) 
								{
										foreach ($arraylist as $Fstrval)
										{
										  echo '<'.$Fstrval.'><![CDATA['.$Getval[$Fstrval].']]></'.$Fstrval.'>';
										};
								}					
					 echo '</'.$T.'>';
					 echo '</DefaultUserlist>';
					//clear_cache_files();
			    exit;
				}
		elseif ($action=='d')
			 {		
			 
		    $Getshipping_status =$_POST['shipping_status'];
	        $Getpay_status =  $_POST['pay_status'];
			$GetDefAS =  $_POST['DefAS'];
		    $now_time = local_date('Y-m-d H:i:s', time());
				$lastday_time = local_date('Y-m-d H:i:s', time()-86400);
				$start_time = isset($_POST['start_time']) && trim($_POST['start_time']) ? trim($_POST['start_time']) : $lastday_time;
				$end_time = isset($_POST['end_time']) && trim($_POST['end_time']) ? trim($_POST['end_time']) : $now_time;
				$start_time = strtotime($start_time)-8*60*60;
				$end_time =  strtotime($end_time)-8*60*60;
				if ($start_time==$end_time) {
				    $end_time = $start_time+86400;
				}
 			
				$GetshippingSql="";
				$GetpaySql="";
				if ($Getshipping_status!="")  {$GetshippingSql="  and shipping_status in (" . $Getshipping_status . ")";}
			    if ($Getpay_status!="") {$GetpaySql="  and pay_status in (" . $Getpay_status . ")";}		
				$shop_name = $_CFG['shop_name'];
				$shop_url = $ecs->url();
				$shop_address = $_CFG['shop_address'];
				$service_phone = $_CFG['service_phone'];
				$print_time = local_date($_CFG['time_format']);				
				$order_ids = $GLOBALS['db']->getAll("SELECT o.order_id,u.user_name" .
						" FROM " . $GLOBALS['ecs']->table('order_info') . " o" .
						" LEFT JOIN " . $GLOBALS['ecs']->table('users') . " u ON u.user_id=o.user_id" .
						" WHERE add_time>='" . $start_time . "' AND add_time<'" . $end_time . "'".$GetpaySql.$GetshippingSql.$GetDefAS);			
				header('Content-Type: text/xml;');
				echo '<?xml version="1.0" encoding="utf-8" ?>';
				echo '<order_list>';				
				$region_array = array();
				$region = $db->getAll("SELECT region_id, region_name FROM " . $ecs->table("region"));
				if (!empty($region))
				{
					foreach($region as $region_data)
					{
				    	$region_array[$region_data['region_id']] = $region_data['region_name'];
					}
				}				
				foreach ($order_ids as $val) {
					
				    $order = order_info($val['order_id']);		    
				    echo '<order>';
				    echo '<shop_name><![CDATA['.$shop_name.']]></shop_name>'; 
				    echo '<shop_url><![CDATA['.$shop_url.']]></shop_url>';
				    echo '<shop_address><![CDATA['.$shop_url.']]></shop_address>';
				    echo '<service_phone><![CDATA['.$service_phone.']]></service_phone>';
				    echo '<order_id><![CDATA['.$val['order_id'].']]></order_id>';
				    echo '<order_sn><![CDATA['.$order['order_sn'].']]></order_sn>';
				    echo '<user_name><![CDATA[' . $val['user_name'] . ']]></user_name>';
				    echo '<add_time><![CDATA['.local_date($_CFG['time_format'], $order['add_time']).']]></add_time>';
				    echo '<total_fee><![CDATA['.$order['total_fee'].']]></total_fee>';
					echo '<goods_amount_a><![CDATA['.$order['goods_amount'].']]></goods_amount_a>';
				    echo '<order_amount><![CDATA['.$order['order_amount'].']]></order_amount>';
				    echo '<shipping_fee><![CDATA['.$order['shipping_fee'].']]></shipping_fee>';
				    echo '<consignee><![CDATA['.$order['consignee'].']]></consignee>';
				    echo '<country><![CDATA['.$region_array[$order['country']].']]></country>';
				    echo '<province><![CDATA['.$region_array[$order['province']].']]></province>';
				    echo '<city><![CDATA['.$region_array[$order['city']].']]></city>';
				    echo '<district><![CDATA['.$region_array[$order['district']].']]></district>';
				    echo '<best_time><![CDATA['.$order['best_time'].']]></best_time>';
  				    echo '<invoice_no><![CDATA['.strip_tags(str_replace('&','',str_replace('/','',str_replace('>','',str_replace('<','',str_replace('nbsp;','',$order['invoice_no'])))))).']]></invoice_no>';
					if (!get_magic_quotes_gpc()) {
				        $order['address'] = addslashes($order['address']);
				    }
				    echo '<address><![CDATA['.$order['address'].']]></address>';
				    echo '<zipcode><![CDATA['.$order['zipcode'].']]></zipcode>';
				    echo '<tel><![CDATA['.$order['tel'].']]></tel>';
				    echo '<mobile><![CDATA['.$order['mobile'].']]></mobile>';
					$order['composite_status'] = "1001";
					if ($order['order_status']==OS_CONFIRMED && in_array($order['shipping_status'], array(SS_SHIPPED, SS_RECEIVED)) && $order['pay_status']==PS_UNPAYED) {
						$order['composite_status'] ="1002";
					}
					if ($order['order_status']==OS_CONFIRMED && $order['shipping_status']==SS_PREPARING && in_array($order['pay_status'], array(PS_PAYED, PS_PAYING))) {
						$order['composite_status'] ="1003";
					}
					if ($order['order_status']==OS_CONFIRMED && $order['shipping_status']==SS_UNSHIPPED && in_array($order['pay_status'], array(PS_PAYED, PS_PAYING))) {
						$order['composite_status'] ="1003" ;
					}
					if ($order['order_status']==OS_CONFIRMED && in_array($order['shipping_status'], array(SS_SHIPPED, SS_RECEIVED)) && in_array($order['pay_status'], array(PS_PAYED, PS_PAYING))) {
						$order['composite_status'] ="1004";
					}
					echo '<composite_status><![CDATA['.$order['composite_status'].']]></composite_status>';
				    echo '<order_status><![CDATA['.$order['order_status'].']]></order_status>';
					echo '<shipping_status><![CDATA['.$order['shipping_status'].']]></shipping_status>';
				    echo '<pay_status><![CDATA['.$order['pay_status'].']]></pay_status>';
				    echo '<pay_time><![CDATA['.local_date($_CFG['time_format'], $order['pay_time']).']]></pay_time>';
				    echo '<shipping_name><![CDATA['.$order['shipping_name'].']]></shipping_name>';
					echo '<pay_name><![CDATA['.$order['pay_name'].']]></pay_name>';	
					echo '<to_buyer><![CDATA['.$order['to_buyer'].']]></to_buyer>';	
					echo '<postscript><![CDATA['.strip_tags(str_replace('&','',str_replace('/','',str_replace('>','',str_replace('<','',str_replace('nbsp;','',$order['postscript'])))))).']]></postscript>';
				    echo '<shipping_time><![CDATA['.local_date($_CFG['time_format'], $order['shipping_time']).']]></shipping_time>';
					 
				    $goods_list = array();
				    $goods_attr = array();
    	   $sql = "SELECT o.*, IF(o.product_id > 0, p.product_number, g.goods_number) AS storage, o.goods_attr, g.suppliers_id, IFNULL(b.brand_name, '') AS brand_name,
		   s.suppliers_name,g.goods_thumb,g.goods_img
            FROM " . $ecs->table('order_goods') . " AS o
                LEFT JOIN " . $ecs->table('products') . " AS p
                    ON p.product_id = o.product_id
                LEFT JOIN " . $ecs->table('goods') . " AS g
                    ON o.goods_id = g.goods_id
                LEFT JOIN " . $ecs->table('brand') . " AS b
                    ON g.brand_id = b.brand_id ".
					"LEFT JOIN " . $ecs->table('suppliers') . " AS s ON g.suppliers_id = s.suppliers_id " .
            "WHERE o.order_id = '$order[order_id]'";
			
				    $res = $db->query($sql);
					while ($row = $db->fetchRow($res))
				    {
				        if ($row['is_real'] == 0)
				        {
				            $filename = ROOT_PATH . 'plugins/' . $row['extension_code'] . '/languages/common_' . $_CFG['lang'] . '.php';
				            if (file_exists($filename))
				            {
				                include_once($filename);
				                if (!empty($_LANG[$row['extension_code'].'_link']))
				                {
									 
									
				                    $row['goods_name'] = $row['goods_name'] . sprintf($_LANG[$row['extension_code'].'_link'], $row['goods_id'], $order['order_sn']);
				                }
				            }
				        }				
				        $row['formated_subtotal']       = price_format($row['goods_price'] * $row['goods_number']);
				        $row['formated_goods_price']    = price_format($row['goods_price']);
					 
				
				        $goods_attr[] = explode(' ', trim($row['goods_attr'])); 
				
				        if ($row['extension_code'] == 'package_buy')
				        {
				            $row['storage'] = '';
				            $row['brand_name'] = '';
				            $row['package_goods_list'] = get_package_goods($row['goods_id']);
				        }				
				        $goods_list[] = $row;
				    }				
				    $attr = array();
				    $arr  = array();
				    foreach ($goods_attr AS $index => $array_val)
				    {
				        foreach ($array_val AS $value)
				        {
				            $arr = explode(':', $value);
				            $attr[$index][] =  @array('name' => $arr[0], 'value' => $arr[1]);
				        }
				    }
				  $orderarraylist=explode(",",$F); 							
					foreach ($orderarraylist as $orderstrval)
						{ if ($orderstrval!='')	echo '<'.$orderstrval.'><![CDATA['.$order[$orderstrval].']]></'.$orderstrval.'>';
					  };
  	      echo '</order>';
					echo '<goods_list>';
					foreach ($goods_list as $goods) {					 
					    echo '<goods>';
					    echo '<order_sn><![CDATA['.$order['order_sn'].']]></order_sn>';
					    echo '<order_id><![CDATA['.$order['order_id'].']]></order_id>';
   					    echo '<goods_name><![CDATA['.strip_tags(str_replace('&','',str_replace('/','',str_replace('>','',str_replace('<','',str_replace('nbsp;','',$goods['goods_name'])))))).']]></goods_name>';					 
					    echo '<goods_sn><![CDATA['.$goods['goods_sn'].']]></goods_sn>';
					    echo '<goods_attr><![CDATA['.$goods['goods_attr'].']]></goods_attr>';
					    echo '<goods_price><![CDATA['.$goods['formated_goods_price'].']]></goods_price>';
					    echo '<goods_number><![CDATA['.$goods['goods_number'].']]></goods_number>';
					    echo '<formated_subtotal><![CDATA['.$goods['formated_subtotal'].']]></formated_subtotal>';
					    echo '<goods_weight><![CDATA['.$goods['goods_weight'].']]></goods_weight>';					    
					    echo '<goods_thumb><![CDATA['.$goods['goods_thumb'].']]></goods_thumb>';
					    echo '<goods_img><![CDATA['.$goods['goods_img'].']]></goods_img>';
    					echo '<suppliers_name><![CDATA['.$goods['suppliers_name'].']]></suppliers_name>';
						echo '<brand_name><![CDATA['.$goods['brand_name'].']]></brand_name>';
					    echo '</goods>';
					}
					echo '</goods_list>';   
				 }
				  echo '</order_list>';
			}
		elseif ($action=='f')
	  	{
		  	    $_SESSION['admin_name'] = $admin_user;
				$order_ship = array();
				$order_ship_str = $_POST['order_ship'];
				$order_ship = explode(',', $order_ship_str);
				foreach ($order_ship as $val) {
				    $temp_arr = explode('@', $val);
				    $order_sn = $temp_arr[0];
                    $invoice_no = $temp_arr[1];
				    $shipping_id = $temp_arr[2];
				    $shipping_name=$temp_arr[3];
				    $to_buyer=$temp_arr[4];
				    $order = order_info('-1', $order_sn);	
			  /*开始*/
		       $order_id=$order["order_id"];
			   if (!empty($invoice_no))
				{
				$order_id = intval(trim($order_id));

				 $action_note = '一键发货 迅捷 Send !'.$_POST['V'];	

				/* 查询：根据订单id查询订单信息  */
				if (!empty($order_id))
				{
					$order = order_info($order_id);
				}
				else
				{
					 echo '-106';
					 exit;
				}
			    if ($order['order_status']=="2"||$order['order_status']=="3"||$order['order_status']=="4"	)
					{echo '-106';exit;}				
				

				/* 查询：其他处理 */
				$order['order_time']    = local_date($_CFG['time_format'], $order['add_time']);
				$order['invoice_no']    = $order['shipping_status'] == SS_UNSHIPPED || $order['shipping_status'] == SS_PREPARING ? $_LANG['ss'][SS_UNSHIPPED] : $order['invoice_no'];

				/* 查询：是否保价 */
				$order['insure_yn'] = empty($order['insure_fee']) ? 0 : 1;
				/* 查询：是否存在实体商品 */
				$exist_real_goods = exist_real_goods($order_id);

				
				/* 查询：取得订单商品 */
				$_goods = get_order_goods(array('order_id' => $order['order_id'], 'order_sn' =>$order['order_sn']));

				$attr = $_goods['attr'];
				$goods_list = $_goods['goods_list'];
				unset($_goods);

				/* 查询：商品已发货数量 此单可发货数量 */
				if ($goods_list)
				{
					foreach ($goods_list as $key => $value)
					{
					}
					foreach ($goods_list as $key=>$goods_value)
					{
						if (!$goods_value['goods_id'])
						{
							continue;
						}

						/* 超级礼包 */
						if (($goods_value['extension_code'] == 'package_buy') && (count($goods_value['package_goods_list']) > 0))
						{
							$goods_list[$key]['package_goods_list'] = package_goods($goods_value['package_goods_list'], $goods_value['goods_number'], $goods_value['order_id'], $goods_value['extension_code'], $goods_value['goods_id']);

							foreach ($goods_list[$key]['package_goods_list'] as $pg_key => $pg_value)
							{
								$goods_list[$key]['package_goods_list'][$pg_key]['readonly'] = '';
								/* 使用库存 是否缺货 */
								if ($pg_value['storage'] <= 0 && $_CFG['use_storage'] == '1' && $_CFG['stock_dec_time'] == SDT_SHIP)
								{
									$goods_list[$key]['package_goods_list'][$pg_key]['send'] = $_LANG['act_good_vacancy'];
									$goods_list[$key]['package_goods_list'][$pg_key]['readonly'] = 'readonly="readonly"';
								}
								/* 将已经全部发货的商品设置为只读 */
								elseif ($pg_value['send'] <= 0)
								{
									$goods_list[$key]['package_goods_list'][$pg_key]['send'] = $_LANG['act_good_delivery'];
									$goods_list[$key]['package_goods_list'][$pg_key]['readonly'] = 'readonly="readonly"';
								}
							}
						}
						else
						{
							$goods_list[$key]['sended'] = $goods_value['send_number'];
							$goods_list[$key]['sended'] = $goods_value['goods_number'];
							$goods_list[$key]['send'] = $goods_value['goods_number'] - $goods_value['send_number'];
							$goods_list[$key]['readonly'] = '';
							/* 是否缺货 */
							if ($goods_value['storage'] <= 0 && $_CFG['use_storage'] == '1'  && $_CFG['stock_dec_time'] == SDT_SHIP)
							{
								$goods_list[$key]['send'] = $_LANG['act_good_vacancy'];
								$goods_list[$key]['readonly'] = 'readonly="readonly"';
							}
							elseif ($goods_list[$key]['send'] <= 0)
							{
								$goods_list[$key]['send'] = $_LANG['act_good_delivery'];
								$goods_list[$key]['readonly'] = 'readonly="readonly"';
							}
						}
					}
				}
				
				$suppliers_id = 0;
				
				$delivery['order_sn'] = trim($order['order_sn']);
				$delivery['add_time'] = trim($order['order_time']);
				$delivery['user_id'] = intval(trim($order['user_id']));
				$delivery['how_oos'] = trim($order['how_oos']);
				$delivery['shipping_id'] = trim($order['shipping_id']);
				$delivery['shipping_fee'] = trim($order['shipping_fee']);
				$delivery['consignee'] = trim($order['consignee']);
				$delivery['address'] = trim($order['address']);
				$delivery['country'] = intval(trim($order['country']));
				$delivery['province'] = intval(trim($order['province']));
				$delivery['city'] = intval(trim($order['city']));
				$delivery['district'] = intval(trim($order['district']));
				$delivery['sign_building'] = trim($order['sign_building']);
				$delivery['email'] = trim($order['email']);
				$delivery['zipcode'] = trim($order['zipcode']);
				$delivery['tel'] = trim($order['tel']);
				$delivery['mobile'] = trim($order['mobile']);
				$delivery['best_time'] = trim($order['best_time']);
				$delivery['postscript'] = trim($order['postscript']);
				$delivery['how_oos'] = trim($order['how_oos']);
				$delivery['insure_fee'] = floatval(trim($order['insure_fee']));
				$delivery['shipping_fee'] = floatval(trim($order['shipping_fee']));
				$delivery['agency_id'] = intval(trim($order['agency_id']));
				$delivery['shipping_name'] = trim($order['shipping_name']);

			/* 查询订单信息 */
			$order = order_info($order_id);
			/* 检查能否操作 */
			$operable_list = operable_list($order);
			
			/* 初始化提示信息 */
		   $msg = '';

				/* 定义当前时间 */
			   

				/* 取得订单商品 */
				$_goods = get_order_goods(array('order_id' => $order_id, 'order_sn' => $delivery['order_sn']));
				$goods_list = $_goods['goods_list'];
			

						/* 检查此单发货商品库存缺货情况 */
				/* $goods_list已经过处理 超值礼包中商品库存已取得 */
				$virtual_goods = array();
				$package_virtual_goods = array();
				/* 生成发货单 */
				/* 获取发货单号和流水号 */
				$delivery['delivery_sn'] = get_delivery_sn();
				$delivery_sn = $delivery['delivery_sn'];

				/* 获取当前操作员 */
				$delivery['action_user'] = $_SESSION['admin_name'];

				/* 获取发货单生成时间 */
			define('GMTIME_UTC', gmtime()); 
				$delivery['update_time'] = GMTIME_UTC;
				$delivery_time = $delivery['update_time'];
				$sql ="select add_time from ". $GLOBALS['ecs']->table('order_info') ." WHERE order_sn = '" . $delivery['order_sn'] . "'";
				$delivery['add_time'] =  $GLOBALS['db']->GetOne($sql);
				/* 获取发货单所属供应商 */
				$delivery['suppliers_id'] = $suppliers_id;

				/* 设置默认值 */
				$delivery['status'] = 2; // 正常
				$delivery['order_id'] = $order_id;

				/* 过滤字段项 */
				$filter_fileds = array(
									   'order_sn', 'add_time', 'user_id', 'how_oos', 'shipping_id', 'shipping_fee',
									   'consignee', 'address', 'country', 'province', 'city', 'district', 'sign_building',
									   'email', 'zipcode', 'tel', 'mobile', 'best_time', 'postscript', 'insure_fee',
									   'agency_id', 'delivery_sn', 'action_user', 'update_time',
									   'suppliers_id', 'status', 'order_id', 'shipping_name'
									   );
				$_delivery = array();
				foreach ($filter_fileds as $value)
				{
					$_delivery[$value] = $delivery[$value];
				}
				/* 发货单入库 */
				$query = $db->autoExecute($ecs->table('delivery_order'), $_delivery, 'INSERT', '', 'SILENT');
				$delivery_id = $db->insert_id();
				if ($delivery_id)
				{

					$delivery_goods = array();
					
					//发货单商品入库
					if (!empty($goods_list))
					{
						foreach ($goods_list as $value)
						{
							// 商品（实货）（虚货）
							if (empty($value['extension_code']) || $value['extension_code'] == 'virtual_card')
							{
								$delivery_goods = array('delivery_id' => $delivery_id,
														'goods_id' => $value['goods_id'],
														'product_id' => $value['product_id'],
														'product_sn' => $value['product_sn'],
														'goods_id' => $value['goods_id'],
														'goods_name' => $value['goods_name'],
														'brand_name' => $value['brand_name'],
														'goods_sn' => $value['goods_sn'],
														'send_number' => $value['goods_number'],
														'parent_id' => 0,
														'is_real' => $value['is_real'],
														'goods_attr' => $value['goods_attr']
														);
								/* 如果是货品 */
								if (!empty($value['product_id']))
								{
									$delivery_goods['product_id'] = $value['product_id'];

								}
								$query = $db->autoExecute($ecs->table('delivery_goods'), $delivery_goods, 'INSERT', '', 'SILENT');
								$sql = "UPDATE ".$GLOBALS['ecs']->table('order_goods'). "
						SET send_number = " . $value['goods_number'] . "
						WHERE order_id = '" . $value['order_id'] . "'
						AND goods_id = '" . $value['goods_id'] . "' ";
						$GLOBALS['db']->query($sql, 'SILENT');
							}
							// 商品（超值礼包）
							elseif ($value['extension_code'] == 'package_buy')
							{
								foreach ($value['package_goods_list'] as $pg_key => $pg_value)
								{
									$delivery_pg_goods = array('delivery_id' => $delivery_id,
															'goods_id' => $pg_value['goods_id'],
															'product_id' => $pg_value['product_id'],
															'product_sn' => $pg_value['product_sn'],
															'goods_name' => $pg_value['goods_name'],
															'brand_name' => '',
															'goods_sn' => $pg_value['goods_sn'],
															'send_number' => $value['goods_number'],
															'parent_id' => $value['goods_id'], // 礼包ID
															'extension_code' => $value['extension_code'], // 礼包
															'is_real' => $pg_value['is_real']
															);
									$query = $db->autoExecute($ecs->table('delivery_goods'), $delivery_pg_goods, 'INSERT', '', 'SILENT');
									$sql = "UPDATE ".$GLOBALS['ecs']->table('order_goods'). "
						SET send_number = " . $value['goods_number'] . "
						WHERE order_id = '" . $value['order_id'] . "'
						AND goods_id = '" . $pg_value['goods_id'] . "' ";
						$GLOBALS['db']->query($sql, 'SILENT');
								}
							}
						}
					}
				}
				else
				{
					/* 操作失败 */
					//$links[] = array('text' => $_LANG['order_info'], 'href' => 'order.php?act=info&order_id=' . $order_id);
					//sys_msg($_LANG['act_false'], 1, $links);
					echo '-106'; 
				}
				unset($filter_fileds, $delivery, $_delivery, $order_finish);

				/* 定单信息更新处理 */
				if (true)
				{

					/* 标记订单为已确认 “发货中” */
					/* 更新发货时间 */
					$order_finish = get_order_finish($order_id);
					$shipping_status = SS_SHIPPED_ING;
					if ($order['order_status'] != OS_CONFIRMED && $order['order_status'] != OS_SPLITED && $order['order_status'] != OS_SPLITING_PART)
					{
						$arr['order_status']    = OS_CONFIRMED;
						$arr['confirm_time']    = GMTIME_UTC;
					}
					$arr['order_status'] = $order_finish ? OS_SPLITED : OS_SPLITING_PART; // 全部分单、部分分单
					$arr['shipping_status']     = $shipping_status;
					update_order($order_id, $arr);
				}

				/* 记录log */
				order_action($order['order_sn'], $arr['order_status'], $shipping_status, $order['pay_status'], $action_note);

				/* 清除缓存 */
				//clear_cache_files();

			/* 根据发货单id查询发货单信息 */
			if (!empty($delivery_id))
			{
				$delivery_order = delivery_order_info($delivery_id);
			}
			elseif (!empty($order_sn))
			{

				$delivery_id = $GLOBALS['db']->getOne("SELECT delivery_id FROM " . $ecs->table('delivery_order') . " WHERE order_sn = " . $order_sn );
				$delivery_order = delivery_order_info($delivery_id);
			}
			else
			{
				die('order does not exist');
			}

			/* 如果管理员属于某个办事处，检查该订单是否也属于这个办事处 */
			$sql = "SELECT agency_id FROM " . $ecs->table('admin_user') . " WHERE user_id = '" . $_SESSION['admin_id'] . "'";
			$agency_id = $db->getOne($sql);
			if ($agency_id > 0)
			{
				if ($delivery_order['agency_id'] != $agency_id)
				{
					sys_msg($_LANG['priv_error']);
				}

				/* 取当前办事处信息 */
				$sql = "SELECT agency_name FROM " . $ecs->table('agency') . " WHERE agency_id = '$agency_id' LIMIT 0, 1";
				$agency_name = $db->getOne($sql);
				$delivery_order['agency_name'] = $agency_name;
			}

			/* 取得用户名 */
			if ($delivery_order['user_id'] > 0)
			{
				$user = user_info($delivery_order['user_id']);
				if (!empty($user))
				{
					$delivery_order['user_name'] = $user['user_name'];
				}
			}

			/* 取得区域名 */
			$sql = "SELECT concat(IFNULL(c.region_name, ''), '  ', IFNULL(p.region_name, ''), " .
						"'  ', IFNULL(t.region_name, ''), '  ', IFNULL(d.region_name, '')) AS region " .
					"FROM " . $ecs->table('order_info') . " AS o " .
						"LEFT JOIN " . $ecs->table('region') . " AS c ON o.country = c.region_id " .
						"LEFT JOIN " . $ecs->table('region') . " AS p ON o.province = p.region_id " .
						"LEFT JOIN " . $ecs->table('region') . " AS t ON o.city = t.region_id " .
						"LEFT JOIN " . $ecs->table('region') . " AS d ON o.district = d.region_id " .
					"WHERE o.order_id = '" . $delivery_order['order_id'] . "'";
			$delivery_order['region'] = $db->getOne($sql);

			/* 是否保价 */
			$order['insure_yn'] = empty($order['insure_fee']) ? 0 : 1;

			/* 取得发货单商品 */
			$goods_sql = "SELECT *
						  FROM " . $ecs->table('delivery_goods') . "
						  WHERE delivery_id = " . $delivery_order['delivery_id'];
			$goods_list = $GLOBALS['db']->getAll($goods_sql);

			/* 是否存在实体商品 */
			$exist_real_goods = 0;
			if ($goods_list)
			{
				foreach ($goods_list as $value)
				{
					if ($value['is_real'])
					{
						$exist_real_goods++;
					}
				}
			}

			/* 取得订单操作记录 */
			$act_list = array();
			$sql = "SELECT * FROM " . $ecs->table('order_action') . " WHERE order_id = '" . $delivery_order['order_id'] . "' AND action_place = 1 ORDER BY log_time DESC,action_id DESC";
			$res = $db->query($sql);
			while ($row = $db->fetchRow($res))
			{
				$row['order_status']    = $_LANG['os'][$row['order_status']];
				$row['pay_status']      = $_LANG['ps'][$row['pay_status']];
				$row['shipping_status'] = ($row['shipping_status'] == SS_SHIPPED_ING) ? $_LANG['ss_admin'][SS_SHIPPED_ING] : $_LANG['ss'][$row['shipping_status']];
				$row['action_time']     = local_date($_CFG['time_format'], $row['log_time']);
				$act_list[] = $row;
			}

			/*同步发货*/
			/*判断支付方式是否支付宝*/
			$alipay    = false;
			$order     = order_info($delivery_order['order_id']);  //根据订单ID查询订单信息，返回数组$order
			$payment   = payment_info($order['pay_id']);           //取得支付方式信息

			/* 定义当前时间 */
			define('GMTIME_UTC', gmtime()); // 获取 UTC 时间戳

			/* 根据发货单id查询发货单信息 */
			if (!empty($delivery_id))
			{
				$delivery_order = delivery_order_info($delivery_id);
			}
			else
			{
				die('order does not exist');
			}

			/* 查询订单信息 */
			$order = order_info($order_id);

			/* 检查此单发货商品库存缺货情况 */
			$virtual_goods = array();
			$delivery_stock_sql = "SELECT DG.goods_id, DG.is_real, DG.product_id, SUM(DG.send_number) AS sums, IF(DG.product_id > 0, P.product_number, G.goods_number) AS storage, G.goods_name, DG.send_number
				FROM " . $GLOBALS['ecs']->table('delivery_goods') . " AS DG, " . $GLOBALS['ecs']->table('goods') . " AS G, " . $GLOBALS['ecs']->table('products') . " AS P
				WHERE DG.goods_id = G.goods_id
				AND DG.delivery_id = '$delivery_id'
				AND DG.product_id = P.product_id
				GROUP BY DG.product_id ";

			$delivery_stock_result = $GLOBALS['db']->getAll($delivery_stock_sql);

			/* 如果商品存在规格就查询规格，如果不存在规格按商品库存查询 */
			if(!empty($delivery_stock_result))
			{
				foreach ($delivery_stock_result as $value)
				{
					if (($value['sums'] > $value['storage'] || $value['storage'] <= 0) && (($_CFG['use_storage'] == '1'  && $_CFG['stock_dec_time'] == SDT_SHIP) || ($_CFG['use_storage'] == '0' && $value['is_real'] == 0)))
					{
						/* 操作失败 */
						$links[] = array('text' => $_LANG['order_info'], 'href' => 'order.php?act=delivery_info&delivery_id=' . $delivery_id);
						sys_msg(sprintf($_LANG['act_good_vacancy'], $value['goods_name']), 1, $links);
						break;
					}

					/* 虚拟商品列表 virtual_card*/
					if ($value['is_real'] == 0)
					{
						$virtual_goods[] = array(
									   'goods_id' => $value['goods_id'],
									   'goods_name' => $value['goods_name'],
									   'num' => $value['send_number']
									   );
					}
				}
			}
			else
			{
				$delivery_stock_sql = "SELECT DG.goods_id, DG.is_real, SUM(DG.send_number) AS sums, G.goods_number, G.goods_name, DG.send_number
				FROM " . $GLOBALS['ecs']->table('delivery_goods') . " AS DG, " . $GLOBALS['ecs']->table('goods') . " AS G
				WHERE DG.goods_id = G.goods_id
				AND DG.delivery_id = '$delivery_id'
				GROUP BY DG.goods_id ";
				$delivery_stock_result = $GLOBALS['db']->getAll($delivery_stock_sql);
				foreach ($delivery_stock_result as $value)
				{
					if (($value['sums'] > $value['goods_number'] || $value['goods_number'] <= 0) && (($_CFG['use_storage'] == '1'  && $_CFG['stock_dec_time'] == SDT_SHIP) || ($_CFG['use_storage'] == '0' && $value['is_real'] == 0)))
					{
						/* 操作失败 */
						//$links[] = array('text' => $_LANG['order_info'], 'href' => 'order.php?act=delivery_info&delivery_id=' . $delivery_id);
						//sys_msg(sprintf($_LANG['act_good_vacancy'], $value['goods_name']), 1, $links);
						break;
					}

					/* 虚拟商品列表 virtual_card*/
					if ($value['is_real'] == 0)
					{
						$virtual_goods[] = array(
									   'goods_id' => $value['goods_id'],
									   'goods_name' => $value['goods_name'],
									   'num' => $value['send_number'],
									   );
					}
				}
			}

			/* 发货 */
			/* 处理虚拟卡 商品（虚货） */
			if (is_array($virtual_goods) && count($virtual_goods) > 0)
			{
				foreach ($virtual_goods as $virtual_value)
				{
					virtual_card_shipping($virtual_value,$order['order_sn'], $msg, 'split');
				}
			}

			/* 如果使用库存，且发货时减库存，则修改库存 */
			if ($_CFG['use_storage'] == '1' && $_CFG['stock_dec_time'] == SDT_SHIP)
			{

				foreach ($delivery_stock_result as $value)
				{

					/* 商品（实货）、超级礼包（实货） */
					if ($value['is_real'] != 0)
					{
						//（货品）
						if (!empty($value['product_id']))
						{
							$minus_stock_sql = "UPDATE " . $GLOBALS['ecs']->table('products') . "
												SET product_number = product_number - " . $value['sums'] . "
												WHERE product_id = " . $value['product_id'];
							$GLOBALS['db']->query($minus_stock_sql, 'SILENT');
						}

						$minus_stock_sql = "UPDATE " . $GLOBALS['ecs']->table('goods') . "
											SET goods_number = goods_number - " . $value['sums'] . "
											WHERE goods_id = " . $value['goods_id'];

						$GLOBALS['db']->query($minus_stock_sql, 'SILENT');
					}
				}
			}

			/* 修改发货单信息 */
			$invoice_no = trim($invoice_no);
			$_delivery['invoice_no'] = $invoice_no;
			$_delivery['status'] = 0; // 0，为已发货
			$query = $db->autoExecute($ecs->table('delivery_order'), $_delivery, 'UPDATE', "delivery_id = $delivery_id", 'SILENT');
			if (!$query)
			{
				/* 操作失败 */
				//$links[] = array('text' => $_LANG['delivery_sn'] . $_LANG['detail'], 'href' => 'order.php?act=delivery_info&delivery_id=' . $delivery_id);
				//sys_msg($_LANG['act_false'], 1, $links);
				echo '-106';
			}

			/* 标记订单为已确认 “已发货” */
			/* 更新发货时间 */
			$order_finish = get_all_delivery_finish($order_id);
			$shipping_status = ($order_finish == 1) ? SS_SHIPPED : SS_SHIPPED_PART;
			$arr['shipping_status']     = $shipping_status;
			$arr['shipping_time']       = GMTIME_UTC; // 发货时间
			$arr['invoice_no']          = trim($order['invoice_no'] . '<br>' . $invoice_no, '<br>');
			$arr['shipping_id']=$shipping_id;
			$arr['shipping_name']=$shipping_name;
			update_order($order_id, $arr);

			/* 发货单发货记录log */
			order_action($order['order_sn'], OS_CONFIRMED, $shipping_status, $order['pay_status'], $action_note, null, 1);
			/* 如果当前订单已经全部发货 */
			if ($order_finish)
			{
				/* 如果订单用户不为空，计算积分，并发给用户；发红包 */
				if ($order['user_id'] > 0)
				{
					/* 取得用户信息 */
					$user = user_info($order['user_id']);

					/* 计算并发放积分 */
					$integral = integral_to_give($order);

					log_account_change($order['user_id'], 0, 0, intval($integral['rank_points']), intval($integral['custom_points']), sprintf($_LANG['order_gift_integral'], $order['order_sn']));

					/* 发放红包 */
					send_order_bonus($order_id);
				}

				/* 发送邮件 */
				$cfg = $_CFG['send_ship_email'];
				if ($cfg == '1')
				{
					$order['invoice_no'] = $invoice_no;
					$tpl = get_mail_template('deliver_notice');
					$smarty->assign('order', $order);
					$smarty->assign('send_time', local_date($_CFG['time_format']));
					$smarty->assign('shop_name', $_CFG['shop_name']);
					$smarty->assign('send_date', local_date($_CFG['date_format']));
					$smarty->assign('sent_date', local_date($_CFG['date_format']));
					$smarty->assign('confirm_url', $ecs->url() . 'receive.php?id=' . $order['order_id'] . '&con=' . rawurlencode($order['consignee']));
					$smarty->assign('send_msg_url',$ecs->url() . 'user.php?act=message_list&order_id=' . $order['order_id']);
					$content = $smarty->fetch('str:' . $tpl['template_content']);
					if (!send_mail($order['consignee'], $order['email'], $tpl['template_subject'], $content, $tpl['is_html']))
					{
						$msg = $_LANG['send_mail_fail'];
					}
				}

				/* 如果需要，发短信 */
				if ($GLOBALS['_CFG']['sms_order_shipped'] == '1' && $order['mobile'] != '')
				{
					include_once('../includes/cls_sms.php');
					$sms = new sms();
					$sms->send($order['mobile'], sprintf($GLOBALS['_LANG']['order_shipped_sms'], $order['order_sn'],
						local_date($GLOBALS['_LANG']['sms_time_format']), $GLOBALS['_CFG']['shop_name']), 0);
				}
			}

			/* 清除缓存 */
			//clear_cache_files(); 
			}
					 
					  /*结束*/
					 
					 
                }
			  echo '100'; 
		}
		elseif ($action=='q')
	  	{
		  	    $_SESSION['admin_name'] = $admin_user;
				$order_sn_check = array();
				$order_sn_check_str = $_POST['order_sn'];
				$order_sn_check = explode(',', $order_sn_check_str);
				foreach ($order_sn_check as $val) {				   
				    $order_sn =$val;
 				    $order = order_info('-1', $order_sn);						 
			         /* 标记订单为“收货确认”，如果是货到付款，同时修改订单为已付款 */					
					$arr = array('shipping_status' => SS_RECEIVED);
					$payment = payment_info($order['pay_id']);
					if ($payment['is_cod'])
					{
						$arr['pay_status'] = PS_PAYED;
						$order['pay_status'] = PS_PAYED;
					}
					update_order($order['order_id'], $arr);
					order_action($order['order_sn'], $order['order_status'], SS_RECEIVED, $order['pay_status'], $action_note);
				   
				  }
				   echo '100'; 
		} 		
	    function rsalt($string) {
			$string = base64_decode($string);
			$key = date("Ymd");
			$cipher_alg = MCRYPT_TRIPLEDES;
			$iv = mcrypt_create_iv(mcrypt_get_iv_size($cipher_alg,MCRYPT_MODE_ECB), MCRYPT_RAND); 
			$decrypted_string = mcrypt_decrypt($cipher_alg, $key, $string, MCRYPT_MODE_ECB, $iv); 
			return trim($decrypted_string);
	   }
 
/**
 * 取得订单商品
 * @param   array     $order  订单数组
 * @return array
 */
function get_order_goods($order)
{
    $goods_list = array();
    $goods_attr = array();
    $sql = "SELECT o.*, g.suppliers_id AS suppliers_id,IF(o.product_id > 0, p.product_number, g.goods_number) AS storage, o.goods_attr, IFNULL(b.brand_name, '') AS brand_name, p.product_sn " .
            "FROM " . $GLOBALS['ecs']->table('order_goods') . " AS o ".
            "LEFT JOIN " . $GLOBALS['ecs']->table('products') . " AS p ON o.product_id = p.product_id " .
            "LEFT JOIN " . $GLOBALS['ecs']->table('goods') . " AS g ON o.goods_id = g.goods_id " .
            "LEFT JOIN " . $GLOBALS['ecs']->table('brand') . " AS b ON g.brand_id = b.brand_id " .
            "WHERE o.order_id = '$order[order_id]' ";
    $res = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        // 虚拟商品支持
        if ($row['is_real'] == 0)
        {
            /* 取得语言项 */
            $filename = ROOT_PATH . 'plugins/' . $row['extension_code'] . '/languages/common_' . $GLOBALS['_CFG']['lang'] . '.php';
            if (file_exists($filename))
            {
                include_once($filename);
                if (!empty($GLOBALS['_LANG'][$row['extension_code'].'_link']))
                {
                    $row['goods_name'] = $row['goods_name'] . sprintf($GLOBALS['_LANG'][$row['extension_code'].'_link'], $row['goods_id'], $order['order_sn']);
                }
            }
        }

        $row['formated_subtotal']       = price_format($row['goods_price'] * $row['goods_number']);
        $row['formated_goods_price']    = price_format($row['goods_price']);

        $goods_attr[] = explode(' ', trim($row['goods_attr'])); //将商品属性拆分为一个数组

        if ($row['extension_code'] == 'package_buy')
        {
            $row['storage'] = '';
            $row['brand_name'] = '';
            $row['package_goods_list'] = get_package_goods_list($row['goods_id']);
        }

        //处理货品id
        $row['product_id'] = empty($row['product_id']) ? 0 : $row['product_id'];

        $goods_list[] = $row;
    }

    $attr = array();
    $arr  = array();
    foreach ($goods_attr AS $index => $array_val)
    {
        foreach ($array_val AS $value)
        {
            $arr = explode(':', $value);//以 : 号将属性拆开
            $attr[$index][] =  @array('name' => $arr[0], 'value' => $arr[1]);
        }
    }

    return array('goods_list' => $goods_list, 'attr' => $attr);
}

/**
 * 返回某个订单可执行的操作列表，包括权限判断
 * @param   array   $order      订单信息 order_status, shipping_status, pay_status
 * @param   bool    $is_cod     支付方式是否货到付款
 * @return  array   可执行的操作  confirm, pay, unpay, prepare, ship, unship, receive, cancel, invalid, return, drop
 * 格式 array('confirm' => true, 'pay' => true)
 */
function operable_list($order)
{
    /* 取得订单状态、发货状态、付款状态 */
    $os = $order['order_status'];
    $ss = $order['shipping_status'];
    $ps = $order['pay_status'];
    /* 取得订单操作权限 */
    $actions = $_SESSION['action_list'];
    if ($actions == 'all')
    {
        $priv_list  = array('os' => true, 'ss' => true, 'ps' => true, 'edit' => true);
    }
    else
    {
        $actions    = ',' . $actions . ',';
        $priv_list  = array(
            'os'    => strpos($actions, ',order_os_edit,') !== false,
            'ss'    => strpos($actions, ',order_ss_edit,') !== false,
            'ps'    => strpos($actions, ',order_ps_edit,') !== false,
            'edit'  => strpos($actions, ',order_edit,') !== false
        );
    }

    /* 取得订单支付方式是否货到付款 */
    $payment = payment_info($order['pay_id']);
    $is_cod  = $payment['is_cod'] == 1;

    /* 根据状态返回可执行操作 */
    $list = array();
    if (OS_UNCONFIRMED == $os)
    {
        /* 状态：未确认 => 未付款、未发货 */
        if ($priv_list['os'])
        {
            $list['confirm']    = true; // 确认
            $list['invalid']    = true; // 无效
            $list['cancel']     = true; // 取消
            if ($is_cod)
            {
                /* 货到付款 */
                if ($priv_list['ss'])
                {
                    $list['prepare'] = true; // 配货
                    $list['split'] = true; // 分单
                }
            }
            else
            {
                /* 不是货到付款 */
                if ($priv_list['ps'])
                {
                    $list['pay'] = true;  // 付款
                }
            }
        }
    }
    elseif (OS_CONFIRMED == $os || OS_SPLITED == $os || OS_SPLITING_PART == $os)
    {
        /* 状态：已确认 */
        if (PS_UNPAYED == $ps)
        {
            /* 状态：已确认、未付款 */
            if (SS_UNSHIPPED == $ss || SS_PREPARING == $ss)
            {
                /* 状态：已确认、未付款、未发货（或配货中） */
                if ($priv_list['os'])
                {
                    $list['cancel'] = true; // 取消
                    $list['invalid'] = true; // 无效
                }
                if ($is_cod)
                {
                    /* 货到付款 */
                    if ($priv_list['ss'])
                    {
                        if (SS_UNSHIPPED == $ss)
                        {
                            $list['prepare'] = true; // 配货
                        }
                        $list['split'] = true; // 分单
                    }
                }
                else
                {
                    /* 不是货到付款 */
                    if ($priv_list['ps'])
                    {
                        $list['pay'] = true; // 付款
                    }
                }
            }
            /* 状态：已确认、未付款、发货中 */
            elseif (SS_SHIPPED_ING == $ss || SS_SHIPPED_PART == $ss)
            {
                // 部分分单
                if (OS_SPLITING_PART == $os)
                {
                    $list['split'] = true; // 分单
                }
                $list['to_delivery'] = true; // 去发货
            }
            else
            {
                /* 状态：已确认、未付款、已发货或已收货 => 货到付款 */
                if ($priv_list['ps'])
                {
                    $list['pay'] = true; // 付款
                }
                if ($priv_list['ss'])
                {
                    if (SS_SHIPPED == $ss)
                    {
                        $list['receive'] = true; // 收货确认
                    }
                    $list['unship'] = true; // 设为未发货
                    if ($priv_list['os'])
                    {
                        $list['return'] = true; // 退货
                    }
                }
            }
        }
        else
        {
            /* 状态：已确认、已付款和付款中 */
            if (SS_UNSHIPPED == $ss || SS_PREPARING == $ss)
            {
                /* 状态：已确认、已付款和付款中、未发货（配货中） => 不是货到付款 */
                if ($priv_list['ss'])
                {
                    if (SS_UNSHIPPED == $ss)
                    {
                        $list['prepare'] = true; // 配货
                    }
                    $list['split'] = true; // 分单
                }
                if ($priv_list['ps'])
                {
                    $list['unpay'] = true; // 设为未付款
                    if ($priv_list['os'])
                    {
                        $list['cancel'] = true; // 取消
                    }
                }
            }
            /* 状态：已确认、未付款、发货中 */
            elseif (SS_SHIPPED_ING == $ss || SS_SHIPPED_PART == $ss)
            {
                // 部分分单
                if (OS_SPLITING_PART == $os)
                {
                    $list['split'] = true; // 分单
                }
                $list['to_delivery'] = true; // 去发货
            }
            else
            {
                /* 状态：已确认、已付款和付款中、已发货或已收货 */
                if ($priv_list['ss'])
                {
                    if (SS_SHIPPED == $ss)
                    {
                        $list['receive'] = true; // 收货确认
                    }
                    if (!$is_cod)
                    {
                        $list['unship'] = true; // 设为未发货
                    }
                }
                if ($priv_list['ps'] && $is_cod)
                {
                    $list['unpay']  = true; // 设为未付款
                }
                if ($priv_list['os'] && $priv_list['ss'] && $priv_list['ps'])
                {
                    $list['return'] = true; // 退货（包括退款）
                }
            }
        }
    }
    elseif (OS_CANCELED == $os)
    {
        /* 状态：取消 */
        if ($priv_list['os'])
        {
            $list['confirm'] = true;
        }
        if ($priv_list['edit'])
        {
            $list['remove'] = true;
        }
    }
    elseif (OS_INVALID == $os)
    {
        /* 状态：无效 */
        if ($priv_list['os'])
        {
            $list['confirm'] = true;
        }
        if ($priv_list['edit'])
        {
            $list['remove'] = true;
        }
    }
    elseif (OS_RETURNED == $os)
    {
        /* 状态：退货 */
        if ($priv_list['os'])
        {
            $list['confirm'] = true;
        }
    }

    /* 修正发货操作 */
    if (!empty($list['split']))
    {
        /* 如果是团购活动且未处理成功，不能发货 */
        if ($order['extension_code'] == 'group_buy')
        {
            include_once(ROOT_PATH . 'includes/lib_goods.php');
            $group_buy = group_buy_info(intval($order['extension_id']));
            if ($group_buy['status'] != GBS_SUCCEED)
            {
                unset($list['split']);
                unset($list['to_delivery']);
            }
        }

        /* 如果部分发货 不允许 取消 订单 */
        if (order_deliveryed($order['order_id']))
        {
            $list['return'] = true; // 退货（包括退款）
            unset($list['cancel']); // 取消
        }
    }

    /* 售后 */
    $list['after_service'] = true;

    return $list;
}

/**
 * 订单中的商品是否已经全部发货
 * @param   int     $order_id  订单 id
 * @return  int     1，全部发货；0，未全部发货
 */
function get_order_finish($order_id)
{
    $return_res = 0;

    if (empty($order_id))
    {
        return $return_res;
    }

    $sql = 'SELECT COUNT(rec_id)
            FROM ' . $GLOBALS['ecs']->table('order_goods') . '
            WHERE order_id = \'' . $order_id . '\'
            AND goods_number > send_number';

    $sum = $GLOBALS['db']->getOne($sql);
    if (empty($sum))
    {
        $return_res = 1;
    }

    return $return_res;
}

/**
 * 判断订单的发货单是否全部发货
 * @param   int     $order_id  订单 id
 * @return  int     1，全部发货；0，未全部发货；-1，部分发货；-2，完全没发货；
 */
function get_all_delivery_finish($order_id)
{
    $return_res = 0;

    if (empty($order_id))
    {
        return $return_res;
    }

    /* 未全部分单 */
    if (!get_order_finish($order_id))
    {
        return $return_res;
    }
    /* 已全部分单 */
    else
    {
        // 是否全部发货
        $sql = "SELECT COUNT(delivery_id)
                FROM " . $GLOBALS['ecs']->table('delivery_order') . "
                WHERE order_id = '$order_id'
                AND status = 2 ";
        $sum = $GLOBALS['db']->getOne($sql);
        // 全部发货
        if (empty($sum))
        {
            $return_res = 1;
        }
        // 未全部发货
        else
        {
            /* 订单全部发货中时：当前发货单总数 */
            $sql = "SELECT COUNT(delivery_id)
            FROM " . $GLOBALS['ecs']->table('delivery_order') . "
            WHERE order_id = '$order_id'
            AND status <> 1 ";
            $_sum = $GLOBALS['db']->getOne($sql);
            if ($_sum == $sum)
            {
                $return_res = -2; // 完全没发货
            }
            else
            {
                $return_res = -1; // 部分发货
            }
        }
    }

    return $return_res;
}

/**
 * 取得发货单信息
 * @param   int     $delivery_order   发货单id（如果delivery_order > 0 就按id查，否则按sn查）
 * @param   string  $delivery_sn      发货单号
 * @return  array   发货单信息（金额都有相应格式化的字段，前缀是formated_）
 */
function delivery_order_info($delivery_id, $delivery_sn = '')
{
    $return_order = array();
    if (empty($delivery_id) || !is_numeric($delivery_id))
    {
        return $return_order;
    }

    $where = '';
    /* 获取管理员信息 */
    $admin_info = admin_info();

    /* 如果管理员属于某个办事处，只列出这个办事处管辖的发货单 */
    if ($admin_info['agency_id'] > 0)
    {
        $where .= " AND agency_id = '" . $admin_info['agency_id'] . "' ";
    }

    /* 如果管理员属于某个供货商，只列出这个供货商的发货单 */
    if ($admin_info['suppliers_id'] > 0)
    {
        $where .= " AND suppliers_id = '" . $admin_info['suppliers_id'] . "' ";
    }

    $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('delivery_order');
    if ($delivery_id > 0)
    {
        $sql .= " WHERE delivery_id = '$delivery_id'";
    }
    else
    {
        $sql .= " WHERE delivery_sn = '$delivery_sn'";
    }

    $sql .= $where;
    $sql .= " LIMIT 0, 1";
    $delivery = $GLOBALS['db']->getRow($sql);
    if ($delivery)
    {
        /* 格式化金额字段 */
        $delivery['formated_insure_fee']     = price_format($delivery['insure_fee'], false);
        $delivery['formated_shipping_fee']   = price_format($delivery['shipping_fee'], false);

        /* 格式化时间字段 */
        $delivery['formated_add_time']       = local_date($GLOBALS['_CFG']['time_format'], $delivery['add_time']);
        $delivery['formated_update_time']    = local_date($GLOBALS['_CFG']['time_format'], $delivery['update_time']);

        $return_order = $delivery;
    }

    return $return_order;
}
function admin_info()
{
    $sql = "SELECT * FROM ". $GLOBALS['ecs']->table('admin_user')."
            WHERE user_id = '$_SESSION[admin_id]'
            LIMIT 0, 1";
    $admin_info = $GLOBALS['db']->getRow($sql);

    if (empty($admin_info))
    {
        return $admin_info = array();
    }

    return $admin_info;
}
     
		 //clear_cache_files(); 
?> 