<?php
/*
 +------------------------------------------------------------------------+
 | Kitsune                                                                |
 +------------------------------------------------------------------------+
 | Copyright (c) 2015-2015 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file docs/LICENSE.txt.                        |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

/**
 * Post.php
 * \Kitsune\PostFinder
 *
 * Represents a post
 */
namespace Kitsune;

use Kitsune\Markdown as KMarkdown;

class Post
{
    private $data = [];

    public function getTitle()
    {
        return $this->data['title'];
    }

    public function getSlug()
    {
        return $this->data['slug'];
    }

    public function getDate()
    {
        return $this->data['date'];
    }

    public function getYear()
    {
        return $this->data['year'];
    }

    public function getMonth()
    {
        return $this->data['month'];
    }

    public function getContent()
    {
        return $this->data['content'];
    }

    public function getRaw()
    {
        return $this->data['raw'];
    }

    public function getLink()
    {
        return $this->data['link'];
    }

    public function getTags()
    {
        return $this->data['tags'];
    }

    public function getFile()
    {
        return $this->data['file'];
    }

    public function getDisqusId()
    {
        return $this->data['disqusId'];
    }

    public function getDisqusUrl()
    {
        return $this->data['disqusUrl'];
    }

    public function __construct($config, $markdown)
    {
        $this->config   = $config;
        $this->markdown = $markdown;
        $this->url      = $config->blog->disqus->url;

        $this->init();
    }

    public function load($post)
    {
        /**
         * Initialize the internal array
         */
        $this->init();

        $dateParts           = explode("-", $post['date']);
        $this->data['year']  = $dateParts[0];
        $this->data['month'] = $dateParts[1];
        $this->data['date']  = $post['date'];
        $this->data['slug']  = $post['slug'];
        $this->data['title'] = $post['title'];
        $this->data['link']  = $post['link'];

        if ($this->getLink()) {
            $this->data['disqusUrl'] = $this->getLink();
            $this->data['disqusId']  = sprintf(
                $this->config->blog->disqus->idTemplate,
                $this->getTitle()
            );
        } else {
            $this->data['disqusUrl'] = $this->config->blog->disqus->url
                                     . '/post/'
                                     . $this->getSlug();
            $this->data['disqusId']  = sprintf(
                $this->config->blog->disqus->idTemplate,
                str_replace(['"', "''"], ['', ''], $this->getTitle())
            );
        }

        $this->data['file'] = sprintf(
            '%s/%s/%s-%s.md',
            $dateParts[0],
            $dateParts[1],
            $this->getDate(),
            $this->getSlug()
        );

        $tags = explode(',', $post['tags']);
        foreach ($tags as $tag) {
            $this->data['tags'][] = trim($tag);
        }

        /**
         * Get the cdnUrl
         */
        $cdnUrl = $this->config->cdnUrl;

        /**
         * Get the post itself
         */
        $fileName = K_PATH . '/data/posts/' . $this->getFile();
        if (file_exists($fileName)) {
            $markdown              = new KMarkdown();
            $this->data['raw']     = file_get_contents($fileName);
            $this->data['raw']     = str_replace('{{ cdnUrl }}', $cdnUrl, $this->getRaw());
            $this->data['content'] = $markdown->render($this->getRaw());
        }
    }

    private function init()
    {
        $this->data = [
            'title'     => '',
            'slug'      => '',
            'date'      => '',
            'year'      => '',
            'month'     => '',
            'content'   => '',
            'raw'       => '',
            'link'      => '',
            'tags'      => [],
            'disqusId'  => '',
            'disqusUrl' => '',
            'file'      => '',
        ];
    }
}
