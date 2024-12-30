<?php
/*
    Autor: Abel Anthony Silva Santa Cruz
    Email: abel_silva27@hotmail.com
    Fecha: 2023-10-04
    PASOS:
    1. PRIMERO CORRE GULP EN EL CMD. $ gulp
    2. MUEVE AL ROOT resizeImages.php
    3. EDITA LA CARPETA DE ORIGEN, DESTINO Y MEDIDAS
    4. EN EL CMD, $ php resizeImages.php
    5. LISTO EN LA CARPETA DE DESTINO TENDRAS TUS IMAGENES REDIMENCIONADAS Y MINIFICADAS (webp)

    Descripcion:
    Este es un script que redimensiona imagenes y las guarda en diferentes directorios
        para que puedan ser usadas en diferentes dispositivos
        El script recibe como parametros el directorio donde se encuentran las imagenes
        y el directorio donde se guardaran las imagenes redimencionadas
        y un array con las medidas de las imagenes que se desean obtener
        por defecto se usan las siguientes medidas:
        $medidas=array(
            "s"=>["width"=>150,"height"=>150],
            "m"=>["width"=>375,"height"=>375],
            "l"=>["width"=>750,"height"=>750],
            "xl"=>["width"=>1500,"height"=>1500]
        );
        El script crea los directorios si no existen
        y guarda las imagenes redimencionadas en los directorios
        con los nombres de las imagenes originales
        por ejemplo: si la imagen original se llama "imagen.jpg"
        las imagenes redimencionadas se guardaran con el mismo nombre
        en los directorios "s","m","l","xl"
        y se podran acceder a ellas de la siguiente manera:
        <img src="images/s/imagen.jpg" alt="imagen">
        <img src="images/m/imagen.jpg" alt="imagen">
        <img src="images/l/imagen.jpg" alt="imagen">
        <img src="images/xl/imagen.jpg" alt="imagen">
        El script funciona con imagenes jpg, jpeg, png y webp
        y se puede usar en cualquier proyecto php
        solo se debe incluir el archivo imagenes.php
        y crear una instancia de la clase ImageController

        run on termina with: php resizeImages.php

    NOTA: el script no elimina las imagenes originales
    NOTA: este script esta optimizado para imagenes cuadradas
    NOTA: la resolucion de sus imagenes debe ser mayor a la especificada en el array de medidas sino solo aumentara en px mas no en calidad

*/
 class ImageController{
    public array $imagenes;
    public string $directorio_input;
    public string $directorio_output;
    public array $nuevos_destinos;
    public array $medidas;
    public array $extensionesPermitidas = ["jpg", "jpeg", "png","webp"];

    //directorio relativo o absoulto ambos funcionan
    public function __construct(string $directorio_input,string $directorio_output,array $medidas=null){
        $this->directorio_input=$this->obtenerDirectorio($directorio_input);
        $this->directorio_output=$this->obtenerDirectorio($directorio_output);
        $this->imagenes=$this->obtenerImagenes();
        $this->medidas=$medidas??array(
            "s"=>["width"=>150,"height"=>150],
            "m"=>["width"=>300,"height"=>300],
            "l"=>["width"=>750,"height"=>750]
        );
        $this->nuevos_destinos=$this->obtenerNuevosDestinos();
    }
    public function obtenerDirectorio($directorio){
        if(!file_exists($directorio)){
            mkdir($directorio);
        }
        return $directorio;
    }
    private function obtenerNuevosDestinos(){
        $nuevos_destinos=[];
        foreach($this->medidas as $key=>$medida){
            $nuevos_destinos[$key]=$this->directorio_output."/".$key;
            if (!file_exists($nuevos_destinos[$key])) {
                mkdir($nuevos_destinos[$key]);
            }
        }
        return $nuevos_destinos;
    }
    private function obtenerImagenes() {
        $imagenes = [];
            
        $archivos = scandir($this->directorio_input);
        
        if ($archivos === false) {
            die("No se pudo leer el contenido del directorio.");
        }
        foreach ($archivos as $archivo) {
            $extension = pathinfo($archivo, PATHINFO_EXTENSION);
    
            if (in_array($extension, $this->extensionesPermitidas)) {
                $imagenes[] = $archivo;
            }
        }
    
        return $imagenes;
    }
    //REDIMENCIONA LA IMAGEN Y LA GUARDA EN LOS DIRECTORIOS
    private function redimencionarGuardarImagen($imagen):void{
        $name = $imagen;
        $tmp = __DIR__."/".$this->directorio_input."/".$name;

        $extension = pathinfo($name, PATHINFO_EXTENSION);
        
        print("Redimencionando imagen: ".$tmp."\r\n");
        //obtenemos las medidas originales
        $medidasimagen=getimagesize($tmp);
        $old_width=$medidasimagen[0];
        $old_height=$medidasimagen[1];
    
        // Crea una imagen
        $imagecreated=imagecreatefromstring(file_get_contents($tmp));
    
        foreach($this->medidas as $key=>$medidas){
            $new_width=$medidas["width"];
            $new_height=$medidas["height"];
            
            // Crear una imagen vacía
            $imageout=imagecreatetruecolor($new_width,$new_height);
    
            // Configura el fondo transparente
            imagealphablending($imageout, false);
            imagesavealpha($imageout, true);
    
            // Rellena el fondo transparente con blanco
            $transparent = imagecolorallocatealpha($imageout, 255, 255, 255, 127);
            imagefill($imageout, 0, 0, $transparent);
    
            // Redimensionar
            imagecopyresampled($imageout,$imagecreated,0,0,0,0,$new_width,$new_height,$old_width,$old_height);
    
            // Guardar imagen
            $destino=$this->nuevos_destinos[$key]."/".$name;
            if($extension=="jpg" || $extension=="jpeg"){
                imagejpeg($imageout,$destino);
            }else if($extension=="png"){
                imagepng($imageout,$destino);
            }else if($extension=="webp"){
                imagewebp($imageout,$destino);
            }
            // Liberar memoria
            imagedestroy($imageout);
        }
        // Liberar memoria
        imagedestroy($imagecreated);
    
    }
    public function redimencionarGuardarImagenes():string{
        foreach($this->imagenes as $imagen){
            $this->redimencionarGuardarImagen($imagen);
        }
        return "Imagenes redimencionadas y guardadas correctamente";
    }
 }
$imagenes=new ImageController("src/galeria","src/galeria/240",["s"=>["width"=>150,"height"=>150]]); 
echo $imagenes->redimencionarGuardarImagenes();

