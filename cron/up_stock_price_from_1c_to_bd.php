<?php
ini_set('max_execution_time', 800); //300 seconds = 5 minutes
ini_set('memory_limit', '600000000');



# Подключаем классы и создаем объекты для их использования

//$modx->runSnippet('classes');

//$my_snip = new useful;
//$record = new rec_bd;

echo "<pre>";

# выдернули все товары с сайта? На выходе $vendorCode_bd
    
$query = "select id,vendorCode from goods";
$statement_tv = $modx->query($query);
$result_tv = $statement_tv->fetchAll(PDO::FETCH_ASSOC);
$i=1;
foreach($result_tv as $r_tv){
        $vendorCode_bd[$i]= $r_tv['vendorCode'];
        $id_bd[$i]= $r_tv['id'];
        $i++;
}

# выдергиваем файлы с 1С

$https_user='webuser';
$https_password='web@user';
$url='http://msk1.technolight.ru:7780/br/hs/fandeco/stock_update';

$json = '{}';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json, charset=UTF-8"));
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_USERPWD, "webuser:web@user");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

$result = curl_exec($ch);
curl_close($ch);

$arr_1c_goods=json_decode($result, true);


# Сбрасываем все остатки в базе на 0 с производителями Arte Lamp

$vendor="Arte Lamp";
//$record->set_stock_zero_to_brand($vendor);

echo "<pre>";

$arr_arte_msk = array();
# пробегаем по 
$i=1;
foreach ($arr_1c_goods as $good){
  
    if($good['region']=='Москва' && $good['vendor']=='ARTELAMP'){
        
        $arr_arte_msk[$i]['article'] = $good['article'];
        $arr_arte_msk[$i]['stock'] = $good['stock'];
        $i++;
    }
    
}


print_r($arr_arte_msk);
print_r($arr_1c_goods);

 
function objectToArray($object) {
        if( !is_object($object) && !is_array($object)) {
            return $object;
        }
        if( is_object($object )) {
            $object = get_object_vars($object);
        }
        return array_map('objectToArray', $object);
}
function vendor_code($str){
			$b2=array(
			'album'=>'Album',
            'ambiente_by_brizzi'=>'AMBIENTE by Brizzi',
            'anthologie_quartett'=>'Anthologie Quartett',
            'ares'=>'Ares',
            'arlight'=>'Arlight',
            'aromas'=>'Aromas',
            'arte_lamp'=>'ArteLamp',
            'artemide'=>'Artemide',
            'arti_lampadari'=>'Arti Lampadari',
            'avmazzega'=>'AVmazzega',
            'avonni'=>'Avonni',
            'axo_light'=>'AXO Light',
            'baga'=>'Baga',
            'beby'=>'Beby',
            'bieffe'=>'Bieffe',
            'b_lux'=>'B.Lux',
            'bohemia_ivele'=>'Bohemia Ivele',
            'britop'=>'Britop',
            'brizzi_modern'=>'Brizzi Modern',
            'catellani_smith'=>'Catellani&Smith',
            'ceelite'=>'Ceelite',
            'ceramiche_carlesso'=>'Ceramiche Carlesso',
            'chiaro'=>'Chiaro',
            'citilux'=>'Citilux',
            'crystal_lux'=>'Crystal Lux',
            'dark'=>'Dark',
            'delta_light'=>'Delta Light',
            'dio_d_arte'=>'Dio D\'Arte',
            'divinare'=>'Divinare',
            'eglo'=>'Eglo',
            'egoluce'=>'Egoluce',
            'elvan'=>'Elvan',
            'esedra'=>'Esedra',
            'eurolampart'=>'Eurolampart',
            'fabbian'=>'Fabbian',
            'fagerhult'=>'Fagerhult',
            'faldi'=>'Faldi',
            'faustig'=>'Faustig',
            'favourite'=>'Favourite',
            'florian_light'=>'FlorianLight',
            'flos'=>'Flos',
            'fontana_arte'=>'Fontana Arte',
            'foscarini'=>'Foscarini',
            'gamma_delta'=>'Gamma Delta Group',
            'gauss'=>'Gauss',
            'globo_new'=>'Globo',
            'globo'=>'Globo Акция',
            'ideal_lux'=>'Ideal Lux',
            'idee_design'=>'Idee Design Light',
            'idl'=>'IDL',
            'idlamp'=>'IDLamp',
            'iguzzini'=>'Iguzzini',
            'il_fanale'=>'Il Fanale',
            'ilfari'=>'Ilfari',
            'imas'=>'Imas',
            'ingo_maurer'=>'Ingo Maurer',
            'intra_lighting'=>'Intra Lighting',
            'itre'=>'Itre',
            'ivalo'=>'Ivalo',
            'karman'=>'Karman',
            'kolarz'=>'Kolarz',
            'lamp_international'=>'Lamp International',
            'leds'=>'Leds',
            'leucos'=>'Leucos',
            'lightstar'=>'Lightstar',
            'linea_light'=>'Linea Light',
            'luceplan'=>'LucePlan',
            'lucia_tucci'=>'Lucia Tucci',
            'lucide'=>'Lucide',
            'lumen_center'=>'Lumen Center Italia',
            'lussole'=>'Lussole',
            'luxit'=>'Luxit',
            'luxona'=>'Luxona',
            'mantra'=>'Mantra',
            'marchetti'=>'Marchetti',
            'mar_illuminazione'=>'Mar Illuminazione',
            'markslojd'=>'Markslojd',
            'marset'=>'Marset',
            'martini'=>'Martini',
            'masca'=>'Masca',
            'masiero'=>'Masiero',
            'maytoni'=>'Maytoni',
            'milan_iluminacion'=>'Milan Iluminacion',
            'lampadari'=>'M.M.Lampadari',
            'modular'=>'Modular',
            'molto_luce'=>'Molto Luce',
            'moooi'=>'Moooi',
            'morosini'=>'Morosini',
            'mw_light'=>'Mw-Light',
            'neon_night'=>'Neon-Night',
            'nervilamp'=>'Nervilamp',
            'novotech'=>'Novotech',
            'nowodvorski'=>'Nowodvorski',
            'odeon_light'=>'Odeon Light',
            'ole'=>'OLE',
            'oligo'=>'Oligo',
            'orion'=>'Orion',
            'osgona'=>'Osgona',
            'pallucco_italia'=>'Pallucco Italia',
            'panzeri'=>'Panzeri',
            'passeri'=>'Passeri',
            'penta'=>'Penta',
            'prearo'=>'Prearo',
            'prisma'=>'Prisma',
            'riperlamp'=>'Riperlamp',
            'rotaliana'=>'Rotaliana',
            'royal_botania'=>'Royal Botania',
            'sil_lux'=>'Sil Lux',
            'slamp'=>'Slamp',
            'slv'=>'SLV',
            'sonex'=>'Sonex',
            'spot_light'=>'Spotlight',
            'stil_lux'=>'STIL LUX',
            'st_luce'=>'ST Luce',
            'studio_italia'=>'Studio Italia Design',
            'sylcom'=>'Sylcom',
            'sylvania'=>'Sylvania',
            'terzani'=>'Terzani',
            'tobias_grau'=>'Tobias Grau',
            'toplight'=>'Toplight',
            'traddel'=>'Traddel',
            'vele_luce'=>'Vele Luce',
            'vibia'=>'Vibia',
            'vintage'=>'Vintage',
            'vistosi'=>'Vistosi',
            'wever_ducre'=>'Wever&Ducre',
            'avrora'=>'Аврора',

		);
		return strtr($str, $b2);
	}