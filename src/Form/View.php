<?php

/*
 * This file is part of the Crudity package.
 *
 * (c) Alban Pommeret <alban@aocreation.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Neverdane\Crudity\Form;

use Neverdane\Crudity\Form\Parser\Parser;

/**
 * @package Neverdane\Crudity
 * @author Alban Pommeret <alban@aocreation.com>
 */
class View
{
    const RENDER_TYPE_FILE = 1;
    const RENDER_TYPE_HTML = 2;
    const RENDER_TYPE_OBJECTS = 3;

    public $renderType = self::RENDER_TYPE_HTML;
    public $rendering = null;
    public $formattedRendering = null;
    private $config = null;
    private $parser = null;

    public function __construct($rendering, $renderType = self::RENDER_TYPE_HTML)
    {
        $this->setConfig(Config::getDefault(Config::TYPE_VIEW));
        $this->setRendering($rendering, $renderType);
    }

    public function setRendering($rendering, $renderType)
    {
        $this->rendering = $rendering;
        $this->renderType = $renderType;
        return $this;
    }

    public function render()
    {
        if(is_null($this->formattedRendering)) {
            $parser = $this->getParser();
            $parser->insertConfig($this->getConfig());
            $this->formattedRendering = $parser->getAdapter()->getHtml();
        }
        return $this->formattedRendering;
    }

    /**
     * @return Parser
     */
    public function getParser()
    {
        if(is_null($this->parser)) {
            $this->setParser(new Parser($this->rendering));
        }
        return $this->parser;
    }

    public function setParser($parser)
    {
        $this->parser = $parser;
        return $this;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function setConfig($config = array())
    {
        $this->config = $config;
        return $this;
    }

    public function getConfig()
    {
        return $this->config;
    }
}
