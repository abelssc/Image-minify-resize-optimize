<?php
    require_once "app/config/conexion.php";

    $conexion=Conexion::getInstance()->getConnection();

    $sql="SELECT producto_foto_id,nombre_foto FROM productos_fotos";
    $stmt=$conexion->prepare($sql);
    $stmt->execute();
    $banners=$stmt->fetchAll(PDO::FETCH_ASSOC);
 
    
    $sql="UPDATE productos_fotos SET nombre_foto=:nombre_foto WHERE producto_foto_id=:producto_foto_id";
    $stmt=$conexion->prepare($sql);
    foreach($banners as $banner){
        // cambiar la extension .jpg a .webp
        $banner["nombre_foto"]=str_replace(".jpg",".webp",$banner["nombre_foto"]);
        $stmt->bindParam(":nombre_foto",$banner["nombre_foto"]);
        $stmt->bindParam(":producto_foto_id",$banner["producto_foto_id"]);
        $stmt->execute();
    }