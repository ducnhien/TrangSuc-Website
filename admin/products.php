<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
};

if (isset($_POST['add_product'])) {

    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $price = $_POST['price'];
    $price = filter_var($price, FILTER_SANITIZE_STRING);
    $category = $_POST['category'];
    $category = filter_var($category, FILTER_SANITIZE_STRING);

    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_img/' . $image;

    $select_products = mysqli_query($conn, "SELECT * FROM `products` WHERE name = '$name'") or die('query failed');

    if (mysqli_num_rows($select_products) > 0) {
        $message[] = 'tên sản phẩm đã tồn tại!';
    } else {
        if ($image_size > 2000000) {
            $message[] = 'kích thước hình ảnh quá lớn';
        } else {
            move_uploaded_file($image_tmp_name, $image_folder);

            $insert_product = "INSERT INTO `products`(name, category, price, image) VALUES ('" . mysqli_real_escape_string($conn, $name) . "', '" . mysqli_real_escape_string($conn, $category) . "', '" . mysqli_real_escape_string($conn, $price) . "', '" . mysqli_real_escape_string($conn, $image) . "')";
            mysqli_query($conn, $insert_product);

            $message[] = 'sản phẩm mới được thêm vào!';
        }
    }
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    $delete_product_image = "SELECT * FROM `products` WHERE id = '" . mysqli_real_escape_string($conn, $delete_id) . "'";
    $result = mysqli_query($conn, $delete_product_image);
    $fetch_delete_image = mysqli_fetch_assoc($result);
    unlink('../uploaded_img/' . $fetch_delete_image['image']);

    $delete_product = "DELETE FROM `products` WHERE id = '" . mysqli_real_escape_string($conn, $delete_id) . "'";
    mysqli_query($conn, $delete_product);

    $delete_cart = "DELETE FROM `cart` WHERE pid = '" . mysqli_real_escape_string($conn, $delete_id) . "'";
    mysqli_query($conn, $delete_cart);

    header('location:products.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản phẩm</title>

    <!-- Font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- custom css file link -->
    <link rel="stylesheet" href="../CSS/admin_style.css">
</head>

<body>

    <?php include '../components/admin_header.php' ?>

    <section class="add-products">

        <form action="" method="POST" enctype="multipart/form-data">
            <h3>thêm sản phẩm</h3>
            <input type="text" placeholder="nhập tên sản phẩm" name="name" maxlength="100" class="box">
            <input type="number" min="0" max="9999999999" placeholder="nhập giá sản phẩm" name="price" onkeypress="if(this.value.length == 10) return false;" class="box">
            <select name="category" class="box" required>
                <option value="" disabled selected>chọn danh mục --</option>
                <option value="main dish">Dây chuyền</option>
                <option value="fast food">Nhẫn</option>
                <option value="drinks">Vòng</option>
            </select>
            <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png, image/webp" required>
            <input type="submit" value="thêm sản phẩm" name="add_product" class="btn">
        </form>

    </section>

    <section class="show-products" style="padding-top: 0;">

        <div class="box-container">
            <?php
            $show_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
            if (mysqli_num_rows($show_products) > 0) {
                while ($fetch_products = mysqli_fetch_assoc($show_products)) {
            ?>
                    <div class="box">
                        <img src="../uploaded_img/<?= $fetch_products['image']; ?>" alt="">
                        <div class="flex">
                            <div class="price"><span></span><?= $fetch_products['price']; ?><span> VND</span></div>
                        </div>
                        <div class="name"><?= $fetch_products['name']; ?></div>
                        <div class="flex-btn">
                            <a href="update_product.php?update=<?= $fetch_products['id']; ?>" class="option-btn">update</a>
                            <a href="products.php?delete=<?= $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('xóa sản phẩm này?');">xóa</a>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<p class="empty">chưa có sản phẩm nào được thêm vào!</p>';
            }
            ?>
        </div>

    </section>

    <script src="../js/admin_script.js"></script>

</body>

</html>