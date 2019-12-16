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
	include_once ("iniciar.php");

	if ($copy)
		$_page->objManage->ObjectPaste($cod_objmanage,$_page->obj->cod_object);
	if ($pastelink)
		$_page->objManage->ObjectPasteAsLink($cod_objmanage);
	if ($move)
		$_page->objManage->ObjectMove($cod_objmanage,$cod_object);
	if ($clear)
		$_page->objManage->ClearManageTable();
		
	header ("Location:"._URL.$return_path);
?>