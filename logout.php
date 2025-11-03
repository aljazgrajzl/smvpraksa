<?php
// Začni sejo, če še ni začeta
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Počisti vse sejne spremenljivke
$_SESSION = array();

// Uniči sejo
session_destroy();

// Izbriši sejni piškotek
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// Zagotovi, da so vsi bufferji izpraznjeni
while (ob_get_level()) {
    ob_end_clean();
}

// Preusmeri na prijavno stran
header("Location: index.php");
exit();
?>