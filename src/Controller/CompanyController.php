<?php

namespace App\Controller;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController
{
    public function __construct(private readonly CompanyRepository $companyRepository)
    {
    }

    #[Route('/companies', name: 'app_companies', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $search = $request->query->get('search');
        $companyData = $this->companyRepository->getAggregatedData($search);
        return $this->render('company/index.html.twig', [
            'companies' => $companyData,
            'search' => $search,
        ]);
    }

    #[Route('/companies/{id}', name: 'app_company_edit', methods: ['GET'])]
    public function  edit(int $id){
        $company = $this->companyRepository->find($id);
        return $this->render('company/edit.html.twig', [
            'company' => $company,
        ]);
    }

    #[Route('/companies', name: 'app_company_submit', methods: ['POST'])]
    public function  submit(Request $request){
        // OWASP 3 - XSS Injection - unescaped at input
        $id = $request->request->get('id');
        $name = $request->request->get('name');
        $company = $id ? $this->companyRepository->find($id) : new Company();
        $company->setName($name);

        $this->companyRepository->save($company, true);
        return $this->redirectToRoute('app_companies');

    }
}
