<?php
/**
 * Route translator
 *
 * @version    7.6
 * @package    core
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2014 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    https://adiantiframework.com.br/license-template
 */
class AdiantiRouteTranslator
{
    public static function translate($url, $format = TRUE)
    {
        /**
         // manual entries
         $routes = array();
         $routes['class=TipoProdutoList'] = 'tipo-produto-list';
         $routes['class=TipoProdutoList&method=onReload'] = 'tipo-produto-list';
         $routes['class=TipoProdutoForm&method=onEdit']   = 'tipo-produto-edit';
         $routes['class=TipoProdutoForm&method=onDelete'] = 'tipo-produto-ondelete';
         $routes['class=TipoProdutoForm&method=delete']   = 'tipo-produto-delete';
         */
        
        // automatic parse .htaccess
        $routes = self::parseHtAccess();
        
        $keys = array_map('strlen', array_keys($routes));
        array_multisort($keys, SORT_DESC, $routes);
        
        foreach ($routes as $pattern => $short)
        {
            $new_url = self::replace($url, $pattern, $short);
            if ($url !== $new_url)
            {
                return $new_url;
            }
        }
        
        foreach ($routes as $pattern => $short)
        {
            // ignore default page loading methods
            $pattern = str_replace(['&method=onReload', '&method=onShow'], ['',''], $pattern);
            $new_url = self::replace($url, $pattern, $short);
            if ($url !== $new_url)
            {
                return $new_url;
            }
        }
        
        if ($format)
        {
            return 'index.php?'.$url;
        }
        
        return $url;
    }
    
    /**
     * Replace URL with pattern by short version
     * @param $url full original URL
     * @param $pattern pattern to be replaced
     * @param $short short version
     */
    private static function replace($url, $pattern, $short)
    {
        if (strpos($url, $pattern) !== FALSE)
        {
            $url = str_replace($pattern.'&', $short.'?', $url);
            if (strlen($url) == strlen($pattern))
            {
                $url = str_replace($pattern, $short, $url);
            }
        }
        return $url;
    }
    
    /**
     * Parse HTAccess routes
     * returns ARRAY[action] = route
     *     Ex: ARRAY["class=TipoProdutoList&method=onReload"] = "tipo-produto-list"
     */
    public static function parseHtAccess()
    {
        $rotas = [];
        if (file_exists('.htaccess'))
        {
            $rules = file('.htaccess');
            foreach ($rules as $rule)
            {
                $rule = preg_replace('/\s+/', ' ',$rule);
                $rule_parts = explode(' ', $rule);
                
                if ($rule_parts[0] == 'RewriteRule')
                {
                    $source = $rule_parts[1];
                    $target = $rule_parts[2];
                    $source = str_replace(['^', '$'], ['',''], $source);
                    $target = str_replace('&%{QUERY_STRING}', '', $target);
                    $target = str_replace(' [NC]', '', $target);
                    $target = str_replace('index.php?', '', $target);
                    $rotas[$target] = $source;
                }
            }
        }
        
        return $rotas;
    }
}