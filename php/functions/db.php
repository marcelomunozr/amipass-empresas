<?php
    function createConnection(){
        $conn = false;
        try{
            $conn = new PDO('mysql:host=mysqlpro.intercity.cl;dbname=amipass_db', 'amipass_sql', 'amsql*2011');
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $e){
            echo "ERROR: " . $e->getMessage();
        }
        return $conn;
    }

    function insertData($data = array()){
        $conn = createConnection();
        $query = $conn->prepare("INSERT INTO registros_conversion (nombre, email, telefono, cargo, empresa, via_contacto)
        VALUES(:nombre, :email, :telefono, :cargo, :empresa, :via_contacto)");
        $nombre         = RemoveXSS($data['nombre']);
        $email          = RemoveXSS($data['email']);
        $telefono       = RemoveXSS($data['telefono']);
        $cargo          = RemoveXSS($data['cargo']);
        $empresa        = RemoveXSS($data['empresa']);
        $via_contacto   = RemoveXSS($data['por-medio']);
        $fecha_registro = date('Y-m-d H:i:s');
        $values = array($nombre, $email, $telefono, $cargo, $empresa, $via_contacto);
        $res = $query->execute(array(
            ':nombre' => $nombre,
            ':email' => $email,
            ':telefono' => $telefono,
            ':cargo' => $cargo,
            ':empresa' => $empresa,
            ':via_contacto' => $via_contacto
        ));
        if($res){
            $datos = array();
            $datos['nombre'] = $nombre;
            $datos['email'] = $email;
            $datos['telefono'] = $telefono;
            $datos['cargo'] = $cargo;
            $datos['empresa'] = $empresa;
            $datos['via_contacto'] = $via_contacto;
            $datos['fecha_registro'] = $fecha_registro;
            $destinatarios = array(
                'Marcelo Amengual' => 'm.amengual@amipass.com',
                'Gloria Opazo ' => 'g.opazo@amipass.com'
            );
            sendMail($datos, $destinatarios);
            return true;
        }else{
            return $conn->errorInfo();
        }
    }

    function sendMail($values = array(), $destinatarios = array()){
        $header .= 'From: Amipass Empresas <no-reply@amipass.com>' . " \r\n";
        $header .= "X-Mailer: PHP/" . phpversion() . " \r\n";
        $header .= "Mime-Version: 1.0 \r\n";
        $header .= "Content-type: text/html; charset=utf-8 \r\n";
        $mensaje='Se recibió el siguiente correo desde <a href="http://www.amipass.com/empresas/">Amipass Empresas</a> : <br>
        Nombre: '.$values['nombre'].'<br>
        Email: '.$values['email'].'<br>
        Teléfono: '.$values['telefono'].'<br>
        Cargo: '.$values['cargo'].'<br>
        Empresa: '.$values['empresa'].'<br>
        Contactar por medio de: '.$values['via_contacto'].'<br>';
        $asunto = 'Datos de Amipass Empresas';
        $para = '';
        $indice = 0;
        foreach($destinatarios as $nombre=>$destinatario){
            $indice += 1;
            if(count($destinatarios) > 1 && $indice < count($destinatarios)){
                $para .= $nombre.' <'.$destinatario.'>, ';
            }else{
                $para .= $nombre.' <'.$destinatario.'>';
            }
        }
        if(mail($para, $asunto, $mensaje, $header)){
            return true;
        }else{
            return false;
        }
    }
    
    
    function RemoveXSS($val) {
        // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
        // this prevents some character re-spacing such as <java\0script>
        // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
        $val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val);
        // straight replacements, the user should never need these since they're normal characters
        // this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29>
        $search = 'abcdefghijklmnopqrstuvwxyz';
        $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $search .= '1234567890!@#$%^&*()';
        $search .= '~`";:?+/={}[]-_|\'\\';
        for ($i = 0; $i < strlen($search); $i++) {
            // ;? matches the ;, which is optional
            // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars
            // &#x0040 @ search for the hex values
            $val = preg_replace('/(&#[x|X]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
            // &#00064 @ 0{0,7} matches '0' zero to seven times
            $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
        }
        // now the only remaining whitespace attacks are \t, \n, and \r
        $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
        $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
        $ra = array_merge($ra1, $ra2);
        $found = true; // keep replacing as long as the previous round replaced something
        while ($found == true) {
            $val_before = $val;
            for ($i = 0; $i < sizeof($ra); $i++) {
                $pattern = '/';
                for ($j = 0; $j < strlen($ra[$i]); $j++) {
                    if ($j > 0) {
                        $pattern .= '(';
                        $pattern .= '(&#[x|X]0{0,8}([9][a][b]);?)?';
                        $pattern .= '|(&#0{0,8}([9][10][13]);?)?';
                        $pattern .= ')?';
                    }
                    $pattern .= $ra[$i][$j];
                }
                $pattern .= '/i';
                $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
                $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
                if ($val_before == $val) {
                    // no replacements were made, so exit the loop
                    $found = false;
                }
            }
        }
        return $val;
    }

    

?>