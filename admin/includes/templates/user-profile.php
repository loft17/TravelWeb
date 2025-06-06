<?php
include $_SERVER['DOCUMENT_ROOT'] . '/admin/includes/auth/protect.php';
?>

<!-- page title area start -->
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">XX</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="index.html">Home</a></li>
                    <li><span>Dashboard</span></li>
                </ul>
            </div>
        </div>
        <div class="col-sm-6 clearfix">
            <div class="user-profile pull-right">
                <img class="avatar user-thumb" src="/admin/assets/images/author/avatar.png" alt="avatar">
                <h4 class="user-name dropdown-toggle" data-toggle="dropdown">
                    <?php echo htmlspecialchars($_SESSION['user_name']); ?> 
                    <i class="fa fa-angle-down"></i>
                </h4>

                <div class="dropdown-menu">
                    <a class="dropdown-item" href="/admin/includes/auth/logout.php">Log Out</a>
                </div>
            </div>
        </div>
    </div>
</div>
