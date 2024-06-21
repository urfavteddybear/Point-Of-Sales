<?php

function uploadimg(){
    $namafile   = $_FILES['image']['name'];
    $ukuran     = $_FILES['image']['size'];
    $tmp        = $_FILES['image']['tmp_name'];

    // validasi file gambar yg boleh di upload
    $ekstensiGambarValid    = ['jpg', 'jpeg', 'png', 'gif'];
    $ekstensiGambar         = explode('.', $namafile);
    $ekstensiGambar         = strtolower(end($ekstensiGambar));
    if(!in_array($ekstensiGambar, $ekstensiGambarValid)) {
        echo '<script>
                alert("File yg anda upload bukan gambar, data gagal ditambahkan ! ");
        
            </script>';
            return false;
    }


    // validasi ukuran gambar max 1 MB
    if ($ukuran > 1000000) {
        echo '<script>
                alert("Ukuran gambar tidak boleh lebih dari 1 MB ");
        
            </script>';
            return false;
    }

    $namaFileBaru   = rand(10, 1000) . '-' . $namafile;
    move_uploaded_file($tmp, '../asset/image/' . $namaFileBaru);
    return $namaFileBaru;

}

function getData($sql){
    global $conn;

    $result = mysqli_query($conn, $sql);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)){
        $rows[] = $row;
    }
    return $rows;
}

?>