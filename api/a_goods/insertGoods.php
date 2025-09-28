<?php
include_once '../../config/Database.php';
include_once '../../model/A_Goods.php';

$database = new Database();
$db = $database->connect();

$goods = new A_Goods($db);
//$randomNumber = rand(100, 1000);
$uniqueName = time() . '_' . rand(1000, 9999);
//$lastId = $temporary->getLastId() + 1;
$fn = $uniqueName.".jpg";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_FILES['image'])) {
        
        $targetDir = "../../img/a_img/";
        $targetFile = $targetDir . basename($_FILES["image"]["name"]);
		//$filename = time() . '_' . uniqid() . '.jpg'; // Adjust extension based on actual file type
        //$target_file = $upload_dir . $filename;
		$f=$targetDir ."". $fn;
		$ff="a_img/".$fn;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"],  $f)) {
			
			 $data = json_decode($_POST['data'], true);

            $goodsId = $data['goodsId'];
            $categoryId = $data['categoryId'];
            $name = $data['name'];
            $description = $data['description'];

            $image = $ff;
		
			$insertedBy = $data['insertedBy'];
			
			

$goods->insertGoods($name,$categoryId,$image,$description,$insertedBy);

			//include_once '../settings/increaseNewUpdateCode.php';
			
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
