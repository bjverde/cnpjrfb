/*
 * ----------------------------------------------------------------------------
 * Formdin 5 Framework
 * SourceCode https://github.com/bjverde/formDin5
 * @author Reinaldo A. Barrêto Junior
 * 
 * É uma reconstrução do FormDin 4 Sobre o Adianti 7.X
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
 * Este programa é distribuí1do na esperança que possa ser útil, mas SEM NENHUMA
 * GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou
 * APLICAÇÃO EM PARTICULAR. Veja a Licen?a Pública Geral GNU/LGPL em portugu?s
 * para maiores detalhes.
 *
 * Você deve ter recebido uma cópia da GNU LGPL versão 3, sob o título
 * "LICENCA.txt", junto com esse programa. Se não, acesse <http://www.gnu.org/licenses/>
 * ou escreva para a Fundação do Software Livre (FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA 02111-1301, USA.
 */


/**
 * Controle de inatividade por tempo
 * 
 * @author Reinaldo A. Barrêto Junior
 */

// Proteção contra redeclaração
if (typeof window.FormDin5LogoutTimer !== 'undefined') {
    console.warn('FormDin5LogoutTimer já foi carregado, pulando redeclaração');
} else {

class FormDin5LogoutTimer {
    constructor(config = {}) {
        // Configurações padrão (compatíveis com TotemConfig.php)
        this.config = {
            // Tempos (usando nomes do TotemConfig.php)
            inactivityTimeout: config.timeout_ms || config.inactivityTimeout || 60000,
            checkInterval: config.check_interval || config.checkInterval || 1000,
            
            // Mensagens (usando nomes do TotemConfig.php)
            msg_final: config.msg_final || 'Sessão finalizada por inatividade',
            titulo_sessao: config.titulo_sessao || 'Sessão Expirada',
            
            // Cores normais (usando nomes do TotemConfig.php)
            normal_fonte_cor: config.normal_fonte_cor || '#28a745',
            normal_fonte_size: config.normal_fonte_size || '1.5em',
            normal_fonte_weight: config.normal_fonte_weight || 'normal',
            
            // Configurações de aviso (usando nomes do TotemConfig.php)
            aviso_limite_superior: config.aviso_limite_superior || 0.5,
            aviso_fonte_cor: config.aviso_fonte_cor || '#ffc107',
            aviso_fundo: config.aviso_fundo || '#fff3cd',
            aviso_borda: config.aviso_borda || '4px solid #f39c12',
            aviso_fonte_weight: config.aviso_fonte_weight || 'bold',
            
            // Configurações críticas (usando nomes do TotemConfig.php)
            critico_limite_superior: config.critico_limite_superior || 0.15,
            critico_fonte_cor: config.critico_fonte_cor || '#ffffff',
            critico_fundo: config.critico_fundo || '#dc3545',
            critico_borda: config.critico_borda || '4px solid #bd2130',
            critico_fonte_weight: config.critico_fonte_weight || 'bold',
            
            // URLs (usando nomes do TotemConfig.php)
            logoutUrl: config.logout_url || config.logoutUrl || 'index.php?class=LoginForm&method=onLogout&static=1',
            
            // Eventos monitorados (usando nomes do TotemConfig.php)
            events: config.events || ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click', 'keyup'],
            
            // Configurações de áudio (usando nomes do TotemConfig.php)
            audio_enabled: config.audio_enabled !== undefined ? config.audio_enabled : true,
            audio_frequency_normal: config.audio_frequency_normal || 800,
            audio_frequency_critical: config.audio_frequency_critical || 1000,
            audio_beeps_normal: config.audio_beeps_normal || 3,
            audio_beeps_critical: config.audio_beeps_critical || 5,
            audio_volume: config.audio_volume || 0.2,
            
            // Configurações de debug
            debug: config.debug !== undefined ? config.debug : false
        };
        
        // Estados internos
        this.timer = null;
        this.warningTimer = null;
        this.lastActivity = Date.now();
        this.countdownInterval = null;
        this.currentState = 'normal'; // normal, aviso, critico
        
        // Elementos DOM
        this.elements = {}; // ← FIX: Inicializa o objeto elements
        this.timerElement = null;
        this.parentElement = null;
        
        // Função de debug condicional
        this.debugLog = (...args) => {
            if (this.config.debug) {
                console.log(...args);
            }
        };
        
        this.init();
    }
    
    /**
     * Inicializa o sistema
     */
    init() {
        this.debugLog('=== INICIANDO FORMDIN5 LOGOUT TIMER ===');
        this.debugLog('Configurações recebidas:', this.config);
        
        this.debugLog('1. Procurando elementos...');
        const elementsFound = this.findElements();
        this.debugLog('Elementos encontrados:', elementsFound);
        
        this.debugLog('2. Vinculando eventos...');
        this.bindEvents();
        
        this.debugLog('3. Resetando timer...');
        this.resetTimer();
        
        this.debugLog('4. Iniciando countdown...');
        this.startCountdown();
        
        this.debugLog('=== FORMDIN5 LOGOUT TIMER INICIALIZADO ===');
        this.debugLog('Timeout configurado:', this.config.inactivityTimeout / 1000 + 's');
        this.debugLog('Aviso em:', Math.round(this.config.inactivityTimeout * this.config.aviso_limite_superior / 1000) + 's');
        this.debugLog('Crítico em:', Math.round(this.config.inactivityTimeout * this.config.critico_limite_superior / 1000) + 's');
    }
    
    /**
     * Encontra elementos DOM necessários
     */
    findElements() {
        this.debugLog('=== PROCURANDO ELEMENTOS DOM ===');
        
        // Procura primeiro pelos elementos específicos da interface TotemView.php
        this.elements.countdownDisplay = document.getElementById('countdown-display');
        this.elements.countdownTimer = document.getElementById('countdown-timer');

        this.debugLog('countdown-display encontrado:', !!this.elements.countdownDisplay);
        this.debugLog('countdown-timer encontrado:', !!this.elements.countdownTimer);

        // Elementos fallback (se não encontrar os principais)
        if (!this.elements.countdownDisplay) {
            this.debugLog('Procurando countdown-display-fallback...');
            this.elements.countdownDisplay = document.getElementById('countdown-display-fallback');
        }
        
        if (!this.elements.countdownTimer) {
            this.debugLog('Procurando countdown-timer-fallback...');
            this.elements.countdownTimer = document.getElementById('countdown-timer-fallback');
        }



        // Define aliases para compatibilidade com métodos existentes
        this.timerElement = this.elements.countdownTimer;
        this.parentElement = this.elements.countdownDisplay;

        this.debugLog('timerElement definido:', !!this.timerElement);
        this.debugLog('parentElement definido:', !!this.parentElement);
        
        if (this.timerElement) {
            this.debugLog('timerElement ID:', this.timerElement.id);
            this.debugLog('timerElement conteúdo inicial:', this.timerElement.innerHTML);
        }

        this.debugLog('=== FIM PROCURA ELEMENTOS ===');

        return this.elements.countdownDisplay && this.elements.countdownTimer;
    }
    
    /**
     * Vincula eventos de atividade
     */
    bindEvents() {
        const self = this;
        
        this.config.events.forEach(eventType => {
            document.addEventListener(eventType, () => {
                self.onActivity();
            }, { passive: true, capture: true });
        });
        
        // Visibilidade da página
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                self.onActivity();
            }
        });
    }
    
    /**
     * Manipula atividade do usuário
     */
    onActivity() {
        const now = Date.now();
        this.debugLog('*** ATIVIDADE DETECTADA ***');
        this.debugLog('Timestamp:', new Date(now).toLocaleTimeString());
        
        this.lastActivity = now;
        this.resetTimer();
        this.setState('normal');
        
        this.debugLog('Timer resetado, voltando ao estado normal');
    }
    
    /**
     * Reseta todos os timers
     */
    resetTimer() {
        this.clearTimers();
        this.setTimers();
    }
    
    /**
     * Limpa timers existentes
     */
    clearTimers() {
        if (this.timer) {
            clearTimeout(this.timer);
            this.timer = null;
        }
        
        if (this.warningTimer) {
            clearTimeout(this.warningTimer);
            this.warningTimer = null;
        }
    }
    
    /**
     * Configura novos timers
     */
    setTimers() {
        const self = this;
        
        // Timer para logout final
        this.timer = setTimeout(() => {
            self.executeLogout();
        }, this.config.inactivityTimeout);
    }
    
    /**
     * Define o estado visual atual
     */
    setState(state) {
        if (this.currentState === state) return;
        
        this.currentState = state;
        
        // Reset classes CSS
        if (this.parentElement) {
            this.parentElement.className = this.parentElement.className.replace(/countdown-(inactive|normal|warning|critical)/g, '');
            this.parentElement.style.backgroundColor = '';
            this.parentElement.style.borderLeft = '';
        }
        document.body.style.backgroundColor = '';
        
        switch (state) {
            case 'normal':
                if (this.parentElement) {
                    this.parentElement.className += ' countdown-normal';
                }
                if (this.timerElement) {
                    this.timerElement.style.color = this.config.normal_fonte_cor;
                    this.timerElement.style.fontSize = this.config.normal_fonte_size;
                    this.timerElement.style.fontWeight = this.config.normal_fonte_weight;
                }
                break;
                
            case 'aviso':
                if (this.parentElement) {
                    this.parentElement.className += ' countdown-warning';
                    this.parentElement.style.backgroundColor = this.config.aviso_fundo;
                    this.parentElement.style.borderLeft = this.config.aviso_borda;
                }
                if (this.timerElement) {
                    this.timerElement.style.color = this.config.aviso_fonte_cor;
                    this.timerElement.style.fontWeight = this.config.aviso_fonte_weight;
                }
                
                if (this.config.audio_enabled) {
                    this.playAlertSound(false);
                }
                this.debugLog('Estado: AVISO - ' + Math.round(this.getRemainingTime() / 1000) + 's restantes');
                break;
                
            case 'critico':
                if (this.parentElement) {
                    this.parentElement.className += ' countdown-critical';
                    this.parentElement.style.backgroundColor = this.config.critico_fundo;
                    this.parentElement.style.borderLeft = this.config.critico_borda;
                }
                if (this.timerElement) {
                    this.timerElement.style.color = this.config.critico_fonte_cor;
                    this.timerElement.style.fontWeight = this.config.critico_fonte_weight;
                }
                
                // Fundo crítico na página
                document.body.style.backgroundColor = this.config.critico_fundo;
                
                if (this.config.audio_enabled) {
                    this.playAlertSound(true); // Som mais intenso
                }
                this.debugLog('Estado: CRÍTICO - ' + Math.round(this.getRemainingTime() / 1000) + 's restantes');
                break;
        }
    }
    

    
    /**
     * Inicia o countdown visual
     */
    startCountdown() {
        this.debugLog('=== INICIANDO COUNTDOWN ===');
        
        // Mostra o elemento de countdown se estiver oculto
        if (this.parentElement) {
            this.debugLog('Tornando elemento visível...');
            this.parentElement.style.display = 'block';
        } else {
            console.error('parentElement não encontrado!');
        }
        
        this.debugLog('Definindo estado inicial como normal...');
        this.setState('normal');
        
        this.debugLog('Fazendo primeira atualização do countdown...');
        this.updateCountdown();
        
        this.debugLog('Iniciando intervalo de 1000ms...');
        this.countdownInterval = setInterval(() => {
            this.updateCountdown();
            
            const remaining = this.getRemainingTime();
            if (remaining <= 0) {
                this.debugLog('TEMPO ESGOTADO - executando logout');
                this.executeLogout();
            }
        }, 1000);
        
        this.debugLog('=== COUNTDOWN INICIADO ===');
    }
    
    /**
     * Atualiza o countdown visual
     */
    updateCountdown() {
        const remaining = this.getRemainingTime();
        
        this.debugLog('=== UPDATE COUNTDOWN ===');
        this.debugLog('Tempo restante (ms):', remaining);
        this.debugLog('timerElement encontrado:', !!this.timerElement);
        this.debugLog('timerElement ID:', this.timerElement ? this.timerElement.id : 'N/A');
        
        if (remaining > 0) {
            // Formata o tempo
            const minutes = Math.floor(remaining / 60000);
            const seconds = Math.floor((remaining % 60000) / 1000);
            const timeString = minutes + 'm ' + seconds + 's';
            
            this.debugLog('Tempo formatado:', timeString);
            
            // Atualiza elemento principal do countdown
            if (this.timerElement) {
                this.debugLog('Atualizando countdown-timer com:', timeString);
                this.timerElement.innerHTML = timeString;
                this.debugLog('Conteúdo atual do elemento:', this.timerElement.innerHTML);
            } else {
                console.error('timerElement não encontrado! Procurando novamente...');
                // Tenta encontrar o elemento novamente
                this.timerElement = document.getElementById('countdown-timer');
                if (this.timerElement) {
                    this.debugLog('Elemento encontrado na segunda tentativa!');
                    this.timerElement.innerHTML = timeString;
                } else {
                    console.error('Elemento countdown-timer ainda não foi encontrado no DOM');
                }
            }
            

            
            // Define o estado baseado nos limites
            const percentRemaining = remaining / this.config.inactivityTimeout;
            this.debugLog('Percentual restante:', Math.round(percentRemaining * 100) + '%');
            
            if (percentRemaining <= this.config.critico_limite_superior) {
                this.debugLog('Mudando para estado CRÍTICO');
                this.setState('critico');
            } else if (percentRemaining <= this.config.aviso_limite_superior) {
                this.debugLog('Mudando para estado AVISO');
                this.setState('aviso');
            } else {
                this.debugLog('Mantendo estado NORMAL');
                this.setState('normal');
            }
        } else {
            // Tempo esgotado
            this.debugLog('TEMPO ESGOTADO!');
            if (this.timerElement) {
                this.timerElement.innerHTML = '0m 0s';
            }
            this.setState('critico');
        }
        
        this.debugLog('=== FIM UPDATE COUNTDOWN ===');
    }
    
    /**
     * Obtém tempo restante em millisegundos
     */
    getRemainingTime() {
        const elapsed = Date.now() - this.lastActivity;
        return Math.max(0, this.config.inactivityTimeout - elapsed);
    }
    
    /**
     * Reproduz som de alerta usando configurações do TotemConfig.php
     */
    playAlertSound(intense = false) {
        if (!this.config.audio_enabled) {
            return; // Áudio desabilitado via configuração
        }
        
        try {
            const context = new (window.AudioContext || window.webkitAudioContext)();
            const beeps = intense ? this.config.audio_beeps_critical : this.config.audio_beeps_normal;
            const baseFrequency = intense ? this.config.audio_frequency_critical : this.config.audio_frequency_normal;
            
            for (let i = 0; i < beeps; i++) {
                setTimeout(() => {
                    const oscillator = context.createOscillator();
                    const gain = context.createGain();
                    
                    oscillator.connect(gain);
                    gain.connect(context.destination);
                    
                    // Frequência varia no modo crítico para ser mais chamativo
                    oscillator.frequency.value = intense ? baseFrequency + (i * 200) : baseFrequency;
                    oscillator.type = 'sine';
                    gain.gain.setValueAtTime(this.config.audio_volume, context.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, context.currentTime + 0.3);
                    
                    oscillator.start();
                    oscillator.stop(context.currentTime + 0.3);
                }, i * 300);
            }
        } catch (error) {
            console.warn('Audio não suportado:', error.message);
        }
    }
    
    /**
     * Executa o logout automático usando configurações do TotemConfig.php
     */
    executeLogout() {
        this.debugLog('Executando logout automático');
        
        this.clearTimers();
        if (this.countdownInterval) {
            clearInterval(this.countdownInterval);
        }
        
        // Mostra mensagem personalizada usando configurações
        if (typeof Adianti !== 'undefined' && Adianti.dialog) {
            Adianti.dialog.info(this.config.titulo_sessao, this.config.msg_final, () => {
                this.redirectToLogout();
            });
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: this.config.titulo_sessao,
                text: this.config.msg_final,
                icon: 'warning',
                confirmButtonText: 'OK'
            }).then(() => {
                this.redirectToLogout();
            });
        } else {
            alert(this.config.msg_final);
            this.redirectToLogout();
        }
    }
    
    /**
     * Redireciona para logout
     */
    redirectToLogout() {
        window.location.href = this.config.logoutUrl;
    }
    
    /**
     * Para o sistema
     */
    stop() {
        this.clearTimers();
        if (this.countdownInterval) {
            clearInterval(this.countdownInterval);
        }
        this.setState('normal');
        this.debugLog('FormDin5LogoutTimer parado');
    }
    
    /**
     * Obtém status atual
     */
    getStatus() {
        return {
            isActive: this.timer !== null,
            currentState: this.currentState,
            timeRemaining: this.getRemainingTime(),
            lastActivity: new Date(this.lastActivity),
            config: this.config
        };
    }
}

// Função de debug estático
FormDin5LogoutTimer.debugLog = function(config, ...args) {
    if (config && config.debug) {
        console.log(...args);
    }
};

// Método estático para inicialização rápida
FormDin5LogoutTimer.init = function(config) {
    FormDin5LogoutTimer.debugLog(config, '=== FormDin5LogoutTimer.init() CHAMADO ===');
    FormDin5LogoutTimer.debugLog(config, 'Config recebida:', config);
    
    if (window.formDin5LogoutTimerControl) {
        FormDin5LogoutTimer.debugLog(config, 'Parando instância anterior...');
        window.formDin5LogoutTimerControl.stop();
    }
    
    FormDin5LogoutTimer.debugLog(config, 'Criando nova instância do FormDin5LogoutTimer...');
    window.formDin5LogoutTimerControl = new FormDin5LogoutTimer(config);
    
    FormDin5LogoutTimer.debugLog(config, '✅ FormDin5LogoutTimer.init() concluído');
    FormDin5LogoutTimer.debugLog(config, 'Instância criada:', !!window.formDin5LogoutTimerControl);
    
    return window.formDin5LogoutTimerControl;
};



// Torna disponível globalmente
window.FormDin5LogoutTimer = FormDin5LogoutTimer;

} // Fim da proteção contra redeclaração
