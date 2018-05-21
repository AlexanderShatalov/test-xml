<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
require(__DIR__ . '/classExport.php');
?>
<?
# фильтр для категорий
$arFilterSection = Array(
	'SECTION_ID' => 1166,
	'GLOBAL_ACTIVE'=>'Y'
);

# фильтр для товаров
$arFilterElement = Array(
	'SECTION_ID' => 1166,
	'INCLUDE_SUBSECTIONS' => 'Y'
);
# фильтр для товаров
$category_list = CIBlockSection::GetList(Array('SORT' => 'ASC'), $arFilterSection, true);
$element_list = CIBlockElement::GetList(Array('SORT' => 'ASC'), $arFilterElement);

# формирование файла экспорта
# добавление валюты руками для примера - можно так же получить с сайта все существующие
$yml = new yml();
$yml->set_shop("name", "company");
$yml->add_currency("RUB");

# добавление категорий
while($resultCat = $category_list->Fetch()){  
    $yml->add_category($resultCat['SEARCHABLE_CONTENT'], $resultCat['ID'], $resultCat['IBLOCK_SECTION_ID']);
}

# добавление товаров
while ($resultEl = $element_list->Fetch()) {
	$delivery = true;
	$forPrice = CPrice::GetBasePrice($resultEl['ID']);
	$data = array(
        'url' => $resultEl['DETAIL_PAGE_URL'],              //url товара
        'price' => $forPrice['PRICE'],                      //цена товара
        'currencyID' => $forPrice['CURRENCY'],              //идентификатор валюты товара
        'categoryId' => $resultEl['IBLOCK_SECTION_ID'],     //идентификатор категории товара
		'picture' => $resultEl['PREVIEW_PICTURE'],          //изображение товара
		'delivery' => $delivery,                            //возможность доставки
		'name' => $resultEl['NAME'],                        //наименование товара
        'vendor' => $resultEl['DETAIL_PAGE_URL'],           //производитель
        'vendorCode' => $resultEl['DETAIL_PAGE_URL'],       //код производителя
		'description' => $resultEl['SEARCHABLE_CONTENT'],   //описание товара
		'country_of_origin' => $resultEl['DETAIL_PAGE_URL'],//страна предназначения
		'downloadable' => $resultEl['DETAIL_PAGE_URL'],     //возможность скачать	
    );
	$yml->add_offer($resultEl['ID'], $data);
}

$writeData = $yml->get_xml();
if(file_put_contents("test.xml", $writeData)){
	echo 'file created';
}
else {
	throw new Exception('Ошибка создания фала выгрузки');
}

?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>