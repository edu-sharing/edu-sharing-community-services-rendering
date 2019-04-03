<?php
header('Content-Disposition :attachment; filename="'.$_GET['filename'].'"');
header('Location: ' . $_GET['downloadUrl']);
