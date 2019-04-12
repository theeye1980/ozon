<?php
define('MODX_API_MODE', true);
require_once('/var/www/www-root/data/mail.ftp-technolight.ru/index.php');
require_once('/var/www/www-root/data/mail.ftp-technolight.ru/good_bd.php');

$modx=new modX();
$modx->initialize('web');
/*  

	Скрипт обновления товаров БД из выгрузки fandeco.ru

*/
$mark=3;

ini_set('max_execution_time', 1200); //300 seconds = 5 minutes
ini_set('memory_limit', '1400000000');

echo "<pre>";
# 1. выдернули все товары с БЮ? На выходе $vendorCode_bd
    $query = "select vendorCode,ozon_product_id,stock from goods where 1";
    $statement_tv = $modx->query($query);
    $result_tv = $statement_tv->fetchAll(PDO::FETCH_ASSOC);
    $i=1;
    foreach($result_tv as $r_tv){
        $vendorCode_bd[$i]= $r_tv['vendorCode'];
        $ozon_product_id_bd[$i]= $r_tv['ozon_product_id'];
        $stock[$i]= $r_tv['stock'];
        $i++;
    }
    

# формируем цикл прохода по всем страницам 
    $m=1;
    $i=1;
    $json_items='';
        foreach($ozon_product_id_bd as $product_id){
            if($product_id){
                // $product_id - номер товара в озоне
                $key=array_search($product_id,$ozon_product_id_bd);
                
                
                # получаем актуальные остатки по артикулу
                
                $good_item = new good_bd;
                $good_item->get_art_by_product_id($product_id);
                
                # формируем json для обновления остатков
                
                # устанавливаем набор json для обновления остатков
                
                if(! $good_item->stock) $good_item->stock=0;
                $json_items .= $modx->getChunk('stock_item',array(
                                                            'product_id' => $product_id,
                                                            'offer_id' => $good_item->vendorCode,
                                                            'stock' => $good_item->stock
                ));
                
                //Как набирается сотня товаров - отправляем запрос в 
                if($i>100){
                    # вырезаем последнюю запятую в $json_items
        
                    $json_items = substr($json_items,0,-1);
                    $json_set = $modx->getChunk('stock_json',array('stock_items' => $json_items
                                                                    
                        ));
                    //echo $json_set;
                    
                    # обновляем остатки на товары
                    $url='/v1/products/stocks';   
                    $json = $json_set;
                    
                    echo $json.'<br><br>';
                    
                    $set_stock=$modx->runSnippet('curl_ozon',array(
                                                        'url'=>$url,
                                                        'json'=>$json
                                                ));
                    $set_stock_arr=json_decode($set_stock, true);
                    $error=$arr_info['result'][0]['errors'][0];
                    
                    print_r ($set_stock_arr);
    
                    $json_items='';
                    $m++;
                    $i=0;
                }
                
                //if($m>3) break;
                $i++;
            }
        }
# записываем запись, что обновление произведено
	$today = getdate();
	$today_ts=$today[0];
	//$this->today_ts=$today_ts;
	$query = "update `orders_ozon` set `stocks_up`='$today_ts' where `id`='1'";
	echo $query;
    $modx->query($query);