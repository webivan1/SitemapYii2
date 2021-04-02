<?php
/**
 * Created by PhpStorm.
 * User: maltsev
 * Date: 20.10.2017
 * Time: 14:53
 */

namespace webivan\sitemap\models;

use Yii;

class XmlGenerate
{
    /**
     * @property SitemapComponent
     */
    private $component;

    /**
     * @property \XMLWriter
     */
    private $xml;

    /**
     * @property array
     */
    private $items = [];

    /**
     * @property array
     */
    private $files = [];

    /**
     * Prefix sitemap filename
     *
     * @property int
     */
    private static $docPrefixName = 0;

    /**
     * init
     *
     * @param bool $clearAll
     */
    public function __construct($clearAll = true)
    {
        $this->component = Yii::$app->sitemapComponent;

        $this->xml = new \XMLWriter();

        $this->files = glob($this->component->pathSitemapFiles . '/*.xml');

        if (!empty($this->files) && $clearAll === true) {
            $this->deleteAll();
        }
    }

    /**
     * Delete all files *.xml
     */
    public function deleteAll()
    {
        foreach ($this->files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Get items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Start XML document file
     */
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
        } else {
            self::$docPrefixName++;
            $this->openDocument();
        }

        $this->xml->openURI($fullPathFile);
        $this->xml->startDocument('1.0', 'UTF-8');
        $this->xml->setIndent(true);
        $this->xml->startElement('urlset');
        $this->xml->writeAttribute('xmlns', $this->component->xmlns);
    }

    /**
     * End XML document file
     */
    private function endDocument()
    {
        $this->xml->endElement();
        $this->xml->endDocument();
    }

    /**
     * Create file
     *
     * @return bool
     */
    public function createFile()
    {
        if (empty($this->items)) {
            return false;
        }

        self::$docPrefixName += 1;

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

    /**
     * Add new record sitemap
     *
     * @param ItemUrlConfigure[] $urls
     * @return void
     */
    public function appendTo(array $urls)
    {
        foreach ($urls as $urlObject) {
            $this->items[] = $urlObject->accessTags();

            if (sizeof($this->items) >= $this->component->maxMapRecords) {
                $this->createFile();
            }
        }
    }

    /**
     * Create document wrapper
     *
     * @param string $domain
     * @return string
     */
    public function wrapper()
    {
        $this->xml->openMemory();
        $this->xml->startDocument('1.0', 'UTF-8');
        $this->xml->startElement('sitemapindex');
        $this->xml->writeAttribute('xmlns', $this->component->xmlns);

        if (!empty($this->files)) {
            foreach ($this->files as $file) {
                $absoluteFile = rtrim($this->component->domain, '/') . str_replace(Yii::getAlias('@webroot'), '', $file);

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
