<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;
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
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('company/index.html.twig', [
            'company' => $user->getCompany(),
        ]);
    }

    #[Route('/companies/{id}', name: 'app_company_edit', methods: ['GET'])]
    public function  edit(int $id): Response
    {
        $company = $this->companyRepository->find($id);
        return $this->render('company/edit.html.twig', [
            'company' => $company,
        ]);
    }
}
