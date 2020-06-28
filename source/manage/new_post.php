<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @description Arquivo
 * @copyright GPL © 2007
 * @package publicare
 *
 * Este arquivo é parte do programa Publicare
 * Publicare é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da Licença Pública Geral GNU 
 * como publicada pela Fundação do Software Livre (FSF); na versão 3 da Licença, ou (na sua opinião) qualquer versão.
 * Este programa é distribuído na esperança de que possa ser  útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita 
 * de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU junto com este programa, se não, veja <http://www.gnu.org/licenses/>.
 */
global $_page;

$prefixo = isset($_REQUEST['prefixo'])?htmlspecialchars($_REQUEST['prefixo'], ENT_QUOTES, "UTF-8"):"";
$cod_objeto = isset($_REQUEST['cod_objeto'])?(int)htmlspecialchars($_REQUEST['cod_objeto'], ENT_QUOTES, "UTF-8"):$_page->config["portal"]["objroot"];
if ($cod_objeto==0) $cod_objeto = $_page->config["portal"]["objroot"];

header("Location:".$_page->config["portal"]["url"].'/do/new_'.$prefixo.'/'.$cod_objeto.'.html');