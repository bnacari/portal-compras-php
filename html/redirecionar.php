<?php
function redirecionar($destino) {
    if (isset($destino)){
        // header("Location: $destino");
        echo "<script>window.location.href = '$destino';</script>";
        exit();

    } else {
        echo "<script>window.history.back();</script>";
        exit();
    }
    exit;
}
?>
