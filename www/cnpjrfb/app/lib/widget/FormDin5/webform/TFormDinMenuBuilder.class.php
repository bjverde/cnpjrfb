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
 * Classe para criação dinâmica de menus XML e renderização via TMenu.
 * 
 * ------------------------------------------------------------------------
 * Esse é o FormDin 5, que é uma reconstrução do FormDin 4 Sobre o Adianti 8.X
 * os parâmetros do metodos foram marcados com:
 * 
 * NOT_IMPLEMENTED = Parâmetro não implementados, talvez funcione em 
 *                   verões futuras do FormDin. Não vai fazer nada
 * DEPRECATED = Parâmetro que não vai funcionar no Adianti e foi mantido
 *              para o impacto sobre as migrações. Vai gerar um Warning
 * FORMDIN5 = Parâmetro novo disponivel apenas na nova versão
 * ------------------------------------------------------------------------
 * 
 * @author Reinaldo A. Barrêto Junior
 * 
 * Exemplo de uso:
 * 
 * ```php
 * $builder = new MenuBuilder();
 * $builder->addMenuItem('1',null, "Acesso-Web", null, "fa:address-card fa-fw");
 *    $builder->addMenuItem('1.1','1', "Cadastrar", null, "fa:list fa-fw");
 *      $builder->addMenuItem('1.1.1','1.1', "Sistema", "acessoSistema", "fa:minus fa-fw");
 *      $builder->addMenuItem('1.1.2','1.1', "Grupo", "acessoGrupo", "fa:minus fa-fw");
 * $builder->addMenuItem('1.2','1',"Relacionar", null, "fa:list fa-fw");
 *      $builder->addMenuItem('1.2.1','1.2',"Grupo->Pessoas", "acessoGrupoPessoas", "fa:minus fa-fw");
 *      $builder->addMenuItem('1.2.2','1.2',"Pessoa->Grupos", "acessoPessoaGrupos", "fa:minus fa-fw");
 *      $builder->addMenuItem('1.2.3','1.2',"Órgão->Pessoas", "acessoOrgaoPessoas", "fa:minus fa-fw");
 *      $builder->addMenuItem('1.2.4','1.2',"Pessoa->Órgãos", "acessoPessoaOrgaos", "fa:minus fa-fw");
 * 
 * $allowedActions = ['acessoSistema', 'acessoGrupo']; // Lista de ações permitidas
 * 
 * //Retorna todos os itens
 * //echo $builder->getXML(); // Retorna o XML gerado do menu
 * echo $builder->show(); // Retorna o HTML gerado do menu
 * 
 * //Retorna filtrando os itens
 * //echo $builder->getXML($allowedActions); // Retorna o XML gerado do menu
 * echo $builder->show($allowedActions); // Retorna o HTML gerado do menu
 * ```
 */
class TFormDinMenuBuilder
{
    private DOMDocument $doc;
    private DOMElement $root;
    /** @var array<string, DOMElement> */
    private array $elements = [];

    public function __construct() {
        $this->doc = new DOMDocument('1.0', 'UTF-8');
        $this->doc->formatOutput = true;
        $this->root = $this->doc->createElement('menu');
        $this->doc->appendChild($this->root);
        $this->elements['0'] = $this->root;
    }

    /**
     * Adiciona um menuitem no menu
     *
     * @param string $idMenu ID único do menuitem.
     * @param string $idPai ID do elemento pai (NULL ou "0" para raiz).
     * @param string $label Texto que aparecerá como label do menuitem.
     * @param string|null $action Conteúdo do elemento <action> (opcional).
     * @param string|null $icon Conteúdo do elemento <icon> (opcional)..
     * @throws Exception Se o idPai não for encontrado ou se idMenu já existir.
     */
    public function addMenuItem(string $idMenu, string|null $idPai, string $label, string|null $action = null, string|null $icon=null): void {
        if (isset($this->elements[$idMenu])) {
            throw new Exception("ID '{$idMenu}' já existe.");
        }

        $idPai = is_null($idPai)?'0':$idPai;
        if (!isset($this->elements[$idPai])) {
            throw new Exception("ID pai '{$idPai}' não encontrado.");
        }

        $icon = is_null($icon)?'fa:minus fa-fw':$icon;

        // Define o elemento onde o novo item será inserido
        $parentElement = $this->elements[$idPai];

        // Se o pai não for a raiz e for um <menuitem>, garante que tenha um submenu (<menu>)
        if ($idPai !== "0" && $parentElement->nodeName === 'menuitem') {
            $submenu = null;
            foreach ($parentElement->childNodes as $child) {
                if ($child->nodeName === 'menu') {
                    $submenu = $child;
                    break;
                }
            }
            if (!$submenu) {
                $submenu = $this->doc->createElement('menu');
                $parentElement->appendChild($submenu);
            }
            $parentElement = $submenu;
        }

        // Cria o novo menuitem
        $menuItem = $this->doc->createElement('menuitem');
        $menuItem->setAttribute('id', $idMenu);
        $menuItem->setAttribute('label', $label);
        $parentElement->appendChild($menuItem);

        // Adiciona o <icon>
        $iconElement = $this->doc->createElement('icon', $icon);
        $menuItem->appendChild($iconElement);

        // Se a ação for fornecida, adiciona o <action>
        if (!is_null($action)) {
            $actionElement = $this->doc->createElement('action', $action);
            $menuItem->appendChild($actionElement);
        }

        // Armazena o menuitem no array para referência futura
        $this->elements[$idMenu] = $menuItem;
    }

    /**
     * Função recursiva para filtrar os nós do menu.
     * @param DOMNode $node Nó atual a ser verificado.
     * @param array $allowed Array com as actions permitidas.
     * @return bool Indica se o nó possui (ou contém) uma action permitida.
     */
    public function filterMenu(DOMNode $node, array $allowed): bool {
        $hasAllowed = false;

        // Se o nó for um menuitem, verifica se ele possui uma action permitida
        if ($node->nodeName === 'menuitem') {
            // Verifica se há tag <action> permitida no menuitem
            foreach ($node->childNodes as $child) {
                if ($child->nodeName === 'action' && in_array(trim($child->nodeValue), $allowed)) {
                    $hasAllowed = true;
                    break;
                }
            }
        }

        // Se o nó tem filhos, processa-os recursivamente
        if ($node->hasChildNodes()) {
            // Usamos iterator_to_array para evitar problemas ao remover nós durante a iteração
            foreach (iterator_to_array($node->childNodes) as $child) {
                // Se for um nó de menu, processa seus itens
                if ($child->nodeName === 'menu') {
                    // Percorre os menuitems internos do menu
                    $validItems = 0;
                    foreach (iterator_to_array($child->childNodes) as $menuItem) {
                        if ($menuItem->nodeName === 'menuitem') {
                            // Se o menuitem interno possuir uma action permitida (direta ou em submenus), conta como válido
                            if ($this->filterMenu($menuItem, $allowed)) {
                                $validItems++;
                            }
                        }
                    }
                    // Se houver itens válidos, marcamos o nó pai como válido
                    if ($validItems > 0) {
                        $hasAllowed = true;
                    } else {
                        // Se o menu não contém nenhum menuitem com action permitida, remove o nó <menu>
                        $node->removeChild($child);
                    }
                } elseif ($child->nodeType === XML_ELEMENT_NODE) {
                    // Para outros elementos, processa recursivamente
                    if ($this->filterMenu($child, $allowed)) {
                        $hasAllowed = true;
                    }
                }
            }
        }

        // Se for um menuitem e não tiver action permitida, remove-o (se possível)
        if ($node->nodeName === 'menuitem' && !$hasAllowed && $node->parentNode !== null) {
            $node->parentNode->removeChild($node);
        }

        return $hasAllowed;
    }

    /**
     * Retorna o XML gerado filtrado pelas ações permitidas.
     *
     * @param array $allowedActions Lista de ações permitidas.
     * @return string XML formatado.
     */
    public function getXML(array $allowedActions = []): string {
        if (!empty($allowedActions)) {
            $this->filterMenu($this->doc->documentElement,$allowedActions);
        }
        return $this->doc->saveXML();
    }

    /**
     * Renderiza o menu filtrado usando a classe TMenu e retorna o HTML gerado.
     *
     * @param array $allowedActions Lista de ações permitidas.
     * @return string HTML do menu renderizado.
     */
    public function show(array $allowedActions = []): string {
        $xml = new SimpleXMLElement($this->getXML($allowedActions));

        // Cria o menu usando a classe TMenu
        $menu = new TMenu($xml, null, 1, 'sidebar-dropdown list-unstyled collapse', 'sidebar-item', 'sidebar-link collapsed');
        $menu->class = 'sidebar-nav';
        $menu->id    = 'side-menu';

        ob_start();
        $menu->show();
        return ob_get_clean();
    }
}