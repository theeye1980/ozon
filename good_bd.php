<?php
define('MODX_API_MODE', true);

class good_bd {
    
    var $ozon_product_id;
    public   $type_of=array(
    "Подвесы"=>667,
    "Потолочные светильники"=>671,
    "Настенные светильники"=>661,
    "Тарелки"=>660,
    "Люстра подвесная"=>667,
    "Люстра потолочная"=>671,
    "Люстра каскадная"=>667,
    "Бра с одним плафоном"=>643,
    "Бра с двумя плафонами"=>643,
    "Бра с тремя и более плафонами"=>643,
    "Подсветки для зеркал"=>661,
    "Акцентное освещение"=>661,
    "Споты с одним плафоном"=>660,
    "Споты с двумя плафонами"=>660,
    "Споты с тремя и более плафонами"=>660,
    "Трековые светильники"=>660,
    "С одной лампой"=>646,
    "С двумя лампами"=>646,
    "С тремя и более лампами"=>646,
    "Настольные лампы декоративные"=>663,
    "Настольные лампы офисные"=>663,
    "Настольные лампы на струбцине"=>663,
    "Настольные лампы детские"=>663,
    "Уличные настенные светильники"=>689,
    "Уличные наземные свтеильники"=>676,
    "Уличные подвесные светильники"=>689,
    "Наружные светильники на солнечных батареях"=>676,
    "Уличные встраиваемые светильники"=>689,
    "Наружные светильники"=>689,
    "Наружные прожекторы"=>689,
    "Уличные потолочные светильники"=>689,
    "Торшеры с одним плафоном"=>659,
    "Торшеры с двумя и более плафонами"=>659,
    "Гирлянды"=>691,
    "Декоративные нити"=>691,
    "Дюралайты"=>691,
    "Комплекты для украшения"=>648,
    "Ленты"=>684,
    "Светодиодные фигуры"=>684
);
    function result_sql($sql){
        global $modx;
        $statement = $modx->query($sql);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    function get_all_goods(){
		global $modx;
        $query = "select `id` from goods";
		//echo $query;
        $statement_tv = $modx->query($query);
        $result_tv = $statement_tv->fetchAll(PDO::FETCH_ASSOC);
		$i=1;
        foreach($result_tv as $r_tv){
			$ids[$i]=$r_tv['id'];
			$i++;
		}
		return $ids;
	
	}
	function get_good_by_id($id) { # функция вытаскивания данных из Базы в объект
        global $modx;
        $this->id=$id;
		$query = "select * from goods where `id`=".$this->id;
        $statement_tv = $modx->query($query);
        $result_tv = $statement_tv->fetchAll(PDO::FETCH_ASSOC);
        $this->fetching($result_tv);
    }
    function get_good($ozon_product_id) { # функция вытаскивания данных из Базы в объект
        global $modx;
        $this->ozon_product_id=$ozon_product_id;
        
        $query = "select * from goods where `ozon_product_id`=".$this->ozon_product_id;
        $statement_tv = $modx->query($query);
        $result_tv = $statement_tv->fetchAll(PDO::FETCH_ASSOC);
        $this->fetching($result_tv);
    }
	function get_art_by_product_id($ozon_product_id) { # вытаскивает номер артикула из БД по id товара озона
		global $modx;
		$this->ozon_product_id=$ozon_product_id;
        
        $query = "select vendorCode,stock from goods where `ozon_product_id`=".$this->ozon_product_id;
        $statement_tv = $modx->query($query);
        $result_tv = $statement_tv->fetchAll(PDO::FETCH_ASSOC);
		foreach($result_tv as $r_tv){
			$this->vendorCode=$r_tv['vendorCode'];
			$this->stock=$r_tv['stock'];			
		}
	}
	function get_good_by_art($art) { # функция вытаскивания данных из Базы в объект
        global $modx;
        $this->ozon_product_id='Не задан';
        
		if(strpos($art,"'")) $art=str_replace("'","_",$art);
        $query = "select * from goods where `vendorCode`='$art'";
        $statement_tv = $modx->query($query);
        $result_tv = $statement_tv->fetchAll(PDO::FETCH_ASSOC);
        $this->fetching($result_tv);
    }
	function fetching($result_tv){
		foreach($result_tv as $r_tv){
			$this->barcode=$r_tv['barcode'];
			$this->ozon_product_id=$r_tv['ozon_product_id'];
			$this->xml_di=$r_tv['xml_di'];
			$this->main_picture=$r_tv['main_picture'];
			$this->dop_picture=$r_tv['dop_picture'];
			$this->stock=$r_tv['stock'];
			$this->price=$r_tv['price'];
			$this->old_price=$r_tv['old_price'];
			$this->cat_id=$r_tv['cat_id'];
			$this->parent_cat_id=$r_tv['parent_cat_id'];
			$this->cat=$r_tv['cat'];
			$this->parent_cat=$r_tv['parent_cat'];
			$this->name=$r_tv['name'];
			$this->vendor=$r_tv['vendor'];
			$this->vendorCode=$r_tv['vendorCode'];
			$this->description=$r_tv['description'];
			$this->description_gen=$r_tv['description_gen'];
			$this->weight=$r_tv['weight'];
			$this->length=$r_tv['length'];
			$this->height=$r_tv['height'];
			$this->width=$r_tv['width'];
			$this->diameter=$r_tv['diameter'];
			$this->mesto_montazha=$r_tv['mesto_montazha'];
			$this->lamp_type=$r_tv['lamp_type'];
			$this->soket=$r_tv['soket'];
			$this->power=$r_tv['power'];
			$this->arm_material=$r_tv['arm_material'];
			$this->col_plafond=$r_tv['col_plafond'];
			$this->form_plafond=$r_tv['form_plafond'];
			$this->warranty=$r_tv['warranty'];
			$this->mat_plafond=$r_tv['mat_plafond'];
			$this->ploshad=$r_tv['ploshad'];
			$this->length_box=$r_tv['length_box'];
			$this->width_box=$r_tv['width_box'];
			$this->height_box=$r_tv['height_box'];
			$this->country_origin=$r_tv['country_origin'];
			$this->country_production=$r_tv['country_production'];
			$this->arm_color=$r_tv['arm_color'];
			$this->collection=$r_tv['collection'];
			$this->forma=$r_tv['forma'];
			$this->lamp_sum=$r_tv['lamp_sum'];
			$this->ip=$r_tv['ip'];
			$this->style=$r_tv['style'];
			$this->interior=$r_tv['interior'];
			$this->status=$r_tv['status'];
			$this->error=$r_tv['error'];
			$this->err_update=$r_tv['err_update'];
			$this->last_ozon_up=$r_tv['last_ozon_up'];
		}
		# обрабатываем некоторые данные под формат озона
		
	   $this->ploshad=str_replace(' м2','',$this->ploshad);	
	   
	   $this->height_box=str_replace(' см.','',$this->height_box);
	   $this->length_box=str_replace(' см.','',$this->length_box); 
	   $this->width_box=str_replace(' см.','',$this->width_box);
	   $this->height_box=str_replace('.',',',$this->height_box);
	   $this->length_box=str_replace('.',',',$this->length_box); 
	   $this->width_box=str_replace('.',',',$this->width_box);
	   $this->height_box=$this->height_box*10;
	   $this->length_box=$this->length_box*10; 
	   $this->width_box=$this->width_box*10;
	   $this->weight=str_replace(' кг.','',$this->weight);
	   $this->weight=$this->weight*1000; //переводим вес в граммы
	   $this->main_picture=str_replace(' ','%20',$this->main_picture);
    
		
	}
	function rec_update_stock($ozon_product_id,$status,$error){ # функция записи в бд результатов попытки обновления стока
		global $modx;
		$today = getdate();
		$today_ts=$today[0];
		$this->today_ts=$today_ts;
		
		$sql="update goods set `status`='$status',`error`='$error',`last_ozon_stock_up`='$today_ts' where `ozon_product_id`=$ozon_product_id";
		echo $sql;
		$modx->query($sql);
	}
}
class rec_bd{
    
    function inc_get_up_page($new_page){
		global $modx;
		$sql="update `orders_ozon` set `update_page`='$new_page' where `id`=1";
		echo $sql;
		$modx->query($sql);
	}
    function get_up_page(){ # получает текущий номер страницы из БД
		global $modx;
        $query = "select `update_page` from orders_ozon where `id`=1";
		$statement_tv = $modx->query($query);
        $result_tv = $statement_tv->fetchAll(PDO::FETCH_ASSOC);
		
        foreach($result_tv as $r_tv){
			$page=$r_tv['update_page'];
		}
		return $page;
	
	}
	function set_last_order_num($last_order_id){
		global $modx;
		$sql="update orders_ozon set `ozon_id`='$last_order_id' where `id`=1";
		echo $sql;
		$modx->query($sql);
	}
	function set_validity($valid,$id){
		global $modx;
		$sql="update goods set `valid`='$valid' where `id`=$id";
		$modx->query($sql);
	}
	function rec_update_good($ozon_product_id,$out){ # функция записи в бд результатов попытки обновления товара
		global $modx;
		$today = getdate();
		$today_ts=$today[0];
		$this->today_ts=$today_ts;
		
		$sql="update goods set `err_update`='$out',`last_ozon_up`='$today_ts' where `ozon_product_id`=$ozon_product_id";
		$modx->query($sql);
	}
	function rec_update_stock($ozon_product_id,$status,$error){ # функция записи в бд результатов попытки обновления стока
		global $modx;
		$today = getdate();
		$today_ts=$today[0];
		$this->today_ts=$today_ts;
		
		$sql="update goods set `status`='$status',`error`='$error',`last_ozon_stock_up`='$today_ts' where `ozon_product_id`=$ozon_product_id";
		echo $sql;
		$modx->query($sql);
	}
	function get_goods_to_push(){ # функция получения артикулов, которые не добавлены в озон и не имеют task_id
		global $modx;
				
		$sql="SELECT * FROM `goods` WHERE `ozon_product_id`=0 and `task_id` = 0";
		echo $sql;
		$statement = $modx->query($sql);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
		$i=0;
		foreach($result as $r){
			$this->vendorCode[$i]=$r['vendorCode'];
			$i++;
		}
	}
	function get_last_order_num(){ # функция получения номера последнего заказа, о котором уведомлялись менеджеры
		global $modx;
		$sql="SELECT `ozon_id` FROM `orders_ozon` WHERE `id` = 1";
		$statement = $modx->query($sql);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
		foreach($result as $r){
			$this->id=$r['ozon_id'];
		}
	}
	function rec_add_good($art,$get_arr){ # функция записи в бд результатов попытки записи товара, на входе массив ответа
		# записываем либо task_id, либо код ошибки
		global $modx;
		$today = getdate();
		$today_ts=$today[0];
		$this->today_ts=$today_ts;
		
		
		$task_id=$get_arr['result']['task_id'];
		$add_error=$get_arr['error']['data'][0]['key'];
		$add_error_value=$get_arr['error']['data'][0]['value'];
		
		$add_error .= ' / '.$add_error_value;
		
		$sql="update goods set `task_id`='$task_id',`add_error`='$add_error',`last_ozon_push`='$today_ts' where `vendorCode`='$art'";
		echo $sql;
		$modx->query($sql);
	}
	function add_ozon_id($ozon_id,$art){ # функция записи в бд id озона
		global $modx;
		$query = "update goods set `ozon_product_id`=$ozon_id where `vendorCode`='$art'";
		echo $query;
        $modx->query($query);
	}
	function stocks_prices_from_teleport($id,$price,$stock,$action_price,$sale){ # записываем остатки и цены из телепорта в БД
	    global $modx;
	    if($sale=='true') {
	      $query = "update goods set `stock`='$stock',`price`='$action_price',`old_price`='$price' where `id`='$id'";  
	    } else {
	      $query = "update goods set `stock`='$stock',`price`='$price',`old_price`='0' where `id`='$id'";  
	    }
		
		echo $query.'<br>';
        $modx->query($query);
	}
	function set_stock_zero_to_brand($vendor){ # устанавливаем остатки, равные нулю для всех товаров бренда
	    global $modx;
		$query = "update goods set `stock`=0 where `vendor`='$vendor'";
		echo $query;
        $modx->query($query);
	}
}
class attr_values{
	
	function plafond_color_value($str){
			$b2=array(
			'Бежевый'=>'1',
			'Хром'=>'24',
			'Разноцветный'=>'39',
			'Белый'=>'2',
			'Медный'=>'38',
			'Прозрачный'=>'16',
			'Серебристый'=>'37',
			'Бронза'=>'5',
			'Коричневый'=>'12',
			'Желтый'=>'7',
			'Черный'=>'35',
			'Матовый'=>'16',
			'Серый'=>'23',
			'Фиолетовый'=>'33',
			'Золотистый'=>'9',
			'Красный'=>'13',
			'Зеленый'=>'8',
			'Оранжевый'=>'15',
			'Матовый никель'=>'31',
			'Голубой'=>'6',
			'Розовый'=>'17',
			'Синий'=>'25',
			'Янтарный'=>'28',
			'Латунь'=>'30',
			'Сиреневый'=>'26',
			'Никель'=>'24'

		);
		return strtr($str, $b2);
	}
	function armature_color_value($str){
			$b2=array(
			'Алюминий'=>'40',
			'Бежевый'=>'3',
			'Белый'=>'1',
			'Бронза'=>'17',
			'Голубой'=>'14',
			'Желтый'=>'8',
			'Зеленый'=>'13',
			'Золотистый'=>'12',
			'Коричневый'=>'5',
			'Красный'=>'9',
			'Латунь'=>'12',
			'Матовый'=>'2',
			'Матовый никель'=>'30',
			'Медный'=>'39',
			'Никель'=>'7',
			'Оранжевый'=>'15',
			'Прозрачный'=>'2',
			'Разноцветный'=>'',
			'Розовый'=>'10',
			'Серебристый'=>'40',
			'Серый'=>'6',
			'Синий'=>'11',
			'Сиреневый'=>'18',
			'Фиолетовый'=>'16',
			'Хром'=>'7',
			'Черный'=>'4'
		);
		return strtr($str, $b2);
	}
	function plafond_material_value($str){
			$b2=array(
			'Акрил'=>'47',
			'Алюминий'=>'52',
			'Бетон'=>'146',
			'Бумага'=>'75',
			'Гипс'=>'146',
			'Дерево'=>'114',
			'Камень'=>'146',
			'Канат'=>'430',
			'Керамика'=>'172',
			'Кожа'=>'179',
			'Металл'=>'235',
			'Органза'=>'285',
			'Пластик'=>'328',
			'Полимер'=>'328',
			'Ракушка'=>'622',
			'Ротанг'=>'367',
			'Сталь'=>'399',
			'Стекло'=>'402',
			'Текстиль'=>'415',
			'Ткань'=>'430',
			'Хрусталь'=>'486'

		);
		return strtr($str, $b2);
	}
	function lamp_socket_collection($str){
			$b2=array(
			'E14'=>'35',
			'E27'=>'36',
			'G10'=>'41',
			'G4'=>'45',
			'G5'=>'46',
			'G5.3'=>'47',
			'G9'=>'48',
			'GU10'=>'49',
			'GU4'=>'51',
			'GU5.3'=>'52',
			'GX5.3'=>'53',
			'GY6.35'=>'56',
			'GZ10'=>'49',
			'LED'=>'84',
			'R50'=>'126',
			'R7S'=>'128'

		);
		return strtr($str, $b2);
	}
	
	function ip_value($str){
			$b2=array(
			'20'=>'3',
            '21'=>'3',
            '22'=>'3',
            '23'=>'3',
            '24'=>'3',
            '25'=>'3',
            '26'=>'3',
            '27'=>'3',
            '28'=>'3',
            '29'=>'3',
            '33'=>'3',
            '40'=>'4',
            '43'=>'4',
            '44'=>'4',
            '53'=>'4',
            '54'=>'4',
            '55'=>'4',
            '63'=>'5',
            '65'=>'5',
            '66'=>'5',
            '67'=>'5',
            '68'=>'5'

		);
		return strtr($str, $b2);
	}
		function lamp_type_value($str){
			$b2=array(
			'Накаливания'=>'6',
            'Галогеновая'=>'2',
            'Светодиодная'=>'7'

		);
		return strtr($str, $b2);
	}
	
}
class mailer{
	function new_order($last_order_id){ # функция уведомления менеджеров о наличии новых заказов
		global $modx;
		$message = $modx->getChunk('tplOrderToManager');
		$subj="Пришел заказ с Озон #$last_order_id";
		
        $modx->getService('mail', 'mail.modPHPMailer');
        $modx->mail->set(modMail::MAIL_BODY,$message);
        $modx->mail->set(modMail::MAIL_FROM,'mail@ftp-technolight.ru');
        $modx->mail->set(modMail::MAIL_FROM_NAME,'ftp-technolight.ru');
        $modx->mail->set(modMail::MAIL_SUBJECT,$subj);
        $modx->mail->address('to','v.kosarev@list.ru');
        $modx->mail->address('to','info@fandeco.ru');
        $modx->mail->address('reply-to','mail@artelamp.it');
        $modx->mail->setHTML(true);
        if (!$modx->mail->send()) {
            $modx->log(modX::LOG_LEVEL_ERROR,'An error occurred while trying to send the email: '.$modx->mail->mailer->ErrorInfo);
        }
        $modx->mail->reset();
		
	}
	function main_mail($message,$subject,$to){ # функция уведомления менеджеров о наличии новых заказов
		global $modx;
		
		$modx->getService('mail', 'mail.modPHPMailer');
        $modx->mail->set(modMail::MAIL_BODY,$message);
        $modx->mail->set(modMail::MAIL_FROM,'mail@ftp-technolight.ru');
        $modx->mail->set(modMail::MAIL_FROM_NAME,'ftp-technolight.ru');
        $modx->mail->set(modMail::MAIL_SUBJECT,$subject);
        $modx->mail->address('to',$to);
        $modx->mail->address('reply-to','mail@artelamp.it');
        $modx->mail->setHTML(true);
        if (!$modx->mail->send()) {
            $modx->log(modX::LOG_LEVEL_ERROR,'An error occurred while trying to send the email: '.$modx->mail->mailer->ErrorInfo);
        }
        $modx->mail->reset();
		
	}
	
}
class useful{
    function objectToArray($object) {
        if( !is_object($object) && !is_array($object)) {
            return $object;
        }
        if( is_object($object )) {
            $object = get_object_vars($object);
        }
        return array_map('objectToArray', $object);
    }
}