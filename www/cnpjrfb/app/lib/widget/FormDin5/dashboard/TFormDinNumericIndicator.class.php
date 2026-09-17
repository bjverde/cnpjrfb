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

//namespace app\lib\widget\FormDin5\dashboard;

use Adianti\Widget\Chart\TNumericIndicator;

class TFormDinNumericIndicator extends TNumericIndicator
{
    private $fontColor = '#000000';
    private $cardColor = '#ffffff';
    
    // Propriedades re-declaradas porque são private no TNumericIndicator
    protected $icon;
    protected $iconColor = '#ffffff'; // Cor padrão do ícone
    protected $value;
    protected $iconColorBackground = '#0d6efd'; //Cor padrão do background do icone
    protected $linkUrl;
    protected $linkText;
    protected $linkTarget = '_blank';
    protected $numberSuffix = '';
    protected $layout = 'v2';
    
    /**
     * Construtor da classe
     */
    public function __construct()
    {
        parent::__construct();
        // Remove a máscara padrão imposta pelo TChartBase do Adianti.
        // A partir de agora, a formatação só ocorrerá se for chamada explicitamente no objeto.
        $this->numericMask = null;
    }
    
    // Novos comportamentos encapsulados
    /**
     * Define a cor da fonte (texto) do indicador.
     * @param string $color Cor no formato hexadecimal, rgb, etc. Ex: '#000000'
     */
    public function setFontColor($color = '#000000')
    {
        $this->fontColor = $color;
    }
    
    /**
     * Define a cor de fundo do cartão principal.
     * @param string $color Cor no formato hexadecimal, rgb, etc. Ex: '#ffffff'
     */
    public function setCardColor($color = '#ffffff')
    {
        $this->cardColor = $color;
    }
    
    /**
     * Define o layout do componente ('v2' ou 'v3').
     * @param string $layout Versão do layout
     */
    public function setLayout($layout)
    {
        if (!in_array($layout, ['v2', 'v3'])) {
            throw new \Exception("Layout inválido ('{$layout}'). O método setLayout() aceita apenas 'v2' ou 'v3'.");
        }
        $this->layout = $layout;
    }
    
    /**
     * Retorna o layout atual.
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Define a classe do ícone a ser exibido.
     * ATENÇÃO: É necessário informar o nome completo da classe da biblioteca de ícones.
     * Exemplo: 'fa fa-car' ou 'fas fa-car fa-fw' (e não apenas 'car').
     * @param string $icon Classes CSS do ícone
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * Define a cor do ícone.
     * @param string $color Cor no formato hexadecimal, rgb, etc. Ex: '#ff0000'
     */
    public function setIconColor($color = '#ffffff')
    {
        $this->iconColor = $color;
    }

    /**
     * Define o valor numérico (ou texto) a ser exibido.
     * @param mixed $value Valor a ser exibido
     */
    public function setValue($value)
    {
        // Verifica se o usuário passou uma string contendo vírgula (tentando formatar manualmente)
        if (is_string($value) && strpos($value, ',') !== false) {
            throw new \Exception("O valor informado ('{$value}') contém vírgula. O PHP exige números puros com ponto (.) para casas decimais no setValue(). Para formatar a visualização com vírgula na tela, passe o número puro e utilize o método setNumericMask(casas_decimais, separador_decimal, separador_milhar). Exemplo: setNumericMask(2, ',', '.').");
        }

        if (!is_numeric($value)) {
            throw new \Exception("O valor informado no método setValue() no TFormDinNumericIndicator não é numérico ('{$value}'). Para inserir prefixos de moeda, utilize o método setNumberPrefix('R$ ').");
        }
        $this->value = $value;
    }

    /**
     * Define a cor de fundo primária do container do ícone.
     * @param string $color Cor de fundo (classe ou hexadecimal)
     */
    public function setIconColorBackground($color)
    {
        $this->iconColorBackground = $color;
    }
    
    /**
     * Define o texto do link no rodapé do componente.
     * @param string $text Texto do link (ex: 'Mais informações')
     */
    public function setLinkText($text)
    {
        $this->linkText = $text;
    }

    /**
     * Define a URL de destino do link do rodapé.
     * @param string $url URL para qual o usuário será redirecionado
     */
    public function setLinkUrl($url)
    {
        $this->linkUrl = $url;
    }
    
    /**
     * Define o target do link (ex: '_blank' para nova aba).
     * @param string $target Target do link
     */
    public function setLinkTarget($target)
    {
        $this->linkTarget = $target;
    }
    
    /**
     * Retorna a cor atual da fonte.
     * @return string
     */
    public function getFontColor()
    {
        return $this->fontColor;
    }
    
    /**
     * Retorna a cor atual do cartão.
     * @return string
     */
    public function getCardColor()
    {
        return $this->cardColor;
    }
    
    /**
     * Retorna as classes css do ícone.
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Retorna a cor configurada para o ícone.
     * @return string
     */
    public function getIconColor()
    {
        return $this->iconColor;
    }
    
    /**
     * Retorna o valor que está sendo exibido, de forma pura (sem formatações).
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * Retorna o valor formatado de acordo com a máscara, prefixo e sufixo configurados.
     * para formatar o valor use setNumericMask(0, ',', '.') ou setNumericMask(2, ',', '.') etc.
     * @return string
     */
    public function getFormattedValue()
    {
        $value = $this->getValue();
        
        // Aplica a máscara apenas se foi explicitamente configurada
        if (!empty($this->numericMask) && is_numeric($value)) {
            $value = (float) $value;
            $value = number_format($value, $this->numericMask[0], $this->numericMask[1], $this->numericMask[2]);
        }
        
        if (!empty($this->numberPrefix)) {
            $value = $this->numberPrefix . ' ' . $value;
        }
        
        if (!empty($this->numberSuffix)) {
            $value = $value . $this->numberSuffix;
        }
        
        return $value;
    }
    
    /**
     * Retorna a cor de fundo primária do container do ícone.
     * @return string
     */
    public function getIconColorBackground()
    {
        return $this->iconColorBackground;
    }

    /**
     * Retorna o texto do link.
     * @return string
     */
    public function getLinkText()
    {
        return $this->linkText;
    }

    /**
     * Retorna a URL do link.
     * @return string
     */
    public function getLinkUrl()
    {
        return $this->linkUrl;
    }

    /**
     * Retorna o target do link.
     * @return string
     */
    public function getLinkTarget()
    {
        return $this->linkTarget;
    }

    /**
     * Define o sufixo a ser exibido após o número.
     * @param string $suffix Sufixo (ex: ' °C', ' %')
     */
    public function setNumberSuffix($suffix)
    {
        $this->numberSuffix = $suffix;
    }

    /**
     * Renderiza o componente carregando o template HTML do FormDin5 e 
     * processando as variáveis encapsuladas no componente.
     */
    public function show()
    {
        $iconColor = $this->getIconColor();
        $iconColorBackground = $this->getIconColorBackground();
        
        // Regra específica solicitada: se for v3 e não mudou iconColorBackground, ele assume a cor do cartão
        // Obs: No HTML da v3, coloquei uma opacidade no ícone, portanto ele ficará com um tom mais escuro perfeitamente como na imagem
        if ($this->getLayout() === 'v3' && $iconColor === '#ffffff') {
            $iconColor = '#000000';
        }
        if ($this->getLayout() === 'v3' && $iconColorBackground === '#0d6efd') {
            $iconColorBackground = $this->getCardColor();
        }

        // Seleciona o template correto
        $templateFile = "app/lib/widget/FormDin5/resources/info-box-{$this->getLayout()}.html";
        
        // Renderiza usando o template
        $infoBox = new THtmlRenderer($templateFile);
        $infoBox->enableSection('main', [
            'title'      => $this->getTitle(), //Herdado do TChartBase
            'icon'       => $this->getIcon(),
            'iconColor'  => $iconColor,
            'background' => $iconColorBackground,
            'value'      => $this->getFormattedValue(),
            'fontColor'  => $this->getFontColor(),
            'cardColor'  => $this->getCardColor()
        ]);
        
        if (!empty($this->linkUrl) && !empty($this->linkText)) {
            $infoBox->enableSection('has_link', [
                'linkUrl'    => $this->getLinkUrl(),
                'linkText'   => $this->getLinkText(),
                'linkTarget' => $this->getLinkTarget(),
                'fontColor'  => $this->getFontColor()
            ]);
        }
        
        parent::add($infoBox);
        //Chamando show do parente do parente
        \Adianti\Widget\Base\TElement::show();
    }
}