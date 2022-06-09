<?php
namespace Adianti\Service;

use Adianti\Core\AdiantiApplicationConfig;
use Adianti\Util\AdiantiStringConversion;
use Adianti\Database\TTransaction;
use Adianti\Database\TRepository;
use Adianti\Database\TCriteria;
use Adianti\Database\TFilter;
use Adianti\Database\TExpression;

use StdClass;
use Exception;

/**
 * MultiSearch backend
 *
 * @version    7.4
 * @package    service
 * @author     Pablo Dall'Oglio
 * @author     Matheus Agnes Dias
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class AdiantiMultiSearchService
{
    /**
     * Search by the given word inside a model
     */
	public static function onSearch($param = null)
	{
        $key  = $param['key'];
        $ini  = AdiantiApplicationConfig::get();
        $seed = APPLICATION_NAME . ( !empty($ini['general']['seed']) ? $ini['general']['seed'] : 's8dkld83kf73kf094' );
        $hash = md5("{$seed}{$param['database']}{$param['key']}{$param['column']}{$param['model']}");
        $mask = $param['mask'];
        
        if ($hash == $param['hash'])
        {
            try
            {
                TTransaction::openFake($param['database']);
                $info = TTransaction::getDatabaseInfo();
                $default_op = $info['type'] == 'pgsql' ? 'ilike' : 'like';
                $operator   = !empty($param['operator']) ? $param['operator'] : $default_op;
                
                $repository = new TRepository($param['model']);
                $criteria = new TCriteria;
                if ($param['criteria'])
                {
                    $criteria = unserialize( base64_decode(str_replace(array('-', '_'), array('+', '/'), $param['criteria'])) );
                }
    
                $columns = explode(',', $param['column']);
                
                if (!isset($param['value']))
                {
                    $param['value'] = '';
                }
                
                if ($columns)
                {
                    $dynamic_criteria = new TCriteria;
                    
                    if (empty($param['onlyidsearch']))
                    {
                        foreach ($columns as $column)
                        {
                            if (stristr(strtolower($operator),'like') !== FALSE)
                            {
                                $param['value'] = str_replace(' ', '%', $param['value']);
                                
                                if (in_array($info['type'], ['mysql', 'oracle', 'mssql', 'dblib', 'sqlsrv']))
                                {
                                    $filter = new TFilter("lower({$column})", $operator, strtolower("%{$param['value']}%"));
                                }
                                else
                                {
                                    $filter = new TFilter($column, $operator, "%{$param['value']}%");
                                }
                            }
                            else
                            {
                                $filter = new TFilter($column, $operator, $param['value']);
                            }
        
                            $dynamic_criteria->add($filter, TExpression::OR_OPERATOR);
                        }
                    }
                    
                    $id_search_value = ((!empty($param['idtextsearch']) && $param['idtextsearch'] == '1') || ((defined("{$param['model']}::IDPOLICY")) AND (constant("{$param['model']}::IDPOLICY") == 'uuid'))) ? $param['value'] : (int) $param['value'];
                    
                    if ($param['idsearch'] == '1' and !empty( $id_search_value ))
                    {
                        $dynamic_criteria->add( new TFilter($key, '=', $id_search_value), TExpression::OR_OPERATOR);
                    }
                }
                
                if (!$dynamic_criteria->isEmpty())
                {
                    $criteria->add($dynamic_criteria, TExpression::AND_OPERATOR);
                }
                $criteria->setProperty('order', $param['orderColumn']);
                $criteria->setProperty('limit', 1000);
                
                $items = array();
                
                if (!empty($param['value']) || $param['minlength'] == '0')
                {
                    $collection = $repository->load($criteria, FALSE);
                    
                    foreach ($collection as $object)
                    {
                        $k = $object->$key;
                        $maskvalues = $mask;
                        
                        $maskvalues = $object->render($maskvalues);
                        
                        // replace methods
                        $methods = get_class_methods($object);
                        if ($methods)
                        {
                            foreach ($methods as $method)
                            {
                                if (stristr($maskvalues, "{$method}()") !== FALSE)
                                {
                                    $maskvalues = str_replace('{'.$method.'()}', $object->$method(), $maskvalues);
                                }
                            }
                        }
                        
                        $c = $maskvalues;
                        if ( $k != null && $c != null )
                        {
                            $c = AdiantiStringConversion::assureUnicode($c);
                            
                            if (!empty($k) && !empty($c))
                            {
                                $items[] = "{$k}::{$c}";
                            }
                        }
                    }
                }
                
                $ret = array();
                $ret['result'] = $items;
                echo json_encode($ret);
                TTransaction::close();
            }
            catch (Exception $e)
            {
                $ret = array();
                $ret['result'] = array("1::".$e->getMessage());
                
                echo json_encode($ret);
            }
        }
	}
}
