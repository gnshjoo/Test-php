<?php
/*  2015-06-29 전제현  */

/* 패스트캣 검색엔진용 함수들, 패스트캣 API는 해당 파일에서 별도로 require */
include_once("class/common.php");

$searchUrl = "http://cloud.gncloud.io:22678";
$collection = "VM";
$page = 1;
$keyword = "";

if ($_GET['keyword']) { /* 검색 여부를 체크하여 검색을 했을 시 POST 값을 별개의 변수에 저장함 */

	$rowsOfScreen = 10;	/* 한 화면에 표현 가능한 줄의 갯수 */
	$navOfScreen = 5;	/* 한 화면에 표현 가능한 내비게이터의 갯수 */

	$keyword = $_GET['keyword'];
	if ($_GET['prev']) {
		$prevKeyword = $_GET['prev'];
	}
	$page = number_format($_GET['page']);
	$startItem = (($page-1) * $rowsOfScreen) + 1;
	$lengthItem = $rowsOfScreen;

    /* SearchQueryStringer 클래스 초기화 (검색할 컬렉션 설정) */
    $query = new SearchQueryStringer($collection);
    /* FastcatCommunicator 클래스 초기화 (검색엔진 통신정보) */
    $fastcat = new FastcatCommunicator($searchUrl);

    //검색엔진과 통신 (검색)
    $result = searching($collection, $fastcat, $query, $keyword, $startItem, $lengthItem);

    /* PageNavigator 클래스 초기화 (페이징 기능 지원) */
    $pagingOption = new PageNavigator($rowsOfScreen, $navOfScreen);
    //총갯수 입력
    $pagingOption->setTotal($result["total_count"]);
    /* 초기화 끝 */
    }
?>

<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Fastcat PHP 검색페이지</title>
<!-- Favicon -->
<link rel="shortcut icon" href="img/fastcat-favicon.ico">
<!-- 부트스트랩 CSS -->
<link href="common/bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="common/bootstrap/css/template_style.css" rel="stylesheet">
<!-- IE8 에서 HTML5 요소와 미디어 쿼리를 위한 HTML5 shim 와 Respond.js -->
<!-- WARNING: Respond.js 는 당신이 file:// 을 통해 페이지를 볼 때는 동작하지 않습니다. -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
<!-- jQuery (부트스트랩의 자바스크립트 플러그인을 위해 필요합니다) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.js"></script>
<!-- 모든 컴파일된 플러그인을 포함합니다 (아래), 원하지 않는다면 필요한 각각의 파일을 포함하세요 -->
<script src="common/bootstrap/js/bootstrap.js"></script>

<script type="text/javascript">
/* 페이지 링크 */
function goPage(page) {
    $("form[name='fastcat_search']").find("input[name='page']").val(page);
	$("form[name='fastcat_search']").submit();
}
</script>

</head>
<body cz-shortcut-listen="true">
<nav class="navbar navbar-inverse navbar-fixed-top">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="index.php">TEST 검색</a>
		</div>
		<div id="navbar" class="navbar-collapse collapse">
			<form name="fastcat_search" class="navbar-form navbar-right" method="get" enctype="multipart/form-data">
				<input type="hidden" name="page" value="<?=$page?>">
				<div class="form-group">
					<div id="search-keyword-input-div">
						<input type="text" name="keyword" placeholder="검색어를 입력해 주세요" class="form-control" size="40" <?php if ($keyword) { ?>value="<?=$keyword?>"<?php } ?> autocomplete=off>
					</div>
				</div>
				<button type="submit" class="btn btn-primary">검색</button>
			</form>
		</div>
	</div>
</nav>

<div class="container">
	<div class="row">
		<div class="col-sm-12">
		<?php if (strlen($keyword) != 0) { /* 검색을 했을 경우 */ ?>
			<?php if ($result["total_count"] > 0) { ?>
				<div class="search_summary">
					<span class="search_keyword"><?=$keyword?></span>에 대한 검색 결과 (총 <?=number_format($result["total_count"])?>건)
				</div>
				<?php $resultArray = $result["result"]; ?>
				<?php if($resultArray != null) { ?>
				<ul class="search_list">
					<?php for ($cnt=0; $cnt < 10; $cnt++) { ?>
						<?php $item = $resultArray[$cnt]; ?>
                    <li>
                        <div class="result_item">
                            <?=$item["PRODUCTMAKER"]?>: <?=$item["PRODUCTNAME"]?>: <?=$item["ADDDESCRIPTION"]?> </br><?=$item["SIMPLEDESCRIPTION"] ?>
                        </div>
                    </li>
					<?php } ?>
				</ul>
				<?php //페이지 네비게이션 출력 ?>
				<div class="page-count">
					<ul class="page-count-list">
					<?php for ($pageInx=$pagingOption->startPage($page); $pageInx <= $pagingOption->endPage($page); $pageInx++) { ?>
						<li>
						<?php if($pageInx==$page) { ?>
							<b><?=$pageInx?></b>
						<?php } else { ?>
							<span onclick="goPage(<?=$pageInx?>)" style="cursor:pointer;">[<?=$pageInx?>]</span>
						<?php } ?>
						</li>
					<?php } ?>
					</ul>
				</div>
				<?php } ?>
			<?php } else if ($result["total_count"] == 0) { ?>
                <div class="not_found"> <p><b>'<?=$keyword?>'</b>에 대한 검색결과가 없습니다.</b></p>
					<ul>
						<li>단어의 철자가 정확한지 확인해 보세요.</li>
						<li>한글을 영어로 혹은 영어를 한글로 입력했는지 확인해 보세요.</li>
						<li>검색어의 단어 수를 줄이거나, 보다 일반적인 검색어로 다시 검색해 보세요.</li>
						<li>두 단어 이상의 검색어인 경우, 띄어쓰기를 확인해 보세요.</li>
					</ul>
				</div>
			<?php } ?>	
		<?php } else { ?>
		검색을 시작하십시오.
		<?php } ?>
		</div>
	</div> <!-- /.row -->
</div> <!-- /.container -->
<footer>
    <div class="navbar-mobile navbar-fixed-bottom">
        <span class="glyphicon glyphicon-info-sign"></span>
        <span class="text">2017 Fastcat.</span>
    </div>
</footer>
</body>
</html>
