<?php

if (userLogin()['level'] == 3) {
    header("location:" . $main_url . "error-page.php");
    exit();
}

function generateId(){
    global $conn;
    $queryId = mysqli_query($conn, "SELECT max(id_barang) as maxid FROM tbl_barang");
    $data = mysqli_fetch_array($queryId);
    $maxid = $data['maxid'];

    // Ensure $maxid is not null before passing it to substr
    if ($maxid !== null) {
        $noUrut = (int) substr($maxid, 4, 3);
    } else {
        $noUrut = 0; // or handle this case appropriately
    }

    $noUrut++;
    $maxid = "BRG-" . sprintf("%03s", $noUrut);

    return $maxid;
}

function insert($data){
    global $conn;

    $id             = mysqli_real_escape_string($conn, $data['kode']);
    $barcode        = mysqli_real_escape_string($conn, $data['barcode']);
    $name           = mysqli_real_escape_string($conn, $data['name']);
    $satuan         = mysqli_real_escape_string($conn, $data['satuan']);
    $harga_beli     = mysqli_real_escape_string($conn, $data['harga_beli']);
    $harga_jual     = mysqli_real_escape_string($conn, $data['harga_jual']);
    $stockmin       = mysqli_real_escape_string($conn, $data['stock_minimal']);
    $gambar         = mysqli_real_escape_string($conn, $_FILES['image']['name']);


    $cekBarcode     = mysqli_query($conn, "SELECT * FROM tbl_barang WHERE barcode = '$barcode'");
    if (mysqli_num_rows($cekBarcode)) {
        echo '<script>alert("Kode barcode sudah ada, barang gagal ditambahkan")</script>';
        return false;
    }

    //upload gambar barang
    if ($gambar != null) {
        $gambar = uploadimg(null, $id);
    }else {
        $gambar = 'default-brg.png';
    }

    //gambar tidak sesuai validasi
    if($gambar == '') {
        return false;
    }

    $sqlBrg    = "INSERT INTO tbl_barang VALUE ('$id', '$barcode', '$name', '$harga_beli', '$harga_jual', 0, '$satuan', '$stockmin', '$gambar')";
    mysqli_query($conn, $sqlBrg);

    return mysqli_affected_rows($conn);
}

function delete($id, $gbr){
    global $conn;
    $sqlDel = "DELETE FROM tbl_barang WHERE id_barang= '$id'";
    mysqli_query($conn, $sqlDel);

    if ($gbr != 'default-brg.png') {
        unlink('../asset/image/' . $gbr);
    }

    return mysqli_affected_rows($conn);
}

function update($data){
    global $conn;

    $id             = mysqli_real_escape_string($conn, $data['kode']);
    $barcode        = mysqli_real_escape_string($conn, $data['barcode']);
    $name           = mysqli_real_escape_string($conn, $data['name']);
    $satuan         = mysqli_real_escape_string($conn, $data['satuan']);
    $harga_beli     = mysqli_real_escape_string($conn, $data['harga_beli']);
    $harga_jual     = mysqli_real_escape_string($conn, $data['harga_jual']);
    $stockmin       = mysqli_real_escape_string($conn, $data['stock_minimal']);
    $gbrLama       = mysqli_real_escape_string($conn, $data['oldImg']);
    $gambar         = mysqli_real_escape_string($conn, $_FILES['image']['name']);

    //cek barcode lama
    $queryBarcode = mysqli_query($conn, "SELECT * FROM tbl_barang WHERE id_barang = '$id'");
    $dataBrg      = mysqli_fetch_assoc($queryBarcode);
    $curBarcode   = $dataBrg['barcode'];
    // barcode baru
    $cekBarcode     = mysqli_query($conn, "SELECT * FROM tbl_barang WHERE barcode = '$barcode'");


    // jika barcode diganti
    if ($barcode !== $curBarcode) {
        // jika barcode sudah ada
        if (mysqli_num_rows($cekBarcode)) {
            echo '<script>alert("Kode barcode sudah ada, barang gagal diperbarui")</script>';
            return false;
        }
    }

    //cek gambar 
    if ($gambar != null) {
        $url = "index.php";
        if ($gbrLama == 'default-brg.png') {
            $nmgbr = $id;
        } else {
            $nmgbr = $id . '-' . rand(10, 1000);
        }
        $imgBrg = uploadimg($url, $nmgbr);
        if ($gbrLama != 'default-brg.png') {
            @unlink('../asset/image/' .$gbrLama);
        }
    }else {
        $imgBrg = $gbrLama;
    }


    mysqli_query($conn, "UPDATE tbl_barang SET 
                                        barcode     = '$barcode',
                                        nama_barang = '$name',
                                        harga_beli = $harga_beli,
                                        harga_jual = $harga_jual,
                                        satuan = '$satuan',
                                        stock_minimal = $stockmin,
                                        gambar = '$imgBrg'
                                        WHERE id_barang = '$id'
                                        ");

    return mysqli_affected_rows($conn);
}