<?php
  function checkmail($mail)
  {
   $mail=trim($mail);
   if (strlen($mail)==0) return -1;
   if (!preg_match("#^([a-z0-9-_\.]+)@(([a-z0-9-]+\.)+(com|ru|net|org|mil|edu|gov|arpa|info|biz|inc|name|[a-z]{2})|[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})$#i",$mail))
    return -1;
   return $mail;
  }
?>