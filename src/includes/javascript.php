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
$js = new Includes(array("jquery-3.3.1.min.js", "bootstrap.min.js", "modernizr.custom.js", "classie.js", "gnmenu.js", "jquery.complexify.js", "funcoes.js", "jquery.validate.min.js", "additional-methods.min.js", "messages_pt_BR.js", "jquery.mask.js", "scripts_custom.js", "jodit.min.js"), "js");
$js->imprimeResultado();
exit();