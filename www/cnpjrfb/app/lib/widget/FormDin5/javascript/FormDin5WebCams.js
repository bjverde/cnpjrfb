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


let videoStream; // Variável para armazenar o stream de vídeo

function fd5VideoSpec() {
    var video = document.querySelector('video');
    const constraints = {
        video: {
          width: {
            min: 1280,
            ideal: 1920,
            max: 2560,
          },
          height: {
            min: 720,
            ideal: 1080,
            max: 1440,
          },
        },
      };
    return constraints;
}


function fd5VideoStop(id){
  if (videoStream) {
      let tracks = videoStream.getTracks(); // Obtém todas as faixas de mídia do stream
      tracks.forEach(track => track.stop()); // Para cada faixa, interrompe a captura
      videoStream = null; // Define a variável do stream como null para indicar que não há stream ativo
      let video = document.querySelector('#'+id+'_video');
      video.srcObject = null; // Remove o stream do vídeo
  }
}

/**
 * Verifica se o modo de exibição atual é igual ao desejado
 * @param {string} idElemento  - 01: id do elemento html iniciando com # 
 * @param {string} modoExibicao- 02: block ou none
 * @returns 
 */
function fd5VideoAlternarDisplay(idElemento, modoExibicao) {
  var elemento = document.querySelector(idElemento);
  if (window.getComputedStyle(elemento).display === modoExibicao) {
      return;
  }
  elemento.style.display = modoExibicao; // Alterna para o modo de exibição desejado
}

function fd5VideoGetImgDimensoes(id){
  let divPrincipal  = document.querySelector('#'+id+'_videodiv');
  let proporcaoAlturaLargura = 9/16; // Proporção 16:9 (altura:largura)
  let largura = divPrincipal.offsetWidth;
  let altura = largura * proporcaoAlturaLargura; // Calcula a altura com base na largura

  var imgDimensoes = {
     height: altura,
     width : largura
  };
  return imgDimensoes;
}

function fd5VideoStart(id){
   if ( !"mediaDevices" in navigator ||
        !"getUserMedia" in navigator.mediaDevices ) {
    __adianti_error('Error', 'Camera API is not available in your browser');
    return;
  }
  fd5VideoStop(id);
  fd5VideoAlternarDisplay('#'+id+'_video','block');
  fd5VideoAlternarDisplay('#'+id+'_videoCanvas','none');
  fd5VideoAlternarDisplay('#'+id+'_videoCanvasUpload','none');
  
  let video = document.querySelector('#'+id+'_video');
  let divPrincipal = fd5VideoGetImgDimensoes(id);

	navigator.mediaDevices.getUserMedia({video:true})
	.then(stream => {
		video.srcObject = stream;
		video.play();
    videoStream = stream; // Armazena o stream de vídeo na variável

    video.height= divPrincipal.height;
    video.width = divPrincipal.width;
	})
	.catch(error => {
    __adianti_error('Error', error);
		console.log(error);
	});	
}


function fd5VideoCaminhoSite(){
  let pathname = window.location.pathname;
  let partes   = pathname.split('index.php');
  return partes[0];
}


/**
 * Sub função do fd5VideoSaveTmpAdianti só para facilitar leitura e manutenção
 * Pega um dataURL e converte para um File
 * @param {object} dataURL  - recebe o resultado do canvas.toDataURL()
 * @param {string} nameFile - nome do arquivo
 * @returns {File}
 */
function dataUrltoFile(dataURL,nameFile) {
  let byteString = atob(dataURL.split(',')[1]);
  let mimeType = dataURL.split(',')[0].split(':')[1].split(';')[0];
  let n = byteString.length;
  let u8arr = new Uint8Array(n);
  while (n--) {
      u8arr[n] = byteString.charCodeAt(n);
  }
  let name = nameFile;
  return new File([u8arr], name, {type:mimeType});
}


/**
 * Sub função do fd5VideoCampiturar só para facilitar leitura e manutenção
 * Coloca uma imagem sobre a imagem da camera para indicar que está correto
 * @param {object} canvas         - um objeto do tipo HTMLCanvasElement
 * @param {string} imgPathFeedBack- caminho da imagem para dar o FeedBack visual
 * @param {string} imgPercent     - percentual da imagem
 * @returns {void}
 */
function fd5VideoCampiturarSucesso(canvas,imgPathFeedBack, imgPercent) {
  let context = canvas.getContext('2d');
  let pathImg = fd5VideoCaminhoSite();
  pathImg = pathImg+imgPathFeedBack;

  // Carregar a imagem PNG com fundo transparente
  let imagemPNG = new Image();
  imagemPNG.onload = function() {
    let novaLargura = imagemPNG.width  * imgPercent;
    let novaAltura  = imagemPNG.height * imgPercent;

    // Desenhar a imagem PNG sobre a imagem do vídeo no canto direito superior
    let posX = 25; // Posição X (canto direito)
    let posY = 25; // Posição Y (canto superior)
    context.drawImage(imagemPNG, posX, posY, novaLargura, novaAltura);
  };
  imagemPNG.src = pathImg; // Substitua pelo caminho da imagem PNG com fundo transparente
}

/**
 * Sub função do fd5VideoCampiturar só para facilitar leitura e manutenção
 * Pegar o ScreenShot gerado e envia para o servidor 
 * @param {string} id             - O ID do elemento de vídeo a ser capturado.
 * @param {object} canvasCapturado- um objeto do tipo HTMLCanvasElement
 * @param {object} video          - 
 * @param {string} imgPathFeedBack- caminho da imagem para dar o FeedBack visual
 * @param {string} imgPercent     - percentual da imagem
 * @returns {void}
 */
function fd5VideoSaveTmpAdianti(id,canvasCapturado,video,imgPathFeedBack, imgPercent){
  try {
    let nameFile = 'image' + Math.floor((Math.random() * 1000000) + 1) + '.png';
    let hiddenField = document.querySelector('#'+id);
    hiddenField.value = nameFile;

    // Obter as dimensões reais do vídeo
    let videoWidth = video.videoWidth;
    let videoHeight = video.videoHeight;

    // Calcular a proporção do vídeo
    let proporcao = videoWidth / videoHeight;

    // Definir a altura máxima como 720 pixels
    let maxHeight = 1000;

    // Calcular a largura proporcional com base na altura máxima
    let maxWidth = maxHeight * proporcao;

    // Verificar se a largura excede o máximo permitido
    if (maxWidth > videoWidth) {
      // Se exceder, usar a largura original do vídeo
      maxWidth = videoWidth;
      maxHeight = maxWidth / proporcao;
    }

    // Criar um novo canvas com as dimensões corretas para a conversão
    let scaledCanvas = document.createElement('canvas');
    scaledCanvas.width = maxWidth;
    scaledCanvas.height = maxHeight;
    let context = scaledCanvas.getContext('2d');

    // Desenhar a imagem capturada no novo canvas com as dimensões corretas
    context.drawImage(canvasCapturado, 0, 0, canvasCapturado.width, canvasCapturado.height, 0, 0, maxWidth, maxHeight);

    // Converter o canvas para um arquivo e enviar para o servidor
    let dataURL = scaledCanvas.toDataURL('image/png', 0.9); // Defina o tipo de imagem e a qualidade desejada (0.9 neste exemplo)
    let file = dataUrltoFile(dataURL,nameFile);
    let formdata = new FormData();
    formdata.append(id, nameFile);
    formdata.append('arquivo', file);

    let pathSite = fd5VideoCaminhoSite();
    pathSite = pathSite+'app/lib/widget/FormDin5/callback/upload.class.php';

    let ajax = new XMLHttpRequest();
    ajax.open("POST", pathSite,true);
    ajax.send(formdata);

    // Tratando a resposta da requisição (opcional)
    ajax.onreadystatechange = function() {
      if (ajax.readyState === XMLHttpRequest.DONE) {
          if (ajax.status === 200) {
            fd5VideoCampiturarSucesso(canvasCapturado,imgPathFeedBack, imgPercent);
          } else {
            __adianti_error('Error', 'Error ao gravar a imagem, informe o problema');
          }
      }
    };
  }
  catch (e) {
      __adianti_error('Error', e);
  }
}


/**
 * Faz um ScreenShot de streem de vídeo e coloca no elemento canvas
 * @param {string} id             - O ID do elemento de vídeo a ser capturado.
 * @param {string} imgPathFeedBack- caminho da imagem para dar o FeedBack visual
 * @param {string} imgPercent     - percentual da imagem
 * @returns {void}
 */
function fd5VideoCampiturar(id,imgPathFeedBack, imgPercent){
  try {
    let idVideo= '#'+id+'_video';
    var video  = document.querySelector(idVideo);
    fd5VideoAlternarDisplay(idVideo,'none');

    let idCanvas= '#'+id+'_videoCanvas';
    var canvasCapturado = document.querySelector(idCanvas);
    fd5VideoAlternarDisplay(idCanvas,'block');

    // Obter as dimensões reais do vídeo
    let videoWidth = video.videoWidth;
    let videoHeight = video.videoHeight;

    // Obter as dimensões do contêiner
    let divPrincipal = fd5VideoGetImgDimensoes(id);

    // Calcular as dimensões do canvas mantendo a proporção do vídeo
    let proporcao = videoWidth / videoHeight;
    let canvasWidth = divPrincipal.width;
    let canvasHeight = canvasWidth / proporcao;

    // Limitar a altura do canvas de acordo com as dimensões máximas
    if (canvasHeight > divPrincipal.height) {
      canvasHeight = divPrincipal.height;
      canvasWidth = canvasHeight * proporcao;
    }

    canvasCapturado.width = canvasWidth;
    canvasCapturado.height = canvasHeight;

    let context= canvasCapturado.getContext('2d');
    context.drawImage(video, 0, 0, canvasWidth, canvasHeight);

    fd5VideoSaveTmpAdianti(id,canvasCapturado,video,imgPathFeedBack, imgPercent);
    fd5VideoStop(id);
  }
  catch (e) {
      __adianti_error('Error', e);
  }
}