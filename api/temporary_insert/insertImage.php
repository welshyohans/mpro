<?php
include_once '../../config/Database.php';
include_once '../../model/Temporary.php';

$database = new Database();
$db = $database->connect();

$temporary = new Temporary($db,0);//new update code is not necessary
$lastId = $temporary->getLastId() + 1;
$fn = $lastId.".jpg";
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
            $categoryId = $data['categoryId'];
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
