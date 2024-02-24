<?php
$tov = IdByName($arg[0], "tab1", "tb1_id", "translit");
$str = "<div class=\"cont\"><h1>Распродажа " . ($tov == 1 ? "шин" : "дисков") . "</h1>";
$res = NomenRasp($tov);
while ($tvone = mysql_fetch_object($res)) {
    if ($tvone->T4Pic != '-') {
        if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/images/tovar/" . ($tov == 1 ? "tyres" : "discs") . "/" . $tvone->T4Pic)) {
            $pic = "/img_resize.php?newh=105&amp;infile=" . urlencode("/images/tovar/" . ($tov == 1 ? "tyres" : "discs") . "/" . $tvone->T4Pic);
        } else $pic = "/images/pic/fishtyres.gif";
    } else {
        $pic = "/images/pic/fishtyres.gif";
    }
    $str .= "<div class=\"tvpod\"><div class=\"left\"><div class=\"image\"><a href=\"/card/" . $tvone->total_id . ".html\" style=\"background:url({$pic}) no-repeat center center\"></a></div>" .
        ($tov == 1 ? "<div class=\"icons\">" . ($tvone->tab10_id ? "<img src=\"/images/des/icons/" . $tvone->t10p . "_s.jpg\" class=\"seas\"/>" : "") . ($tvone->tab2_id ? "<img src=\"/images/des/icons/" . $tvone->t2p . "_s.jpg\" class=\"auto\"/>" : "") . "</div>" : "") .
        "</div><a href=\"#\" class=\"name\">" . $tvone->all_name . "</a><div class=\"frm\">
    <form method=\"post\" name=\"inb" . $tvone->total_id . "\" action=\"/bask/" . $tvone->total_id . "/add.html\"><input type='text' name='t' value='4' size='1' maxlength=\"3\" class=\"cntbsk\">
    <input type='submit' value='В корзину' name='add' class=\"inbsk\"></form></div></div>";
}
$str .= "</div>";
