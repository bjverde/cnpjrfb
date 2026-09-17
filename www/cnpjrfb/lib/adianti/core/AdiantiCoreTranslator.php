<?php
namespace Adianti\Core;

/**
 * Framework translation class for internal messages
 *
 * @version    8.6
 * @package    core
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license
 * @alias      TAdiantiCoreTranslator
 */
class AdiantiCoreTranslator
{
    private static $instance; // singleton instance
    private $lang;            // target language
    private $messages;
    private $sourceMessages;
    
    /**
     * Class Constructor
     */
    private function __construct()
    {
        $this->messages = [];
        $this->messages['en'][] = 'Loading';
        $this->messages['en'][] = 'File not found';
        $this->messages['en'][] = 'Search';
        $this->messages['en'][] = 'Register';
        $this->messages['en'][] = 'Record saved';
        $this->messages['en'][] = 'Do you really want to delete ?';
        $this->messages['en'][] = 'Record deleted';
        $this->messages['en'][] = 'Records deleted';
        $this->messages['en'][] = 'Function';
        $this->messages['en'][] = 'Table';
        $this->messages['en'][] = 'Tool';
        $this->messages['en'][] = 'Data';
        $this->messages['en'][] = 'Open';
        $this->messages['en'][] = 'Save';
        $this->messages['en'][] = 'List';
        $this->messages['en'][] = 'Delete';
        $this->messages['en'][] = 'Delete selected';
        $this->messages['en'][] = 'Edit';
        $this->messages['en'][] = 'Cancel';
        $this->messages['en'][] = 'Yes';
        $this->messages['en'][] = 'No';
        $this->messages['en'][] = 'January';
        $this->messages['en'][] = 'February';
        $this->messages['en'][] = 'March';
        $this->messages['en'][] = 'April';
        $this->messages['en'][] = 'May';
        $this->messages['en'][] = 'June';
        $this->messages['en'][] = 'July';
        $this->messages['en'][] = 'August';
        $this->messages['en'][] = 'September';
        $this->messages['en'][] = 'October';
        $this->messages['en'][] = 'November';
        $this->messages['en'][] = 'December';
        $this->messages['en'][] = 'Today';
        $this->messages['en'][] = 'Close';
        $this->messages['en'][] = 'Field for action ^1 not defined';
        $this->messages['en'][] = 'Field ^1 not exists or contains NULL value';
        $this->messages['en'][] = 'Use the ^1 method';
        $this->messages['en'][] = 'Form with no fields';
        $this->messages['en'][] = 'E-mail not sent';
        $this->messages['en'][] = 'The field ^1 can not be less than ^2 characters';
        $this->messages['en'][] = 'The field ^1 can not be greater than ^2 characters';
        $this->messages['en'][] = 'The field ^1 can not be less than ^2';
        $this->messages['en'][] = 'The field ^1 can not be greater than ^2';
        $this->messages['en'][] = 'The field ^1 is required';
        $this->messages['en'][] = 'The field ^1 has not a valid CNPJ';
        $this->messages['en'][] = 'The field ^1 has not a valid CPF';
        $this->messages['en'][] = 'The field ^1 contains an invalid e-mail';
        $this->messages['en'][] = 'The field ^1 must be numeric';
        $this->messages['en'][] = 'No active transactions';
        $this->messages['en'][] = 'Object not found';
        $this->messages['en'][] = 'Object ^1 not found in ^2';
        $this->messages['en'][] = 'Method ^1 does not accept null values';
        $this->messages['en'][] = 'Method ^1 must receive a parameter of type ^2';
        $this->messages['en'][] = 'Style ^1 not found in ^2';
        $this->messages['en'][] = 'You must call ^1 constructor';
        $this->messages['en'][] = 'You must call ^1 before ^2';
        $this->messages['en'][] = 'You must pass the ^1 (^2) as a parameter to ^3';
        $this->messages['en'][] = 'The parameter (^1) of ^2 is required';
        $this->messages['en'][] = 'The parameter (^1) of ^2 constructor is required';
        $this->messages['en'][] = 'You have already added a field called "^1" inside the form';
        $this->messages['en'][] = 'Quit the application ?';
        $this->messages['en'][] = 'Use the addField() or setFields() to define the form fields';
        $this->messages['en'][] = 'Check if the action (^1) exists';
        $this->messages['en'][] = 'Information';
        $this->messages['en'][] = 'Error';
        $this->messages['en'][] = 'Exception';
        $this->messages['en'][] = 'Question';
        $this->messages['en'][] = 'The class ^1 was not accepted as argument. The class informed as parameter must be subclass of ^2.';
        $this->messages['en'][] = 'The class ^1 was not accepted as argument. The class informed as parameter must implement ^2.';
        $this->messages['en'][] = 'The class ^1 was not found. Check the class name or the file name. They must match';
        $this->messages['en'][] = 'Reserved property name (^1) in class ^2';
        $this->messages['en'][] = 'Action (^1) must be static to be used in ^2';
        $this->messages['en'][] = 'Trying to access a non-existent property (^1)';
        $this->messages['en'][] = 'Form not found. Check if you have passed the field (^1) to the setFields()';
        $this->messages['en'][] = 'Class ^1 not found in ^2';
        $this->messages['en'][] = 'You must call ^1 before add this component';
        $this->messages['en'][] = 'Driver not found';
        $this->messages['en'][] = 'Search record';
        $this->messages['en'][] = 'Field';
        $this->messages['en'][] = 'Record updated';
        $this->messages['en'][] = 'Records updated';
        $this->messages['en'][] = 'Input';
        $this->messages['en'][] = 'Class ^1 not found';
        $this->messages['en'][] = 'Method ^1 not found';
        $this->messages['en'][] = 'Check the class name or the file name';
        $this->messages['en'][] = 'Clear';
        $this->messages['en'][] = 'Select';
        $this->messages['en'][] = 'You must define the field for the action (^1)';
        $this->messages['en'][] = 'The section (^1) was not closed properly';
        $this->messages['en'][] = 'The method (^1) just accept values of type ^2 between ^3 and ^4';
        $this->messages['en'][] = 'The internal class ^1 can not be executed';
        $this->messages['en'][] = 'The minimum version required for PHP is ^1';
        $this->messages['en'][] = '^1 was not defined. You must call ^2 in ^3';
        $this->messages['en'][] = 'Database';
        $this->messages['en'][] = 'Constructor';
        $this->messages['en'][] = 'Records';
        $this->messages['en'][] = 'Description';
        $this->messages['en'][] = 'Error while copying file to ^1';
        $this->messages['en'][] = 'Permission denied';
        $this->messages['en'][] = 'Extension not allowed';
        $this->messages['en'][] = 'Hash error';
        $this->messages['en'][] = 'Invalid parameter (^1) in ^2';
        $this->messages['en'][] = 'Warning';
        $this->messages['en'][] = 'No records found';
        $this->messages['en'][] = '^1 to ^2 from ^3 records';
        $this->messages['en'][] = 'PHP Module not found';
        $this->messages['en'][] = 'The parameter (^1) of ^2 must not be empty';
        $this->messages['en'][] = 'Return is not a valid JSON. Check the URL';
        $this->messages['en'][] = 'Required fields';
        $this->messages['en'][] = 'CSRF Error';
        $this->messages['en'][] = 'Add';
        $this->messages['en'][] = 'Expand';
        $this->messages['en'][] = 'Server has received no file';
        $this->messages['en'][] = 'Check the server limits';
        $this->messages['en'][] = 'The current limit is';
        $this->messages['en'][] = 'Reset';
        $this->messages['en'][] = 'Scale horizontal';
        $this->messages['en'][] = 'Scale vertical';
        $this->messages['en'][] = 'Move';
        $this->messages['en'][] = 'Crop';
        $this->messages['en'][] = 'Zoom in';
        $this->messages['en'][] = 'Zoom out';
        $this->messages['en'][] = 'Rotate right';
        $this->messages['en'][] = 'Rotate left';
        $this->messages['en'][] = 'Sunday';
        $this->messages['en'][] = 'Monday';
        $this->messages['en'][] = 'Tuesday';
        $this->messages['en'][] = 'Wednesday';
        $this->messages['en'][] = 'Thursday';
        $this->messages['en'][] = 'Friday';
        $this->messages['en'][] = 'Saturday';
        $this->messages['en'][] = 'Softdelete is not active';
        $this->messages['en'][] = 'Use of target containers along with windows is not allowed';
        $this->messages['en'][] = 'View mode';
        $this->messages['en'][] = 'Zoom mode';
        $this->messages['en'][] = 'Months';
        $this->messages['en'][] = 'Months with days';
        $this->messages['en'][] = 'Days';
        $this->messages['en'][] = 'Days with hours';
        $this->messages['en'][] = 'Large';
        $this->messages['en'][] = 'Medium';
        $this->messages['en'][] = 'Small';
        $this->messages['en'][] = 'Condensed';
        $this->messages['en'][] = 'Extension not found: ^1';
        $this->messages['en'][] = 'Click to search';
        $this->messages['en'][] = 'No results';
        $this->messages['en'][] = 'selected';
        $this->messages['en'][] = 'Select all';
        $this->messages['en'][] = 'Clear the selection';
        $this->messages['en'][] = 'None selected';
        $this->messages['en'][] = 'Found (^1) records in "^2". Record cannot be deleted';
        $this->messages['en'][] = 'Database not found';
        $this->messages['en'][] = 'Unsupported type';
        $this->messages['en'][] = 'Send';
        $this->messages['en'][] = 'Adjust image';
        $this->messages['en'][] = 'Signature area';
        $this->messages['en'][] = 'Sum';
        $this->messages['en'][] = 'Min';
        $this->messages['en'][] = 'Max';
        $this->messages['en'][] = 'Average';
        $this->messages['en'][] = 'Count';
        $this->messages['en'][] = 'Total';
        $this->messages['en'][] = 'The method ^1 cannot be used together with the method ^2';
        
        
        $this->messages['pt'][] = 'Carregando';
        $this->messages['pt'][] = 'Arquivo não encontrado';
        $this->messages['pt'][] = 'Buscar';
        $this->messages['pt'][] = 'Registrar';
        $this->messages['pt'][] = 'Registro salvo';
        $this->messages['pt'][] = 'Deseja realmente excluir ?';
        $this->messages['pt'][] = 'Registro excluído';
        $this->messages['pt'][] = 'Registros excluídos';
        $this->messages['pt'][] = 'Função';
        $this->messages['pt'][] = 'Tabela';
        $this->messages['pt'][] = 'Ferramenta';
        $this->messages['pt'][] = 'Dados';
        $this->messages['pt'][] = 'Abrir';
        $this->messages['pt'][] = 'Salvar';
        $this->messages['pt'][] = 'Listar';
        $this->messages['pt'][] = 'Excluir';
        $this->messages['pt'][] = 'Excluir selecionados';
        $this->messages['pt'][] = 'Editar';
        $this->messages['pt'][] = 'Cancelar';
        $this->messages['pt'][] = 'Sim';
        $this->messages['pt'][] = 'Não';
        $this->messages['pt'][] = 'Janeiro';
        $this->messages['pt'][] = 'Fevereiro';
        $this->messages['pt'][] = 'Março';
        $this->messages['pt'][] = 'Abril';
        $this->messages['pt'][] = 'Maio';
        $this->messages['pt'][] = 'Junho';
        $this->messages['pt'][] = 'Julho';
        $this->messages['pt'][] = 'Agosto';
        $this->messages['pt'][] = 'Setembro';
        $this->messages['pt'][] = 'Outubro';
        $this->messages['pt'][] = 'Novembro';
        $this->messages['pt'][] = 'Dezembro';
        $this->messages['pt'][] = 'Hoje';
        $this->messages['pt'][] = 'Fechar';
        $this->messages['pt'][] = 'Campo para a ação ^1 não definido';
        $this->messages['pt'][] = 'Campo ^1 não existe ou contém valor NULL';
        $this->messages['pt'][] = 'Use o método ^1';
        $this->messages['pt'][] = 'Formulário sem campos';
        $this->messages['pt'][] = 'E-mail não enviado';
        $this->messages['pt'][] = 'O campo ^1 não pode ter menos de ^2 caracteres';
        $this->messages['pt'][] = 'O campo ^1 não pode ter mais de ^2 caracteres';
        $this->messages['pt'][] = 'O campo ^1 não pode ser menor que ^2';
        $this->messages['pt'][] = 'O campo ^1 não pode ser maior que ^2';
        $this->messages['pt'][] = 'O campo ^1 é obrigatório';
        $this->messages['pt'][] = 'O campo ^1 não contém um CNPJ válido';
        $this->messages['pt'][] = 'O campo ^1 não contém um CPF válido';
        $this->messages['pt'][] = 'O campo ^1 contém um e-mail inválido';
        $this->messages['pt'][] = 'O campo ^1 deve ser numérico';
        $this->messages['pt'][] = 'Sem transação ativa com a base de dados';
        $this->messages['pt'][] = 'Objeto não encontrado';
        $this->messages['pt'][] = 'Objeto ^1 não encontrado em ^2';
        $this->messages['pt'][] = 'Mtodo ^1 não aceita valores NULOS';
        $this->messages['pt'][] = 'Método ^1 deve receber um parâmetro do tipo ^2';
        $this->messages['pt'][] = 'Estilo ^1 não encontrado em ^2';
        $this->messages['pt'][] = 'Você deve executar o construtor de ^1';
        $this->messages['pt'][] = 'Você deve executar ^1 antes de ^2';
        $this->messages['pt'][] = 'Você deve passar o ^1 (^2) como parâmetro para ^3';
        $this->messages['pt'][] = 'O parâmetro (^1) de ^2 é obrigatório';
        $this->messages['pt'][] = 'O parâmetro (^1) do construtor de ^2 é obrigatório';
        $this->messages['pt'][] = 'Você já adicionou um campo chamado "^1" ao formulário';
        $this->messages['pt'][] = 'Fechar a aplicação ?';
        $this->messages['pt'][] = 'Use addField() ou setFields() para definir os campos do formulário';
        $this->messages['pt'][] = 'Verifique se a ação (^1) existe';
        $this->messages['pt'][] = 'Informação';
        $this->messages['pt'][] = 'Erro';
        $this->messages['pt'][] = 'Exceção';
        $this->messages['pt'][] = 'Questão';
        $this->messages['pt'][] = 'A classe ^1 não foi aceita como argumento. O parâmetro deve ser subclasse de ^2.';
        $this->messages['pt'][] = 'A classe ^1 não foi aceita como argumento. O parâmetro deve implementar ^2.';
        $this->messages['pt'][] = 'A classe ^1 não foi encontrada. Verifique o nome da classe ou do arquivo. Eles devem coincidir';
        $this->messages['pt'][] = 'Nome de propriedade reservado (^1) na classe ^2';
        $this->messages['pt'][] = 'A ação (^1) deve ser estática para ser usada em ^2';
        $this->messages['pt'][] = 'Tentativa de acesso à uma propriedade não existente (^1)';
        $this->messages['pt'][] = 'Formulário não encontrado. Verifique se você passou o campo (^1) para o setFields()';
        $this->messages['pt'][] = 'Classe ^1 não encontrada em ^2';
        $this->messages['pt'][] = 'Você deve executar ^1 antes de adicionar o componente';
        $this->messages['pt'][] = 'Driver não encontrado';
        $this->messages['pt'][] = 'Buscar registro';
        $this->messages['pt'][] = 'Campo';
        $this->messages['pt'][] = 'Registro atualizado';
        $this->messages['pt'][] = 'Registros atualizados';
        $this->messages['pt'][] = 'Entrada';
        $this->messages['pt'][] = 'Classe ^1 não encontrada';
        $this->messages['pt'][] = 'Método ^1 não encontrado';
        $this->messages['pt'][] = 'Verifique o nome da classe ou do arquivo';
        $this->messages['pt'][] = 'Limpar';
        $this->messages['pt'][] = 'Selecionar';
        $this->messages['pt'][] = 'Você deve definir o campo para a ação (^1)';
        $this->messages['pt'][] = 'A seção (^1) não foi fechada adequadamente';
        $this->messages['pt'][] = 'O método ^1 somente aceita valores do tipo ^2 entre ^3 e ^4';
        $this->messages['pt'][] = 'A classe interna ^1 não pode ser executada';
        $this->messages['pt'][] = 'A versão mínima requerida para o PHP é ^1';
        $this->messages['pt'][] = '^1 não definido. Você deve executar ^2 no ^3';
        $this->messages['pt'][] = 'Database';
        $this->messages['pt'][] = 'Construtor';
        $this->messages['pt'][] = 'Registros';
        $this->messages['pt'][] = 'Descrição';
        $this->messages['pt'][] = 'Falha ao copiar arquivo para ^1';
        $this->messages['pt'][] = 'Permissão negada';
        $this->messages['pt'][] = 'Extensão não permitida';
        $this->messages['pt'][] = 'Erro de hash';
        $this->messages['pt'][] = 'Parâmetro (^1) inválido em ^2';
        $this->messages['pt'][] = 'Atenção';
        $this->messages['pt'][] = 'Nenhum registro encontrado';
        $this->messages['pt'][] = '^1 a ^2 de ^3 registros';
        $this->messages['pt'][] = 'Módulo PHP não encontrado';
        $this->messages['pt'][] = 'O parâmetro (^1) de ^2 não deve ser vazio';
        $this->messages['pt'][] = 'Retorno não é JSON válido. Verifique a URL';
        $this->messages['pt'][] = 'Campos obrigatórios';
        $this->messages['pt'][] = 'Erro de CSRF';
        $this->messages['pt'][] = 'Adicionar';
        $this->messages['pt'][] = 'Expandir';
        $this->messages['pt'][] = 'O servidor não recebeu o arquivo';
        $this->messages['pt'][] = 'Verifique os limites do servidor';
        $this->messages['pt'][] = 'O limite atual é';
        $this->messages['pt'][] = 'Reverter';
        $this->messages['pt'][] = 'Escala horizontal';
        $this->messages['pt'][] = 'Escala vertical';
        $this->messages['pt'][] = 'Mover';
        $this->messages['pt'][] = 'Cortar';
        $this->messages['pt'][] = 'Aumentar zoom';
        $this->messages['pt'][] = 'Diminuir zoom';
        $this->messages['pt'][] = 'Rotacionar para a direita';
        $this->messages['pt'][] = 'Rotacionar para a esquerda';
        $this->messages['pt'][] = 'Domingo';
        $this->messages['pt'][] = 'Segunda';
        $this->messages['pt'][] = 'Terça';
        $this->messages['pt'][] = 'Quarta';
        $this->messages['pt'][] = 'Quinta';
        $this->messages['pt'][] = 'Sexta';
        $this->messages['pt'][] = 'Sábado';
        $this->messages['pt'][] = 'Softdelete não está ativo';
        $this->messages['pt'][] = 'Não é permitido usar target cointainers junto com janelas';
        $this->messages['pt'][] = 'Modo de visualização';
        $this->messages['pt'][] = 'Modo de zoom';
        $this->messages['pt'][] = 'Meses';
        $this->messages['pt'][] = 'Meses com dias';
        $this->messages['pt'][] = 'Dias';
        $this->messages['pt'][] = 'Dias com horas';
        $this->messages['pt'][] = 'Grande';
        $this->messages['pt'][] = 'Médio';
        $this->messages['pt'][] = 'Pequeno';
        $this->messages['pt'][] = 'Condensado';
        $this->messages['pt'][] = 'Extensão não encontrada: ^1';
        $this->messages['pt'][] = 'Clique para buscar';
        $this->messages['pt'][] = 'Sem resultados';
        $this->messages['pt'][] = 'selecionados';
        $this->messages['pt'][] = 'Selecionar todos';
        $this->messages['pt'][] = 'Limpar seleção';
        $this->messages['pt'][] = 'Nenhum selecionado';
        $this->messages['pt'][] = 'Encontrados (^1) registros em "^2". O registro não pode ser excluído';
        $this->messages['pt'][] = 'Banco de dados não encontrado';
        $this->messages['pt'][] = 'Tipo não suportado';
        $this->messages['pt'][] = 'Enviar';
        $this->messages['pt'][] = 'Ajustar imagem';
        $this->messages['pt'][] = 'Área de Assinatura';
        $this->messages['pt'][] = 'Soma';
        $this->messages['pt'][] = 'Mínimo';
        $this->messages['pt'][] = 'Máximo';
        $this->messages['pt'][] = 'Média';
        $this->messages['pt'][] = 'Qtde';
        $this->messages['pt'][] = 'Total';
        $this->messages['pt'][] = 'O método ^1 não pode ser usado em conjunto com o método ^2';
        
        $this->messages['es'][] = 'Cargando';
        $this->messages['es'][] = 'Archivo no encontrado';
        $this->messages['es'][] = 'Buscar';
        $this->messages['es'][] = 'Registrar';
        $this->messages['es'][] = 'Registro guardado';
        $this->messages['es'][] = 'Deseas realmente eliminar ?';
        $this->messages['es'][] = 'Registro eliminado';
        $this->messages['es'][] = 'Registros eliminados';
        $this->messages['es'][] = 'Función';
        $this->messages['es'][] = 'Tabla';
        $this->messages['es'][] = 'Herramienta';
        $this->messages['es'][] = 'Datos';
        $this->messages['es'][] = 'Abrir';
        $this->messages['es'][] = 'Guardar';
        $this->messages['es'][] = 'Listar';
        $this->messages['es'][] = 'Eliminar';
        $this->messages['es'][] = 'Eliminar seleccionados';
        $this->messages['es'][] = 'Modificar';
        $this->messages['es'][] = 'Cancelar';
        $this->messages['es'][] = 'Si';
        $this->messages['es'][] = 'No';
        $this->messages['es'][] = 'Enero';
        $this->messages['es'][] = 'Febrero';
        $this->messages['es'][] = 'Marzo';
        $this->messages['es'][] = 'Abril';
        $this->messages['es'][] = 'Mayo';
        $this->messages['es'][] = 'Junio';
        $this->messages['es'][] = 'Julio';
        $this->messages['es'][] = 'Agosto';
        $this->messages['es'][] = 'Septiembre';
        $this->messages['es'][] = 'Octubre';
        $this->messages['es'][] = 'Noviembre';
        $this->messages['es'][] = 'Diciembre';
        $this->messages['es'][] = 'Hoy';
        $this->messages['es'][] = 'Cerrar';
        $this->messages['es'][] = 'Campo para la acción ^1 no definido';
        $this->messages['es'][] = 'Campo ^1 no existe o contiene valor NULL';
        $this->messages['es'][] = 'Utilize el método ^1';
        $this->messages['es'][] = 'Formulário sin campos';
        $this->messages['es'][] = 'E-mail no enviado';
        $this->messages['es'][] = 'El campo ^1 no puede ter menos de ^2 caracteres';
        $this->messages['es'][] = 'El campo ^1 no puede ter mas de ^2 caracteres';
        $this->messages['es'][] = 'El campo ^1 no puede ser menor que ^2';
        $this->messages['es'][] = 'El campo ^1 no puede ser mayor que ^2';
        $this->messages['es'][] = 'El campo ^1 es obligatório';
        $this->messages['es'][] = 'El campo ^1 no contiene un CNPJ válido';
        $this->messages['es'][] = 'El campo ^1 no contiene un CPF válido';
        $this->messages['es'][] = 'El campo ^1 contiene un e-mail inválido';
        $this->messages['es'][] = 'El campo ^1 debe ser numérico';
        $this->messages['es'][] = 'Sin transacción activa con la base de datos';
        $this->messages['es'][] = 'Objeto no encontrado';
        $this->messages['es'][] = 'Objeto ^1 no encontrado en ^2';
        $this->messages['es'][] = 'Método ^1 no acepta valores NULOS';
        $this->messages['es'][] = 'Método ^1 debe recibir un parámetro del tipo ^2';
        $this->messages['es'][] = 'Estilo ^1 no encontrado en ^2';
        $this->messages['es'][] = 'Usted debe ejecutar el constructor de ^1';
        $this->messages['es'][] = 'Usted debe executar ^1 antes de ^2';
        $this->messages['es'][] = 'Usted debe pasar el ^1 (^2) como parámetro para ^3';
        $this->messages['es'][] = 'El parámetro (^1) de ^2 es obligatório';
        $this->messages['es'][] = 'El parámetro (^1) del constructor de ^2 es obligatório';
        $this->messages['es'][] = 'Usted ya agregó un campo llamado "^1" al formulário';
        $this->messages['es'][] = 'Cerrar la aplicación ?';
        $this->messages['es'][] = 'Utilize addField() o setFields() para definir los campos del formulário';
        $this->messages['es'][] = 'Verifique si la acción (^1) existe';
        $this->messages['es'][] = 'Información';
        $this->messages['es'][] = 'Error';
        $this->messages['es'][] = 'Excepción';
        $this->messages['es'][] = 'Pregunta';
        $this->messages['es'][] = 'La classe ^1 no fue aceptada como argumento. El parámetro debe ser subclasse de ^2.';
        $this->messages['es'][] = 'La classe ^1 no fue aceptada como argumento. El parámetro debe inplementar ^2.';
        $this->messages['es'][] = 'La classe ^1 no fue encontrada. Verifique el nombre de la classe o del archivo. Ellos deben coincidir ';
        $this->messages['es'][] = 'Nombre de propiedad reservado (^1) en la classe ^2';
        $this->messages['es'][] = 'La acción (^1) debe ser estática para ser utilizada en ^2';
        $this->messages['es'][] = 'Intento de acceso a una propiedad no existente (^1)';
        $this->messages['es'][] = 'Formulário no encontrado. Verifique si usted envió el campo (^1) para setFields()';
        $this->messages['es'][] = 'Classe ^1 no encontrada en ^2';
        $this->messages['es'][] = 'Usted debe ejecutar ^1 antes de agregar el componente';
        $this->messages['es'][] = 'Driver no encontrado';
        $this->messages['es'][] = 'Buscar registro';
        $this->messages['es'][] = 'Campo';
        $this->messages['es'][] = 'Registro actualizado';
        $this->messages['es'][] = 'Registros actualizados';
        $this->messages['es'][] = 'Entrada';
        $this->messages['es'][] = 'Classe ^1 no encontrada';
        $this->messages['es'][] = 'Método ^1 no encontrado';
        $this->messages['es'][] = 'Verifique el nombre de la classe o del archivo';
        $this->messages['es'][] = 'Limpiar';
        $this->messages['es'][] = 'Seleccionar';
        $this->messages['es'][] = 'Usted debe definir el campo para la acción (^1)';
        $this->messages['es'][] = 'la selección (^1) no fue cerrad correctamente';
        $this->messages['es'][] = 'El método ^1 solamente acepta valores del tipo ^2 entre ^3 y ^4';
        $this->messages['es'][] = 'La classe interna ^1 no puede ser ejecutada';
        $this->messages['es'][] = 'La versión mínima requerida para el PHP es ^1';
        $this->messages['es'][] = '^1 no definido. Usted debe ejecutar ^2 en ^3';
        $this->messages['es'][] = 'Database';
        $this->messages['es'][] = 'Constructor';
        $this->messages['es'][] = 'Registros';
        $this->messages['es'][] = 'Descripción';
        $this->messages['es'][] = 'Falla al copiar archivo para ^1';
        $this->messages['es'][] = 'Permiso denegado';
        $this->messages['es'][] = 'Extensión no permitida';
        $this->messages['es'][] = 'Error de hash';
        $this->messages['es'][] = 'Parámetro (^1) inválido en ^2';
        $this->messages['es'][] = 'Atención';
        $this->messages['es'][] = 'Ningun registro encontrado';
        $this->messages['es'][] = '^1 a ^2 de ^3 registros';
        $this->messages['es'][] = 'Módulo PHP no encontrado';
        $this->messages['es'][] = 'El parametro (^1) de ^2 no puede estar vacío';
        $this->messages['es'][] = 'El retorno no es un JSON válido. Verifique la URL';
        $this->messages['es'][] = 'Campos requeridos';
        $this->messages['es'][] = 'Error de CSRF';
        $this->messages['es'][] = 'Agregar';
        $this->messages['es'][] = 'Expandir';
        $this->messages['es'][] = 'El servidor no ha recibido ningún archivo';
        $this->messages['es'][] = 'Verifique os limites do servidor';
        $this->messages['es'][] = 'El límite actual es';
        $this->messages['es'][] = 'Retroceder';
        $this->messages['es'][] = 'Escala horizontal';
        $this->messages['es'][] = 'Escala vertical';
        $this->messages['es'][] = 'Moverse';
        $this->messages['es'][] = 'Cortar';
        $this->messages['es'][] = 'Acercars';
        $this->messages['es'][] = 'Alejar';
        $this->messages['es'][] = 'Girar a la derecha';
        $this->messages['es'][] = 'Girar a la izquierda';
        $this->messages['es'][] = 'Domingo';
        $this->messages['es'][] = 'Lunes';
        $this->messages['es'][] = 'Martes';
        $this->messages['es'][] = 'Miércoles';
        $this->messages['es'][] = 'Jueves';
        $this->messages['es'][] = 'Viernes';
        $this->messages['es'][] = 'Sábado';
        $this->messages['es'][] = 'Softdelete no esta activo';
        $this->messages['es'][] = 'No se permite el uso de contenedores de destino junto con ventanas';
        $this->messages['es'][] = 'Modo de vista';
        $this->messages['es'][] = 'Modo de zoom';
        $this->messages['es'][] = 'Meses';
        $this->messages['es'][] = 'Meses con dias';
        $this->messages['es'][] = 'Días';
        $this->messages['es'][] = 'Dias con horas';
        $this->messages['es'][] = 'Grande';
        $this->messages['es'][] = 'Medio';
        $this->messages['es'][] = 'Pequeño';
        $this->messages['es'][] = 'Condensado';
        $this->messages['es'][] = 'Extensión no encontrada: ^1';
        $this->messages['es'][] = 'Haga clic para buscar';
        $this->messages['es'][] = 'No hay resultados';
        $this->messages['es'][] = 'seleccionados';
        $this->messages['es'][] = 'Seleccionar todo';
        $this->messages['es'][] = 'Borrar la selección';
        $this->messages['es'][] = 'Ninguno seleccionada';
        $this->messages['es'][] = 'Se encontraron (^1) registros en "^2". El registro no se puede eliminar';
        $this->messages['es'][] = 'Base de datos no encontrada';
        $this->messages['es'][] = 'Tipo no admitido';
        $this->messages['es'][] = 'Enviar';
        $this->messages['es'][] = 'Ajustar imagem';
        $this->messages['es'][] = 'Área de Firma';
        $this->messages['es'][] = 'Suma';
        $this->messages['es'][] = 'Mínimo';
        $this->messages['es'][] = 'Máximo';
        $this->messages['es'][] = 'Promedio';
        $this->messages['es'][] = 'Conteo';
        $this->messages['es'][] = 'Total';
        $this->messages['es'][] = 'El método ^1 no se puede usar junto con el método ^2';
        
        $this->messages['it'][] = 'Caricamento';
        $this->messages['it'][] = 'File non trovato';
        $this->messages['it'][] = 'Cerca';
        $this->messages['it'][] = 'Registrare';
        $this->messages['it'][] = 'Record salvato';
        $this->messages['it'][] = 'Vuoi davvero eliminare?';
        $this->messages['it'][] = 'Record eliminato';
        $this->messages['it'][] = 'Record eliminati';
        $this->messages['it'][] = 'Funzione';
        $this->messages['it'][] = 'Tabella';
        $this->messages['it'][] = 'Strumento';
        $this->messages['it'][] = 'Dati';
        $this->messages['it'][] = 'Apri';
        $this->messages['it'][] = 'Salva';
        $this->messages['it'][] = 'Lista';
        $this->messages['it'][] = 'Elimina';
        $this->messages['it'][] = 'Elimina selezionati';
        $this->messages['it'][] = 'Modifica';
        $this->messages['it'][] = 'Annulla';
        $this->messages['it'][] = 'Sì';
        $this->messages['it'][] = 'No';
        $this->messages['it'][] = 'Gennaio';
        $this->messages['it'][] = 'Febbraio';
        $this->messages['it'][] = 'Marzo';
        $this->messages['it'][] = 'Aprile';
        $this->messages['it'][] = 'Maggio';
        $this->messages['it'][] = 'Giugno';
        $this->messages['it'][] = 'Luglio';
        $this->messages['it'][] = 'Agosto';
        $this->messages['it'][] = 'Settembre';
        $this->messages['it'][] = 'Ottobre';
        $this->messages['it'][] = 'Novembre';
        $this->messages['it'][] = 'Dicembre';
        $this->messages['it'][] = 'Oggi';
        $this->messages['it'][] = 'Chiudi';
        $this->messages['it'][] = 'Campo per l\'azione ^1 non definito';
        $this->messages['it'][] = 'Il campo ^1 non esiste o contiene valore NULL';
        $this->messages['it'][] = 'Usa il metodo ^1';
        $this->messages['it'][] = 'Modulo senza campi';
        $this->messages['it'][] = 'Email non inviata';
        $this->messages['it'][] = 'Il campo ^1 non può essere inferiore a ^2 caratteri';
        $this->messages['it'][] = 'Il campo ^1 non può essere superiore a ^2 caratteri';
        $this->messages['it'][] = 'Il campo ^1 non può essere inferiore a ^2';
        $this->messages['it'][] = 'Il campo ^1 non può essere superiore a ^2';
        $this->messages['it'][] = 'Il campo ^1 è obbligatorio';
        $this->messages['it'][] = 'Il campo ^1 non ha un CNPJ valido';
        $this->messages['it'][] = 'Il campo ^1 non ha un CPF valido';
        $this->messages['it'][] = 'Il campo ^1 contiene un\'email non valida';
        $this->messages['it'][] = 'Il campo ^1 deve essere numerico';
        $this->messages['it'][] = 'Nessuna transazione attiva';
        $this->messages['it'][] = 'Oggetto non trovato';
        $this->messages['it'][] = 'Oggetto ^1 non trovato in ^2';
        $this->messages['it'][] = 'Il metodo ^1 non accetta valori nulli';
        $this->messages['it'][] = 'Il metodo ^1 deve ricevere un parametro di tipo ^2';
        $this->messages['it'][] = 'Stile ^1 non trovato in ^2';
        $this->messages['it'][] = 'Devi chiamare il costruttore ^1';
        $this->messages['it'][] = 'Devi chiamare ^1 prima di ^2';
        $this->messages['it'][] = 'Devi passare ^1 (^2) come parametro a ^3';
        $this->messages['it'][] = 'Il parametro (^1) di ^2 è obbligatorio';
        $this->messages['it'][] = 'Il parametro (^1) del costruttore ^2 è obbligatorio';
        $this->messages['it'][] = 'Hai già aggiunto un campo chiamato "^1" nel modulo';
        $this->messages['it'][] = 'Uscire dall\'applicazione?';
        $this->messages['it'][] = 'Usa addField() o setFields() per definire i campi del modulo';
        $this->messages['it'][] = 'Verifica se l\'azione (^1) esiste';
        $this->messages['it'][] = 'Informazione';
        $this->messages['it'][] = 'Errore';
        $this->messages['it'][] = 'Eccezione';
        $this->messages['it'][] = 'Domanda';
        $this->messages['it'][] = 'La classe ^1 non è stata accettata come argomento. La classe passata come parametro deve essere sottoclasse di ^2.';
        $this->messages['it'][] = 'La classe ^1 non è stata accettata come argomento. La classe passata come parametro deve implementare ^2.';
        $this->messages['it'][] = 'La classe ^1 non è stata trovata. Verifica il nome della classe o del file. Devono corrispondere';
        $this->messages['it'][] = 'Nome proprietà riservato (^1) nella classe ^2';
        $this->messages['it'][] = 'L\'azione (^1) deve essere statica per essere utilizzata in ^2';
        $this->messages['it'][] = 'Tentativo di accesso a una proprietà inesistente (^1)';
        $this->messages['it'][] = 'Modulo non trovato. Verifica se hai passato il campo (^1) a setFields()';
        $this->messages['it'][] = 'Classe ^1 non trovata in ^2';
        $this->messages['it'][] = 'Devi chiamare ^1 prima di aggiungere questo componente';
        $this->messages['it'][] = 'Driver non trovato';
        $this->messages['it'][] = 'Cerca record';
        $this->messages['it'][] = 'Campo';
        $this->messages['it'][] = 'Record aggiornato';
        $this->messages['it'][] = 'Record aggiornati';
        $this->messages['it'][] = 'Input';
        $this->messages['it'][] = 'Classe ^1 non trovata';
        $this->messages['it'][] = 'Metodo ^1 non trovato';
        $this->messages['it'][] = 'Verifica il nome della classe o del file';
        $this->messages['it'][] = 'Cancella';
        $this->messages['it'][] = 'Seleziona';
        $this->messages['it'][] = 'Devi definire il campo per l\'azione (^1)';
        $this->messages['it'][] = 'La sezione (^1) non è stata chiusa correttamente';
        $this->messages['it'][] = 'Il metodo (^1) accetta solo valori di tipo ^2 tra ^3 e ^4';
        $this->messages['it'][] = 'La classe interna ^1 non può essere eseguita';
        $this->messages['it'][] = 'La versione minima richiesta per PHP è ^1';
        $this->messages['it'][] = '^1 non è stato definito. Devi chiamare ^2 in ^3';
        $this->messages['it'][] = 'Database';
        $this->messages['it'][] = 'Costruttore';
        $this->messages['it'][] = 'Record';
        $this->messages['it'][] = 'Descrizione';
        $this->messages['it'][] = 'Errore durante la copia del file in ^1';
        $this->messages['it'][] = 'Permesso negato';
        $this->messages['it'][] = 'Estensione non consentita';
        $this->messages['it'][] = 'Errore hash';
        $this->messages['it'][] = 'Parametro non valido (^1) in ^2';
        $this->messages['it'][] = 'Avviso';
        $this->messages['it'][] = 'Nessun record trovato';
        $this->messages['it'][] = '^1 a ^2 da ^3 record';
        $this->messages['it'][] = 'Modulo PHP non trovato';
        $this->messages['it'][] = 'Il parametro (^1) di ^2 non deve essere vuoto';
        $this->messages['it'][] = 'Il ritorno non è un JSON valido. Verifica l\'URL';
        $this->messages['it'][] = 'Campi obbligatori';
        $this->messages['it'][] = 'Errore CSRF';
        $this->messages['it'][] = 'Aggiungi';
        $this->messages['it'][] = 'Espandi';
        $this->messages['it'][] = 'Il server non ha ricevuto alcun file';
        $this->messages['it'][] = 'Verifica i limiti del server';
        $this->messages['it'][] = 'Il limite attuale è';
        $this->messages['it'][] = 'Reimposta';
        $this->messages['it'][] = 'Scala orizzontale';
        $this->messages['it'][] = 'Scala verticale';
        $this->messages['it'][] = 'Sposta';
        $this->messages['it'][] = 'Ritaglia';
        $this->messages['it'][] = 'Zoom avanti';
        $this->messages['it'][] = 'Zoom indietro';
        $this->messages['it'][] = 'Ruota a destra';
        $this->messages['it'][] = 'Ruota a sinistra';
        $this->messages['it'][] = 'Domenica';
        $this->messages['it'][] = 'Lunedì';
        $this->messages['it'][] = 'Martedì';
        $this->messages['it'][] = 'Mercoledì';
        $this->messages['it'][] = 'Giovedì';
        $this->messages['it'][] = 'Venerdì';
        $this->messages['it'][] = 'Sabato';
        $this->messages['it'][] = 'Softdelete non è attivo';
        $this->messages['it'][] = 'L\'uso di contenitori di destinazione insieme alle finestre non è consentito';
        $this->messages['it'][] = 'Modalità visualizzazione';
        $this->messages['it'][] = 'Modalità zoom';
        $this->messages['it'][] = 'Mesi';
        $this->messages['it'][] = 'Mesi con giorni';
        $this->messages['it'][] = 'Giorni';
        $this->messages['it'][] = 'Giorni con ore';
        $this->messages['it'][] = 'Grande';
        $this->messages['it'][] = 'Medio';
        $this->messages['it'][] = 'Piccolo';
        $this->messages['it'][] = 'Compresso';
        $this->messages['it'][] = 'Estensione non trovata: ^1';
        $this->messages['it'][] = 'Clicca per cercare';
        $this->messages['it'][] = 'Nessun risultato';
        $this->messages['it'][] = 'selezionato';
        $this->messages['it'][] = 'Seleziona tutto';
        $this->messages['it'][] = 'Cancella la selezione';
        $this->messages['it'][] = 'Nessuna selezione';
        $this->messages['it'][] = 'Trovati (^1) record in "^2". Il record non può essere eliminato';
        $this->messages['it'][] = 'Database non trovato';
        $this->messages['it'][] = 'Tipo non supportato';
        $this->messages['it'][] = 'Inviare';
        $this->messages['it'][] = 'Regolare immagine';
        $this->messages['it'][] = 'Area di Firma';
        $this->messages['it'][] = 'Somma';
        $this->messages['it'][] = 'Minimo';
        $this->messages['it'][] = 'Massimo';
        $this->messages['it'][] = 'Media';
        $this->messages['it'][] = 'Conteggio';
        $this->messages['it'][] = 'Totale';
        $this->messages['it'][] = 'Il metodo ^1 non può essere utilizzato insieme al metodo ^2';
        
        $this->messages['fr'][] = 'Chargement';
        $this->messages['fr'][] = 'Fichier non trouvé';
        $this->messages['fr'][] = 'Recherche';
        $this->messages['fr'][] = 'Enregistrer';
        $this->messages['fr'][] = 'Enregistrement sauvegardé';
        $this->messages['fr'][] = 'Voulez-vous vraiment supprimer ?';
        $this->messages['fr'][] = 'Enregistrement supprimé';
        $this->messages['fr'][] = 'Enregistrements supprimés';
        $this->messages['fr'][] = 'Fonction';
        $this->messages['fr'][] = 'Table';
        $this->messages['fr'][] = 'Outil';
        $this->messages['fr'][] = 'Données';
        $this->messages['fr'][] = 'Ouvrir';
        $this->messages['fr'][] = 'Sauvegarder';
        $this->messages['fr'][] = 'Liste';
        $this->messages['fr'][] = 'Supprimer';
        $this->messages['fr'][] = 'Supprimer la sélection';
        $this->messages['fr'][] = 'Modifier';
        $this->messages['fr'][] = 'Annuler';
        $this->messages['fr'][] = 'Oui';
        $this->messages['fr'][] = 'Non';
        $this->messages['fr'][] = 'Janvier';
        $this->messages['fr'][] = 'Février';
        $this->messages['fr'][] = 'Mars';
        $this->messages['fr'][] = 'Avril';
        $this->messages['fr'][] = 'Mai';
        $this->messages['fr'][] = 'Juin';
        $this->messages['fr'][] = 'Juillet';
        $this->messages['fr'][] = 'Août';
        $this->messages['fr'][] = 'Septembre';
        $this->messages['fr'][] = 'Octobre';
        $this->messages['fr'][] = 'Novembre';
        $this->messages['fr'][] = 'Décembre';
        $this->messages['fr'][] = 'Aujourd\'hui';
        $this->messages['fr'][] = 'Fermer';
        $this->messages['fr'][] = 'Champ pour l\'action ^1 non défini';
        $this->messages['fr'][] = 'Le champ ^1 n\'existe pas ou contient une valeur NULL';
        $this->messages['fr'][] = 'Utilisez la méthode ^1';
        $this->messages['fr'][] = 'Formulaire sans champs';
        $this->messages['fr'][] = 'E-mail non envoyé';
        $this->messages['fr'][] = 'Le champ ^1 ne peut pas contenir moins de ^2 caractères';
        $this->messages['fr'][] = 'Le champ ^1 ne peut pas contenir plus de ^2 caractères';
        $this->messages['fr'][] = 'Le champ ^1 ne peut pas être inférieur à ^2';
        $this->messages['fr'][] = 'Le champ ^1 ne peut pas être supérieur à ^2';
        $this->messages['fr'][] = 'Le champ ^1 est requis';
        $this->messages['fr'][] = 'Le champ ^1 n\'a pas un CNPJ valide';
        $this->messages['fr'][] = 'Le champ ^1 n\'a pas un CPF valide';
        $this->messages['fr'][] = 'Le champ ^1 contient un e-mail invalide';
        $this->messages['fr'][] = 'Le champ ^1 doit être numérique';
        $this->messages['fr'][] = 'Aucune transaction active';
        $this->messages['fr'][] = 'Objet non trouvé';
        $this->messages['fr'][] = 'Objet ^1 non trouvé dans ^2';
        $this->messages['fr'][] = 'La méthode ^1 n\'accepte pas les valeurs nulles';
        $this->messages['fr'][] = 'La méthode ^1 doit recevoir un paramètre de type ^2';
        $this->messages['fr'][] = 'Style ^1 non trouvé dans ^2';
        $this->messages['fr'][] = 'Vous devez appeler le constructeur ^1';
        $this->messages['fr'][] = 'Vous devez appeler ^1 avant ^2';
        $this->messages['fr'][] = 'Vous devez passer ^1 (^2) comme paramètre à ^3';
        $this->messages['fr'][] = 'Le paramètre (^1) de ^2 est requis';
        $this->messages['fr'][] = 'Le paramètre (^1) du constructeur ^2 est requis';
        $this->messages['fr'][] = 'Vous avez déjà ajouté un champ nommé "^1" dans le formulaire';
        $this->messages['fr'][] = 'Quitter l\'application ?';
        $this->messages['fr'][] = 'Utilisez addField() ou setFields() pour définir les champs du formulaire';
        $this->messages['fr'][] = 'Vérifiez si l\'action (^1) existe';
        $this->messages['fr'][] = 'Information';
        $this->messages['fr'][] = 'Erreur';
        $this->messages['fr'][] = 'Exception';
        $this->messages['fr'][] = 'Question';
        $this->messages['fr'][] = 'La classe ^1 n\'a pas été acceptée comme argument. La classe fournie en paramètre doit être une sous-classe de ^2.';
        $this->messages['fr'][] = 'La classe ^1 n\'a pas été acceptée comme argument. La classe fournie en paramètre doit implémenter ^2.';
        $this->messages['fr'][] = 'La classe ^1 n\'a pas été trouvée. Vérifiez le nom de la classe ou du fichier. Ils doivent correspondre';
        $this->messages['fr'][] = 'Nom de propriété réservé (^1) dans la classe ^2';
        $this->messages['fr'][] = 'L\'action (^1) doit être statique pour être utilisée dans ^2';
        $this->messages['fr'][] = 'Tentative d\'accès à une propriété inexistante (^1)';
        $this->messages['fr'][] = 'Formulaire non trouvé. Vérifiez si vous avez passé le champ (^1) à setFields()';
        $this->messages['fr'][] = 'Classe ^1 non trouvée dans ^2';
        $this->messages['fr'][] = 'Vous devez appeler ^1 avant d\'ajouter ce composant';
        $this->messages['fr'][] = 'Pilote non trouvé';
        $this->messages['fr'][] = 'Rechercher un enregistrement';
        $this->messages['fr'][] = 'Champ';
        $this->messages['fr'][] = 'Enregistrement mis à jour';
        $this->messages['fr'][] = 'Enregistrements mis à jour';
        $this->messages['fr'][] = 'Entrée';
        $this->messages['fr'][] = 'Classe ^1 non trouvée';
        $this->messages['fr'][] = 'Méthode ^1 non trouvée';
        $this->messages['fr'][] = 'Vérifiez le nom de la classe ou du fichier';
        $this->messages['fr'][] = 'Effacer';
        $this->messages['fr'][] = 'Sélectionner';
        $this->messages['fr'][] = 'Vous devez définir le champ pour l\'action (^1)';
        $this->messages['fr'][] = 'La section (^1) n\'a pas été correctement fermée';
        $this->messages['fr'][] = 'La méthode (^1) accepte uniquement des valeurs de type ^2 entre ^3 et ^4';
        $this->messages['fr'][] = 'La classe interne ^1 ne peut pas être exécutée';
        $this->messages['fr'][] = 'La version minimale requise pour PHP est ^1';
        $this->messages['fr'][] = '^1 n\'a pas été défini. Vous devez appeler ^2 dans ^3';
        $this->messages['fr'][] = 'Base de données';
        $this->messages['fr'][] = 'Constructeur';
        $this->messages['fr'][] = 'Enregistrements';
        $this->messages['fr'][] = 'Description';
        $this->messages['fr'][] = 'Erreur lors de la copie du fichier vers ^1';
        $this->messages['fr'][] = 'Permission refusée';
        $this->messages['fr'][] = 'Extension non autorisée';
        $this->messages['fr'][] = 'Erreur de hachage';
        $this->messages['fr'][] = 'Paramètre invalide (^1) dans ^2';
        $this->messages['fr'][] = 'Avertissement';
        $this->messages['fr'][] = 'Aucun enregistrement trouvé';
        $this->messages['fr'][] = '^1 à ^2 sur ^3 enregistrements';
        $this->messages['fr'][] = 'Module PHP non trouvé';
        $this->messages['fr'][] = 'Le paramètre (^1) de ^2 ne doit pas être vide';
        $this->messages['fr'][] = 'Le retour n\'est pas un JSON valide. Vérifiez l\'URL';
        $this->messages['fr'][] = 'Champs requis';
        $this->messages['fr'][] = 'Erreur CSRF';
        $this->messages['fr'][] = 'Ajouter';
        $this->messages['fr'][] = 'Développer';
        $this->messages['fr'][] = 'Le serveur n\'a reçu aucun fichier';
        $this->messages['fr'][] = 'Vérifiez les limites du serveur';
        $this->messages['fr'][] = 'La limite actuelle est';
        $this->messages['fr'][] = 'Réinitialiser';
        $this->messages['fr'][] = 'Échelle horizontale';
        $this->messages['fr'][] = 'Échelle verticale';
        $this->messages['fr'][] = 'Déplacer';
        $this->messages['fr'][] = 'Rogner';
        $this->messages['fr'][] = 'Zoom avant';
        $this->messages['fr'][] = 'Zoom arrière';
        $this->messages['fr'][] = 'Rotation à droite';
        $this->messages['fr'][] = 'Rotation à gauche';
        $this->messages['fr'][] = 'Dimanche';
        $this->messages['fr'][] = 'Lundi';
        $this->messages['fr'][] = 'Mardi';
        $this->messages['fr'][] = 'Mercredi';
        $this->messages['fr'][] = 'Jeudi';
        $this->messages['fr'][] = 'Vendredi';
        $this->messages['fr'][] = 'Samedi';
        $this->messages['fr'][] = 'Softdelete n\'est pas actif';
        $this->messages['fr'][] = 'L\'utilisation de conteneurs cibles avec des fenêtres n\'est pas autorisée';
        $this->messages['fr'][] = 'Mode d\'affichage';
        $this->messages['fr'][] = 'Mode zoom';
        $this->messages['fr'][] = 'Mois';
        $this->messages['fr'][] = 'Mois avec jours';
        $this->messages['fr'][] = 'Jours';
        $this->messages['fr'][] = 'Jours avec heures';
        $this->messages['fr'][] = 'Grand';
        $this->messages['fr'][] = 'Moyen';
        $this->messages['fr'][] = 'Petit';
        $this->messages['fr'][] = 'Condensé';
        $this->messages['fr'][] = 'Extension non trouvée : ^1';
        $this->messages['fr'][] = 'Cliquez pour rechercher';
        $this->messages['fr'][] = 'Aucun résultat';
        $this->messages['fr'][] = 'sélectionné';
        $this->messages['fr'][] = 'Tout sélectionner';
        $this->messages['fr'][] = 'Effacer la sélection';
        $this->messages['fr'][] = 'Aucune sélection';
        $this->messages['fr'][] = '(^1) enregistrements trouvés dans "^2". L\'enregistrement ne peut pas être supprimé';
        $this->messages['fr'][] = 'Base de données non trouvée';
        $this->messages['fr'][] = 'Type non pris en charge';
        $this->messages['fr'][] = 'Envoyer';
        $this->messages['fr'][] = 'Ajuster l\'image';
        $this->messages['fr'][] = 'Zone de Signature';
        $this->messages['fr'][] = 'Somme';
        $this->messages['fr'][] = 'Minimum';
        $this->messages['fr'][] = 'Maximum';
        $this->messages['fr'][] = 'Moyenne';
        $this->messages['fr'][] = 'Compte';
        $this->messages['fr'][] = 'Total';
        $this->messages['fr'][] = 'La méthode ^1 ne peut pas être utilisée avec la méthode ^2';
        
        
        $this->messages['de'][] = 'Laden';
        $this->messages['de'][] = 'Datei nicht gefunden';
        $this->messages['de'][] = 'Suche';
        $this->messages['de'][] = 'Registrieren';
        $this->messages['de'][] = 'Datensatz gespeichert';
        $this->messages['de'][] = 'Möchten Sie wirklich löschen?';
        $this->messages['de'][] = 'Datensatz gelöscht';
        $this->messages['de'][] = 'Datensätze gelöscht';
        $this->messages['de'][] = 'Funktion';
        $this->messages['de'][] = 'Tabelle';
        $this->messages['de'][] = 'Werkzeug';
        $this->messages['de'][] = 'Daten';
        $this->messages['de'][] = 'Öffnen';
        $this->messages['de'][] = 'Speichern';
        $this->messages['de'][] = 'Liste';
        $this->messages['de'][] = 'Löschen';
        $this->messages['de'][] = 'Ausgewählte löschen';
        $this->messages['de'][] = 'Bearbeiten';
        $this->messages['de'][] = 'Abbrechen';
        $this->messages['de'][] = 'Ja';
        $this->messages['de'][] = 'Nein';
        $this->messages['de'][] = 'Januar';
        $this->messages['de'][] = 'Februar';
        $this->messages['de'][] = 'März';
        $this->messages['de'][] = 'April';
        $this->messages['de'][] = 'Mai';
        $this->messages['de'][] = 'Juni';
        $this->messages['de'][] = 'Juli';
        $this->messages['de'][] = 'August';
        $this->messages['de'][] = 'September';
        $this->messages['de'][] = 'Oktober';
        $this->messages['de'][] = 'November';
        $this->messages['de'][] = 'Dezember';
        $this->messages['de'][] = 'Heute';
        $this->messages['de'][] = 'Schließen';
        $this->messages['de'][] = 'Feld für Aktion ^1 nicht definiert';
        $this->messages['de'][] = 'Feld ^1 existiert nicht oder enthält NULL-Wert';
        $this->messages['de'][] = 'Verwenden Sie die Methode ^1';
        $this->messages['de'][] = 'Formular ohne Felder';
        $this->messages['de'][] = 'E-Mail nicht gesendet';
        $this->messages['de'][] = 'Das Feld ^1 darf nicht weniger als ^2 Zeichen enthalten';
        $this->messages['de'][] = 'Das Feld ^1 darf nicht mehr als ^2 Zeichen enthalten';
        $this->messages['de'][] = 'Das Feld ^1 darf nicht kleiner als ^2 sein';
        $this->messages['de'][] = 'Das Feld ^1 darf nicht größer als ^2 sein';
        $this->messages['de'][] = 'Das Feld ^1 ist erforderlich';
        $this->messages['de'][] = 'Das Feld ^1 enthält keine gültige CNPJ';
        $this->messages['de'][] = 'Das Feld ^1 enthält keine gültige CPF';
        $this->messages['de'][] = 'Das Feld ^1 enthält eine ungültige E-Mail-Adresse';
        $this->messages['de'][] = 'Das Feld ^1 muss numerisch sein';
        $this->messages['de'][] = 'Keine aktiven Transaktionen';
        $this->messages['de'][] = 'Objekt nicht gefunden';
        $this->messages['de'][] = 'Objekt ^1 in ^2 nicht gefunden';
        $this->messages['de'][] = 'Methode ^1 akzeptiert keine Nullwerte';
        $this->messages['de'][] = 'Methode ^1 muss einen Parameter vom Typ ^2 erhalten';
        $this->messages['de'][] = 'Stil ^1 in ^2 nicht gefunden';
        $this->messages['de'][] = 'Sie müssen den Konstruktor ^1 aufrufen';
        $this->messages['de'][] = 'Sie müssen ^1 vor ^2 aufrufen';
        $this->messages['de'][] = 'Sie müssen ^1 (^2) als Parameter an ^3 übergeben';
        $this->messages['de'][] = 'Der Parameter (^1) von ^2 ist erforderlich';
        $this->messages['de'][] = 'Der Parameter (^1) des Konstruktors ^2 ist erforderlich';
        $this->messages['de'][] = 'Sie haben bereits ein Feld namens "^1" im Formular hinzugefügt';
        $this->messages['de'][] = 'Anwendung beenden?';
        $this->messages['de'][] = 'Verwenden Sie addField() oder setFields(), um die Formularfelder zu definieren';
        $this->messages['de'][] = 'Überprüfen Sie, ob die Aktion (^1) existiert';
        $this->messages['de'][] = 'Information';
        $this->messages['de'][] = 'Fehler';
        $this->messages['de'][] = 'Ausnahme';
        $this->messages['de'][] = 'Frage';
        $this->messages['de'][] = 'Die Klasse ^1 wurde nicht als Argument akzeptiert. Die als Parameter angegebene Klasse muss eine Unterklasse von ^2 sein.';
        $this->messages['de'][] = 'Die Klasse ^1 wurde nicht als Argument akzeptiert. Die als Parameter angegebene Klasse muss ^2 implementieren.';
        $this->messages['de'][] = 'Die Klasse ^1 wurde nicht gefunden. Überprüfen Sie den Klassennamen oder Dateinamen. Sie müssen übereinstimmen';
        $this->messages['de'][] = 'Reservierter Eigenschaftsname (^1) in Klasse ^2';
        $this->messages['de'][] = 'Aktion (^1) muss statisch sein, um in ^2 verwendet zu werden';
        $this->messages['de'][] = 'Versuch, auf eine nicht vorhandene Eigenschaft (^1) zuzugreifen';
        $this->messages['de'][] = 'Formular nicht gefunden. Überprüfen Sie, ob Sie das Feld (^1) an setFields() übergeben haben';
        $this->messages['de'][] = 'Klasse ^1 in ^2 nicht gefunden';
        $this->messages['de'][] = 'Sie müssen ^1 aufrufen, bevor Sie diese Komponente hinzufügen';
        $this->messages['de'][] = 'Treiber nicht gefunden';
        $this->messages['de'][] = 'Datensatz suchen';
        $this->messages['de'][] = 'Feld';
        $this->messages['de'][] = 'Datensatz aktualisiert';
        $this->messages['de'][] = 'Datensätze aktualisiert';
        $this->messages['de'][] = 'Eingabe';
        $this->messages['de'][] = 'Klasse ^1 nicht gefunden';
        $this->messages['de'][] = 'Methode ^1 nicht gefunden';
        $this->messages['de'][] = 'Überprüfen Sie den Klassennamen oder Dateinamen';
        $this->messages['de'][] = 'Löschen';
        $this->messages['de'][] = 'Auswählen';
        $this->messages['de'][] = 'Sie müssen das Feld für die Aktion (^1) definieren';
        $this->messages['de'][] = 'Der Abschnitt (^1) wurde nicht ordnungsgemäß geschlossen';
        $this->messages['de'][] = 'Die Methode (^1) akzeptiert nur Werte vom Typ ^2 zwischen ^3 und ^4';
        $this->messages['de'][] = 'Die interne Klasse ^1 kann nicht ausgeführt werden';
        $this->messages['de'][] = 'Die Mindestversion für PHP ist ^1';
        $this->messages['de'][] = '^1 wurde nicht definiert. Sie müssen ^2 in ^3 aufrufen';
        $this->messages['de'][] = 'Datenbank';
        $this->messages['de'][] = 'Konstruktor';
        $this->messages['de'][] = 'Datensätze';
        $this->messages['de'][] = 'Beschreibung';
        $this->messages['de'][] = 'Fehler beim Kopieren der Datei nach ^1';
        $this->messages['de'][] = 'Zugriff verweigert';
        $this->messages['de'][] = 'Erweiterung nicht erlaubt';
        $this->messages['de'][] = 'Hash-Fehler';
        $this->messages['de'][] = 'Ungültiger Parameter (^1) in ^2';
        $this->messages['de'][] = 'Warnung';
        $this->messages['de'][] = 'Keine Datensätze gefunden';
        $this->messages['de'][] = '^1 bis ^2 von ^3 Datensätzen';
        $this->messages['de'][] = 'PHP-Modul nicht gefunden';
        $this->messages['de'][] = 'Der Parameter (^1) von ^2 darf nicht leer sein';
        $this->messages['de'][] = 'Rückgabe ist kein gültiges JSON. Überprüfen Sie die URL';
        $this->messages['de'][] = 'Erforderliche Felder';
        $this->messages['de'][] = 'CSRF-Fehler';
        $this->messages['de'][] = 'Hinzufügen';
        $this->messages['de'][] = 'Erweitern';
        $this->messages['de'][] = 'Server hat keine Datei empfangen';
        $this->messages['de'][] = 'Überprüfen Sie die Servergrenzen';
        $this->messages['de'][] = 'Das aktuelle Limit ist';
        $this->messages['de'][] = 'Zurücksetzen';
        $this->messages['de'][] = 'Horizontal skalieren';
        $this->messages['de'][] = 'Vertikal skalieren';
        $this->messages['de'][] = 'Bewegen';
        $this->messages['de'][] = 'Zuschneiden';
        $this->messages['de'][] = 'Vergrößern';
        $this->messages['de'][] = 'Verkleinern';
        $this->messages['de'][] = 'Nach rechts drehen';
        $this->messages['de'][] = 'Nach links drehen';
        $this->messages['de'][] = 'Sonntag';
        $this->messages['de'][] = 'Montag';
        $this->messages['de'][] = 'Dienstag';
        $this->messages['de'][] = 'Mittwoch';
        $this->messages['de'][] = 'Donnerstag';
        $this->messages['de'][] = 'Freitag';
        $this->messages['de'][] = 'Samstag';
        $this->messages['de'][] = 'Softdelete ist nicht aktiv';
        $this->messages['de'][] = 'Verwendung von Zielcontainern zusammen mit Fenstern ist nicht erlaubt';
        $this->messages['de'][] = 'Ansichtsmodus';
        $this->messages['de'][] = 'Zoommodus';
        $this->messages['de'][] = 'Monate';
        $this->messages['de'][] = 'Monate mit Tagen';
        $this->messages['de'][] = 'Tage';
        $this->messages['de'][] = 'Tage mit Stunden';
        $this->messages['de'][] = 'Groß';
        $this->messages['de'][] = 'Mittel';
        $this->messages['de'][] = 'Klein';
        $this->messages['de'][] = 'Kompakt';
        $this->messages['de'][] = 'Erweiterung nicht gefunden: ^1';
        $this->messages['de'][] = 'Klicken zum Suchen';
        $this->messages['de'][] = 'Keine Ergebnisse';
        $this->messages['de'][] = 'ausgewählt';
        $this->messages['de'][] = 'Alle auswählen';
        $this->messages['de'][] = 'Auswahl löschen';
        $this->messages['de'][] = 'Keine Auswahl';
        $this->messages['de'][] = '(^1) Datensätze in "^2" gefunden. Datensatz kann nicht gelöscht werden';
        $this->messages['de'][] = 'Datenbank nicht gefunden';
        $this->messages['de'][] = 'Nicht unterstützter Typ';
        $this->messages['de'][] = 'Senden';
        $this->messages['de'][] = 'Bild anpassen';
        $this->messages['de'][] = 'Unterschriftsbereich';
        $this->messages['de'][] = 'Summe';
        $this->messages['de'][] = 'Minimum';
        $this->messages['de'][] = 'Maximum';
        $this->messages['de'][] = 'Durchschnitt';
        $this->messages['de'][] = 'Anzahl';
        $this->messages['de'][] = 'Gesamt';
        $this->messages['de'][] = 'Die Methode ^1 kann nicht zusammen mit der Methode ^2 verwendet werden';
        
        //fim
	
        foreach ($this->messages as $lang => $messages)
        {
            $this->sourceMessages[$lang] = array_flip( $this->messages[ $lang ] );
        }
    }
    
    /**
     * Returns the singleton instance
     * @return  Instance of self
     */
    public static function getInstance()
    {
        // if there's no instance
        if (empty(self::$instance))
        {
            // creates a new object
            self::$instance = new self;
        }
        // returns the created instance
        return self::$instance;
    }
    
    /**
     * Define the target language
     * @param $lang Target language index
     */
    public static function setLanguage($lang)
    {
        $instance = self::getInstance();
        
        if (substr( (string) $lang,0,4) == 'auto')
        {
            $parts = explode(',', $lang);
            $lang = $parts[1];
            
            if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE']))
            {
                $autolang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
                if (in_array($autolang, array_keys($instance->messages)))
                {
                    $lang = $autolang;
                }
            }
        }
        
        if (in_array($lang, array_keys($instance->messages)))
        {
            $instance->lang = $lang;
        }
    }
    
    /**
     * Returns the target language
     * @return Target language index
     */
    public static function getLanguage()
    {
        $instance = self::getInstance();
        return $instance->lang;
    }
    
    /**
     * Translate a word to the target language
     * @param $word     Word to be translated
     * @return          Translated word
     */
    public static function translate($word, $param1 = NULL, $param2 = NULL, $param3 = NULL, $param4 = NULL)
    {
        $source_language = 'en';

        // get the self unique instance
        $instance = self::getInstance();
        // search by the numeric index of the word
        
        if (isset($instance->sourceMessages[$source_language][$word]) and !is_null($instance->sourceMessages[$source_language][$word]))
        {
            $key = $instance->sourceMessages[$source_language][$word];
            
            // get the target language
            $language = self::getLanguage();
            // returns the translated word
            $message = $instance->messages[$language][$key];
            
            if (isset($param1))
            {
                $message = str_replace('^1', $param1, $message);
            }
            if (isset($param2))
            {
                $message = str_replace('^2', $param2, $message);
            }
            if (isset($param3))
            {
                $message = str_replace('^3', $param3, $message);
            }
            if (isset($param4))
            {
                $message = str_replace('^4', $param4, $message);
            }
            return $message;
        }
        else
        {
            return 'Message not found: '. $word;
        }
    }
}
