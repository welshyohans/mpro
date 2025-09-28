<?php
include_once '../../config/Database.php';
include_once '../../model/Fantay.php';

$database = new Database();
$db = $database->connect();

$goods = new Fantay($db);
$randomNumber = rand(100, 10000);
//$lastId = $temporary->getLastId() + 1;
$fn = $randomNumber.".jpg";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_FILES['image'])) {
        
        $targetDir = "../../img/fantay/";
        $targetFile = $targetDir . basename($_FILES["image"]["name"]);
		//$filename = time() . '_' . uniqid() . '.jpg'; // Adjust extension based on actual file type
        //$target_file = $upload_dir . $filename;
		$f=$targetDir ."". $fn;
		$ff="fantay/".$fn;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"],  $f)) {
			
			 $data = json_decode($_POST['data'], true);

            $id = $data['id'];
            $categoryId = $data['categoryId'];
            $name = $data['name'];
            $description = $data['description'];
            $isAvailable = $data['isAvailable'];
            $price = $data['price'];
            $image = $ff;
			
			

$goods->insertGoods($id,$name,$price,$description,$isAvailable,$categoryId,$image);

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
