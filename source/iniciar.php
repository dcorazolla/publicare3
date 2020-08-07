<?php
/**
* Publicare - O CMS Público Brasileiro
* @description 
* @copyright MIT © 2020
* @package publicare
*
 * Este arquivo é parte do programa Publicare
 * 
 * Copyright (c) 2020 Publicare
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
*/


// inicia sessao caso não esteja iniciada ainda
if (!isset($_SESSION)) 
{
    // definindo nome de sessao proprio, para evitar roubo de sessao/cookie
//    session_name(md5('pbl'.$_SERVER["REMOTE_ADDR"]));
    
    // utilizando cookies
    ini_set("session.use_cookies", true);
    // apenas cookies
    ini_set("session.use_only_cookies", true);
    // permite apenas sessoes inicializadas por aqui
    ini_set("session.use_strict_mode", true);
    // bloqueia acesso ao cookie de sessao por scripts
    ini_set("session.cookie_httponly", true);
    
    session_start();
}

// inclui funcoes requeridas pelo publicare
require ("funcoes.php");

// define timezone
date_default_timezone_set($PBLCONFIG["portal"]["tz"]);

// inicia tratamento de URL
$url = htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, "UTF-8");

//xd($url);

// removendo index.php da url
$url = preg_replace('/index\.php/', '', $url);
// removendo slash repetido
$url = preg_replace('/(\/)\1+/', '/', $url);
$_SERVER['REQUEST_URI'] = $url;
// retira variaveis passadas por _GET da url
$aurl = preg_split("[\?]", $url);
$url = $aurl[0];

$vurlpbl = preg_split("[\/]", $PBLCONFIG["portal"]["url"]);


// inclusao das classes publicare
require ("constantes.php");
require ("lib/adodb/adodb-exceptions.inc.php");
require ("lib/adodb/adodb.inc.php");
require ("data.php");
require ("lib/phpmailer/src/Exception.php");
require ("lib/phpmailer/src/PHPMailer.php");
require ("lib/phpmailer/src/SMTP.php");
require ("lib/phpqrcode/qrlib.php");

// iniciando banco de dados
$_db = new DBLayer($PBLCONFIG);


$incluir = "";
$amigavel = "";
$action = "";
$path = $_SERVER["DOCUMENT_ROOT"];
$cod_root = $PBLCONFIG["portal"]["objroot"];

// caso tenha array de dominios definido, verifica codigo objeto root do dominio
//if (isset($_dominios))
//{
//    $dominio = htmlspecialchars($_SERVER['HTTP_HOST'], ENT_QUOTES, "UTF-8");
//    foreach ($_dominios as $dom => $dom1) 
//    {
//        if ($dominio == $dom) $cod_root = $_dominios[$dominio];
//    }
//}

$cod_objeto = $cod_root;
$cod_blob = 0;
//x($url);
$url = $PBLCONFIG["portal"]["pasta"]!=""?str_ireplace("/".$PBLCONFIG["portal"]["pasta"]."/", "/", $url):$url;
$url = preg_replace("[\/+]", "/", $url);
$arrUrl = preg_split("[\/]", $url);
//xd($url);


if (isset($arrUrl[1]))
{
    switch ($arrUrl[1])
    {
        // blob
        case "blob":
            $cod_blob = isset($arrUrl[3])?$arrUrl[3]:$cod_blob;
            $action = "/blob/".$arrUrl[2];
            break;
        // includes
        case "include":
            include('includes/' . $arrUrl[2] . '.php');
            exit();
            break;

        // formulario de login
        case "login":
//        case "cadastro":
//        case "esquecisenha":
            if (file_exists($_SERVER["DOCUMENT_ROOT"]."/html/template/view_".$arrUrl[1].".php"))
            {
                $action = "/html/".$arrUrl[1];
                if (isset($arrUrl[2])) 
                {
                    $cod_objeto = identificaCodigoObjeto($arrUrl[2], 0);
                    if ($cod_objeto==0)
                    {
                        $amigavel = $arrUrl[2];
                    }
                }
            }
            else
            {
                $incluir = "includes/".$arrUrl[1].".php";
            }
            break;
            
        // chamando arquivos pasta objects
        case "html":
            if ($arrUrl[2] == "objects")
            {
                $tempFile = "";
                for ($i = 3; $i < count($arrUrl); $i++)
                {
                    $tempFile .= "/" . $arrUrl[$i];
                }
                if (file_exists($path . "/html/objects" . $tempFile))
                {
                    $incluir = $path . "/html/objects" . $tempFile;
                }
                if (file_exists($path . "/html/objects" . $tempFile.".php"))
                {
                    $incluir = $path . "/html/objects" . $tempFile.".php";
                }
//                xd($incluir);
            }
            break;

        // /content/view
        case "content":
            if ($arrUrl[2] == "view")
            {
                $action = "/content/view";
                $cod_objeto = identificaCodigoObjeto($arrUrl[3], $cod_root);
            }
            break;

        // manage ou do
        case "manage":
        case "do":
            $action = "/" . $arrUrl[1] . "/" . $arrUrl[2];
            if (isset($arrUrl[3]))
            {
                $cod_objeto = identificaCodigoObjeto($arrUrl[3], $cod_root);
            }
//            xd($cod_objeto);
            break;

        // nenhum caso conhecido, tenta url amigavel
        default:
            $action = "/content/view";
            // remove extensoes
            $temp = preg_split("[\.]", $arrUrl[1]);
            $arrUrl[1] = $temp[0];
            // garante url sem caracteres especiais
            $arrUrl[1] = limpaString($arrUrl[1]);
            // tudo para minusculo
            $arrUrl[1] = strtolower($arrUrl[1]);
            $amigavel = filter_var($arrUrl[1], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            break;
    }
}
else
{
    $action = "/content/view";
}




// verifica se eh url amigavel
if ($amigavel != "")
{
    // procura no banco pela url amigavel
    $sql = "SELECT ".$_db->tabelas["objeto"]["nick"].".".$_db->tabelas["objeto"]["colunas"]["cod_objeto"]." AS cod_objeto "
            . " FROM ".$_db->tabelas["objeto"]["nome"]." ".$_db->tabelas["objeto"]["nick"]." "
            . " WHERE ".$_db->tabelas["objeto"]["nick"].".".$_db->tabelas["objeto"]["colunas"]["url_amigavel"]." = '" . $amigavel . "'";
    $rs = $_db->ExecSQL($sql, 0, 1);
    while ($row = $rs->FetchRow())
    {
        $cod_objeto = (int)$row["cod_objeto"];
    }
}



// verifica existencia de action
if ($action=="" || isset($_GET["action"]) || isset($_POST["action"]))
{
    // se nao tiver, verifica action em get
    if (isset($_GET["action"])) $action = htmlspecialchars ($_GET["action"], ENT_QUOTES, "UTF-8");
    // se nao tiver em get, verifica em post
    elseif (isset($_POST["action"])) $action = htmlspecialchars ($_POST["action"], ENT_QUOTES, "UTF-8");
    // se nao tiver em post, define action como "/content/view"
    else $action = "/content/view";
}

$page = new Pagina($_db, $cod_objeto, $cod_blob);

// se for inclusao de arquivo, inclui o mesmo e termina execucao
if ($incluir != "") {
    $incluir = str_replace("\.\.\/", "", $incluir);
    include($incluir);
    exit();
}
