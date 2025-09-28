<?php
include_once '../../config/Database.php';
include_once '../../model/Temporary.php';

$database = new Database();
$db = $database->connect();

$temporary = new Temporary($db,0);//new update code is not necessary
//$randomNumber = rand(100, 1000);
$uniqueName = time() . '_' . rand(1000, 9999);
//$lastId = $temporary->getLastId() + 1;
$fn = $uniqueName.".jpg";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_FILES['image'])) {
        
        $targetDir = "../../img/mob/";
        $targetFile = $targetDir . basename($_FILES["image"]["name"]);
		//$filename = time() . '_' . uniqid() . '.jpg'; // Adjust extension based on actual file type
        //$target_file = $upload_dir . $filename;
		$f=$targetDir ."". $fn;
		$ff="mob/".$fn;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"],  $f)) {
			
			 $data = json_decode($_POST['data'], true);

            $goodsId = $data['goodsId'];
             $categoryId = ($data['categoryId'] == 54) ? 12 : $data['categoryId'];
            $model = $data['model'];
            $description = $data['description'];
            $seo = $data['seo'];
            $minOrder = $data['minOrder'];
            $maxOrder = $data['maxOrder'];
            $discountStarts = $data['discountStarts'];
            $showInHome = $data['showInHome'];
            $isAvailable = $data['isAvailable'];
            $purePrice = $data['purePrice'];
            $imageUrl = $ff;
			$isUpdate = $data['isUpdate'];
			$shopId = $data['shopId'];
			
			

$rrr= $temporary->insertGoodsToTemporary($goodsId,$categoryId,10,$model,$description,$seo,$purePrice,$minOrder,$maxOrder,0,$showInHome,$imageUrl,$discountStarts,$isAvailable,0,$isUpdate,$shopId);

$temporary->insertToSupplierGoods($rrr,$shopId,$purePrice);

			include_once '../settings/increaseNewUpdateCode.php';
			
            echo json_encode(array('status' => 'success', 'message' => 'Image uploaded successfully'.$ff));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Failed to move the uploaded file'));
        }
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Image field not present in the request'));
    }
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Invalid request method'));
}
?>
