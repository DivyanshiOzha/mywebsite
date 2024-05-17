<?php
session_start();
$ip_add = getenv("REMOTE_ADDR");
include "db.php";

if(isset($_POST["category"])){
    $category_query = "SELECT * FROM categories";
    $run_query = mysqli_query($con, $category_query) or die(mysqli_error($con));
    echo "<div class='aside'><h3 class='aside-title'>Categories</h3><div class='btn-group-vertical'>";
    if(mysqli_num_rows($run_query) > 0){
        $i=1;
        while($row = mysqli_fetch_array($run_query)){
            $cid = $row["cat_id"];
            $cat_name = $row["cat_title"];
            $sql = "SELECT COUNT(*) AS count_items FROM products WHERE product_cat=$i";
            $query = mysqli_query($con, $sql);
            if(!$query){
                echo "Error: " . mysqli_error($con);
                continue;
            }
            $row = mysqli_fetch_array($query);
            $count = $row["count_items"];
            $i++;
            echo "<div type='button' class='btn navbar-btn category' cid='$cid'>
                    <a href='#'><span></span>$cat_name<small class='qty'>($count)</small></a>
                  </div>";
        }
        echo "</div>";
    }
}

if(isset($_POST["brand"])){
    $brand_query = "SELECT * FROM brands";
    $run_query = mysqli_query($con, $brand_query);
    echo "<div class='aside'><h3 class='aside-title'>Brand</h3><div class='btn-group-vertical'>";
    if(mysqli_num_rows($run_query) > 0){
        $i=1;
        while($row = mysqli_fetch_array($run_query)){
            $bid = $row["brand_id"];
            $brand_name = $row["brand_title"];
            $sql = "SELECT COUNT(*) AS count_items FROM products WHERE product_brand=$i";
            $query = mysqli_query($con, $sql);
            if(!$query){
                echo "Error: " . mysqli_error($con);
                continue;
            }
            $row = mysqli_fetch_array($query);
            $count = $row["count_items"];
            $i++;
            echo "<div type='button' class='btn navbar-btn selectBrand' bid='$bid'>
                    <a href='#'><span></span>$brand_name<small>($count)</small></a>
                  </div>";
        }
        echo "</div>";
    }
}

if(isset($_POST["page"])){
    $sql = "SELECT * FROM products";
    $run_query = mysqli_query($con, $sql);
    if(!$run_query){
        echo "Error: " . mysqli_error($con);
        exit();
    }
    $count = mysqli_num_rows($run_query);
    $pageno = ceil($count/9);
    for($i=1;$i<=$pageno;$i++){
        echo "<li><a href='#product-row' page='$i' id='page' class='active'>$i</a></li>";
    }
}

if(isset($_POST["getProduct"])){
    $limit = 9;
    if(isset($_POST["setPage"])){
        $pageno = $_POST["pageNumber"];
        $start = ($pageno * $limit) - $limit;
    }else{
        $start = 0;
    }
    $product_query = "SELECT * FROM products,categories WHERE product_cat=cat_id LIMIT $start,$limit";
    $run_query = mysqli_query($con, $product_query);
    if(!$run_query){
        echo "Error: " . mysqli_error($con);
        exit();
    }
    if(mysqli_num_rows($run_query) > 0){
        while($row = mysqli_fetch_array($run_query)){
            $pro_id    = $row['product_id'];
            $pro_cat   = $row['product_cat'];
            $pro_brand = $row['product_brand'];
            $pro_title = $row['product_title'];
            $pro_price = $row['product_price'];
            $pro_image = $row['product_image'];
            $cat_name = $row["cat_title"];
            echo "<div class='col-md-4 col-xs-6'>
                    <a href='product.php?p=$pro_id'><div class='product'>
                        <div class='product-img'><img src='product_images/$pro_image' style='max-height: 170px;' alt=''>
                            <div class='product-label'>
                                <span class='sale'>-30%</span>
                                <span class='new'>NEW</span>
                            </div>
                        </div></a>
                        <div class='product-body'>
                            <p class='product-category'>$cat_name</p>
                            <h3 class='product-name header-cart-item-name'><a href='product.php?p=$pro_id'>$pro_title</a></h3>
                            <h4 class='product-price header-cart-item-info'>$pro_price<del class='product-old-price'>$990.00</del></h4>
                            <div class='product-rating'>
                                <i class='fa fa-star'></i>
                                <i class='fa fa-star'></i>
                                <i class='fa fa-star'></i>
                                <i class='fa fa-star'></i>
                                <i class='fa fa-star'></i>
                            </div>
                            <div class='product-btns'>
                                <button class='add-to-wishlist'><i class='fa fa-heart-o'></i><span class='tooltipp'>add to wishlist</span></button>
                                <button class='add-to-compare'><i class='fa fa-exchange'></i><span class='tooltipp'>add to compare</span></button>
                                <button class='quick-view'><i class='fa fa-eye'></i><span class='tooltipp'>quick view</span></button>
                            </div>
                        </div>
                        <div class='add-to-cart'>
                            <button pid='$pro_id' id='product' class='add-to-cart-btn block2-btn-towishlist' href='#'><i class='fa fa-shopping-cart'></i> add to cart</button>
                        </div>
                    </div>
                </div>";
        }
    }
}

if(isset($_POST["get_seleted_Category"]) || isset($_POST["selectBrand"]) || isset($_POST["search"])){
    if(isset($_POST["get_seleted_Category"])){
        $id = $_POST["cat_id"];
        $sql = "SELECT * FROM products,categories WHERE product_cat = '$id' AND product_cat=cat_id";
    }else if(isset($_POST["selectBrand"])){
        $id = $_POST["brand_id"];
        $sql = "SELECT * FROM products,categories WHERE product_brand = '$id' AND product_cat=cat_id";
    }else {
        $keyword = $_POST["keyword"];
        header('Location:store.php');
        $sql = "SELECT * FROM products,categories WHERE product_cat=cat_id AND product_keywords LIKE '%$keyword%'";
    }
    $run_query = mysqli_query($con, $sql);
    if(!$run_query){
        echo "Error: " . mysqli_error($con);
        exit();
    }
    while($row = mysqli_fetch_array($run_query)){
        $pro_id    = $row['product_id'];
        $pro_cat   = $row['product_cat'];
        $pro_brand = $row['product_brand'];
        $pro_title = $row['product_title'];
        $pro_price = $row['product_price'];
        $pro_image = $row['product_image'];
        $cat_name = $row["cat_title"];
        echo "<div class='col-md-4 col-xs-6'>
                <a href='product.php?p=$pro_id'><div class='product'>
                    <div class='product-img'>
                        <img  src='product_images/$pro_image'  style='max-height: 170px;' alt=''>
                        <div class='product-label'>
                            <span class='sale'>-30%</span>
                            <span class='new'>NEW</span>
                        </div>
                    </div></a>
                    <div class='product-body'>
                        <p class='product-category'>$cat_name</p>
                        <h3 class='product-name header-cart-item-name'><a href='product.php?p=$pro_id'>$pro_title</a></h3>
                        <h4 class='product-price header-cart-item-info'>$pro_price<del class='product-old-price'>$990.00</del></h4>
                        <div class='product-rating'>
                            <i class='fa fa-star'></i>
                            <i class='fa fa-star'></i>
                            <i class='fa fa-star'></i>
                            <i class='fa fa-star'></i>
                            <i class='fa fa-star'></i>
                        </div>
                        <div class='product-btns'>
                            <button class='add-to-wishlist' tabindex='0'><i class='fa fa-heart-o'></i><span class='tooltipp'>add to wishlist</span></button>
                           
