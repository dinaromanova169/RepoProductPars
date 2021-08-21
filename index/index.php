 if($arProviders[$arItem['UF_PROVIDER']]['UF_OPTOMOLL_ID']){
        $section = 2; 
    }elseif($arProviders[$arItem['UF_PROVIDER']]['UF_SECTIONS_DEFAULT']){
        $section = $arProviders[$arItem['UF_PROVIDER']]['UF_SECTIONS_DEFAULT'];
    }else{
        $section = 31;
    }

    $arLoadProductArray = Array(
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => 1,
        "NAME" => 'Арт.',
        'IBLOCK_SECTION' => $section,
        "PROPERTY_VALUES"=> $PROP,
        "DETAIL_PICTURE" => CFile::MakeFileArray($image),
        "PREVIEW_TEXT" => preg_replace('/http\S*($|\s)/', '', trim($textNew)),
        "PREVIEW_TEXT_TYPE" => 'html'
        // 'CREATED_BY' => 1
    );
    // Если у поставщика стоит отметка только упаковками и скрипт не определил кол-во
    // в упаковке, то товар не активируем
    if($arProviders[$arItem['UF_PROVIDER']]['UF_TYPE_OF_SALE'] == 'packaging' && !$upakNum){
        $arLoadProductArray['ACTIVE'] = 'N';
    }
    
    // continue;
    if($PRODUCT_ID = $el->Add($arLoadProductArray, false, false, true)){
        // echo $PRODUCT_ID;

        // добавляем товар
        $arFields = array(
            "ID" => $PRODUCT_ID, 
            'TYPE' => 1,
                // "QUANTITY" => $arrEl['CATALOG_QUANTITY']
        );
        CCatalogProduct::Add($arFields, false);
        // цены
        $p = CPrice::Add(
            [
                'PRODUCT_ID' => $PRODUCT_ID,
                'CURRENCY' => 'RUB',
                'PRICE' =>  $price,
                'CATALOG_GROUP_ID' => 1
            ],
            true
        );
        CPrice::Add(
            [
                'PRODUCT_ID' => $PRODUCT_ID,
                'EXTRA_ID' => 454, //0%
                'CURRENCY' => 'RUB',
                'CATALOG_GROUP_ID' => 2
            ],
            true
        );

        $strEntityDataClass2::update($arItem['ID'], ['UF_ID_PRODUCT' => $PRODUCT_ID]);
        ++$z;
    } else{
        echo "Error: ".$el->LAST_ERROR;
        $strEntityDataClass2::update($arItem['ID'], ['UF_COMMENT' => $el->LAST_ERROR]);
    }
    echo ++$i."\n";
    // break;
}
