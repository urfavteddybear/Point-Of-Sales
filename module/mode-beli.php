<?php

function generateNo(){
    global $conn;

    $queryNo = mysqli_query($conn, "SELECT max(no_beli) as maxno FROM tbl_beli_head");
    $row = mysqli_fetch_assoc($queryNo);
    $maxno = $row["maxno"];

    // Ensure $maxno is not null before passing it to substr
    if ($maxno !== null) {
        $noUrut = (int) substr($maxno, 2, 4);
    } else {
        $noUrut = 0; // or handle this case appropriately
    }

    $noUrut++;
    $maxno = 'PB' . sprintf("%04s", $noUrut);

    return $maxno;
}

?>
