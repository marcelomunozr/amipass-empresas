<?php
    require_once('functions/db.php');
    $method = $_SERVER['REQUEST_METHOD'];
    if($method != 'POST'){
        header("HTTP/1.0 405 Method not allowed", 405);
        echo 'Metodo no permitido, solo POST';
        exit();
    }else{
        if(!empty($_POST)){
            $salida = array();
            $datos = array();
            $datos['nombre']        = ($_POST['nombre']) ? $_POST['nombre'] : '';
            $datos['email']         = ($_POST['email']) ? $_POST['email'] : '';
            $datos['telefono']      = ($_POST['fono']) ? $_POST['fono'] : '';
            $datos['cargo']         = ($_POST['cargo']) ? $_POST['cargo'] : '';
            $datos['empresa']       = ($_POST['empresa']) ? $_POST['empresa'] : '';
            $datos['por-medio']     = ($_POST['por-medio']) ? $_POST['por-medio'] : '';
            if($datos['por-medio'] == 'correo'){
				$datos['telefono'] = 0;
            } elseif($datos['por-medio'] == 'llamada'){
				$datos['email'] = 0;
            }
            $res = insertData($datos);
            if($res === true){
                $salida['exito']    = 1;
                $salida['msg']      = 'Formulario ingresado correctamente';
            }else{
                $salida['exito']    = 0;
                $salida['msg']      = 'Error al salvar el formulario: '.$res;
            }
            header('Content-Type: application/json');
            echo json_encode($salida);
            exit();
        }else{
            header("HTTP/1.0 400 Bad Request", 400);
            exit();
        }
    }
?>