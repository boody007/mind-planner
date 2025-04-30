<!-- Sessions Starting -->
<?php 
    session_start();
    $campaign = [];
    $loading_screen_show = true;
?>
<!-- Required Meta -->
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Master Style -->
<link rel="stylesheet" href="assets/css/master.css?v=<?= time() ?>">
<!-- Bootstrap Style -->
<link rel="stylesheet" href="assets/css/bootstrap.min.css?v=<?= time() ?>">
<!-- FavIcon -->
<link rel="shortcut icon" href="favicon.png?v=<?= time() ?>" type="image/x-icon">
<!-- jQuery Use -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!-- FontAwesome Style -->
<link rel="stylesheet" href="assets/css/fontawesome.min.css?=v<?= time() ?>">