<?php
  session_start();      
  session_destroy();
  Header("Location: /adm/index.html");
?>