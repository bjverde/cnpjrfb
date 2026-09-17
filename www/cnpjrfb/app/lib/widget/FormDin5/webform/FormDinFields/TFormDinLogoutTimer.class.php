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
 * Classe para um relogio de logout por tempo de inatividade.
 * Verifica se o usuário está inativo e, após um tempo configurado,
 * exibe avisos visuais e sonoros antes de efetuar o logout automático.
 * 
 * 
 * @author Reinaldo A. Barrêto Junior
 */
class TFormDinLogoutTimer extends TFormDinGenericField
{
    protected $adiantiObj;
    protected $idDivLogoutTimer;

    // === ATRIBUTOS DE CONFIGURAÇÃO ===
    // Tempos
    private $timeout_seconds = 60;
    private $timeout_ms = 60000;
    private $check_interval = 1000;
    
    // Mensagens
    private $msg_final = 'Sessão finalizada por inatividade';
    private $titulo_sessao = 'Sessão Expirada';
    
    // Cores e estilos normais (verde)
    private $normal_fonte_cor = '#28a745';
    private $normal_fonte_size = '1.5em';
    private $normal_fonte_weight = 'normal';
    
    // Configurações de aviso (amarelo/laranja)
    private $aviso_limite_superior = 0.5;
    private $aviso_fonte_cor = '#ffc107';
    private $aviso_fundo = '#fff3cd';
    private $aviso_borda = '4px solid #f39c12';
    private $aviso_fonte_weight = 'bold';
    
    // Configurações críticas (branco/vermelho)
    private $critico_limite_superior = 0.15;
    private $critico_fonte_cor = '#ffffff';
    private $critico_fundo = '#dc3545';
    private $critico_borda = '4px solid #bd2130';
    private $critico_fonte_weight = 'bold';
    
    // URLs
    private $logout_url = 'index.php?class=LoginForm&method=onLogout&static=1';
    
    // Eventos monitorados
    private $events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click', 'keyup'];
    
    // Configurações de áudio
    private $audio_enabled = true;
    private $audio_frequency_normal = 800;
    private $audio_frequency_critical = 1000;
    private $audio_beeps_normal = 3;
    private $audio_beeps_critical = 5;
    private $audio_volume = 0.2;
    
    // Configurações de debug
    private $debug = false;
    
    // Configurações do ícone
    private $icon_class = 'fas fa-clock';
    private $icon_color = '';
    private $icon_size = '';
    private $icon_margin = '10px';



    /**
     * Construtor do Timer de Logout por Inatividade
     *
     * @param string  $idField         -01: ID do campo
     * @param string  $label           -02: Label do campo, usado para validações
     * @param int     $timeoutSeconds  -03: Tempo limite em segundos para logout automático. Default: 60 segundos
     * @param boolean $debug           -04: Ativa debug no JavaScript. Default FALSE = sem debug, TRUE = com debug
     * @return TElement
     */
    public function __construct(string $idField
                               ,string $label
                               ,int $timeoutSeconds = 60
                               ,bool $debug = false
                               )
    {
        $this->setTimeoutSeconds($timeoutSeconds);
        $this->debug = $debug;
        $this->setIdDivLogoutTimer($idField);     
        $adiantiObj = $this->getDivLogoutTimer($idField);
        parent::__construct($adiantiObj,$this->getIdDivLogoutTimer(),$label,false,null,null);
        $this->setLabel($label,false);

        return $this->getAdiantiObj();
    }

    
    /**
     * Método mágico para GET/SET de propriedades
     * Converte camelCase para snake_case (ex: getTimeoutSeconds -> timeout_seconds)
     * 
     * @param string $method Nome do método
     * @param array $args Argumentos do método
     * @return mixed Valor da propriedade ou true para setters
     * @throws Exception Se a propriedade não existir
     */
    public function __call($method, $args)
    {
        // Getter mágico (ex: getTimeoutSeconds -> timeout_seconds)
        if (strpos($method, 'get') === 0) {
            $property = $this->camelToSnake(substr($method, 3));
            
            if (property_exists($this, $property)) {
                return $this->$property;
            }
            
            throw new Exception("Propriedade '{$property}' não existe em TFormDinLogoutTimer");
        }
        
        // Setter mágico (ex: setTimeoutSeconds -> timeout_seconds)
        if (strpos($method, 'set') === 0) {
            $property = $this->camelToSnake(substr($method, 3));
            
            if (property_exists($this, $property)) {
                $this->$property = $args[0];
                
                // Se alterar timeout_seconds, atualizar timeout_ms automaticamente
                if ($property === 'timeout_seconds') {
                    $this->updateTimeoutMs();
                }
                
                return true;
            }
            
            throw new Exception("Propriedade '{$property}' não existe em TFormDinLogoutTimer");
        }
        
        throw new Exception("Método '{$method}' não existe em TFormDinLogoutTimer");
    }
    
    /**
     * Converte camelCase para snake_case
     * 
     * @param string $input String em camelCase
     * @return string String em snake_case
     */
    private function camelToSnake($input)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $input));
    }
    
    /**
     * Obtém configuração específica
     * 
     * @param string $key Chave da configuração
     * @param mixed $default Valor padrão se não encontrar
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (property_exists($this, $key)) {
            return $this->$key;
        }
        
        return $default;
    }
    
    /**
     * Define configuração específica
     * 
     * @param string $key Chave da configuração
     * @param mixed $value Valor da configuração
     * @return bool
     */
    public function set($key, $value)
    {
        if (property_exists($this, $key)) {
            $this->$key = $value;
            
            // Se alterar timeout_seconds, atualizar timeout_ms automaticamente
            if ($key === 'timeout_seconds') {
                $this->updateTimeoutMs();
            }
            
            return true;
        }
        
        return false;
    }

    /**
     * Métodos de conveniência para timeout
     */
    public function getTimeoutSeconds()
    {
        return $this->timeout_seconds;
    }
    
    public function setTimeoutSeconds($seconds)
    {
        $this->timeout_seconds = (int)$seconds;
        $this->updateTimeoutMs();
    }
    
    /**
     * Atualiza automaticamente timeout_ms baseado em timeout_seconds
     * 
     * @return void
     */
    private function updateTimeoutMs()
    {
        $this->timeout_ms = $this->timeout_seconds * 1000;
    }
    
    public function getTimeoutMs()
    {
        return $this->timeout_ms;
    }
    
    /**
     * Métodos de conveniência para cores
     */
    public function getNormalColor()
    {
        return $this->normal_fonte_cor;
    }
    
    public function setNormalColor($color)
    {
        $this->normal_fonte_cor = $color;
    }
    
    public function getWarningColor()
    {
        return $this->aviso_fonte_cor;
    }
    
    public function setWarningColor($color)
    {
        $this->aviso_fonte_cor = $color;
    }
    
    public function getCriticalColor()
    {
        return $this->critico_fonte_cor;
    }
    
    public function setCriticalColor($color)
    {
        $this->critico_fonte_cor = $color;
    }
    
    /**
     * Métodos de conveniência para limites
     */
    public function getWarningLimit()
    {
        return $this->aviso_limite_superior;
    }
    
    public function setWarningLimit($limit)
    {
        $this->aviso_limite_superior = (float)$limit;
    }
    
    public function getCriticalLimit()
    {
        return $this->critico_limite_superior;
    }
    
    public function setCriticalLimit($limit)
    {
        $this->critico_limite_superior = (float)$limit;
    }
    
    /**
     * Métodos de conveniência para áudio
     */
    public function isAudioEnabled()
    {
        return $this->audio_enabled;
    }
    
    public function setAudioEnabled($enabled)
    {
        $this->audio_enabled = (bool)$enabled;
    }
    
    public function getAudioVolume()
    {
        return $this->audio_volume;
    }
    
    public function setAudioVolume($volume)
    {
        $this->audio_volume = max(0.0, min(1.0, (float)$volume));
    }
    
    /**
     * Métodos de conveniência para configuração do ícone
     */
    public function getIconClass()
    {
        return $this->icon_class;
    }
    
    public function setIconClass($iconClass)
    {
        $this->icon_class = $iconClass;
    }
    
    public function getIconColor()
    {
        return $this->icon_color;
    }
    
    public function setIconColor($color)
    {
        $this->icon_color = $color;
    }
    
    public function getIconSize()
    {
        return $this->icon_size;
    }
    
    public function setIconSize($size)
    {
        $this->icon_size = $size;
    }
    
    public function getIconMargin()
    {
        return $this->icon_margin;
    }
    
    public function setIconMargin($margin)
    {
        $this->icon_margin = $margin;
    }
    
    /**
     * Gera o estilo CSS inline para o ícone
     * 
     * @return string Estilo CSS para o ícone
     */
    private function getIconStyle()
    {
        $styles = [];
        
        if (!empty($this->icon_color)) {
            $styles[] = "color: {$this->icon_color}";
        }
        
        if (!empty($this->icon_size)) {
            $styles[] = "font-size: {$this->icon_size}";
        }
        
        if (!empty($this->icon_margin)) {
            $styles[] = "margin-right: {$this->icon_margin}";
        }
        
        return empty($styles) ? 'margin-right: 10px;' : implode('; ', $styles) . ';';
    }
    
    /**
     * Métodos de conveniência para eventos monitorados
     */
    public function getEvents()
    {
        return $this->events;
    }
    
    /**
     * Define os eventos que serão monitorados para detectar atividade do usuário
     * 
     * Eventos JavaScript disponíveis:
     * 
     * EVENTOS DE MOUSE:
     * - 'mousedown'    : Quando o botão do mouse é pressionado
     * - 'mouseup'      : Quando o botão do mouse é solto
     * - 'mousemove'    : Quando o mouse se move
     * - 'click'        : Quando ocorre um clique
     * - 'dblclick'     : Quando ocorre um duplo clique
     * - 'contextmenu'  : Quando o menu de contexto é aberto (botão direito)
     * - 'wheel'        : Quando a roda do mouse é usada
     * 
     * EVENTOS DE TECLADO:
     * - 'keydown'      : Quando uma tecla é pressionada
     * - 'keyup'        : Quando uma tecla é solta
     * - 'keypress'     : Quando uma tecla é pressionada e solta (deprecated, use keydown)
     * 
     * EVENTOS DE TOQUE (TOUCH):
     * - 'touchstart'   : Quando o toque na tela inicia
     * - 'touchend'     : Quando o toque na tela termina
     * - 'touchmove'    : Quando o dedo se move na tela
     * - 'touchcancel'  : Quando o toque é cancelado
     * 
     * EVENTOS DE FOCO:
     * - 'focus'        : Quando um elemento ganha foco
     * - 'blur'         : Quando um elemento perde foco
     * - 'focusin'      : Quando um elemento ou seus filhos ganham foco
     * - 'focusout'     : Quando um elemento ou seus filhos perdem foco
     * 
     * EVENTOS DE NAVEGAÇÃO:
     * - 'scroll'       : Quando a página é rolada
     * - 'resize'       : Quando a janela é redimensionada
     * - 'beforeunload' : Antes da página ser descarregada
     * - 'pagehide'     : Quando a página fica oculta
     * - 'pageshow'     : Quando a página fica visível
     * 
     * EVENTOS DE FORMULÁRIO:
     * - 'input'        : Quando o valor de um campo de entrada muda
     * - 'change'       : Quando o valor de um elemento muda
     * - 'select'       : Quando texto é selecionado
     * - 'submit'       : Quando um formulário é enviado
     * 
     * CONFIGURAÇÃO PADRÃO RECOMENDADA:
     * ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click', 'keyup']
     * 
     * CONFIGURAÇÃO MÍNIMA (apenas interações essenciais):
     * ['mousedown', 'keydown', 'touchstart']
     * 
     * CONFIGURAÇÃO COMPLETA (máxima sensibilidade):
     * ['mousedown', 'mouseup', 'mousemove', 'click', 'keydown', 'keyup', 
     *  'touchstart', 'touchend', 'touchmove', 'scroll', 'focus', 'input']
     * 
     * @param array $events Array com os nomes dos eventos JavaScript a serem monitorados
     * @return bool True se configurado com sucesso
     * @throws InvalidArgumentException Se o array estiver vazio ou contiver eventos inválidos
     * 
     * @example
     * // Configuração padrão
     * $timer->setEvents(['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click', 'keyup']);
     * 
     * // Apenas eventos de mouse e teclado
     * $timer->setEvents(['mousedown', 'keydown']);
     * 
     * // Incluindo eventos de toque para dispositivos móveis
     * $timer->setEvents(['mousedown', 'keydown', 'touchstart', 'touchmove']);
     */
    public function setEvents(array $events)
    {
        // Validação: array não pode estar vazio
        if (empty($events)) {
            throw new InvalidArgumentException('O array de eventos não pode estar vazio');
        }
        
        // Lista de eventos JavaScript válidos
        $validEvents = [
            // Mouse
            'mousedown', 'mouseup', 'mousemove', 'click', 'dblclick', 'contextmenu', 'wheel',
            // Teclado
            'keydown', 'keyup', 'keypress',
            // Toque
            'touchstart', 'touchend', 'touchmove', 'touchcancel',
            // Foco
            'focus', 'blur', 'focusin', 'focusout',
            // Navegação
            'scroll', 'resize', 'beforeunload', 'pagehide', 'pageshow',
            // Formulário
            'input', 'change', 'select', 'submit'
        ];
        
        // Validação: verificar se todos os eventos são válidos
        $invalidEvents = array_diff($events, $validEvents);
        if (!empty($invalidEvents)) {
            throw new InvalidArgumentException(
                'Eventos inválidos encontrados: ' . implode(', ', $invalidEvents) . 
                '. Eventos válidos: ' . implode(', ', $validEvents)
            );
        }
        
        // Remove duplicatas e reindexar array
        $this->events = array_values(array_unique($events));
        
        return true;
    }
    
    /**
     * Adiciona um evento à lista de eventos monitorados
     * 
     * @param string $event Nome do evento JavaScript a ser adicionado
     * @return bool True se adicionado com sucesso
     * @throws InvalidArgumentException Se o evento for inválido
     * 
     * @example
     * $timer->addEvent('resize');       // Adiciona monitoramento de redimensionamento
     * $timer->addEvent('beforeunload'); // Adiciona monitoramento antes de sair da página
     */
    public function addEvent(string $event)
    {
        // Lista de eventos JavaScript válidos
        $validEvents = [
            // Mouse
            'mousedown', 'mouseup', 'mousemove', 'click', 'dblclick', 'contextmenu', 'wheel',
            // Teclado
            'keydown', 'keyup', 'keypress',
            // Toque
            'touchstart', 'touchend', 'touchmove', 'touchcancel',
            // Foco
            'focus', 'blur', 'focusin', 'focusout',
            // Navegação
            'scroll', 'resize', 'beforeunload', 'pagehide', 'pageshow',
            // Formulário
            'input', 'change', 'select', 'submit'
        ];
        
        // Validação
        if (!in_array($event, $validEvents)) {
            throw new InvalidArgumentException(
                "Evento inválido: '{$event}'. Eventos válidos: " . implode(', ', $validEvents)
            );
        }
        
        // Adiciona apenas se não existir
        if (!in_array($event, $this->events)) {
            $this->events[] = $event;
        }
        
        return true;
    }
    
    /**
     * Remove um evento da lista de eventos monitorados
     * 
     * @param string $event Nome do evento JavaScript a ser removido
     * @return bool True se removido com sucesso
     * 
     * @example
     * $timer->removeEvent('scroll');    // Remove monitoramento de rolagem
     * $timer->removeEvent('mousemove'); // Remove monitoramento de movimento do mouse
     */
    public function removeEvent(string $event)
    {
        $key = array_search($event, $this->events);
        if ($key !== false) {
            unset($this->events[$key]);
            $this->events = array_values($this->events); // Reindexar array
            return true;
        }
        
        return false;
    }
    
    /**
     * Verifica se um evento está sendo monitorado
     * 
     * @param string $event Nome do evento JavaScript
     * @return bool True se o evento está sendo monitorado
     * 
     * @example
     * if ($timer->hasEvent('touchstart')) {
     *     echo 'Eventos de toque estão sendo monitorados';
     * }
     */
    public function hasEvent(string $event)
    {
        return in_array($event, $this->events);
    }

    /**
     * Obtém as configurações completas do totem
     * 
     * @return array Configurações do totem
     */
    public function getConfig()
    {
        return [
            // === TEMPOS ===
            'timeout_seconds' => $this->getTimeoutSeconds(),
            'timeout_ms' => $this->getTimeoutMs(),
            'check_interval' => $this->check_interval,
            
            // === MENSAGENS ===
            'msg_final' => $this->msg_final,
            'titulo_sessao' => $this->titulo_sessao,
            
            // === CORES E ESTILOS NORMAIS (VERDE) ===
            'normal_fonte_cor' => $this->normal_fonte_cor,
            'normal_fonte_size' => $this->normal_fonte_size,
            'normal_fonte_weight' => $this->normal_fonte_weight,
            
            // === CONFIGURAÇÕES DE AVISO (AMARELO/LARANJA) ===
            'aviso_limite_superior' => $this->aviso_limite_superior,
            'aviso_fonte_cor' => $this->aviso_fonte_cor,
            'aviso_fundo' => $this->aviso_fundo,
            'aviso_borda' => $this->aviso_borda,
            'aviso_fonte_weight' => $this->aviso_fonte_weight,
            
            // === CONFIGURAÇÕES CRÍTICAS (BRANCO/VERMELHO) ===
            'critico_limite_superior' => $this->critico_limite_superior,
            'critico_fonte_cor' => $this->critico_fonte_cor,
            'critico_fundo' => $this->critico_fundo,
            'critico_borda' => $this->critico_borda,
            'critico_fonte_weight' => $this->critico_fonte_weight,
            
            // === URLS ===
            'logout_url' => $this->logout_url,
            
            // === EVENTOS MONITORADOS ===
            'events' => $this->events,
            
            // === CONFIGURAÇÕES DE ÁUDIO ===
            'audio_enabled' => $this->audio_enabled,
            'audio_frequency_normal' => $this->audio_frequency_normal,
            'audio_frequency_critical' => $this->audio_frequency_critical,
            'audio_beeps_normal' => $this->audio_beeps_normal,
            'audio_beeps_critical' => $this->audio_beeps_critical,
            'audio_volume' => $this->audio_volume,
            
            // === CONFIGURAÇÕES DE DEBUG ===
            'debug' => $this->debug,
            
            // === CONFIGURAÇÕES DO ÍCONE ===
            'icon_class' => $this->icon_class,
            'icon_color' => $this->icon_color,
            'icon_size' => $this->icon_size,
            'icon_margin' => $this->icon_margin,
        ];
    }    
    
    /**
     * Reset para valores padrão
     */
    public function resetToDefaults()
    {
        $this->timeout_seconds = 60;
        $this->updateTimeoutMs();  // Calcula automaticamente timeout_ms
        $this->check_interval = 1000;
        $this->msg_final = 'Sessão finalizada por inatividade';
        $this->titulo_sessao = 'Sessão Expirada';
        $this->normal_fonte_cor = '#28a745';
        $this->normal_fonte_size = '1.5em';
        $this->normal_fonte_weight = 'normal';
        $this->aviso_limite_superior = 0.5;
        $this->aviso_fonte_cor = '#ffc107';
        $this->aviso_fundo = '#fff3cd';
        $this->aviso_borda = '4px solid #f39c12';
        $this->aviso_fonte_weight = 'bold';
        $this->critico_limite_superior = 0.15;
        $this->critico_fonte_cor = '#ffffff';
        $this->critico_fundo = '#dc3545';
        $this->critico_borda = '4px solid #bd2130';
        $this->critico_fonte_weight = 'bold';
        $this->logout_url = 'index.php?class=LoginForm&method=onLogout&static=1';
        $this->events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click', 'keyup'];
        $this->audio_enabled = true;
        $this->audio_frequency_normal = 800;
        $this->audio_frequency_critical = 1000;
        $this->audio_beeps_normal = 3;
        $this->audio_beeps_critical = 5;
        $this->audio_volume = 0.2;
        $this->debug = false;
        
        // Resetar configurações do ícone
        $this->icon_class = 'fas fa-clock';
        $this->icon_color = '';
        $this->icon_size = '';
        $this->icon_margin = '10px';
    }

    /**
     * Converte configurações para JavaScript
     * 
     * @return string JSON das configurações
     */
    public function toJavaScript()
    {
        $config = $this->getConfig();
        return json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Obtém apenas configurações visuais
     * 
     * @return array
     */
    public function getVisualConfig()
    {
        return [
            'normal' => [
                'fonte_cor' => $this->normal_fonte_cor,
                'fonte_size' => $this->normal_fonte_size,
                'fonte_weight' => $this->normal_fonte_weight,
            ],
            'aviso' => [
                'fonte_cor' => $this->aviso_fonte_cor,
                'fundo' => $this->aviso_fundo,
                'borda' => $this->aviso_borda,
                'fonte_weight' => $this->aviso_fonte_weight,
                'limite' => $this->aviso_limite_superior,
            ],
            'critico' => [
                'fonte_cor' => $this->critico_fonte_cor,
                'fundo' => $this->critico_fundo,
                'borda' => $this->critico_borda,
                'fonte_weight' => $this->critico_fonte_weight,
                'limite' => $this->critico_limite_superior,
            ],
        ];
    }
    //--------------------------------------------------------------------
    public function setIdDivLogoutTimer($idDivLogoutTimer)
    {
        $this->idDivLogoutTimer = $idDivLogoutTimer;
    }
    public function getIdDivLogoutTimer(){
        return $this->idDivLogoutTimer;
    }
    //--------------------------------------------------------------------
    private function getDivLogoutTimer($idField){
        
        $scriptMain = new TElement('script');
        $scriptMain->setProperty('src', 'app/lib/widget/FormDin5/javascript/FormDin5LogoutTimer.js?v='.FormDinHelper::version());
        $scriptMain->setProperty('type','text/javascript');
        
        $scriptInit = new TElement('script');
        $scriptInit->setProperty('src', 'app/lib/widget/FormDin5/javascript/FormDin5LogoutTimerInit.js?v='.FormDinHelper::version());
        $scriptInit->setProperty('type','text/javascript');
       
        $js_content = "
        var FORMDIN5_LOGOUT_TIMER_CONFIG = ".$this->toJavaScript().";
        
        // Função de debug inline
        function inlineDebugLog() {
            if (FORMDIN5_LOGOUT_TIMER_CONFIG && FORMDIN5_LOGOUT_TIMER_CONFIG.debug) {
                console.log.apply(console, arguments);
            }
        }
        
        // Inicialização automática (será executada pelo FormDin5LogoutTimerInit.js)
        inlineDebugLog('FormDin5LogoutTimer configurado e pronto para inicialização');
        
        // Força inicialização após pequeno delay
        setTimeout(function() {
            inlineDebugLog('=== FORÇANDO INICIALIZAÇÃO ===');
            inlineDebugLog('FormDin5LogoutTimer disponível:', typeof FormDin5LogoutTimer !== 'undefined');
            inlineDebugLog('initFormDin5LogoutTimer disponível:', typeof initFormDin5LogoutTimer !== 'undefined');
            
            if (typeof initFormDin5LogoutTimer === 'function') {
                inlineDebugLog('Chamando initFormDin5LogoutTimer...');
                initFormDin5LogoutTimer(FORMDIN5_LOGOUT_TIMER_CONFIG);
            } else if (typeof FormDin5LogoutTimer !== 'undefined' && typeof FormDin5LogoutTimer.init === 'function') {
                inlineDebugLog('Chamando FormDin5LogoutTimer.init diretamente...');
                FormDin5LogoutTimer.init(FORMDIN5_LOGOUT_TIMER_CONFIG);
            } else {
                console.error('Nenhum método de inicialização encontrado!');
                inlineDebugLog('Variáveis disponíveis:', Object.keys(window));
            }
        }, 1000);
        ";
        $scriptConfig = new TElement('script');
        $scriptConfig->type = 'text/javascript';
        $scriptConfig->add($js_content);                                
        
        $divDivLogoutTimer = new TElement('div');
        $divDivLogoutTimer->class = 'fd5DivLogoutTimer';
        $divDivLogoutTimer->setProperty('id',$this->getIdDivLogoutTimer().'_div');
        $divDivLogoutTimer->add('
                <div id="countdown-display">
                    <i class="' . $this->icon_class . '" style="' . $this->getIconStyle() . '"></i><span id="countdown-timer">--</span>
                </div>
        ');

        $divDivLogoutTimer->add($scriptMain);
        $divDivLogoutTimer->add($scriptInit);
        $divDivLogoutTimer->add($scriptConfig);
        return $divDivLogoutTimer;
    }
}