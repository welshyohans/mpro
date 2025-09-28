<?php
include_once '../../config/Database.php';
include_once '../../model/Temporary.php';

$database = new Database();
$db = $database->connect();

$temporary = new Temporary($db,0);//new update code is not necessary
$randomNumber = rand(100, 1000);
$lastId = $temporary->getLastId() + 1;
$fn = $lastId."".$randomNumber.".jpg";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_FILES['image'])) {
        
        $targetDir = "../../img/cover/";
        $targetFile = $targetDir . basename($_FILES["image"]["name"]);

		$f=$targetDir ."". $fn;
		$ff="cover/".$fn;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"],  $f)) {
			
			 $data = json_decode($_POST['data'], true);


            $imageUrl = $ff;
			$supplierId = $data['supplierId'];
			$id = $data['id'];
	
	if($id == 0){
	   $temporary->insertCoverImage($imageUrl,$supplierId); 
	}else{
	    $temporary->updateCoverImage($id,$imageUrl,$supplierId); 
	}



		//	include_once '../settings/increaseNewUpdateCode.php';
			
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
