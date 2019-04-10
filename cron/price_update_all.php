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

echo "<pre>";
# 1. выдернули все товары с БЮ? На выходе $vendorCode_bd
    $query = "select vendorCode,ozon_product_id,stock,price,old_price from goods where 1";
    $statement_tv = $modx->query($query);
    $result_tv = $statement_tv->fetchAll(PDO::FETCH_ASSOC);
    $i=1;
    foreach($result_tv as $r_tv){
        $vendorCode_bd[$i]= $r_tv['vendorCode'];
        $ozon_product_id_bd[$i]= $r_tv['ozon_product_id'];
        $price[$i]= $r_tv['price'];
        $old_price[$i]= $r_tv['old_price'];
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

                # формируем json для обновления остатков
                # устанавливаем набор json для обновления остатков
                
                //if(! $good_item->old_price) $good_item->old_price=0;
                //if(! $good_item->price) $good_item->price=0;
                
                $json_items .= $modx->getChunk('price_item',array(
                                                            'product_id' => $product_id,
                                                            'price' => $price[$key],
                                                            'old_price' => $old_price[$key]
                ));
                
                //Как набирается сотня товаров - отправляем запрос в 
                if($i>100){
                    # вырезаем последнюю запятую в $json_items
        
                    $json_items = substr($json_items,0,-1);
                    $json_set = $modx->getChunk('price_json',array('price_items' => $json_items
                                                                    
                        ));
                    //echo $json_set;
                    
                    # обновляем остатки на товары
                    $url='/v1/products/prices';   
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
	$query = "update `orders_ozon` set `prices_up`='$today_ts' where `id`='1'";
	echo $query;
    $modx->query($query);
	
?>