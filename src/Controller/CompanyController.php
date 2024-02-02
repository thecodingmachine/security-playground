<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;
use App\Exception\CompanyNotFoundException;
use App\Form\CompanyFormType;
use App\Repository\CompanyRepository;
use App\Serializer\CompanyNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController
{
    public function __construct(private readonly CompanyRepository $companyRepository, private readonly CompanyNormalizer $companyNormalizer)
    {
    }

    #[Route('/companies', name: 'app_companies', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);

        return $this->render('company/index.html.twig', [
            'company' => $user->getCompany(),
        ]);
    }

    #[Route('/companies/{id}/about/ajax', name: 'app_company_about', methods: ['GET'])]
    public function about(int $id): JsonResponse
    {
        $company = $this->companyRepository->find($id);
        if ($company === null) {
            throw new CompanyNotFoundException("The company with the $id was not found");
        }

        return new JsonResponse(['company' => $this->companyNormalizer->normalize($company)]);
    }

    #[Route('/companies/{id}', name: 'app_company_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        $company = $this->companyRepository->find($id);
        if ($company === null) {
            throw new CompanyNotFoundException("The company with the $id was not found");
        }

        $form = $this->createForm(CompanyFormType::class, $company);

        $form->handleRequest($request);
        if ($request->isMethod('POST') && $form->isSubmitted() && $form->isValid()) {
            $resource = $form->getData();

            $this->companyRepository->save($resource, true);

            $this->addFlash('success', "Votre société a bien été modifiée");
        }

        return $this->render('company/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/companies-old', name: 'app_companies_old', methods: ['GET'])]
    public function search(Request $request): Response
    {
        $search = $request->query->get('search');
        $companyData = $this->companyRepository->getAggregatedData($search);

        return $this->render('company-old/index.html.twig', [
            'companies' => $companyData,
            'search' => $search,
        ]);
    }

    #[Route('/companies-old/{id}', name: 'app_company_edit_old', methods: ['GET'])]
    public function editOld(int $id) {
        $company = $this->companyRepository->find($id);
        return $this->render('company-old/edit.html.twig', [
            'company' => $company,
        ]);
    }

    #[Route('/companies-old', name: 'app_company_submit_old', methods: ['POST'])]
    public function submit(Request $request){
        $id = $request->request->get('id');
        $name = $request->request->get('name');
        $company = $id ? $this->companyRepository->find($id) : new Company();
        $company->setName($name);

        $this->companyRepository->save($company, true);
        return $this->redirectToRoute('app_companies_old');

    }
}
