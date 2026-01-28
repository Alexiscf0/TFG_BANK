<?php
if (!extension_loaded("mongodb")) {
    die("ERROR: La extensión 'mongodb' NO está cargada en php.ini");
}
echo "¡ÉXITO! La extensión está funcionando.";
?>