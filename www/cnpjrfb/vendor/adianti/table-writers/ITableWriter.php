<?php
/**
 * Define uma interface para escrita de tabelas
 * @author Pablo Dall'Oglio
 */
interface ITableWriter
{
    public function __construct($widths);
    public function addStyle($stylename, $fontface, $fontsize, $fontstyle, $fontcolor, $fillcolor, $border = null);
    public function addRow();
    public function addCell($content, $align, $stylename, $colspan = 1);
    public function save($filename);
}
