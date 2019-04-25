<?php
define('MODX_API_MODE', true);
require_once('/var/www/www-root/data/mail.ftp-technolight.ru/index.php');
$modx=new modX();
$modx->initialize('web');


$modx->runSnippet('classes');

ini_set('max_execution_time', 3600); //300 seconds = 5 minutes
ini_set('memory_limit', '2000000000');


# создаем объекты для работы с БД и почтой
$record = new rec_bd;
$mail_sender = new mailer;

# Выдергиваем номер последнего заказа, по которому уведомляли менеджеров

$record->get_last_order_num();

$url='/v1/order/list';

/*
$json = '{
  "since": "2019-01-01T14:39:46.033Z",
  "to": "2019-04-09T14:39:46.033Z",
  "delivery_schema": "fbs"
}';

*/

$since="2019-04-24T14:39:46.033Z";
$to = date("c");

$json=$modx->getChunk('order_list_json',array(
                        'since_date'=>$since,
                        'to_date'=>$to                   
                    ));

echo "<br> $url <br> $json";

$get_ex=$modx->runSnippet('curl_ozon',array('url'=>$url,
                                            'json'=>$json
                        ));
echo     "Вот-  ".$get_ex->result;                    
                        
$get_arr=json_decode($get_ex, true);
echo "<hr>";

$orders=$get_arr['result']['order_ids'];
$num_orders=count($orders);

$last_order_id=$orders[$num_orders-1];

echo "Всего заказов - $num_orders, последний $last_order_id, уведомляли -". $record->id ."<br><br>";

if($last_order_id-$record->id>0){
    
    # шлем письма
    
    $mail_sender->new_order($last_order_id);
    
    # записываем новый $last_order_id
    
    $record->set_last_order_num($last_order_id);
    
}
print_r($orders);