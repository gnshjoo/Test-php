<?php
require_once "fastcat_api.php";

//실질적인 검색기능을 구현해 놓은 함수, 모든 검색 옵션을 여기에서 설정한다.
function searching(
        $collection,
        $fastcat,
        $query,
        $keyword,
        $startItem,
        $lengthItem) {
    $searchField = array("UIPRODUCTINDEX");
    $startTag="<strong>";
    $endTag="</strong>";
	// 정확도 기준 내림차순 정렬
	$query->addRankingEntry("_score");

    //검색필드를 이용해 검색 식 구성 ( 검색어 매칭 점수 : 10점)
    $query->andSearchEntry($searchField, $keyword, SearchQueryStringer::KEYWORD_AND,10);
    // SearchQueryStringer 를 이용해 검색식 구성
    $query->setCollection($collection)
        ->setFieldList("ID,BUNDLEKEY,PRODUCTCODE,SHOPCODE,SHOPPRODUCTCODE,PRODUCTNAME,PRODUCTMAKER,MAKERKEYWORD,PRODUCTBRAND,BRANDKEYWORD,PRODUCTMODEL,MODELWEIGHT,PRODUCTIMAGEURL,LOWESTPRICE,PCPRICE,MOBILEPRICE,AVERAGEPRICE,SHOPQUANTITY,DISCONTINUED,CATEGORYCODE1,CATEGORYCODE2,CATEGORYCODE3,CATEGORYCODE4,CATEGORYNAME,CATEGORYKEYWORD,CATEGORYWEIGHT,REGISTERDATE,MANUFACTUREDATE,POPULARITYSCORE,TOTALPRICE,MANAGERKEYWORD,PRODUCTCLASSIFICATION,PROMOTIONPRICE,BUNDLENAME,BUNDLEDISPLAYSEQUENCE,PRICECOMPARESERVICEYN,PRODTYPE,SIMPLEDESCRIPTION,ADDDESCRIPTION,CMDESCRIPTION,MODIFYDATE,MAKERCODE,BRANDCODE,MOVIEYN,PRICELOCKYN,STVIEWBIT,NATTRIBUTEVALUESEQ,SIMPLEDICTIONARYCODE,SWFDISPLAYYN,GIFDISPLAYYN,SWFFILEYN,GIFFILEYN,OPTIONTYPE,SAVEPLUSQ,TOTALCAPACITY,STANDARDCAPACITY,UNIT,OPTIONNAME,SELECTYN,BRPS,PRICETYPE,INITIALPRICE,DISCOUNTRATE,DISPYN,MOBILEDESCRIPTION,WRITECNT,ACTIONTAG,PRODUCTREGISTERTYPE,PRICECOMPARISONSTOPYN,CATEGORYDISPYN,BUNDLESELECTYN")
        ->setUserDataKeyword($keyword)
        ->setLength($startItem,$lengthItem)
        ->setHighlight($startTag, $endTag);
    // FastcatCommunicator 를 이용해 검색엔진과 통신
    $jsonStr = $fastcat->communicate("/service/search.json",$query->getQueryString(),"");
    // json_decode 를 이용해 받아온 검색결과 파싱
    return json_decode($jsonStr,true);
}
