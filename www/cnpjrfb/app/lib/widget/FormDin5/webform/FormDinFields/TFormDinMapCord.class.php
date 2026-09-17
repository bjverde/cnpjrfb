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
 * ----------------------------------------------------------------------------
 */

class TFormDinMapCord extends TFormDinGenericField
{
    protected $adiantiObj;
    private $idDivMap = null;
    private $showFields = null;
    private $fieldsReadOnly = null;
    private $defaultLat = null;
    private $defaultLon = null;
    private $zoom = null;
    private $height = null;
    private $geoJsonPath = null;

    /**
     * Geolocalização interativa usando o Leaflet.js
     *
     * @param string  $idField         -01: ID do campo base
     * @param string  $label           -02: Label do campo, usado para validações
     * @param boolean $boolRequired    -03: Campo obrigatório ou não. Default FALSE
     * @param boolean $showFields      -04: TRUE (Default) or FALSE, Mostrar campos numéricos de lat e lon
     * @param boolean $fieldsReadOnly  -05: TRUE ou FALSE (Default), Campos somente leitura
     * @param double  $defaultLat      -06: Latitude inicial padrão. Default -15.793889 (Brasília)
     * @param double  $defaultLon      -07: Longitude inicial padrão. Default -47.882778 (Brasília)
     * @param int     $zoom            -08: Nível de zoom inicial do mapa. Default 12
     * @param int     $height          -09: Altura do mapa em pixels. Default 400
     * @param string  $geoJsonPath     -10: Caminho para arquivo GeoJSON a ser plotado. Default null
     * @return TElement
     */
    public function __construct(string $idField
                               ,string $label
                               ,$boolRequired  = null
                               ,$showFields    = null
                               ,$fieldsReadOnly= null
                               ,$defaultLat    = null
                               ,$defaultLon    = null
                               ,$zoom          = null
                               ,$height        = null
                               ,$geoJsonPath   = null
                               )
    {
        $this->setIdDivMap($idField);
        $this->setShowFields($showFields);
        $this->setFieldsReadOnly($fieldsReadOnly);
        $this->setDefaultLat($defaultLat);
        $this->setDefaultLon($defaultLon);
        $this->setZoom($zoom);
        $this->setHeight($height);
        $this->setGeoJsonPath($geoJsonPath);

        $adiantiObj = $this->getDivMapElement($idField, $boolRequired);
        parent::__construct($adiantiObj, $this->getIdDivMap(), $label, false, null, null);
        $this->setLabel($label, $boolRequired);

        return $this->getAdiantiObj();
    }

    //--------------------------------------------------------------------
    public function setGeoJsonPath($geoJsonPath)
    {
        $this->geoJsonPath = $geoJsonPath;
    }
    public function getGeoJsonPath()
    {
        return $this->geoJsonPath;
    }

    //--------------------------------------------------------------------
    public function setIdDivMap($idDivMap)
    {
        $this->idDivMap = $idDivMap;
    }
    public function getIdDivMap()
    {
        return $this->idDivMap;
    }

    //--------------------------------------------------------------------
    public function setShowFields($showFields)
    {
        $this->showFields = is_null($showFields) ? true : (bool)$showFields;
    }
    public function getShowFields()
    {
        return $this->showFields;
    }

    //--------------------------------------------------------------------
    public function setFieldsReadOnly($fieldsReadOnly)
    {
        $this->fieldsReadOnly = is_null($fieldsReadOnly) ? false : (bool)$fieldsReadOnly;
    }
    public function getFieldsReadOnly()
    {
        return $this->fieldsReadOnly;
    }

    //--------------------------------------------------------------------
    public function setDefaultLat($defaultLat)
    {
        $this->defaultLat = is_null($defaultLat) ? -15.793889 : (float)$defaultLat;
    }
    public function getDefaultLat()
    {
        return $this->defaultLat;
    }

    //--------------------------------------------------------------------
    public function setDefaultLon($defaultLon)
    {
        $this->defaultLon = is_null($defaultLon) ? -47.882778 : (float)$defaultLon;
    }
    public function getDefaultLon()
    {
        return $this->defaultLon;
    }

    //--------------------------------------------------------------------
    public function setZoom($zoom)
    {
        $this->zoom = is_null($zoom) ? 12 : (int)$zoom;
    }
    public function getZoom()
    {
        return $this->zoom;
    }

    //--------------------------------------------------------------------
    public function setHeight($height)
    {
        $this->height = is_null($height) ? 400 : (int)$height;
    }
    public function getHeight()
    {
        return $this->height;
    }

    //--------------------------------------------------------------------
    private function getNumericField($idField, $label, $boolRequired)
    {
        $numericField = new TFormDinNumericField($idField, $label, 18, $boolRequired, 16, false, null, -90, 90, false, null, null, null, null, null, null, true, null, '.');
        if ($this->getFieldsReadOnly()) {
            $numericField->setReadOnly(true);
        }
        return $numericField->getAdiantiObj();
    }

    private function getHiddenField($idField, $boolRequired)
    {
        $hiddenField = new TFormDinHiddenField($idField, null, $boolRequired);
        if ($this->getFieldsReadOnly()) {
            $hiddenField->setReadOnly(true);
        }
        return $hiddenField->getAdiantiObj();
    }

    //--------------------------------------------------------------------
    private function getDivMapElement($idField, $boolRequired)
    {
        $divWrapper = new TElement('div');
        $divWrapper->class = 'fd5DivMapCordWrapper';
        $divWrapper->setProperty('id', $this->getIdDivMap() . '_mapwrapper');

        // Cria o elemento da DIV do mapa
        $divMap = new TElement('div');
        $divMap->setProperty('id', $idField . '_map');
        $divMap->setProperty('style', 'height: ' . $this->getHeight() . 'px; width: 100%; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 10px; position: relative; z-index: 1;');

        // Elementos de importação de CSS e JS locais do Leaflet
        $linkCss = new TElement('link');
        $linkCss->setProperty('rel', 'stylesheet');
        $linkCss->setProperty('href', 'app/lib/widget/FormDin5/leaflet/leaflet.css');

        $scriptJsLeaflet = new TElement('script');
        $scriptJsLeaflet->setProperty('src', 'app/lib/widget/FormDin5/leaflet/leaflet.js');

        $scriptJsMap = new TElement('script');
        $scriptJsMap->setProperty('src', 'app/lib/widget/FormDin5/javascript/FormDin5MapCord.js?appver=' . FormDinHelper::version());

        // Campos de inputs de Latitude e Longitude
        $adiantiObjLat = null;
        $adiantiObjLon = null;
        if ($this->getShowFields() == true) {
            $adiantiObjLat = $this->getNumericField($idField . '_lat', 'Latitude', $boolRequired);
            $adiantiObjLon = $this->getNumericField($idField . '_lon', 'Longitude', $boolRequired);
        } else {
            $adiantiObjLat = $this->getHiddenField($idField . '_lat', $boolRequired);
            $adiantiObjLon = $this->getHiddenField($idField . '_lon', $boolRequired);
        }

        // Script inline para inicialização do mapa de maneira assíncrona/segura
        $scriptInit = new TElement('script');
        $readOnlyStr = $this->getFieldsReadOnly() ? 'true' : 'false';
        $geoJsonPathStr = $this->getGeoJsonPath() ? json_encode($this->getGeoJsonPath()) : 'null';
        $scriptInit->add("
            setTimeout(function() {
                if (typeof fd5InitMap === 'function') {
                    fd5InitMap('{$idField}', {$this->getDefaultLat()}, {$this->getDefaultLon()}, {$this->getZoom()}, {$readOnlyStr}, {$geoJsonPathStr});
                } else {
                    let checkInterval = setInterval(function() {
                        if (typeof fd5InitMap === 'function') {
                            clearInterval(checkInterval);
                            fd5InitMap('{$idField}', {$this->getDefaultLat()}, {$this->getDefaultLon()}, {$this->getZoom()}, {$readOnlyStr}, {$geoJsonPathStr});
                        }
                    }, 100);
                }
            }, 100);
        ");

        // Adiciona todos os componentes ao container wrapper
        $divWrapper->add($linkCss);
        $divWrapper->add($scriptJsLeaflet);
        $divWrapper->add($scriptJsMap);
        $divWrapper->add($divMap);
        $divWrapper->add($adiantiObjLat);
        $divWrapper->add($adiantiObjLon);
        $divWrapper->add($scriptInit);

        return $divWrapper;
    }
}
