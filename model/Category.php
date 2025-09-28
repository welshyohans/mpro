<?php

class Category{

    private $conn;
    public function __construct($conn){
        $this->conn = $conn;
    }

    public function getAllCategories() {
        $query = "SELECT * FROM category";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $allCategory = array();
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);

                 $eachCategory = array(
                    "categoryId" => $id,
                    "name" => $name,
                    "parentId" => $parent_id,
                    "isAvailable" =>$is_available, 
                    "toSubCategory" =>$to_sub_category,
                    "isUpdate"=>1
                 );

                 array_push($allCategory,$eachCategory);
            }
        }
        return $allCategory;
    }
	
	    public function getAllSimilarGlass() {
        $query = "SELECT * FROM glass_similarity";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $allGlass = array();
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);

                 $eachGlass = array(
                     "glassId" =>$id,
                    "categoryId" => $category_id,
                    "model" => $model,
                    "size" => $size,
                    "imageUrl"=>$image_url,
                    "priority"=>$priority,
                    "similarityOne" =>$similarity_one,
                    "brand"=>$brand,
                    "parentId"=>$parent_id,
                    "parentModel"=>$parent_model,
                    "parentSize"=>$parent_size
                 );

                 array_push($allGlass,$eachGlass);
            }
        }
        return $allGlass;
    }
	
	    public function getCategoryForEdit(){
        $query = "SELECT * FROM category WHERE parent_id!=0 ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $num = $stmt->rowCount();

        $allCategory = array();
        if($num>0){
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);

                 $eachCategory = array(
                    "categoryId" => $id,
                    "name" => $name,
                    "parentId" => $parent_id,
                    "isAvailable" =>$is_available, 
                    "toSubCategory" =>$to_sub_category,
                    "isUpdate"=>1
                 );

                 array_push($allCategory,$eachCategory);
            }
        }
        return $allCategory;
    }
}


?>