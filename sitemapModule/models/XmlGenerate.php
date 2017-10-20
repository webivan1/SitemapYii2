<?php
/**
 * Created by PhpStorm.
 * User: maltsev
 * Date: 20.10.2017
 * Time: 14:53
 */

namespace common\modules\sitemap\models;

use Yii;

class XmlGenerate
{
    private $component;
    private $xml;
    private $items = [];
    private $files = [];

    private static $docPrefixName = 0;

    public function __construct($clearAll = true)
    {
        $this->component = Yii::$app->sitemapComponent;

        $this->xml = new \XMLWriter();

        $this->files = glob($this->component->pathSitemapFiles . '/*.xml');

        if (!empty($this->files) && $clearAll === true) {
            $this->deleteAll($this->files);
        }
    }

    public function deleteAll(array $files)
    {
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function getItems()
    {
        return $this->items;
    }

    private function openDocument()
    {
        $fullPathFile = $this->component->pathSitemapFiles
            . '/'
            . $this->component->baseNameFile
            . (self::$docPrefixName >= 1 ? self::$docPrefixName : null)
            . '.xml';

        if (!is_file($fullPathFile)) {
            $fs = fopen($fullPathFile, 'w+');
            fclose($fs);

            @chmod($fullPathFile, 0777);
        }

        $this->xml->openURI($fullPathFile);
        $this->xml->startDocument('1.0', 'UTF-8');
        $this->xml->setIndent(true);
        $this->xml->startElement('urlset');
        $this->xml->writeAttribute('xmlns', $this->component->xmlns);
    }

    private function endDocument()
    {
        $this->xml->endElement();
        $this->xml->endDocument();
    }

    public function createFile()
    {
        if (empty($this->items)) {
            return false;
        }

        $this->openDocument();

        foreach ($this->items as $attributes) {
            $this->xml->startElement('url');

            foreach ($attributes as $key => $value) {
                if (!empty($value)) {
                    $this->xml->writeElement($key, $value);
                }
            }

            $this->xml->endElement();
        }

        $this->items = [];

        $this->endDocument();

        return true;
    }

    public function appendTo(array $urls)
    {
        foreach ($urls as $urlObject) {
            $this->items[] = $urlObject->getAttributes();

            if (sizeof($this->items) >= $this->component->maxMapRecords) {
                $this->createFile();
                self::$docPrefixName++;
            }
        }
    }

    public function wrapper($domain)
    {
        $this->xml->openMemory();
        $this->xml->startDocument('1.0', 'UTF-8');
        $this->xml->startElement('sitemapindex');
        $this->xml->writeAttribute('xmlns', $this->component->xmlns);

        if (!empty($this->files)) {
            foreach ($this->files as $file) {
                $absoluteFile = rtrim($domain, '/') . str_replace(Yii::getAlias('@webroot'), '', $file);

                $this->xml->startElement('sitemap');
                $this->xml->writeElement('loc', $absoluteFile);
                $this->xml->writeElement('lastmod', date('c', filectime($file)));
                $this->xml->endElement();
            }
        }

        $this->xml->endElement();
        $this->xml->endDocument();

        return $this->xml->outputMemory();
    }
}