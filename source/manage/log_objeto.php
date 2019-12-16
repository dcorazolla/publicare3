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

global $_page, $loglist, $log, $count;

	$loglist = $_page->_log->PegaLogObjeto($_page, $_page->_objeto->Valor($_page, "cod_objeto"));
	if (count ($loglist))
	{
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('#tabelaLista').dataTable({
			responsive: true,
			language: linguagemDataTable,
			order: [[ 1, "asc" ]],
		});
	});
</script>
<script src="include/javascript_datatable" type="text/javascript"></script>
<link href="include/css_datatable" rel="stylesheet" type="text/css">

<!-- === Menu === -->
<ul class="nav nav-tabs">
  <li><a href="do/preview/<?php echo($_page->_objeto->Valor($_page, 'cod_objeto')) ?>.html">Indice do Objeto</a></li>
  <li><a href="do/log_workflow/<?php echo($_page->_objeto->Valor($_page, 'cod_objeto')) ?>.html">Workflow</a></li>
  <li class="active"><a href="do/log_objeto/<?php echo($_page->_objeto->Valor($_page, 'cod_objeto')) ?>.html">Log Status</a></li>
</ul>
<!-- === FInal === Menu === -->

<!-- === Log Status === -->
<div class="panel panel-primary">
    <div class="panel-heading"><h3><b>Log Status</b></h3></div>
	<div class="panel-body">
		
		<!-- === Listagem do Log Status === -->
		<div class="panel panel-info modelo_propriedade">
			<div class="panel-heading">
				<div class="row">
					<div class="col-sm-9"><h3 class="font-size20" style="line-height: 30px;"><?php echo($_page->_objeto->Valor($_page, "titulo")); echo " <i>[cod: ".$_page->_objeto->Valor($_page, "cod_objeto")."]</i>"; ?></h3></div>
					<div class="col-sm-3 text-right titulo-icones">
						<a class="ABranco" href="<?php echo(_URL); ?><?php echo($_page->_objeto->Valor($_page, "url"));?>" rel="tooltip" data-color-class="primary" data-animate="animated fadeIn" data-toggle="tooltip" data-original-title="Visualizar objeto" data-placement="left" title="Visualizar Objeto"><i class='fapbl fapbl-eye'></i></a>
					</div>
				</div>
			</div>

			<div class="panel-body">
								   
				<!-- === Tabela Listar Conteúdo (DATATABLE) === -->
				<table id="tabelaLista" class="display" style="width:100%">
					<thead>
						<tr>
							<th>Usuário</th>
							<th>Operação</th>
							<th>Data</th>
						</tr>
					</thead>
					<tbody>
<?php
	$count=0;
//        xd($loglist);
	if (isset($loglist) && is_array($loglist)){
		foreach($loglist as $log)
		{
			if ($count++%2)
				$class="pblTextoLogImpar";
			else
				$class="pblTextoLogPar";
			echo '<tr>';
			echo '<td width="62%">';
			echo $log['usuario'];
			echo '</td>'."\n";
			echo '<td width="18%">';
			echo $log['operacao'];
			echo '</td>'."\n";
			echo '<td width="18%">';
			echo $log['estampa'];
			echo '</td>'."\n";
			echo '</tr>'."\n\n";
		}
	}
?>
					</tbody>
					<tfoot>
						<tr>
							<th>Usuário</th>
							<th>Operação</th>
							<th>Data</th>
						</tr>
					</tfoot>
				</table>
				<!-- === Final === Tabela Listar Conteúdo (DATATABLE) === -->

			</div>
		</div>
		<!-- === Final === Listagem do Log Status === -->
		
	</div>
</div>
<!-- === Log Status === -->
<?php
	}
	else {
	include("manage/vazio.php");	
	}
?>