<?php

namespace Kitsune\Controllers;

use FeedWriter\RSS2;
use FeedWriter\Item;
use Phalcon\Http\Response;
use Kitsune\Controller;

class PostsController extends Controller
{
    public function indexAction($page = 1)
    {
        $this->view->setVar(
            'posts',
            $this->finder->getLatest($page, $this->config->blog->postsPerPage)
        );
        $this->view->setVar('pages', $this->finder->getPages($page));
    }

    public function tagAction($tag)
    {
        $this->view->pick('posts/index');
        $this->view->setVar('posts', $this->finder->getLatestByTag($tag, 10));
    }

    /**
     * Handles the RSS action. Constructs the rss feed of the latest posts. The
     * number of posts to return is stored in the configuration section
     *
     * @return Response
     */
    public function rssAction()
    {
        $feed = new RSS2();
        $feed->setEncoding('UTF-8');
        $feed->setTitle($this->config->rss->title);
        $feed->setDescription($this->config->rss->description);
        $feed->setLink($this->getFullUrl());

        $posts = $this->finder->getLatest(1);
        foreach ($posts as $post) {
            $feedItem = new Item();
            $feedItem->setTitle($post->getTitle());
            $feedItem->setLink($this->getFullUrl('/post/' . $post->getSlug()));
            $feedItem->setDescription($post->getContent());
            $feedItem->setDate($post->getDate());

            $feed->addItem($feedItem);
        }

        $response = new Response();
        $response->setHeader('Content-Type', 'application/xml');
        $response->setContent($feed->generateFeed());

        return $response;
    }

    /**
     * Handles the viewing of a post. The $slug can be either a number or a
     * string (actual slug). The number is when we have previous posts i.e.
     * from Disqus
     *
     * @param string|integer $slug The unique identifier of the post
     */
    public function viewAction($slug)
    {
        $post = $this->finder->get($slug);
        if (is_null($post)) {
            $this->dispatcher->forward(
                [
                    'controller' => 'errors',
                    'action'     => 'show404'
                ]
            );
        }

        $this->view->setVar('posts', $post);
        $this->view->setVar('post', $post);
        $this->view->setVar('title', $post ? $post->getTitle() : '');
    }

    public function aboutAction()
    {
        $this->tag->setTitle('About');
    }

    public function disclaimerAction()
    {
        $this->tag->setTitle('Disclaimer');
    }


    public function viewLegacyBySlugAction($time, $slug)
    {
        $this->dispatcher->forward(
            [
                'controller' => 'errors',
                'action'     => 'show404'
            ]
        );
    }

    public function viewLegacyByTimeAction($time, $slug)
    {
        $this->dispatcher->forward(
            [
                'controller' => 'errors',
                'action'     => 'show404'
            ]
        );
    }

    protected function getFullUrl($uri = '/')
    {
        return $this->request->getScheme() . '://' . $this->request->getServerName() . $uri;
    }
}
