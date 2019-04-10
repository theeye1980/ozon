<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/good_bd.php';
ini_set('max_execution_time', 3600); //300 seconds = 5 minutes
ini_set('memory_limit', '2000000000');


# получаем входные данные - сколько товаров выводить и сколько пропустить

$page=$_GET['page'];
$page_size=$_GET['page_size'];

if(! isset($page)) $page=1;
if(! isset($page_size)) $page_size=3;

# создаем объект для записи данных в базу

$record = new rec_bd;

# формируем запрос на получение списка товаров, результат в массив $get_arr

$out='';
$url='/v1/product/list';

$json = $modx->getChunk('list_page_size',array('page'=>$page,'page_size'=>$page_size));

$get_ex=$modx->runSnippet('curl_ozon',array('url'=>$url,
                                            'json'=>$json,
                                            'clientId'=>$clientId,
                                            'ApiKey'=>$ApiKey
                        ));
$get_arr=json_decode($get_ex, true);

# Пробегаем по каждому товару в полученном массиве

foreach($get_arr['result']['items'] as $good){

    # Получить по товару из локальной таблицы
    
    $good_item = new good_bd;
    $good_item->get_good_by_art ($good['offer_id']);
    
    if($good_item->ozon_product_id == 0) {
        echo "<br> по товару" . $good['offer_id'] . "нет записи ozon_product_id <br>"; 
        # записываем в БД ozon_product_id
        $record->add_ozon_id($good['product_id'],$good['offer_id']);
    }
    
    echo $good['product_id'].'--'.$good['offer_id'].'<br>';
    $product_id=$good['product_id'];
    
    # Устанавливаем товару характеристики в Озоне
    
    $out = $modx->runSnippet('update_good_json',array('offer_id'=> $good['product_id'] ));
   
    echo $out.'<br><hr>';
      
    # Записываем в БД  этот ответ
        
    $record->rec_update_good($good['product_id'],$out);
    
}