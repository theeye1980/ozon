<?php
define('MODX_API_MODE', true);
require_once('/var/www/www-root/data/mail.ftp-technolight.ru/index.php');
$modx=new modX();
$modx->initialize('web');

$modx->runSnippet('classes');

/*  
	Скрипт обновления товаров БД из выгрузки fandeco.ru
*/

$mark=4;

ini_set('max_execution_time', 2400); //300 seconds = 5 minutes
ini_set('memory_limit', '1200000000');

// временно очищаем все нах товары
//$modx->query("TRUNCATE TABLE `goods`");

# 1. выдернули все товары с сайта? На выходе $vendorCode_bd
    
    $query = "select id,vendorCode from goods";
    $statement_tv = $modx->query($query);
    $result_tv = $statement_tv->fetchAll(PDO::FETCH_ASSOC);

    $i=1;
    foreach($result_tv as $r_tv){
        $vendorCode_bd[$i]= $r_tv['vendorCode'];
        $id_bd[$i]= $r_tv['id'];
        $i++;
    }

# 2. выдернули все товары с хмл
    $xml_array= simplexml_load_file('https://fandeco.ru/media/xml_export/for_QXPlus_all.xml'); // грузим файлик
	$categories =  $xml_array->shop->categories;
	$arr_categories=objectToArray($categories);
	
	$arr_categories=$arr_categories['category'];
	
	$i=0;
	foreach ($categories->category as $item) {
	    $category_id[$i]=$item['id'];
	    $i++;
	}
	$category_id = json_decode(json_encode($category_id), true);
	
	
	# преобразовываем id категорий в одномерный массив
	$i=0;
	foreach($category_id as $c){
	    $arr_category_id[$i]=$c[0];
	   $i++; 
	}

	


	
	$offer =  $xml_array->shop->offers->offer;
	$i=1;
    foreach ($xml_array->shop->offers->offer as $item) {
        $xml_di[$i]=$item['id'];
        $barcode[$i]=$item->barcode;
        $pictures[$i]=$item->picture;
        
        $main_picture[$i]=$item->picture[0];;
        $dop_picture[$i]= json_encode($pictures[$i]);
        
        $stock[$i]=$item->stock;
        $price[$i]=$item->price;
        $old_price[$i]=$item->oldprice;
        $cat_id[$i]=$item->categoryId;
        $parent_cat_id[$i]='';
        
        # Определяем категорию по ID
        $index_cat=array_search($cat_id[$i],$arr_category_id);
        $cat[$i]=$arr_categories[$index_cat];

        $parent_cat[$i]='';
        $name[$i]=$item->name;
        $vendor[$i]=$item->vendor;
        $vendorCode[$i]=$item->vendorCode;
        $description[$i]=$item->description;
  
  # обрабатываем параметры        
       $params[$i]=$item->param;
        foreach ($params[$i] as $param) {
            switch((string) $param['name']) { 
            case 'Вес':
                $weight[$i]=$param;
            break;
            case 'Длина':
                $length[$i]=$param;
            break;
            case 'Высота':
                $height[$i]=$param;
            break;
            case 'Ширина':
                $width[$i]=$param;
            break;
            case 'Диаметр':
                $diameter[$i]=$param;
            break;
            case 'Место монтажа':
                $mesto_montazha[$i]=$param;
            break;
            case 'Тип лампы':
                $lamp_type[$i]=$param;
            break;
            case 'Патрон':
                $soket[$i]=$param;
            break;
            case 'Мощность':
                $power[$i]=$param;
                $power[$i]=str_replace(' W','',$power[$i]);
            break;
            case 'Материал арматуры':
                $arm_material[$i]=$param;
            break;
            case 'Цвет плафона':
                $col_plafond[$i]=$param;
            break;
            case 'Форма плафона':
                $form_plafond[$i]=$param;
            break;
            case 'Гарантия':
                $warranty[$i]=$param;
            break;
            case 'Материал плафона / декора':
                $mat_plafond[$i]=$param;
            break;
            case 'Площадь освещения':
                $ploshad[$i]=$param;
            break;
            case 'Длина коробки':
                $length_box[$i]=$param;
            break;
            case 'Ширина коробки':
                $width_box[$i]=$param;
            break;
            case 'Высота коробки':
                $height_box[$i]=$param;
            break;
            case 'Страна происхождения бренда':
                $country_origin[$i]=$param;
            break;
            case 'Страна':
                $country_production[$i]='';
            break;
            case 'Цвет арматуры':
                $arm_color[$i]=$param;
            break;
            case 'Колекция':
                $collection[$i]=$param;
            break;
            case 'Форма':
                $forma[$i]=$param;
            break;
            case 'Количество ламп':
                $lamp_sum[$i]=$param;
                $lamp_sum[$i]=str_replace(' шт.','',$lamp_sum[$i]);
            break;
            case 'IP':
                $ip[$i]=$param;
            break;
            case 'Стиль Светильника':
                $style[$i]=$param;
            break;
            case 'Интерьер по комнате':
                $interior[$i]=$param;
            break;
            }
        }
        $i++; 
        
    }
    $xml_goods=$i-1;
    
    print_r($xml_goods);

    
   // $xml_goods=10000; //Временно
# Проверка корректного обновления, если     $xml_goods > 100, то все корректно и тогда сбрасываем остаток всех товаров в ноль для новой установки, если нет, уведомляем об ошбике

if($xml_goods>100){
    $query = "update goods set `stock`='0',`mark`='$mark' where 1";
    $modx->query($query);
} else {
     $mail_sender = new mailer;  
     $mail_sender->main_mail("Надо проверить","Не удалось загрузить файл данных","v.kosarev@list.ru");
     break;
}

    
# Пробегаем по всем артикулам xml, если такого товара в БД нет - добавляем, если есть - обновляем price, old_price, stock
    for($j=1;$j<$xml_goods;$j++){
        $index=array_search($vendorCode[$j],$vendorCode_bd);
        if($index) {
           //обновляем цену и остатки 
           echo "$cat_id[$j] - $id_bd[$index] -   $cat[$j] - $vendorCode[$j] <br>";
		   update_good($id_bd[$index],$cat[$j],$mark,$vendorCode[$j],$stock[$j],$price[$j],$old_price[$j],$barcode[$j],$length_box[$j],$width_box[$j],$height_box[$j],$weight[$j],$length[$j],$height[$j],$width[$j],$diameter[$j],$dop_picture[$j]);   
        } else {
           //добавляем новый товар
           insert_good($barcode[$j],$xml_di[$j],$main_picture[$j],$dop_picture[$j],$stock[$j],$price[$j],$old_price[$j],$cat_id[$j],$parent_cat_id[$j],$cat[$j],$parent_cat[$j],$name[$j],$vendor[$j],$vendorCode[$j],$description[$j],$weight[$j],$length[$j],$height[$j],$width[$j],$diameter[$j],$mesto_montazha[$j],$lamp_type[$j],$soket[$j],$power[$j],$arm_material[$j],$col_plafond[$j],$form_plafond[$j],$warranty[$j],$mat_plafond[$j],$ploshad[$j],$length_box[$j],$width_box[$j],$height_box[$j],$country_origin[$j],$country_production[$j],$arm_color[$j],$collection[$j],$forma[$j],$lamp_sum[$j],$ip[$j],$style[$j],$interior[$j]);
   
        }
    }
# записываем запись, что обновление произведено
	$today = getdate();
	$today_ts=$today[0];
	//$this->today_ts=$today_ts;
	$query = "update `orders_ozon` set `goods_xml_up`='$today_ts' where `id`='1'";
	echo $query;
    $modx->query($query);

  
function   update_good($id,$cat,$mark,$vendorCode,$stock,$price,$old_price,$barcode,$length_box,$width_box,$height_box,$weight,$length,$height,$width,$diameter,$dop_picture){
    global $modx;
    $query = "update goods set `cat`='$cat',`stock`='$stock',`mark`='$mark', `price`='$price',`old_price`='$old_price',`barcode`='$barcode',`length_box`='$length_box',`width_box`='$width_box',`height_box`='$height_box',`weight`='$weight',`length`='$length',`height`='$height',`width`='$width',`diameter`='$diameter',`dop_picture`='$dop_picture' where `id`='$id'";
    echo "$query <br><br>";
        $modx->query($query);
    
}
function   update_stock($mark,$vendorCode,$stock){
    global $modx;
    $query = "update goods set `stock`='$stock',`mark`='$mark' where `vendorCode`='$vendorCode'";
    //echo "$query <br><br>";
        $modx->query($query);
    
} 
function insert_good($barcode,$xml_di,$main_picture,$dop_picture,$stock,$price,$old_price,$cat_id,$parent_cat_id,$cat,$parent_cat,$name,$vendor,$vendorCode,$description,$weight,$length,$height,$width,$diameter,$mesto_montazha,$lamp_type,$soket,$power,$arm_material,$col_plafond,$form_plafond,$warranty,$mat_plafond,$ploshad,$length_box,$width_box,$height_box,$country_origin,$country_production,$arm_color,$collection,$forma,$lamp_sum,$ip,$style,$interior) { 
    global $modx;
    
        $query = "INSERT INTO goods (
                        barcode,
                        xml_di,
                        main_picture,
                        dop_picture,
                        stock,
                        price,
                        old_price,
                        cat_id,
                        parent_cat_id,
                        cat,
                        parent_cat,
                        name,
                        vendor,
                        vendorCode,
                        description,
                        weight,
                        length,
                        height,
                        width,
                        diameter,
                        mesto_montazha,
                        lamp_type,
                        soket,
                        power,
                        arm_material,
                        col_plafond,
                        form_plafond,
                        warranty,
                        mat_plafond,
                        ploshad,
                        length_box,
                        width_box,
                        height_box,
                        country_origin,
                        country_production,
                        arm_color,
                        collection,
                        forma,
                        lamp_sum,
                        ip,
                        style,
                        interior
                    ) 
                  VALUES (
                        '$barcode',
                        '$xml_di',
                        '$main_picture',
                        '$dop_picture',
                        '$stock',
                        '$price',
                        '$old_price',
                        '$cat_id',
                        '$parent_cat_id',
                        '$cat',
                        '$parent_cat',
                        '$name',
                        '$vendor',
                        '$vendorCode',
                        '$description',
                        '$weight',
                        '$length',
                        '$height',
                        '$width',
                        '$diameter',
                        '$mesto_montazha',
                        '$lamp_type',
                        '$soket',
                        '$power',
                        '$arm_material',
                        '$col_plafond',
                        '$form_plafond',
                        '$warranty',
                        '$mat_plafond',
                        '$ploshad',
                        '$length_box',
                        '$width_box',
                        '$height_box',
                        '$country_origin',
                        '$country_production',
                        '$arm_color',
                        '$collection',
                        '$forma',
                        '$lamp_sum',
                        '$ip',
                        '$style',
                        '$interior'
                     )";
		//echo "$query <br><br>";
        $modx->query($query);
      
    
}
function objectToArray($object) {
    if( !is_object($object) && !is_array($object)) {
        return $object;
    }
    if( is_object($object )) {
        $object = get_object_vars($object);
    }
    return array_map('objectToArray', $object);
}