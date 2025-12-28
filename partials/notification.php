<?php

if (isset($_SESSION["INFO"])) {
    unset($_SESSION["INFO"]);
}

if (isset($_SESSION["SUCCESS"])) {
    unset($_SESSION["SUCCESS"]);
}

if (isset($_SESSION["ERROR"])) {
    showError("", $_SESSION["ERROR"]);
    unset($_SESSION["ERROR"]);
}
?>