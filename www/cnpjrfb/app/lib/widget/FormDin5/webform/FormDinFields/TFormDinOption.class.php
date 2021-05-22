<?php
/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * @author Reinaldo A. Barrêto Junior
 * 
 * É uma reconstrução do FormDin 4 Sobre o Adianti 7.X
 * @author Luís Eugênio Barbosa do FormDin 4
 * 
 * Adianti Framework é uma criação Adianti Solutions Ltd
 * @author Pablo Dall'Oglio
 * ----------------------------------------------------------------------------
 * This file is part of Formdin Framework.
 *
 * Formdin Framework is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License version 3
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License version 3
 * along with this program; if not,  see <http://www.gnu.org/licenses/>
 * or write to the Free Software Foundation, Inc., 51 Franklin Street,
 * Fifth Floor, Boston, MA  02110-1301, USA.
 * ----------------------------------------------------------------------------
 * Este arquivo é parte do Framework Formdin.
 *
 * O Framework Formdin é um software livre; você pode redistribuí-lo e/ou
 * modificá-lo dentro dos termos da GNU LGPL versão 3 como publicada pela Fundação
 * do Software Livre (FSF).
 *
 * Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA
 * GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou
 * APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/LGPL em português
 * para maiores detalhes.
 *
 * Você deve ter recebido uma cópia da GNU LGPL versão 3, sob o título
 * "LICENCA.txt", junto com esse programa. Se não, acesse <http://www.gnu.org/licenses/>
 * ou escreva para a Fundação do Software Livre (FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA 02111-1301, USA.
 */


/**
 * Classe para criação campo do tipo select
 * ------------------------------------------------------------------------
 * Esse é o FormDin 5, que é uma reconstrução do FormDin 4 Sobre o Adianti 7.X
 * os parâmetros do metodos foram marcados com:
 * 
 * NOT_IMPLEMENTED = Parâmetro não implementados, talvez funcione em 
 *                   verões futuras do FormDin. Não vai fazer nada
 * DEPRECATED = Parâmetro que não vai funcionar no Adianti e foi mantido
 *              para diminuir o impacto sobre as migrações. Vai gerar um Warning
 * FORMDIN5 = Parâmetro novo disponivel apenas na nova versão
 * ------------------------------------------------------------------------
 * 
 * @author Reinaldo A. Barrêto Junior
 */
class TFormDinOption  extends TFormDinGenericField
{
	
	const RADIO = 'radio';
	const CHECK = 'check';
	const SELECT = 'select';
	
	private $arrOptions;
	private $mixValue;
	//private $required;
	private $qtdColunms;
	private $columns;
	private $paddingRight;
	private $multiSelect;
	private $selectSize;
	private $keyField;
	private $displayField;
	private $showMinimal;
	private $nowWrapText;
	private $arrOptionsData;

	private $mixOptions;
	private $mixSearchFields;
	private $dataColumns;
    
	/**
	 * Método construtor
	 * $strDisplayColumn = nome da coluna que será utilizada para preencher as opções que serão exibidas para o usuário
	 * 
	 * @param object  $objAdiantiField -01: Objeto de campo do Adianti
	 * @param string  $id              -02: ID do campo
	 * @param string  $label           -03: Label do campo
	 * @param boolean $boolRequired    -04: Campo obrigatório. Default FALSE = não obrigatório, TRUE = obrigatório
	 * @param mixed   $mixOptions      -05: String "S=SIM,N=NAO,..." ou Array dos valores nos formatos: PHP "id=>value", FormDin, PDO ou Adianti
	 * @param boolean $boolNewLine     -06: Default TRUE = cria nova linha , FALSE = fica depois do campo anterior
	 * @param boolean $boolLabelAbove  -07: Label sobre o campo. Default FALSE = Label mesma linha, TRUE = Label acima
	 * @param array   $mixValue        -08: Informe o ID do array ou array com a lista de ID's no formato "key=>id" para identificar a(s) opção(ões) selecionada(s)
	 * @param boolean $boolMultiSelect -09: Default FALSE = SingleSelect, TRUE = MultiSelect
	 * @param integer $intQtdColumns   -10: Default 1. Num itens que irão aparecer no MultiSelect
	 * @param string  $strInputType    -11: Define o tipo de input a ser gerado. Ex: select, radio ou check
	 * @param string  $strFirstOptionValue- 12: Frist Value in Display, use value NULL for required. Para o valor DEFAULT informe o ID do $mixOptions e $strFirstOptionText = '' e não pode ser null
	 * @param string  $strKeyField     -13: Nome da coluna que será utilizada para preencher os valores das opções
	 * @param string  $strDisplayField -14: Nome da coluna que será utilizada para preencher as opções que serão exibidas para o usuário
	 * @param boolean $boolNowrapText  -15:
	 * @param string  $strDataColumns  -16: informações extras do banco de dados que deverão ser adicionadas na tag option do campo select
	 * @return TFormDinOption
	 */
	public function __construct( $adiantiObj
							   , string $id
							   , string $label
	                           , $boolRequired=null
							   , $mixOptions
							   , $boolNewLine=null
							   , $boolLabelAbove=null
	                           , $mixValue=null
                        	   , $boolMultiSelect=null
                        	   , $intQtdColumns=null
							   , $strInputType=null
							   , $strFirstOptionValue=null
                        	   , $strKeyField=null
                        	   , $strDisplayField=null
                        	   , $boolNowrapText=null
                        	   , $strDataColumns=null 
                        	   )
	{
		$value = is_null($mixValue)?$strFirstOptionValue:$mixValue;
		$this->setFieldType( ($strInputType == null) ? self::SELECT : $strInputType );

		parent::__construct($adiantiObj,$id,$label,$boolRequired,$value,null);
		//parent::setValue( $value );

		$this->setQtdColumns( $intQtdColumns );
	
		$this->setMixOptions( $mixOptions );
		$this->setDisplayField( $strDisplayField );
		$this->setKeyField( $strKeyField );
		$this->setSearchFields( null );
		$this->setDataColumns( $strDataColumns );
		$this->transformOptions();
		$this->setNowrapText($boolNowrapText);

		$this->addItems( $this->getOptions() );
	}

	public function addItems($arrayItens){
		$arrayItens = ArrayHelper::convertArray2OutputFormat($arrayItens,ArrayHelper::TYPE_FORMDIN);
        $this->getAdiantiObj()->addItems($arrayItens);
    }
    public function getItems()
    {
        return $this->getAdiantiObj()->getItems();
	}
	
    public function setUseButton($useButton){
		if($this->getFieldType() == self::SELECT){
			throw new InvalidArgumentException(TFormDinMessage::ERROR_OBJ_OPTION);
		}
        if( !empty($useButton) ){
            $this->getAdiantiObj()->setUseButton();
        }
	}
    public function getButtons()
    {
        return $this->getAdiantiObj()->getButtons();
    }	

    public function setLayout($dir)
    {
		if($this->getFieldType() == self::SELECT){
			throw new InvalidArgumentException(TFormDinMessage::ERROR_OBJ_OPTION);
		}
        $this->getAdiantiObj()->setLayout($dir);
    }
    public function getLayout()
    {
        return $this->getAdiantiObj()->getLayout();
    }
    public function setBreakItems($breakItems)
    {
		if($this->getFieldType() == self::SELECT){
			throw new InvalidArgumentException(TFormDinMessage::ERROR_OBJ_OPTION);
		}
        $this->getAdiantiObj()->setBreakItems($breakItems);
    }
    public function getLabels()
    {
        return $this->getAdiantiObj()->getLabels();
    }

	//-----------------------------------------------------------------------
	/**
	 * Define a quantidade de colunas para distribuição dos checkbox ou radios na tela
	 *
	 * @param integer $intNewValue
	 */
	public function setQtdColumns( $intNewValue=null )
	{
		$this->qtdColunms = (( int ) $intNewValue == 0) ? 1 : ( int ) $intNewValue;
		return $this;
	}
	/**
	 * Recupera a quantidade de colunas para distribuição dos checkbox ou radios na tela
	 *
	 */
	public function getQtdColumns()
	{
		return ( int ) $this->qtdColunms;
	}
	//-----------------------------------------------------------------------
	public function setKeyField( $strNewValue=null )
	{
		$this->keyField = $strNewValue;
		return $this;
	}	
	public function getKeyField()
	{
		return $this->keyField;
	}
	//-----------------------------------------------------------------------
	public function setDisplayField( $strNewValue=null )
	{
		$this->displayField = $strNewValue;
		return $this;
	}
	public function getDisplayField()
	{
		return $this->displayField;
	}
	//-------------------------------------------------------------------------
	public function setFieldType( $newFieldType )
	{
		$this->fldType=$newFieldType;
		return $this;
	}
	public function getFieldType(){ 
		return $this->fldType; 
	}
	//-------------------------------------------------------------------------	
	public function setNowrapText($boolNewValue = null )
	{
		$this->nowWrapText = $boolNewValue;
		return $this;
	}
	public function getNowrapText()
	{
		return $this->nowWrapText === true? true: false;
	}
	//-------------------------------------------------------------------------	
	public function setMixOptions($mixOptions = null )
	{
		$this->mixOptions = $mixOptions;
	}
	public function getMixOptions()
	{
		return $this->mixOptions;
	}
	//-------------------------------------------------------------------------	
	public function setSearchFields($mixSearchFields = null )
	{
		$this->mixSearchFields = $mixSearchFields;
	}
	public function getSearchFields()
	{
		return $this->mixSearchFields;
	}
	//-------------------------------------------------------------------------	
	public function setDataColumns($strDataColumns = null )
	{
		$this->dataColumns = $strDataColumns;
	}
	public function getDataColumns()
	{
		return $this->dataColumns;
	}	
	//-----------------------------------------------------------------------
	/**
	 * Define um array no formato "key=>value" ou string no formato "S=SIM,N=NAO,..." ou
	 * o nome de um pacoteFunção para recuperar do banco de dados, neste
	 * caso pode ser especificada a coluna chave, a coluna descrição e
	 * searchFields como parametros para a função do pacote oracle.
	 *
	 * Ex: $mixSearchFields="cod_uf=53,num_pessoa=20" ou array('COD_UF'=53,'NUM_PESSOA'=>20)
	 * Ex: $strDataColumns = "cod_uf,sig_uf,cod_regiao"
	 *
	 */
	public function transformOptions()
	{
		$mixOptions      = $this->getMixOptions();
		$strDisplayField = $this->getDisplayField();
		$strKeyField     = $this->getKeyField();
		$mixSearchFields = $this->getSearchFields();
		$strDataColumns  = $this->getDataColumns();

		if( empty( $mixOptions ) ) {
			$mixOptions = array('S'=>'');
		}

		if( !is_null($strDataColumns) && trim( $strDataColumns) != '' ) {
			$arrDataColumns	= explode(',',$strDataColumns);
			$strDataColumns	= ','.$strDataColumns.' ';
		}

		if( is_string( $mixOptions ) ) {
			$mixOptions = ArrayHelper::convertString2Array($mixOptions);
		}

		$this->arrOptions = null;
		if( is_array( $mixOptions ) ) {
			$mixOptions = ArrayHelper::convertArray2OutputFormat($mixOptions,ArrayHelper::TYPE_FORMDIN);
			// verificar se o array está no formato oracle
			if( key( $mixOptions ) && is_array( $mixOptions[ key( $mixOptions ) ] ) )
			{
				// assumir a primeira e segunda coluna para popular as opções caso não tenha sido informadas
				if( !isset( $strKeyField ) ){
					if( !$this->getKeyField() ){
						list($strKeyField) = array_keys( $mixOptions );
					}else{
						$strKeyField = $this->getKeyField();
					}
				}

				if( !isset( $strDisplayField ) ) {

					if( !$this->getDisplayField() ){
						list(, $strDisplayField) = array_keys( $mixOptions );
					} else {
						$strDisplayField = $this->getDisplayField();
					}

					if( !isset( $strDisplayField ) ) {
						$strDisplayField = $strKeyField;
					}
				}

				if( $strKeyField && $strDisplayField ) {
					// reconhecer nome da columa em caixa baixa ou alta
					if( !array_key_exists( $strKeyField, $mixOptions ) ){
						$strKeyField = strtoupper( $strKeyField );
						$strDisplayField = strtoupper( $strDisplayField );
					}
					if( !array_key_exists( $strKeyField, $mixOptions ) ){
						$strKeyField = strtolower( $strKeyField );
						$strDisplayField = strtolower( $strDisplayField );
					}
					if( is_array( $mixOptions[ $strKeyField ] ) ){
						foreach( $mixOptions[ $strKeyField ] as $k=>$v ) {
							$this->arrOptions[ $v ] = $mixOptions[ $strDisplayField ][ $k ];
							if( isset( $arrDataColumns ) && is_array( $arrDataColumns ) ){
								foreach($arrDataColumns as $colName ){
									$value='';
									if( isset( $mixOptions[$colName][$k] ) ){
										$value = $mixOptions[$colName][$k];
									} elseif( isset( $mixOptions[strtoupper($colName) ][$k] ) ){
										$value = $mixOptions[strtoupper($colName) ][$k];
									} elseif( isset( $mixOptions[strtolower($colName) ][$k] ) ){
										$value = $mixOptions[strtolower($colName)][$k];
									}
									//$value = $this->specialChars2htmlEntities( $value );
									$value = preg_replace("/\n/",' ',$value);
									$this->arrOptionsData[$v]['data-'.strtolower($colName)] = $value;
								}
							}
						}//Fim ForEach
					}
				}
			} else {
				$this->arrOptions = $mixOptions;
			}
		}

		return $this;
	}

	/**
	 * Recupera o array de opções do campo
	 *
	 */
	public function getOptions()
	{
		return $this->arrOptions;
	}


}