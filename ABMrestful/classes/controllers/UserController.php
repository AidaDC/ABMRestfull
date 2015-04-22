<?php
class UserController extends AbstractController

// tenemos array de la ruta long 2,  si longitud ==2
// coger el segundo valor del arary, y mirar si hay funcion con ese nombre, si hay lo llamas y sino error
{
    protected $userDB;
    public function __construct() {
        
        $this->userDB = userSPDO::singleton();
    }
    
    public function usuarios($peticion) {
        if (strtolower($peticion->method) == 'get' && count($peticion->url_elements) == 1) {
            
            try {
                $stmt = $this->userDB->prepare("SELECT id_usuarios,nombre,apellidos,usuario FROM usuarios");
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                return $result;



            }
            catch(PDOException $e) {
                return "Error1: " . $e->getMessage();
            }
        } else if ( count($peticion->url_elements) > 1) {
         $funcion = ( $peticion->url_elements);
           $siguiente = $this->$funcion[1]($peticion);
                       
            return $siguiente;
        }  
        else {
            return "Error3";
        }
    }

    //independiente
    public function crearUsuarios($peticion) {
        if (strtolower($peticion->method) == 'post' && count($peticion->url_elements) == 2) {
            
            $stmt = $this->userDB->prepare("INSERT INTO usuarios(nombre,apellidos,usuario,pass) VALUES (?,?,?,?)");
            

            $stmt->bindParam(1, $peticion->parameters['nombre']);
            $stmt->bindParam(2, $peticion->parameters['apellidos']);
            $stmt->bindParam(3, $peticion->parameters['usuario']);
            $stmt->bindParam(4, $peticion->parameters['pass']);
          
         
            if ($stmt->execute()) {
                return "Usuario creado correctamente";
            } else {

                return "Error al crear el usuario";
            }
        } else if (strtolower($peticion->method) != 'post') {
            return "Error: " . $peticion->method . " no permitido";
        } else {
            
            return "Error: No se ha de introducir paramatros a traves de la url";
        }
    }


     public function actualizarNombre($peticion) {
        if (strtolower($peticion->method) == 'put' && count($peticion->url_elements) == 3) {
            
            try {
                
                $stmt = $this->userDB->prepare("UPDATE usuarios SET nombre=? WHERE id_usuarios=?");
                
                $stmt->bindParam(1, $peticion->parameters['nombre']);
                $stmt->bindParam(2, $peticion->url_elements[2]);
                $stmt->execute(); 
            
                if ($count = $stmt->rowCount() == 1) {
                    return "Usuario actualizado correctamente";
                } else {
                  
                    return "Error al actualizar usuario";
                }
            }
            
            catch(PDOException $e) {
                return "Error: " . $e->getMessage();
            }
        } else if (strtolower($peticion->method) != 'put') {
            return "Error: " . $peticion->method . " No permitido";
        } else {
            
            return "Error: Solo hay que introducir el id del usuario";
        }
    }

    public function borrarUsuario($peticion) {
        if (strtolower($peticion->method) == 'delete' && count($peticion->url_elements) == 3) {
            
            try {
                
                $stmt = $this->userDB->prepare("DELETE FROM usuarios WHERE id_usuarios=?");
                
                $stmt->bindParam(1, $peticion->url_elements[2]);
                
                $stmt->execute();
                if ($count = $stmt->rowCount() == 1) {
                    return "Usuario borrado correctamente";
                } else {
                    return "Error al borrar el usuario";
                }
            }
            
            catch(PDOException $e) {
                return "Error: " . $e->getMessage();
            }
        } else if (strtolower($peticion->method) != 'delete') {
            return "Error: " . $peticion->method . " no permitido";
        } else {
            
            return "Error: Solo se ha de intruducir el id del usuario";
        }
    }
    
    public function login($peticion) {
        if (strtolower($peticion->method) == 'post' && count($peticion->url_elements) == 2) {
            
            $stmt = $this->userDB->prepare("SELECT nombre FROM usuarios WHERE nombre=? AND pass=?");
            
            $stmt->bindParam(1, $peticion->parameters['nombre']);
            $stmt->bindParam(2, $peticion->parameters['pass']);
            
            $stmt->execute();
            
            if ($result = $stmt->fetchAll(PDO::FETCH_COLUMN, 0)) {
                
                return "Bienvenido/a " . $result[0];
            } else {
                return "Usuario o contraseña incorrectos";
            }
        } else if (strtolower($peticion->method) != 'post') {
            return "Error: " . $peticion->method . " no permitido";
        } else {
            
            return "Error: No se ha de introducir paramatros a traves de la url";
        }
    }
   

}
