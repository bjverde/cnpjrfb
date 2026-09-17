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

class HtmlHelper
{
    public static function getViewPort() 
    {
        return '<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">';
    }

    public static function validateHtmlColorHexa($color) 
    {
        if ( !empty($color) && (preg_match('/^#[\D0-9]{6}\z/', $color) !== 1) ){
            throw new InvalidArgumentException(TFormDinMessage::ERROR_HTML_COLOR_HEXA);
        }
    }

    /**
     * Gera um link para API do WhatsApp
     *
     * @param string $numeroTelefone - formatado ou não com DDI e DDD
     * @param string $msg - mensagem que vai aparecer
     * @param boolean $iconeVerde - default é o icone verde
     * @return string
     */
    public static function linkApiWhatsApp(string $numeroTelefone, string $msg, bool $iconeVerde = true): string 
    {
        $numeroLimpo = str_replace([' ','-','(',')','+'],['','','','',''], $numeroTelefone);
        $icon = "<i class='fab fa-whatsapp green' aria-hidden='true'></i>";
        if($iconeVerde==false){
            $icon = "<i class='fab fa-whatsapp' aria-hidden='true'></i>";
        }
        $link =  "{$icon} <a target='newwindow' href='https://api.whatsapp.com/send?phone={$numeroLimpo}&text={$msg}'> {$numeroTelefone} </a>";
        return $link;
    }


    /**
     * Get List DDD 
     *
     * @return array
     */
    public static function getListDdd() 
    {
        $ddds = [
            68 => 'Acre - 68',
            82 => 'Alagoas - 82',
            96 => 'Amapá - 96',
            92 => 'Amazonas - 92',
            77 => 'Bahia - 77',
            73 => 'Bahia - 73',
            74 => 'Bahia - 74',
            75 => 'Bahia - 75',
            85 => 'Ceará - 85',
            88 => 'Ceará - 88',
            61 => 'Distrito Federal - 61',
            27 => 'Espírito Santo - 27',
            28 => 'Espírito Santo - 28',
            62 => 'Goiás - 62',
            64 => 'Goiás - 64',
            98 => 'Maranhão - 98',
            99 => 'Maranhão - 99',
            65 => 'Mato Grosso - 65',
            66 => 'Mato Grosso - 66',
            67 => 'Mato Grosso do Sul - 67',
            31 => 'Minas Gerais - 31',
            32 => 'Minas Gerais - 32',
            33 => 'Minas Gerais - 33',
            34 => 'Minas Gerais - 34',
            35 => 'Minas Gerais - 35',
            37 => 'Minas Gerais - 37',
            38 => 'Minas Gerais - 38',
            91 => 'Pará - 91',
            93 => 'Pará - 93',
            94 => 'Pará - 94',
            95 => 'Roraima - 95',
            41 => 'Paraná - 41',
            42 => 'Paraná - 42',
            43 => 'Paraná - 43',
            44 => 'Paraná - 44',
            45 => 'Paraná - 45',
            46 => 'Paraná - 46',
            79 => 'Sergipe - 79',
            11 => 'São Paulo - 11',
            12 => 'São Paulo - 12',
            13 => 'São Paulo - 13',
            14 => 'São Paulo - 14',
            15 => 'São Paulo - 15',
            16 => 'São Paulo - 16',
            17 => 'São Paulo - 17',
            18 => 'São Paulo - 18',
            19 => 'São Paulo - 19',
            21 => 'Rio de Janeiro - 21',
            22 => 'Rio de Janeiro - 22',
            24 => 'Rio de Janeiro - 24',
            47 => 'Santa Catarina - 47',
            48 => 'Santa Catarina - 48',
            49 => 'Santa Catarina - 49',
            69 => 'Rondônia - 69',
            63 => 'Tocantins - 63'
        ];

        return $ddds;
    }


    /**
     * Get List DDI whit emojis
     *
     * @return array
     */
    public static function getListDdi() 
    {
        $listPaises = array(
            "+93" => "+93 🇦🇫 Afeganistão",
            "+355" => "+355 🇦🇱 Albânia",
            "+213" => "+213 🇩🇿 Argélia",
            "+1-684" => "+1-684 🇦🇸 Samoa Americana",
            "+376" => "+376 🇦🇩 Andorra",
            "+244" => "+244 🇦🇴 Angola",
            "+1-264" => "+1-264 🇦🇮 Anguilla",
            "+672" => "+672 🇦🇶 Antártida",
            "+1-268" => "+1-268 🇦🇬 Antígua e Barbuda",
            "+54" => "+54 🇦🇷 Argentina",
            "+374" => "+374 🇦🇲 Armênia",
            "+297" => "+297 🇦🇼 Aruba",
            "+61" => "+61 🇦🇺 Austrália",
            "+43" => "+43 🇦🇹 Áustria",
            "+994" => "+994 🇦🇿 Azerbaijão",
            "+1-242" => "+1-242 🇧🇸 Bahamas",
            "+973" => "+973 🇧🇭 Bahrein",
            "+880" => "+880 🇧🇩 Bangladesh",
            "+1-246" => "+1-246 🇧🇧 Barbados",
            "+375" => "+375 🇧🇾 Belarus",
            "+32" => "+32 🇧🇪 Bélgica",
            "+501" => "+501 🇧🇿 Belize",
            "+229" => "+229 🇧🇯 Benin",
            "+1-441" => "+1-441 🇧🇲 Bermudas",
            "+975" => "+975 🇧🇹 Butão",
            "+591" => "+591 🇧🇴 Bolívia",
            "+387" => "+387 🇧🇦 Bósnia e Herzegovina",
            "+267" => "+267 🇧🇼 Botsuana",
            "+55" => "+55 🇧🇷 Brasil",
            "+246" => "+246 🇮🇴 Território Britânico do Oceano Índico",
            "+1-284" => "+1-284 🇻🇬 Ilhas Virgens Britânicas",
            "+673" => "+673 🇧🇳 Brunei",
            "+359" => "+359 🇧🇬 Bulgária",
            "+226" => "+226 🇧🇫 Burkina Faso",
            "+257" => "+257 🇧🇮 Burundi",
            "+855" => "+855 🇰🇭 Camboja",
            "+237" => "+237 🇨🇲 Camarões",
            "+1" => "+1 🇨🇦 Canadá",
            "+238" => "+238 🇨🇻 Cabo Verde",
            "+1-345" => "+1-345 🇰🇾 Ilhas Cayman",
            "+236" => "+236 🇨🇫 República Centro-Africana",
            "+235" => "+235 🇹🇩 Chade",
            "+56" => "+56 🇨🇱 Chile",
            "+86" => "+86 🇨🇳 China",
            "+61" => "+61 🇨🇽 Ilha Christmas",
            "+61" => "+61 🇨🇨 Ilhas Cocos",
            "+57" => "+57 🇨🇴 Colômbia",
            "+269" => "+269 🇰🇲 Comores",
            "+682" => "+682 🇨🇰 Ilhas Cook",
            "+506" => "+506 🇨🇷 Costa Rica",
            "+385" => "+385 🇭🇷 Croácia",
            "+53" => "+53 🇨🇺 Cuba",
            "+599" => "+599 🇨🇼 Curaçao",
            "+357" => "+357 🇨🇾 Chipre",
            "+420" => "+420 🇨🇿 República Tcheca",
            "+243" => "+243 🇨🇩 República Democrática do Congo",
            "+45" => "+45 🇩🇰 Dinamarca",
            "+253" => "+253 🇩🇯 Djibouti",
            "+1-767" => "+1-767 🇩🇲 Dominica",
            "+1-809" => "+1-809 🇩🇴 República Dominicana",
            "+670" => "+670 🇹🇱 Timor-Leste",
            "+593" => "+593 🇪🇨 Equador",
            "+20" => "+20 🇪🇬 Egito",
            "+503" => "+503 🇸🇻 El Salvador",
            "+240" => "+240 🇬🇶 Guiné Equatorial",
            "+291" => "+291 🇪🇷 Eritreia",
            "+372" => "+372 🇪🇪 Estônia",
            "+251" => "+251 🇪🇹 Etiópia",
            "+500" => "+500 🇫🇰 Ilhas Falkland",
            "+298" => "+298 🇫🇴 Ilhas Faroe",
            "+679" => "+679 🇫🇯 Fiji",
            "+358" => "+358 🇫🇮 Finlândia",
            "+33" => "+33 🇫🇷 França",
            "+689" => "+689 🇵🇫 Polinésia Francesa",
            "+241" => "+241 🇬🇦 Gabão",
            "+220" => "+220 🇬🇲 Gâmbia",
            "+995" => "+995 🇬🇪 Geórgia",
            "+49" => "+49 🇩🇪 Alemanha",
            "+233" => "+233 🇬🇭 Gana",
            "+350" => "+350 🇬🇮 Gibraltar",
            "+30" => "+30 🇬🇷 Grécia",
            "+299" => "+299 🇬🇱 Groenlândia",
            "+1-473" => "+1-473 🇬🇩 Granada",
            "+1-671" => "+1-671 🇬🇺 Guam",
            "+502" => "+502 🇬🇹 Guatemala",
            "+44-1481" => "+44-1481 🇬🇬 Guernsey",
            "+224" => "+224 🇬🇳 Guiné",
            "+245" => "+245 🇬🇼 Guiné-Bissau",
            "+592" => "+592 🇬🇾 Guiana",
            "+509" => "+509 🇭🇹 Haiti",
            "+504" => "+504 🇭🇳 Honduras",
            "+852" => "+852 🇭🇰 Hong Kong",
            "+36" => "+36 🇭🇺 Hungria",
            "+354" => "+354 🇮🇸 Islândia",
            "+91" => "+91 🇮🇳 Índia",
            "+62" => "+62 🇮🇩 Indonésia",
            "+98" => "+98 🇮🇷 Irã",
            "+964" => "+964 🇮🇶 Iraque",
            "+353" => "+353 🇮🇪 Irlanda",
            "+44-1624" => "+44-1624 🇮🇲 Ilha de Man",
            "+972" => "+972 🇮🇱 Israel",
            "+39" => "+39 🇮🇹 Itália",
            "+225" => "+225 🇨🇮 Costa do Marfim",
            "+1-876" => "+1-876 🇯🇲 Jamaica",
            "+81" => "+81 🇯🇵 Japão",
            "+44-1534" => "+44-1534 🇯🇪 Jersey",
            "+962" => "+962 🇯🇴 Jordânia",
            "+7" => "+7 🇰🇿 Cazaquistão",
            "+254" => "+254 🇰🇪 Quênia",
            "+686" => "+686 🇰🇮 Kiribati",
            "+383" => "+383 🇽🇰 Kosovo",
            "+965" => "+965 🇰🇼 Kuwait",
            "+996" => "+996 🇰🇬 Quirguistão",
            "+856" => "+856 🇱🇦 Laos",
            "+371" => "+371 🇱🇻 Letônia",
            "+961" => "+961 🇱🇧 Líbano",
            "+266" => "+266 🇱🇸 Lesoto",
            "+231" => "+231 🇱🇷 Libéria",
            "+218" => "+218 🇱🇾 Líbia",
            "+423" => "+423 🇱🇮 Liechtenstein",
            "+370" => "+370 🇱🇹 Lituânia",
            "+352" => "+352 🇱🇺 Luxemburgo",
            "+853" => "+853 🇲🇴 Macau",
            "+389" => "+389 🇲🇰 Macedônia do Norte",
            "+261" => "+261 🇲🇬 Madagascar",
            "+265" => "+265 🇲🇼 Malawi",
            "+60" => "+60 🇲🇾 Malásia",
            "+960" => "+960 🇲🇻 Maldivas",
            "+223" => "+223 🇲🇱 Mali",
            "+356" => "+356 🇲🇹 Malta",
            "+692" => "+692 🇲🇭 Ilhas Marshall",
            "+222" => "+222 🇲🇷 Mauritânia",
            "+230" => "+230 🇲🇺 Maurício",
            "+262" => "+262 🇾🇹 Reunião",
            "+52" => "+52 🇲🇽 México",
            "+691" => "+691 🇫🇲 Micronésia",
            "+373" => "+373 🇲🇩 Moldávia",
            "+377" => "+377 🇲🇨 Mônaco",
            "+976" => "+976 🇲🇳 Mongólia",
            "+382" => "+382 🇲🇪 Montenegro",
            "+1-664" => "+1-664 🇲🇸 Montserrat",
            "+212" => "+212 🇲🇦 Marrocos",
            "+258" => "+258 🇲🇿 Moçambique",
            "+95" => "+95 🇲🇲 Mianmar",
            "+264" => "+264 🇳🇦 Namíbia",
            "+674" => "+674 🇳🇷 Nauru",
            "+977" => "+977 🇳🇵 Nepal",
            "+31" => "+31 🇳🇱 Países Baixos",
            "+687" => "+687 🇳🇨 Nova Caledônia",
            "+64" => "+64 🇳🇿 Nova Zelândia",
            "+505" => "+505 🇳🇮 Nicarágua",
            "+227" => "+227 🇳🇪 Níger",
            "+234" => "+234 🇳🇬 Nigéria",
            "+683" => "+683 🇳🇺 Niue",
            "+850" => "+850 🇰🇵 Coreia do Norte",
            "+1-670" => "+1-670 🇲🇵 Ilhas Marianas do Norte",
            "+47" => "+47 🇳🇴 Noruega",
            "+968" => "+968 🇴🇲 Omã",
            "+92" => "+92 🇵🇰 Paquistão",
            "+680" => "+680 🇵🇼 Palau",
            "+970" => "+970 🇵🇸 Palestina",
            "+507" => "+507 🇵🇦 Panamá",
            "+675" => "+675 🇵🇬 Papua-Nova Guiné",
            "+595" => "+595 🇵🇾 Paraguai",
            "+51" => "+51 🇵🇪 Peru",
            "+63" => "+63 🇵🇭 Filipinas",
            "+64" => "+64 🇵🇳 Ilhas Pitcairn",
            "+48" => "+48 🇵🇱 Polônia",
            "+351" => "+351 🇵🇹 Portugal",
            "+1-787 e +1-939" => "+1-787 e +1-939 🇵🇷 Porto Rico",
            "+974" => "+974 🇶🇦 Catar",
            "+242" => "+242 🇨🇩 República do Congo",
            "+262" => "+262 🇾🇹 Mayotte",
            "+40" => "+40 🇷🇴 Romênia",
            "+7" => "+7 🇷🇺 Rússia",
            "+250" => "+250 🇷🇼 Ruanda",
            "+590" => "+590 🇧🇱 São Bartolomeu",
            "+290" => "+290 🇸🇭 Santa Helena",
            "+1-869" => "+1-869 🇰🇳 São Cristóvão e Nevis",
            "+1-758" => "+1-758 🇱🇨 Santa Lúcia",
            "+590" => "+590 🇲🇫 Saint Martin",
            "+508" => "+508 🇵🇲 São Pedro e Miquelão",
            "+1-784" => "+1-784 🇻🇨 São Vicente e Granadinas",
            "+685" => "+685 🇼🇸 Samoa",
            "+378" => "+378 🇸🇲 San Marino",
            "+239" => "+239 🇸🇹 São Tomé e Príncipe",
            "+966" => "+966 🇸🇦 Arábia Saudita",
            "+221" => "+221 🇸🇳 Senegal",
            "+381" => "+381 🇷🇸 Sérvia",
            "+248" => "+248 🇸🇨 Seychelles",
            "+232" => "+232 🇸🇱 Serra Leoa",
            "+65" => "+65 🇸🇬 Singapura",
            "+1-721" => "+1-721 🇸🇽 Sint Maarten",
            "+421" => "+421 🇸🇰 Eslováquia",
            "+386" => "+386 🇸🇮 Eslovênia",
            "+677" => "+677 🇸🇧 Ilhas Salomão",
            "+252" => "+252 🇸🇴 Somália",
            "+27" => "+27 🇿🇦 África do Sul",
            "+82" => "+82 🇰🇷 Coreia do Sul",
            "+211" => "+211 🇸🇸 Sudão do Sul",
            "+34" => "+34 🇪🇸 Espanha",
            "+94" => "+94 🇱🇰 Sri Lanka",
            "+249" => "+249 🇸🇩 Sudão",
            "+597" => "+597 🇸🇷 Suriname",
            "+47" => "+47 🇸🇯 Svalbard e Jan Mayen",
            "+268" => "+268 🇸🇿 Suazilândia",
            "+46" => "+46 🇸🇪 Suécia",
            "+41" => "+41 🇨🇭 Suíça",
            "+963" => "+963 🇸🇾 Síria",
            "+886" => "+886 🇹🇼 Taiwan",
            "+992" => "+992 🇹🇯 Tajiquistão",
            "+255" => "+255 🇹🇿 Tanzânia",
            "+66" => "+66 🇹🇭 Tailândia",
            "+228" => "+228 🇹🇬 Togo",
            "+690" => "+690 🇹🇰 Tokelau",
            "+676" => "+676 🇹🇴 Tonga",
            "+1-868" => "+1-868 🇹🇹 Trinidad e Tobago",
            "+216" => "+216 🇹🇳 Tunísia",
            "+90" => "+90 🇹🇷 Turquia",
            "+993" => "+993 🇹🇲 Turcomenistão",
            "+1-649" => "+1-649 🇹🇨 Ilhas Turks e Caicos",
            "+688" => "+688 🇹🇻 Tuvalu",
            "+1-340" => "+1-340 🇻🇮 Ilhas Virgens Americanas",
            "+256" => "+256 🇺🇬 Uganda",
            "+380" => "+380 🇺🇦 Ucrânia",
            "+971" => "+971 🇦🇪 Emirados Árabes Unidos",
            "+44" => "+44 🇬🇧 Reino Unido",
            "+1" => "+1 🇺🇸 Estados Unidos",
            "+598" => "+598 🇺🇾 Uruguai",
            "+998" => "+998 🇺🇿 Uzbequistão",
            "+678" => "+678 🇻🇺 Vanuatu",
            "+379" => "+379 🇻🇦 Cidade do Vaticano",
            "+58" => "+58 🇻🇪 Venezuela",
            "+84" => "+84 🇻🇳 Vietnã",
            "+681" => "+681 🇼🇫 Wallis e Futuna",
            "+212" => "+212 🇪🇭 Saara Ocidental",
            "+967" => "+967 🇾🇪 Iêmen",
            "+260" => "+260 🇿🇲 Zâmbia",
            "+263" => "+263 🇿🇼 Zimbábue"
        );
        return $listPaises;
    }
}
?>
