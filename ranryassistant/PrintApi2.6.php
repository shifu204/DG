<?php
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
			$sql="SELECT user_name FROM ". $ecs->table('admin_user') ."WHERE user_name = '" .$admin_user. "'";
			$userrow = $db->getRow($sql);			
			if (!$userrow)
			{
			    echo '106';
			    exit;
			}			
			$sql = "SELECT user_id, user_name, password".
					" FROM " . $ecs->table('admin_user') .
					" WHERE user_name = '" . $admin_user. "' AND password = '" . md5($hash_code) . "'";		 
			$row = $db->getRow($sql);
			if (!$row)
			{
			    echo '107';
			    exit;
			} 
			else
			{ 
			   $_SESSION['admin_name'] =$admin_user;
			} 

			if ($action=='w')
				{				
			    //clear_cache_files();
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
										  echo '<'.$Fstrval.'>'.$Getval[$Fstrval].'</'.$Fstrval.'>';
										};
								}					
					 echo '</'.$T.'>';
					 echo '</DefaultUserlist>';
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
		elseif ($action=='d')
			 {		
		    $Getshipping_status =$_POST['shipping_status'];
	      $Getpay_status =  $_POST['pay_status'];		   
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
						" WHERE add_time>='" . $start_time . "' AND add_time<'" . $end_time . "'".$GetpaySql.$GetshippingSql);			
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
				    echo '<order_amount><![CDATA['.$order['goods_amount'].']]></order_amount>';
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
					echo '<postscript><![CDATA['.strip_tags(str_replace('&','',str_replace('/','',str_replace('>','',str_replace('<','',str_replace('nbsp;','',$order['postscript'])))))).']]></postscript>';
				    echo '<shipping_time><![CDATA['.local_date($_CFG['time_format'], $order['shipping_time']).']]></shipping_time>';
				    $goods_list = array();
				    $goods_attr = array();
				    $sql = "SELECT o.*, o.goods_attr, IFNULL(b.brand_name, '') AS brand_name ,g.goods_thumb,g.goods_img
				            FROM " . $ecs->table('order_goods') . " AS o
				                LEFT JOIN " . $ecs->table('goods') . " AS g
				                    ON o.goods_id = g.goods_id
				                LEFT JOIN " . $ecs->table('brand') . " AS b
				                    ON g.brand_id = b.brand_id
				            WHERE o.order_id = '$order[order_id]'";
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
				  $orderarraylist=split(",",$F); 							
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
				    $shipping_status = SS_SHIPPED;
				    if ($order['order_status'] != OS_CONFIRMED)
				    {
				        $arr['order_status']    = OS_CONFIRMED;
				        $arr['confirm_time']    = gmtime();
				    }
				    $arr['shipping_id']     = $shipping_id;
				    if ($to_buyer != '')
				    {
				       $arr['to_buyer']     = $to_buyer;
				    }				    
				    $arr['shipping_status']     = $shipping_status;
				    $arr['shipping_time']       = gmtime();
				    $arr['invoice_no']          = $invoice_no;
				    update_order($order['order_id'], $arr);
				    $order['invoice_no'] = $invoice_no;
				    $action_note = ' XunJie Assistant Send !'.$_POST['V'];;
				    order_action($order['order_sn'], OS_CONFIRMED, $shipping_status, $order['pay_status'], $action_note);
				    if ($order['user_id'] > 0)
				    {
				        $user = user_info($order['user_id']);
				        $integral = integral_to_give($order);				
				        log_account_change($order['user_id'], 0, 0, intval($integral['rank_points']), intval($integral['custom_points']), sprintf($_LANG['order_gift_integral'], $order['order_sn']));
				        send_order_bonus($order_id);
				    }
				   if ($_CFG['use_storage'] == '1')
						{
							if ($_CFG['stock_dec_time'] == SDT_SHIP)
							{
								change_order_goods_storage($order['order_id']);
							}
							elseif ($_CFG['stock_dec_time'] == SDT_PLACE)
							{
								change_order_goods_storage($order['order_id']);
							}
							$sql = "UPDATE " . $GLOBALS['ecs']->table('order_goods') . "
                            SET send_number = goods_number
                            WHERE order_id = ".$order['order_id'];
                            $GLOBALS['db']->query($sql, 'SILENT');
						}
				    $cfg = $_CFG['send_ship_email'];
				    if ($cfg == '1')
				    {
				        $tpl = get_mail_template('deliver_notice');
				        $content = $smarty->fetch('str:' . $tpl['template_content']);
				        send_mail($order['consignee'], $order['email'], $tpl['template_subject'], $content, $tpl['is_html']);
				    }
				    if ($GLOBALS['_CFG']['sms_order_shipped'] == '1' && $order['mobile'] != '')
				    {
				        include_once('../includes/cls_sms.php');
				        $sms = new sms();
				        $sms->send($order['mobile'], sprintf($GLOBALS['_LANG']['order_shipped_sms'], $order['order_sn'],
				            local_date($GLOBALS['_LANG']['sms_time_format']), $GLOBALS['_CFG']['shop_name']), 0);
				    } 
				  }
				   echo '100'; 
			} 
 
		 //clear_cache_files(); 
?> 