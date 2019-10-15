<?php


namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Entity\AllCompetition;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/competitions")
 */

class CompetitionController extends AbstractController
{
    /**
     * @Route("/competition", name="competition_list")
     */
    public function list(Request $request){
        $repository = $this->getDoctrine()->getRepository(AllCompetition::class);
        $items = $repository->findAll();

        return $this->json(
            [
                'data' => array_map(function (AllCompetition $item){
                    return $this->generateUrl('competition_by_id', ['id' => $item->getId()]);
                }, $items)
            ]
        );
    }



    /**
     * @Route("/competition/{id}", name="competition_by_id", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function competition(AllCompetition $competition){
        return $this->json($competition);
    }

    /**
     * @Route("/add", name="competition_add", methods={"POST"})
     */

    public function add(Request $request){
        /** @var Serializer $serializer */
        $serializer = $this->get('serializer');
        $allCompetition = $serializer->deserialize($request->getContent(), AllCompetition::class, 'json');
        $em = $this->getDoctrine()->getManager();
        $em->persist($allCompetition);
        $em->flush();
        return $this->json($allCompetition);
    }

    /**
     * @Route("/competition/{id}", name="competition_delete", methods={"DELETE"})
     */
    public function delete(AllCompetition $competition){
        $em = $this->getDoctrine()->getManager();
        $em->remove($competition);
        $em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);

    }




}