<?php

include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
    header('location:login.php');
};

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn hàng</title>

    <!-- Font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- custom css file link -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <?php include 'components/user_header.php'; ?>

    <div class="heading">
        <h3>orders</h3>
        <p><a href="home.php">trang chủ</a> / đơn hàng</p>
    </div>

    <section class="orders">

        <h1 class="title">đơn hàng của bạn</h1>

        <div class="box-container">

            <?php
            if ($user_id == '') {
                echo '<p class="empty">đăng nhập để xem đơn hàng</p>';
            } else {
                $select_orders = mysqli_query($conn, "SELECT * FROM `orders` WHERE user_id ='$user_id'") or die('query failed');
                if (mysqli_num_rows($select_orders) > 0) {
                    while ($fetch_orders = mysqli_fetch_assoc($select_orders)) {
            ?>
                        <div class="box">
                            <p>đặt lúc : <span><?= $fetch_orders['placed_on']; ?></span></p>
                            <p>tên : <span><?= $fetch_orders['name']; ?></span></p>
                            <p>email : <span><?= $fetch_orders['email']; ?></span></p>
                            <p>SDT : <span><?= $fetch_orders['number']; ?></span></p>
                            <p>địa chỉ : <span><?= $fetch_orders['address']; ?></span></p>
                            <p>phương thức thanh toán : <span><?= $fetch_orders['method']; ?></span></p>
                            <p>đơn đặt hàng của bạn : <span><?= $fetch_orders['total_products']; ?></span></p>
                            <p>Tổng giá : <span><?= $fetch_orders['total_price']; ?>/-</span></p>
                            <p>tình trạng thanh toán : <span style="color:<?php if ($fetch_orders['payment_status'] == 'pending') {
                                                                        echo 'red';
                                                                    } else {
                                                                        echo 'green';
                                                                    }; ?>"><?= $fetch_orders['payment_status']; ?></span> </p>
                        </div>
            <?php
                    }
                } else {
                    echo '<p class="empty">bạn không có đơn hàng nào!</p>';
                }
            }
            ?>

        </div>

    </section>

    <?php include 'components/footer.php'; ?>

    <script src="js/script.js"></script>

</body>
</html>