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
 * Biblioteca de Funções JavaScript utilizadas pela classe FormDin5
 * É uma reconstrução do FormDin 4 Sobre o Adianti 7.X
 * Criado em :30/04/2020    Por: Reinaldo A. Barrêto Junior
 */

//-------------------------------------------------------------------------------------
// Função para substituir a document.getElementById()
function fwGetObj(nomeObjeto,propriedade)
{
	var app_iframe = document.getElementById('app_iframe');
	if ( app_iframe ){
		return app_iframe.contentWindow.fwGetObj(nomeObjeto,propriedade);
    }
    
	// compatibilidade com formdin3
	app_iframe = document.getElementById('iframe_area_dados');
	if ( app_iframe ){
		return app_iframe.contentWindow.fwGetObj(nomeObjeto,propriedade);
	}
	var obj;

    try {
        obj=jQuery("#"+nomeObjeto).get(0);
    } catch(e){}
    
    if(!obj){
        try{
        obj=jQuery("#"+nomeObjeto+'disabled').get(0);
        } catch(e){}
    }
    if(!obj){
        try{
        obj=jQuery("#"+nomeObjeto+'_disabled').get(0);
        } catch(e){}
    }
        
    // procurar em caixa baixa
    nomeObjeto = nomeObjeto.toLowerCase();
    if(!obj){
        try{
        obj=jQuery("#"+nomeObjeto).get(0);
        } catch(e){}
    }
    if(!obj){
        try{
        obj=jQuery("#"+nomeObjeto+'disabled').get(0);
        } catch(e){}
    }
    if(!obj){
        try{
        obj=jQuery("#"+nomeObjeto+'_disabled').get(0);
        } catch(e){}
    }

	if( obj && propriedade){
		try {
			eval('var prop = obj.'+propriedade);
			return prop;
		} catch(e) {}
	}
	return obj;
}

//-----------------------------------------------------------------------------------
function fwRemoverCaractere(input,codigoAscii)
{
	let output = '';
	if( codigoAscii){
		for (let i = 0; i < input.value.length; i++){
			if ( (input.value.charCodeAt(i) === codigoAscii) ){
				i++;
			}else{
				output += input.value.charAt(i);
			}
		}
		input.value=output;
	}
}
//-------------------------------------------------------------------------------------
function fwCheckNumChar(e,max)
{
	try {
        var obj = fwGetObj(e.id+'_counter');
		var texto = e.value.trim();
		var tamanho = texto.length;
		obj.style.color='#000000';
		if( tamanho > max ){
			fwRemoverCaractere(e,13);
			texto = e.value.trim();
			tamanho = texto.length;
			if( tamanho > max ){
				obj.style.color='red';
				alert('Limite de '+max+' caracteres atingido!');
				texto = texto.substr(0,max);
				e.value=texto;
				var dif = (tamanho-e.value.length);
				if( dif > 1 ){
					alert( 'Foram removidos '+dif+' caracteres do final do texto.')
				}
			}
		}
		obj.innerHTML='caracteres:'+e.value.length+"/"+max;
	} catch(e)	{}
}
//--------------------------------------------------------------------------------------
