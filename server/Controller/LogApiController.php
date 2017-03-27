<?php

namespace BiteCodes\MonologUIBundle\Controller;

use BiteCodes\MonologUIBundle\Filter\LogFilter;
use BiteCodes\MonologUIBundle\Filter\LogPagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LogApiController extends Controller
{
    /**
     * @Route("/")
     * @Method({"OPTIONS", "GET"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function indexAction(Request $request)
    {
        $grouped = $request->query->get('grouped', 0) == 1;

        if ($grouped) {
            $qb = $this->getRepo()->getGroupedLogsQueryBuilder();
        } else {
            $qb = $this->getRepo()->getLogsQueryBuilder();
        }

        $qb = LogFilter::filter($qb, $request->query->all());

        $pager = LogPagination::paginate($qb, $request->query->get('page', 1), $grouped);


        $data = [
            'data' => array_map(function ($log) {
                $log['id'] = (int)$log['id'];
                $log['level'] = (int)$log['level'];
                $log['datetime'] = (int)$log['datetime'];
                if (isset($log['count'])) {
                    $log['count'] = (int)$log['count'];
                }

                return $log;
            }, $pager->getCurrentPageResults()),
            'meta' => [
                'total' => $pager->getNbResults(),
            ],
        ];

        return new JsonResponse($data, Response::HTTP_OK, [
            'Access-Control-Allow-Headers'  => $request->headers->get("Access-Control-Request-Headers"),
            'Access-Control-Allow-Methods'  => $request->headers->get("Access-Control-Request-Method"),
            'Access-Control-Allow-Origin'   => '*',
            'Access-Control-Expose-Headers' => '*',
        ]);
    }

    /**
     * @Route("/{id}")
     * @Method({"OPTIONS", "GET"})
     *
     * @param Request $request
     * @param string  $id
     *
     * @return JsonResponse
     */
    public function showAction(Request $request, $id)
    {
        $log = $this->getRepo()->getLogById($id);

        $similarLogCount = $this->getRepo()->getSimilarLogsQueryBuilder($log)->select('COUNT(*)')->execute()->fetchColumn();

        $data = [
            'data' => $log,
            'meta' => [
                'similar_logs_count' => (int)$similarLogCount,
            ],
        ];

        return new JsonResponse($data, Response::HTTP_OK, [
            'Access-Control-Allow-Headers'  => $request->headers->get("Access-Control-Request-Headers"),
            'Access-Control-Allow-Methods'  => $request->headers->get("Access-Control-Request-Method"),
            'Access-Control-Allow-Origin'   => '*',
            'Access-Control-Expose-Headers' => '*',
        ]);
    }

    /**
     * @Route("/{id}")
     * @Method({"OPTIONS", "DELETE"})
     *
     * @param Request $request
     * @param string  $id
     *
     * @return JsonResponse
     */
    public function deleteAction(Request $request, $id)
    {
        $log = $this->getRepo()->getLogById($id);

        if ($log) {
            $this->getRepo()->removeLog($log, $request->request->get('similar', false));
        }

        return new JsonResponse('', Response::HTTP_NO_CONTENT, [
            'Access-Control-Allow-Headers'  => $request->headers->get("Access-Control-Request-Headers"),
            'Access-Control-Allow-Methods'  => $request->headers->get("Access-Control-Request-Method"),
            'Access-Control-Allow-Origin'   => '*',
            'Access-Control-Expose-Headers' => '*',
        ]);
    }

    /**
     * @return \BiteCodes\MonologUIBundle\Model\LogRepository|object
     */
    protected function getRepo()
    {
        return $this->get('bite_codes_monolog_ui.model.log_repository');
    }
}
