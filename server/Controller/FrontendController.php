<?php

namespace BiteCodes\MonologUIBundle\Controller;

use Limenius\ReactRenderer\Renderer\PhpExecJsReactRenderer;
use Limenius\ReactRenderer\Twig\ReactRenderExtension;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Twig_Extension_StringLoader;

class FrontendController extends Controller
{
    /**
     * @Route("/")
     * @Method("GET")
     */
    public function showAction()
    {
        $renderer = new PhpExecJsReactRenderer(__DIR__ . '/../Resources/js/main.js');
        $ext = new ReactRenderExtension($renderer, 'client_side');

        $twig = $this->get('twig');
        $twig->addExtension(new Twig_Extension_StringLoader());
        $twig->addExtension($ext);

        $html = $twig->render('@BiteCodesMonologUI/monolog-ui.html.twig', [
            'serverUrl' => json_encode($this->generateUrl('bitecodes_monologui_frontend_show')),
            'channels' => json_encode($this->getParameter('bitecodes_monolog_ui.channels')),
        ]);

        return new Response($html);
    }
}