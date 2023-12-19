<?php
function redirecionar($destino) {
    if (isset($destino)){
        header("Location: $destino");
    } else {
        echo "<script>window.history.back();</script>";
    }
    exit;
}
?>
