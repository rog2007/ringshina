<?php

require('func.php');

$str = '';
if (isset($_POST['obrsp'])) {
    $str = UpdateSp1();
}
if (isset($_POST['obrspakb'])) {
    $str = UpdateSpAKB();
}
if (isset($_POST['obnul'])) {
    $loadId = filter_input(INPUT_POST, 'load_id', FILTER_VALIDATE_INT);
    if ((int)$loadId < 0) {
        echo '<p>Укажите какой прайс обнуляем</p><a href="/lprices/">К началу загрузки прайса</a>';
        exit;
    }
    $res = query('SELECT * FROM parser WHERE id=:load_id', [':load_id' => $loadId]);
    if ($res['result'] === false) {
        echo '<p>Поиск загрузки. Ошибка обращения к БД. ' . dbLastErrorToString($res['error']) . '</a>';
        exit;
    }
    if (count($res['data']) == 0) {
        echo '<p>Такая загрузка не найдена. Повторите попытку или обратитесь к разработчику</p>' .
            '<a href="/lprices/">К началу загрузки прайса</a>';
        exit;
    }
    $rs = $res['data'][0];
    $sqlsup = ($rs->suppl == 2 ? '(id_sup=2 OR id_sup=6)' : 'id_sup=' . $rs->suppl);
    $sqlsup .= ($rs->tyres != $rs->wheels ? ($rs->tyres == 1 ? ' AND tab1_id=1' : ' AND tab1_id=2') : '') . ' ' .
        $rs->obnul;
    $result = execute('UPDATE total_suppl LEFT JOIN total ON total_id=id_tov SET cnt_sup=0 WHERE ' . $sqlsup);
    $str .= "<h2>Логи</h2><ul>";
    if ($result === false) {
        $str .= '<li class="error">Ошибка (обнуления) ' . dbLastErrorToString() . ';</li>';
    } else {
        $str .= '<li>Обнулил наличие у поставщика по фильтру (' . $result . ') строк;</li>';
        $str .= UpdateSp1();
    }
}

if (isset($_POST['load_pnew']) || (isset($arg[0]) && $arg[0])) {
    $loadId = filter_input(INPUT_POST, 'load_id', FILTER_VALIDATE_INT);
    $loadId = ($loadId ? $loadId : $arg[0]);
    if ((int)$loadId < 0) {
        echo '<p>Укажите какой прайс загружаем</p><a href="/lprices/">К началу загрузки прайса</a>';
        exit;
    }
    $res = query('SELECT * FROM parser WHERE id=:load_id', [':load_id' => $loadId]);
    if ($res['result'] === false) {
        echo '<p>Поиск загрузки. Ошибка обращения к БД. ' . dbLastErrorToString($res['error']) . '</a>';
        exit;
    }
    $idfrom = filter_input(INPUT_POST, 'idfrom', FILTER_VALIDATE_INT);
    $idto = filter_input(INPUT_POST, 'idto', FILTER_VALIDATE_INT);
    if(!$idfrom) {
        $idfrom = 0;
    }
    if(!$idto) {
        $idto = 10000;
    }
    $res = query('SELECT * FROM parser WHERE id=:load_id', [':load_id' => $loadId]);
    if ($res['result'] === false) {
        echo '<p>Поиск загрузки. Ошибка обращения к БД. ' . dbLastErrorToString($res['error']) . '</a>';
        exit;
    }
    if (count($res['data']) == 0) {
        echo '<p>Такая загрузка не найдена. Повторите попытку или обратитесь к разработчику</p>' .
            '<a href="/lprices/">К началу загрузки прайса</a>';
        exit;
    }
    $rs = $res['data'][0];
    /* константы */
    $allbrands[0] = sql2arr("SELECT tb3_id as id,tb3_nm as nm,'' as reg,alt as alt FROM tab3");
    $allbrands[1] = sql2arr("SELECT tb3_id as id,tb3_nm as nm,'' as reg,alt as alt FROM tab3 where tb3_tov_id=1");
    $allbrands[2] = sql2arr("SELECT tb3_id as id,tb3_nm as nm,'' as reg,alt as alt FROM tab3 where tb3_tov_id=2");
    $allbrands[3] = sql2arr("SELECT `id`,`name` as nm,'' as reg,alt FROM akb_brand");
    $allshlack = sql2arr("SELECT id as id,nm as nm,'' as reg,'' as alt FROM shlak");
    $allomolog = sql2arr("SELECT id as id,om as nm,'' as reg,alt FROM omolog where omvis=1");
    $allrof = sql2arr("SELECT id as id,var as nm,'' as reg,alt FROM run_flat where rvis=1");
    $allpoll = sql2arr("SELECT `id`, `name` as nm, '' as reg, alt FROM akb_rvrt");
    $allklem = sql2arr("SELECT `id`, `name` as nm, '' as reg, alt FROM akb_klemy");
    /* -константы */
    $all_rows = 0;
    $all_rows_check = 0;
    $fl_noidtyre = 0;
    $sql_id = 'INSERT INTO power (id_pow, id, price_name, cnt, price, priceb, cnt1, price1, priceb1, sspid) VALUES ';
    $sql_noid_tyre = 'INSERT INTO power (id_pow, id, t1, brand, t3, model, t4, prof, p_w, p_h, diam, gruz, speed, ' .
        'price_name, run, cnt, price, priceb, cnt1, price1, priceb1, sspid) VALUES ';
    $sql_noid_disc = "insert into power (id_pow,id,t1,brand,t3,t4,model,prof,diam,gruz,speed,ship,p_w,tp,t2,price_name,cnt,price,priceb,cnt1,price1,priceb1,sspid) values ";
    $sql_nobr = "insert into power (id_pow,id,price_name,cnt,price,priceb,cnt1,price1,priceb1,sspid) values";
// переменные для АКБ = начало =
    $sql_id_akb = "insert into akb_tovar_temp (id_tov, full_name, akb_price,akb_count, id_sup) values ";
    $sql_noid_akb = "insert into akb_tovar_temp (id_tov, full_name, name_brand, id_brand,
      name_model, id_model, name_volt, id_volt, name_volume, id_volume, rvrt, klem, akb_price,akb_count, id_sup) values ";
    $sqlAKBNoIdArray = array();
    $sqlAKBIdArray = array();
// переменные для АКБ = конец =
    $all_id = 0;
    $all_noid = 0;
    $all_tyres = 0;
    $all_discs = 0;
    $all_nobr = 0;
    $supplierid = $rs->suppl;
    if ($rs->fileformat == 'excel') {

        if (strpos($rs->filename, 'http://') !== false) {

            $fln_name = explode("[|]", eval('return "' . $rs->filename . '";'));
            copy($fln_name[0], $_SERVER["DOCUMENT_ROOT"] . '/adm/prices/' . $fln_name[4]);
            $loadfile = $_SERVER['DOCUMENT_ROOT'] . '/adm/prices/' . $fln_name[4];
        } else {
            if ($_FILES["xls"]["tmp_name"]) {
                $str .= LoadFilToFtp($rs->filename, $_FILES["xls"]["tmp_name"]);
            } else {
                $str .= '<p>Файл не указан. Загрузка производится из файла загруженного на сервер</p>';
            }
            $loadfile = $_SERVER['DOCUMENT_ROOT'] . '/adm/prices/' . $rs->filename . '.xls';
        }

        $filesize = filesize($loadfile);
        if (!is_file($loadfile)) {
            exit('Ошибка при доступе к файлу ' . $loadfile);
        }
        require_once 'reader.php';
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('CP1251');
        $data->read($loadfile);
        $key = 0;
        if ($rs->tyres || $rs->wheels) {

            $rec_area = sql2arr2('select om,alr from omolog where omvis=1');
            $sql1 = 'SELECT trim(id_tov_sup) as id FROM total_suppl WHERE id_sup=' . $supplierid;
            $idsuppl = sql2arr2($sql1);
            mysql_query('delete from power;');
            mysql_query('alter TABLE power AUTO_INCREMENT=1;');
        }
        if ($rs->akb) {

            mysql_query('delete from akb_tovar_temp;');
            mysql_query('alter TABLE akb_tovar_temp AUTO_INCREMENT=1;');
        }

        $str .= "<p>Временная таблица очищена</p>";

        $index = 0;

        $sheetsArra = explode(',', $rs->sheets);
        $all_rows = 0;

        while (isset($data->boundsheets[$key])) {

            //if ($key == $rs->sheets){
            if (in_array($key, $sheetsArra)) {

                $all_rows += $data->sheets[$key]['numRows'];
                for ($i = 1; $i <= $all_rows; $i++) {

                    $index++;
                    if ($index < $idfrom) {
                        continue;
                    }
                    if ($index > $idto) {
                        break;
                    }

                    for ($j = 1; $j <= $data->sheets[$key]['numCols']; $j++) {

                        if (isset($data->sheets[$key]['cells'][$i][$j])) {
                            $d[$j] = iconv("windows-1251", "utf-8",
                                charset_x_win($data->sheets[$key]['cells'][$i][$j]));
                        } else {

                            $d[$j] = '';
                        }
                    }
// работа с количеством
                    $cnt = WorkThisCount($rs->ccnt);
                    if ((!is_numeric($cnt[1]) || $cnt[1] <= 0) && (!is_numeric($cnt[2]) || $cnt[2] <= 0)) {
                        continue;
                    }
// работа с ценой
                    $price = WorkThisPrice($rs->ccost);

                    if ((!is_numeric($price[1]) || empty($price[1]) || $price[1] == 0) && (!is_numeric($price[2]) || empty($price[2]) || $price[2] == 0)) {
                        continue;
                    }
// работа с ценой закупки
                    $priceb = WorkThisPrice($rs->ccostb);
                    if ((!is_numeric($priceb[1]) || empty($priceb[1]) || $priceb[1] == 0) && (!is_numeric($priceb[2]) || empty($priceb[2]) || $priceb[2] == 0)) {
                        continue;
                    }
// работа с артиклом
                    $artar = WorkThisArt($rs->cart);
// работа с наименованием
                    $cname = eval('return "' . $rs->cname . '";');
                    //       echo $cname;
                    //echo "<br/>";
                    if (empty($cname)) {
                        continue;
                    }

                    $all_rows_check++;
                    if ($artar[1] == '0' || $artar[1] == '') {
                        $art = $artar[2];
                    } else {
                        $art = $artar[1];
                    }
                    $art = trim($art);
                    if ($art == "-1" || $art == "0" || $art == "" || !in_array($art, $idsuppl)) {

                        if ($rs->tyres || $rs->wheels) {

                            parse_data();
                        } else {

                            parseAKB();
                        }
                    } else {

                        if ($rs->tyres || $rs->wheels) {

                            $sql_id .= ($all_id > 0 ? "," : "") . "(" . $all_rows_check . ",'" . str_replace("'", "\'",
                                    $art) .
                                "','" . str_replace("'", "\'",
                                    $cname) . "'," . $cnt[1] . "," . $price[1] . "," . $priceb[1] .
                                "," . ($cnt[2] ? $cnt[2] : "0") . "," . ($price[2] ? $price[2] : "0") . "," . ($priceb[2] ? $priceb[2] : "0") .
                                "," . $supplierid . ")";
                        } else {
                            array_push($sqlAKBIdArray, "('" . $art . "', '" . str_replace("'", "\'", $cname) .
                                "', " . $price . ", " . $cnt . ", " . $supplierid . ')');
                        }
                        $all_id++;
                    }
                }
            }
            $key++;
        }
    } elseif ($rs->fileformat == "xml") {

        $fln_name = explode("[|]", eval('return "' . $rs->filename . '";'));

        if ($fln_name[1] == "") {

            $xml = simplexml_load_file($rs->filename);
        } else {

            $loadfile = $_SERVER['DOCUMENT_ROOT'] . '/adm/prices/' . $fln_name[4];
            $conn_id = ftp_connect($fln_name[1]);
            $login_result = ftp_login($conn_id, $fln_name[2], $fln_name[3]);
            ftp_pasv($conn_id, TRUE);
            if (ftp_get($conn_id, $loadfile, $fln_name[0], FTP_BINARY)) {
                ftp_close($conn_id);
                $str .= "<p>Произведена запись в " . $loadfile . "</p>";
            } else {
                ftp_close($conn_id);
                echo "<p>Не удалось завершить операцию загрузки файла с ftp</p>";
                return '';
            }
            $xml = simplexml_load_file($loadfile);
        }
        $sql1 = 'SELECT trim(id_tov_sup) as id FROM total_suppl WHERE id_sup=' . $supplierid;
        $idsuppl = sql2arr2($sql1);
        mysql_query('delete from power;');
        $str .= "<p>Временная таблица очищена</p>";
        $index = 0;
        foreach ($xml->shop->offers->offer as $tires) {

            $index++;
            if ($index < $idfrom) {
                continue;
            }
            if ($index > $idto) {
                break;
            }

            $all_rows++;
            $cnt = WorkThisCount($rs->ccnt);
            if ((!is_numeric($cnt[1]) || $cnt[1] <= 0) && (!is_numeric($cnt[2]) || $cnt[2] <= 0)) {
                continue;
            }
/// работа с ценой
            $price = WorkThisPrice($rs->ccost);
            if ((!is_numeric($price[1]) || empty($price[1]) || $price[1] == 0) && (!is_numeric($price[2]) || empty($price[2]) || $price[2] == 0)) {
                continue;
            }
// работа с ценой закупки
            $priceb = WorkThisPrice($rs->ccostb);
            if ((!is_numeric($priceb[1]) || empty($priceb[1]) || $priceb[1] == 0) && (!is_numeric($priceb[2]) || empty($priceb[2]) || $priceb[2] == 0)) {
                continue;
            }
// работа с артиклом
            if (!strpos($rs->cart, "['id']")) {

                $art = eval("return \"" . $rs->cart . "\";");
            } else {
                $art = $tires['id'];
            }

            $art = iconv("windows-1251", "utf-8", charset_x_win(trim($art)));

// работа с наименованием
            $cname = eval('return "' . $rs->cname . '";');
            $cname = str_replace('"', '', iconv("windows-1251", "utf-8", charset_x_win($cname)));
            if (empty($cname)) {
                continue;
            }

            $all_rows_check++;

            /* if($artar[1]=='0' || $artar[1]=='') {

              $art = $artar[2];
              } else {

              $art = $artar[1];
              } */
            $art = trim($art);

            if ($art == "-1" || $art == "0" || $art == "" || !in_array($art, $idsuppl)) {

                parse_data();
            } else {

                $sql_id .= ($all_id > 0 ? "," : "") . "(" . $all_rows_check . ",'" . str_replace("'", "\'",
                        $art) . "','" . str_replace("'", "\'",
                        $cname) . "'," . $cnt[1] . "," . $price[1] . "," . $priceb[1] . "," . ($cnt[2] ? $cnt[2] : "0") . "," . ($price[2] ? $price[2] : "0") . "," . ($priceb[2] ? $priceb[2] : "0") . "," . $supplierid . ")";
                $all_id++;
            }
        }
    }

    $str .= "<ul class=\"ul-res\">";
    $str .= "<li>Всего записей: <b>" . $all_rows . "</b> ( прошли проверку: " . $all_rows_check . ")</li>";
    if ($all_rows_check > 0) {
        if ($idfrom == 0) {
            if ($rs->akb) {
                mysql_query("update akb_suppl set cnt_sup=0 where id_sup=" . $rs->suppl);
                $str .= "<li>Обнуление количества в базе с учетом фильтра для данного поставщика <b>" . mysql_affected_rows() . "</b> строк;</li>";
            } else {
                $sqlsup = ($rs->suppl == 2 ? '(id_sup=2 or id_sup=6)' : 'id_sup=' . $rs->suppl);
                $sqlsup .= ($rs->tyres != $rs->wheels ? ($rs->tyres == 1 ? ' and tab1_id=1' : ' and tab1_id=2') : '') . ' ' . $rs->obnul;

                $result = execute('UPDATE total_suppl LEFT JOIN total ON total_id=id_tov SET cnt_sup=0 WHERE ' . $sqlsup);
                if ($result === false) {
                    $strres .= '<li class="error">Ошибка при обнулении данных для поставщика ' . dbLastErrorToString() . ';</li>';
                } else {
                    $strres .= '<li>Обнуление количества в базе с учетом фильтра для данного поставщика: ' . $result . ' строк</li>';
                }
            }
        }
    }
    if ($all_id > 0) {
        if ($rs->akb) {
            $sql_id_akb .= implode(',', $sqlAKBIdArray);
            mysql_query($sql_id_akb . implode(',', $sqlAKBIdArray));
        } else {
            $result = execute($sql_id);
            if ($result === false) {
                $str .= '<li class="error">Ошибка (При поиске идентификаторов) ' . dbLastErrorToString() . ';</li>';
            } else {
                $str .= '<li>Найдены id-ки: ' . $result . '</li>';
            }
            $result = execute('UPDATE total_suppl RIGHT JOIN power ON power.id = total_suppl.id_tov_sup ' .
                ' SET cnt_sup = power.cnt, prs_sup = power.price, prsb_sup = power.priceb, suppl_name = price_name ' .
                'WHERE id_sup=' . $rs->suppl . ' AND id_tov_sup IS NOT NULL AND power.price > 0 AND power.cnt > 0');
            $message = 'Обновлили вспомогательную таблицу с ценами и количеством поставщиков по коду поставщика';
            if ($result === false) {
                $str .= '<li class="error">Ошибка (' . $message . ') ' . dbLastErrorToString() . ';</li>';
            } else {
                $str .= '<li>' . $message . ': ' . $result . ' строк</li>';
            }
            if ($rs->suppl == 2) {
                $result = execute('UPDATE total_suppl RIGHT JOIN power ON power.id = total_suppl.id_tov_sup ' .
                    ' SET cnt_sup = power.cnt, prs_sup = power.price, prsb_sup = power.priceb, suppl_name = price_name ' .
                    'WHERE id_sup = 6 AND id_tov_sup IS NOT NULL AND power.price > 0 AND power.cnt > 0');
                $message = 'Обновлили вспомогательную таблицу с ценами и количеством поставщиков по коду поставщика Пауэр Розница';
                if ($result === false) {
                    $str .= '<li class="error">Ошибка (' . $message . ') ' . dbLastErrorToString() . ';</li>';
                } else {
                    $str .= '<li>' . $message . ': ' . $result . ' строк</li>';
                }
            }
            $result = execute('DELETE power FROM power');
            $message = 'Удаление обработанных строк';
            if ($result === false) {
                $str .= '<li class="error">Ошибка (' . $message . ') ' . dbLastErrorToString() . ';</li>';
            } else {
                $str .= '<li>' . $message . ': ' . $result . ' строк</li>';
            }
        }
    } else {
        $str .= "<li>Найдены id-ки: <b>0</b></li>";
    }
    $str .= "<li>Не найдены id-ки:</li>";
    $akbNoId = count($sqlAKBNoIdArray);
    if ($akbNoId > 0) {

        $sql_noid_akb .= implode(',', $sqlAKBNoIdArray);
        mysql_query($sql_noid_akb);
        $str .= "<li>АКБ отсутствует ID: <b>" . $akbNoId . "</b> (" . mysql_affected_rows() . ")</li>";
    }
    if ($all_nobr > 0) {
        mysql_query($sql_nobr);
        $str .= "<li>Не определен тип товара: <b>" . $all_nobr . "</b> (" . mysql_affected_rows() . ")</li>";
    } else {
        $str .= "<li>Не определен тип товара: <b>0</b></li>";
    }
    if ($all_tyres > 0) {
        $result = execute($sql_noid_tyre);
        $message = 'Загружены шины';
        if ($result === false) {
            $str .= '<li class="error">Ошибка (' . $message . ') ' . dbLastErrorToString() . ';</li>';
        } else {
            $str .= '<li>' . $message . ': ' . $all_tyres . ' (' . $result . ' строк)</li>';
        }
    } else {
        $str .= "<li>Загружены шины: <b>0</b></li>";
    }
    if ($all_discs > 0) {
        $result = execute($sql_noid_disc);
        $message = 'Загружены диски';
        if ($result === false) {
            $str .= '<li class="error">Ошибка (' . $message . ') ' . dbLastErrorToString() . ';</li>';
        } else {
            $str .= '<li>' . $message . ': ' . $all_discs . ' (' . $result . ' строк)</li>';
        }
    } else {
        $str .= "<li>Загружены диски: <b>0</b></li>";
    }
    $str .= "</ul>";
    $str .= ObrabotkaPower($loadId);
}