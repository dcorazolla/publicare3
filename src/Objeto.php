<?php
/**
 * Publicare - O CMS Público Brasileiro
 * @file Objeto.php
 * @description Classe responsável por gerenciar os objetos
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

namespace Pbl;

class Objeto
{
    public $ponteiro=0;
    public $quantidade=0;
    public $caminhoObjeto = array();
    public $metadados;
    public $propriedades;
    public $ArrayMetadados;
    
    public $page;

    /**
     * Método construtor da classe objeto
     * @param Pagina $page - Referencia do objeto page
     * @param mixed $cod_objeto - Pode ser string ou inteiro
     * @return boolean
     */
    function __construct($page, $cod_objeto=-1)
    {
        $this->page = $page;

        if (isset($this->page->config["portal"]["debug"]) && $this->page->config["portal"]["debug"] === true)
        {
            x("objeto::construct cod_objeto=".$cod_objeto);
        }
        
        $this->ArrayMetadados = $this->page->db->metadados;
        if ($cod_objeto!=-1)
        {
            if (is_numeric($cod_objeto))
            {
                $dados = $this->page->adminobjeto->pegarDadosObjetoId($cod_objeto);
            }
            else
            {
                $dados = $this->page->adminobjeto->pegarDadosObjetoTitulo($cod_objeto);
            }

            if (is_array($dados) && sizeof($dados)>2)
            {
                $this->povoar($dados);
                $this->caminhoObjeto = $this->pegarCaminho();
                return true;
            }

            //xd($this);

        }

        //Nao conseguiu selecionar o objeto
        return false;
    }

    /**
     * Povoa objeto do tipo Objeto com os metadados, datas de publicação e validade, url amigavel e tags
     * @param array $dados - Array com os metadados
     */
    function povoar($dados)
    {
        if (isset($this->page->config["portal"]["debug"]) && $this->page->config["portal"]["debug"] === true)
        {
            x("objeto::povoar");
        }
        $this->metadados = $dados;
        $this->metadados['data_publicacao'] = ConverteData($this->metadados['data_publicacao'],1);
        $this->metadados['data_validade'] = ConverteData($this->metadados['data_validade'],1);
        //INCLUIDO O TITULO DO OBJETO NA URL
        if ($this->metadados['url_amigavel'] 
                && $this->metadados['url_amigavel']!="") 
        {
            $this->metadados['url'] = "/".$this->metadados['url_amigavel'];
        }
        else 
        {
            $this->metadados['url']='/content/view/'.$this->metadados['cod_objeto']."/".limpaString($this->metadados['titulo']).".html";
        }
        $this->metadados['tags'] = $this->page->adminobjeto->pegarTags($this->metadados['cod_objeto']);
    }

    /**
     * Retorna caminho do objeto, em string separado por ","
     * @return string
     */
    function pegarCaminho()
    {
        if (isset($this->page->config["portal"]["debug"]) && $this->page->config["portal"]["debug"] === true)
        {
            x("objeto::pegarCaminho");
        }
        return $this->page->adminobjeto->pegarCaminhoObjeto($this->metadados['cod_objeto']);
    }

    /**
     * Retorna array com caminho do objeto
     * @return array
     */
    function pegarCaminhoComTitulo()
    {
        if (isset($this->page->config["portal"]["debug"]) && $this->page->config["portal"]["debug"] === true)
        {
            x("objeto::pegarCaminhoComTitulo");
        }
        $resultado=$this->page->adminobjeto->pegarCaminhoObjetoComTitulo($this->metadados['cod_objeto']);
        return $resultado;
    }

    /**
     * Verifica se objeto est´qa com status publicado
     * @return bool
     */
    function publicado()
    {
        if (isset($this->page->config["portal"]["debug"]) && $this->page->config["portal"]["debug"] === true)
        {
            x("objeto::publicado");
        }
        return ($this->metadados['cod_status']==_STATUS_PUBLICADO);
    }

    /**
     * Retorna valor da propriedade ou metadado
     * @param string $campo
     * @return type
     */
    function valor($campo)
    {
        // if (isset($this->page->config["portal"]["debug"]) && $this->page->config["portal"]["debug"] === true)
        // {
        //     x("objeto::valor campo:".$campo);
        // }
        if ($this->page->adminobjeto->ehMetadado($campo))
        {
            return trim($this->metadados[$campo]);
        }
        elseif ($campo=="tags")
        {

        }
        else
        {
            return trim ($this->propriedade($campo));
        }
    }

    /**
     * Retorna URL para download do blob. Alias de DownloadBlob.
     * @param string $campo - nome da propriedade que contem o blob
     * @return string
     */
    function linkBlob($campo)
    {
        if (isset($this->page->config["portal"]["debug"]) && $this->page->config["portal"]["debug"] === true)
        {
            x("objeto::linkBlob campo:".$campo);
        }
        return $this->baixarBlob($campo);
    }

    /**
     * Retorna URL para realizar download do blob atraves da funcionalidade downloadblob
     * @param string $campo - nome da propriedade que contem o blob
     * @return string
     */
    function baixarBlob($campo)
    {
        if (isset($this->page->config["portal"]["debug"]) && $this->page->config["portal"]["debug"] === true)
        {
            x("objeto::baixarBlob campo:".$campo);
        }
        if (!isset($this->propriedades) || !is_array($this->propriedades))
        {
            $this->propriedades = $this->page->adminobjeto->pegarPropriedades($this->metadados['cod_objeto']);
        }
        return isset($this->propriedades[$campo]['cod_blob'])?$this->page->config["portal"]["url"]."/blob/baixar/".$this->propriedades[$campo]['cod_blob']:"";
    }
    
    /**
     * Exibe blob na tela utilizando funcionalidade viewblob.
     * Utilizado somente para imagens
     * @param string $campo - Nome da propriedade blob
     * @param integer $width - Largura da imagem
     * @param integer $height - Altura da imagem
     * @return bytes - Retorna bytes da imagem para exibição
     */
    function exibirBlob($campo, $width=0, $height=0)
    {
        if (isset($this->page->config["portal"]["debug"]) && $this->page->config["portal"]["debug"] === true)
        {
            x("objeto::exibirBlob campo:".$campo);
        }
        if (!isset($this->propriedades) || !is_array($this->propriedades))
        {
                $this->propriedades = $this->page->adminobjeto->pegarPropriedades($this->metadados['cod_objeto']);
        }

//        return _URL."/html/objects/_viewblob.php?cod_blob=".$this->propriedades[$campo]['cod_blob']."&width=$width&height=$height";
        return isset($this->propriedades[$campo]['cod_blob'])?$this->page->config["portal"]["url"]."/blob/ver/".$this->propriedades[$campo]['cod_blob']."?w=".$width."&h=".$height:"";
        // return $this->page->config["portal"]["url"]."/blob/ver/".$this->propriedades[$campo]['cod_blob']."?w=".$width."&h=".$height;
    }

    /**
     * Exibe miniatura das imagens na tela utilizando funcionalidade viewthumb.
     * Utilizado somente para imagens
     * @param string $campo - Nome da propriedade blob
     * @param integer $width - Largura da imagem
     * @param integer $height - Altura da imagem
     * @return bytes - Retorna bytes da imagem para exibição
     */
    function exibirThumb($campo, $width=0, $height=0)
    {
        if (isset($this->page->config["portal"]["debug"]) && $this->page->config["portal"]["debug"] === true)
        {
            x("objeto::exibirThumb campo:".$campo);
        }
        if (!isset($this->propriedades) || !is_array($this->propriedades))
        {
            $this->propriedades = $this->page->adminobjeto->pegarPropriedades($this->metadados['cod_objeto']);
        }
        
        $largura = $width>0?$width:$this->page->config["portal"]["largurathumb"];
//        return _URL."/html/objects/_viewthumb.php?cod_blob=".$this->propriedades[$campo]['cod_blob']."&width=$width&height=$height";
        // return $this->page->config["portal"]["url"]."/blob/ver/".$this->propriedades[$campo]['cod_blob']."?w=".$largura."&h=".$height;
        return isset($this->propriedades[$campo]['cod_blob'])?$this->page->config["portal"]["url"]."/blob/ver/".$this->propriedades[$campo]['cod_blob']."?w=".$largura."&h=".$height:"";
    }

    function valorParaEdicao($campo)
    {
        if (isset($this->page->config["portal"]["debug"]) && $this->page->config["portal"]["debug"] === true)
        {
            x("objeto::valorParaEdicao campo:".$campo);
        }

        if ($this->page->adminobjeto->ehMetadado($campo))
        {
            return (trim($this->metadados[$campo]));
        }
        else
        {
            return (trim($this->propriedade($campo)));
        }
    }

    function pegarListaPropriedades()
    {
        if (isset($this->page->config["portal"]["debug"]) && $this->page->config["portal"]["debug"] === true)
        {
            x("objeto::pegarListaPropriedades");
        }
        
        if (!is_array($this->propriedades) || count($this->propriedades)==0)
        {
                $this->propriedades = $this->page->adminobjeto->pegarPropriedades($this->metadados['cod_objeto']);
        }
        return $this->propriedades;
    }

    function propriedade($campo)
    {
        if (isset($this->page->config["portal"]["debug"]) && $this->page->config["portal"]["debug"] === true)
        {
            x("objeto::propriedade campo:".$campo);
        }
        
        $campo = strtolower($campo);
        if (!isset($this->propriedades) || !is_array($this->propriedades))
        {
            $this->propriedades = $this->page->adminobjeto->pegarPropriedades($this->metadados['cod_objeto']);
        }
        // if (isset($this->propriedades[$campo])) return $this->propriedades[$campo]['valor'];
        // else return "";
        return isset($this->propriedades[$campo])?$this->propriedades[$campo]['valor']:"";
    }

    /**
     * Retorna o tamanho do blob em Bytes
     * @param string $campo - Nome da propriedade que contem o blob
     * @return int
     */
    function tamanhoBlob($campo)
    {
        if (isset($this->page->config["portal"]["debug"]) && $this->page->config["portal"]["debug"] === true)
        {
            x("objeto::tamanhoBlob campo:".$campo);
        }
        
        if (!isset($this->propriedades) || !is_array($this->propriedades))
        {
            $this->propriedades = $this->page->adminobjeto->pegarPropriedades($this->metadados['cod_objeto']);
        }
        
        // x($this->propriedades);
        // return ($this->propriedades[$campo]['tamanho_blob']);
        return isset($this->propriedades[$campo]['tamanho_blob'])?$this->propriedades[$campo]['tamanho_blob']:"";
    }

    function tipoBlob($campo)
    {
        if (isset($this->page->config["portal"]["debug"]) && $this->page->config["portal"]["debug"] === true)
        {
            x("objeto::tipoBlob campo:".$campo);
        }
        
        if (!isset($this->propriedades) || !is_array($this->propriedades))
        {
            $this->propriedades = $this->page->adminobjeto->pegarPropriedades($this->metadados['cod_objeto']);
        }

        // return ($this->propriedades[$campo]['tipo_blob']);
        return  isset($this->propriedades[$campo]['tipo_blob'])?isset($this->propriedades[$campo]['tipo_blob']):"";
    }
		
    function iconeBlob($campo)
    {
        if (isset($this->page->config["portal"]["debug"]) && $this->page->config["portal"]["debug"] === true)
        {
            x("objeto::iconeBlob campo:".$campo);
        }
        
        $arquivo ='/html/imagens/icnx_'.$this->tipoBlob($campo).'.gif';
        if (file_exists($_SERVER['DOCUMENT_ROOT'].$arquivo))
        {
            return $arquivo;
        }
        else
        {
            return '/html/imagens/icnx_generic.gif';
        }
    }

    function pegarListaFilhos($classe='*',$ordem='peso,titulo',$inicio=-1,$limite=-1)
    {
        if (isset($this->page->config["portal"]["debug"]) && $this->page->config["portal"]["debug"] === true)
        {
            x("objeto::pegarListaFilhos campo:".$campo);
        }
        
        if ($this->metadados['temfilhos'])
        {
            $this->filhos = $this->page->adminobjeto->pegarListaFilhos($this->metadados['cod_objeto'], $classe, $ordem, $inicio, $limite);
            $this->ponteiro = 0;
            $this->quantidade = count($this->filhos);
            return $this->quantidade;
        }
        else
            return false;
    }

    function podeTerFilhos()
    {
        if (isset($this->page->config["portal"]["debug"]) && $this->page->config["portal"]["debug"] === true)
        {
            x("objeto::podeTerFilhos");
        }
        
        return $this->metadados['temfilhos'];
    }

    function pegarProximoFilho()
    {
        if (isset($this->page->config["portal"]["debug"]) && $this->page->config["portal"]["debug"] === true)
        {
            x("objeto::pegarProximoFilho");
        }
        
        if ($this->ponteiro < $this->quantidade)
            return $this->filhos[$this->ponteiro++];
        else
            return false;
    }

    function vaiParaFilho($posicao)
    {
        if (isset($this->page->config["portal"]["debug"]) && $this->page->config["portal"]["debug"] === true)
        {
            x("objeto::vaiParaFilho");
        }
        
        if ($posicao>$this->quantidade)
            return false;
        else
        {
            $this->ponteiro=$posicao;
            return $this->filhos[$this->ponteiro++];
        }
    }

    function ehFilho ($cod_pai)
    {
        if (isset($this->page->config["portal"]["debug"]) && $this->page->config["portal"]["debug"] === true)
        {
            x("objeto::ehFilho cod_pai=".$cod_pai);
        }
        
            //echo "cod_objeto:".$this->valor("cod_objeto");
            //exit;
            return $this->page->adminobjeto->ehFilho($this->valor("cod_objeto"), $cod_pai);
    }
                
}