<?php



namespace App;



class Propiedad{

    protected static $db;
    protected static $columnasDB = ['id', 'titulo', 'precio', 'imagen', 'descripcion', 'habitaciones', 'wc', 'estacionamiento', 'creado', 'vendedores_Id'];

    //errores
    protected static $errores = [];
    
    public $id;
    public $titulo;
    public $precio;
    public $imagen;
    public $descripcion;
    public $habitaciones;
    public $wc;
    public $estacionamiento;
    public $creado;
    public $vendedores_Id;


    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? '';
        $this->titulo = $args['titulo'] ?? '';
        $this->precio = $args['precio'] ?? '';
        $this->imagen = $args['imagen'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->habitaciones = $args['habitaciones'] ?? '';
        $this->wc = $args['wc'] ?? '';
        $this->estacionamiento = $args['estacionamiento'] ?? '';
        $this->creado = date("Y-m-d");
        $this->vendedores_Id = $args['vendedores_Id'] ?? 1 ;
    }

    //definir la conexion a la base de datos
    public static function setDB($database){
        self::$db = $database;
    }

    public function guardar(){

     //sanitizar los datos
    $atributos = $this->sanitizarAtributos();

        //insertar en la base de datos
        $query = "INSERT INTO propiedades (";
$query .= join(', ', array_keys($atributos));
$query .= ") VALUES ('";
$query .= join("','", array_values($atributos));
$query .= "')";
$resultado = self::$db->query($query);
        return $resultado;
    }

    //identificar y unir los atributos de la base de datos
    public function atributos(){
        //mapear los atributos de la clase a un arreglo
        $atributos = []; 
        foreach(self::$columnasDB as $columna){
            if($columna === 'id') continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    public function sanitizarAtributos(){
        $atributos = $this->atributos();
        $sanitizado = [];
        foreach( $atributos as $key => $value){
            $sanitizado[$key] = self::$db->escape_string($value);
        }

        return $sanitizado;
    }

    //validacion
    public static function getErrores(){
        return self::$errores;
    }

    public function validar(){

        if (!$this->titulo) {
        self::$errores[] = "debes añadir el titulo de la propiedad";
    }

    if (!$this->precio) {
        self::$errores[] = "debes añadir el precio de la propiedad";
    }

    if (strlen($this->descripcion) < 50) {
        self::$errores[] = "la descripción de la propiedad debe tener al menos 50 caracteres";
    }

    if (!$this->habitaciones) {
        self::$errores[] = "debes añadir el número de habitaciones de la propiedad";
    }

    if (!$this->wc) {
        self::$errores[] = "debes añadir el número de baños de la propiedad";
    }

    if (!$this->estacionamiento) {
        self::$errores[] = "debes añadir el número de estacionamientos de la propiedad";
    }

    if (!$this->vendedores_Id) {
        self::$errores[] = "debes añadir el vendedor de la propiedad";
    }

    if (!$this->imagen) {
        self::$errores[] = "La imagen de la propiedad no puede estar vacía o tuvo un error al cargarse.";
    }

    return self::$errores;
    }

    //lista todas las propiedades
    public static function all(){
        $query = "SELECT * FROM propiedades";

        $resultado = self::consultarSQL($query); 

        return $resultado;
    }

    public static function consultarSQL($query){
        //consultar la base de datos
        $resultado = self::$db->query($query);

        //iterar sobre los resultados
        $array = [];
        while($registro = $resultado->fetch_assoc()){
            $array[] = self::crearObjeto($registro);
        }

        //liberar la cmemoria
        $resultado->free(); 
        //retornar el resultado

        return $array;
    }

    protected static function crearObjeto($registro){
        $objeto = new self;

        foreach($registro as $key => $value){
            if(property_exists($objeto, $key)){
                $objeto->$key = $value;

            }
        }
        return $objeto;
    }

}