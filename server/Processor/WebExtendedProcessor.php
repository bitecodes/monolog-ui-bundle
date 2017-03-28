<?php

namespace BiteCodes\MonologUIBundle\Processor;

use Symfony\Component\HttpFoundation\RequestStack;

class WebExtendedProcessor
{
    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param  array $record
     *
     * @return array
     */
    public function __invoke(array $record)
    {

        $request = $this->requestStack->getMasterRequest();

        // skip processing if for some reason request data
        // is not present (CLI or wonky SAPIs)
        if (!$request->server->has('REQUEST_URI')) {
            return $record;
        }

        $record['http_server'] = $request ? $request->server->all() : [];
        $record['http_post'] = $request ? $request->request->all() : [];
        $record['http_get'] = $request ? $request->query->all() : [];
        $record['http_body'] = $request ? $request->getContent() : [];

        return $record;
    }
}
