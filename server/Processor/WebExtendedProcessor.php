<?php

namespace BiteCodes\MonologUIBundle\Processor;

use Symfony\Component\HttpFoundation\RequestStack;

class WebExtendedProcessor
{
    /**
     * @var array
     */
    protected $serverData;

    /**
     * @var array
     */
    protected $postData;

    /**
     * @var array
     */
    protected $getData;

    /**
     * @var string
     */
    protected $bodyData;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $request = $requestStack->getMasterRequest();

        $this->serverData = $request->server->all();
        $this->postData = $request->request->all();
        $this->getData = $request->query->all();
        $this->bodyData = $request->getContent();
    }

    /**
     * @param  array $record
     *
     * @return array
     */
    public function __invoke(array $record)
    {
        // skip processing if for some reason request data
        // is not present (CLI or wonky SAPIs)
        if (!isset($this->serverData['REQUEST_URI'])) {
            return $record;
        }

        $record['http_server'] = $this->serverData;
        $record['http_post'] = $this->postData;
        $record['http_get'] = $this->getData;
        $record['http_body'] = $this->bodyData;

        return $record;
    }
}
